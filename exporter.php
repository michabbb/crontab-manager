<?php
require_once 'vendor/autoload.php';

use exporter\config\config;
use exporter\db\db;
use exporter\parser\parser;
use Pimple\Container;
use Slim\Http\Request;
use Slim\Http\Response;

$container = new Container();

try {
    $config = new config();
    $container['config'] = $config;
    $db = new db($container);
    $db->connect();
    $container['db'] = $db;
} catch (\Exception $e) {
    exceptionhandler::handler('Unable to init config', $e);
}

$parser               = new parser();
$arrayallcrontabs     = [];
$nagios_configsServer = [];

if (\exporter\utils\utils::isCLI()) {

	foreach ($config->getServers() as $server => $serverconfig) {
		echo 'Servername: ' . $serverconfig['servername'] . "\n";
		echo 'IP        : ' . $serverconfig['serverip'] . "\n";

		$privatekey = (array_key_exists('privatekey', $serverconfig) && $serverconfig['privatekey']) ? $serverconfig['privatekey'] : null;

		try {
			$SSH = new \exporter\ssh($serverconfig['serverip'], $serverconfig['serverport'], $serverconfig['user'], '', $privatekey);
			$SSH->setDebug(true);
			$SSH->login();
		} catch (RuntimeException $e) {
			throw new RuntimeException($e);
		}

		$nagios_commands = $SSH->getNagiosFromRemoteServer($serverconfig['cronuser']);
		if (($nagios_commands!==false) && (strlen($nagios_commands)>0)) {
			$nagios_configsServer[$server] = $parser->parseNagiosNrpeCfg($nagios_commands);
		}

		$crontab = $SSH->getCrontabFromRemoteServer($serverconfig['cronuser']);
		if ($crontab !== false) {

			$crontab_parsed = $parser->getParsedCrontab($crontab);
			//print_r($crontab_parsed);
			echo 'Array aus getParsedCrontab: ';
			$arrayallcrontabs[$server] = $crontab_parsed;
		}

		$SSH->disconnect();

	}

//$arrayallcrontabs = array_merge(...$arrayallcrontabs);

//print_r($crontab_parsed);
//print_r($arrayallcrontabs);

	$db->begin();

	/** @var array[] $groupdata */
	foreach ($config->getServers() as $servername => $serverconfig) {
		echo 'Server: ' . $servername . "\n";

		$server = new \exporter\objects\db\server($container);
		$server->setDescr($config->getServers()[$servername]['servername']);
		$server->setIp($config->getServers()[$servername]['serverip']);
		$server->delete();

		/** @noinspection ForeachSourceInspection */
		foreach ($nagios_configsServer[$servername] as $nrpe_command_name => $nrpe_command) {
			$NrpeCommand = new \exporter\objects\db\nrpe($container);
			$NrpeCommand->setCommandName($nrpe_command_name);
			$NrpeCommand->setCommand($nrpe_command);
			$NrpeCommand->save($server);
		}

		if (array_key_exists($servername,$arrayallcrontabs)) {

			/** @noinspection ForeachSourceInspection */
			foreach ($arrayallcrontabs[$servername] as $groups => $groupdata) {
				$jobGroup = new \exporter\objects\db\jobGroup($container);
				if ($groupdata['groupcomment']) {
					$jobGroup->setComment($groupdata['groupcomment']);
				}
				$jobList = new \exporter\objects\db\jobList();
				foreach ($groupdata['jobs'] as $jobdata) {
					print_r($jobdata);
					$job = new \exporter\objects\db\job();
					$job->setServer($server);
					$job->setUser($config->getServers()[$servername]['cronuser']);
					$job->setComment($jobdata['comment']);
					$job->setCommentInactive($jobdata['comment_inactive']);
					$job->setCommand($jobdata['matches']['command']);
					$job->setTime(
						$jobdata['matches']['m'],
						$jobdata['matches']['h'],
						$jobdata['matches']['dom'],
						$jobdata['matches']['mon'],
						$jobdata['matches']['dow']
					);
					$jobList->addJob($job);
				}
				$jobGroup->setJobList($jobList);
				try {
					$jobGroup->save();
				} catch (RuntimeException $e) {
					$db->rollback();
					throw new \RuntimeException($e);
				}
			}

		}

	}

	$jobGroup = new \exporter\objects\db\jobGroup($container);
	$jobGroup->deleteNotAssigned();

	$db->commit();

} else {

	$app = new \Slim\App;

	header('Access-Control-Allow-Origin: *'); //in case you are using the php internal webserver

	$app->get('/server', function (Request $request, Response $response, array $args) use ($container) {
		//$name = $args['name'];
		$server = new \exporter\objects\db\server($container);
		$response->withHeader('Access-Control-Allow-Origin','*');
		$response->getBody()->write(json_encode($server->getall()['result']));
		return $response;
	});

	$app->get('/cronjobs', function (Request $request, Response $response, array $args) use ($container) {
		//$name = $args['name'];
		$jobGroup = new \exporter\objects\db\jobGroup($container);
		$response->withHeader('Access-Control-Allow-Origin','*');
		$response->getBody()->write(json_encode($jobGroup->getall()['result']));
		return $response;
	});

	$app->get('/nrpe', function (Request $request, Response $response, array $args) use ($container) {
		//$name = $args['name'];
		$nrpe = new \exporter\objects\db\nrpe($container);
		$response->withHeader('Access-Control-Allow-Origin','*');
		$response->getBody()->write(json_encode($nrpe->getall()['result']));
		return $response;
	});

	try {
		$app->run();
	} catch (\Slim\Exception\MethodNotAllowedException $e) {
		echo $e->getTraceAsString();
	} catch (\Slim\Exception\NotFoundException $e) {
		echo $e->getTraceAsString();
	} catch (Exception $e) {
		echo $e->getTraceAsString();
	}

}