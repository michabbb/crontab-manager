<?php
require_once 'vendor/autoload.php';

use exporter\config\config;
use exporter\db\db;
use exporter\parser\parser;
use GetOpt\GetOpt;
use GetOpt\Option;
use Pimple\Container;

$getopt = new Getopt(array(
    new Option('m', 'mode', Getopt::REQUIRED_ARGUMENT),
    new Option('t', 'todo', Getopt::OPTIONAL_ARGUMENT)
));
$getopt->parse();

// print_r($getopt);

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

$parser = new parser();
$arrayallcrontabs = [];

foreach ($config->getServers() as $server => $serverconfig) {
    echo 'Servername: ' . $serverconfig['servername'] . "\n";
    echo 'IP        : ' . $serverconfig['serverip'] . "\n";

    $privatekey = (array_key_exists('privatekey', $serverconfig) && $serverconfig['privatekey']) ? $serverconfig['privatekey'] : NULL;

    try {
        $SSH = new \exporter\ssh($serverconfig['serverip'], $serverconfig['serverport'], $serverconfig['user'], '', $privatekey);
        $SSH->setDebug(true);
        $SSH->login();
    } catch (RuntimeException $e) {
        throw new RuntimeException($e);
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
foreach ($arrayallcrontabs as $servername => $serverjobs) {
    echo 'Server: ' . $servername . "\n";

    $server = new \exporter\objects\db\server($container);
    $server->setDescr($config->getServers()[$servername]['servername']);
    $server->setIp($config->getServers()[$servername]['serverip']);
    $server->delete();

    /** @var array[] $serverjobs */
    foreach ($serverjobs as $groups => $groupdata) {
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

$jobGroup = new \exporter\objects\db\jobGroup($container);
$jobGroup->deleteNotAssigned();

$db->commit();


