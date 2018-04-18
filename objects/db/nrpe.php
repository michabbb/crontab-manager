<?php

namespace exporter\objects\db;


use exporter\db\db;
use Pimple\Container;

class nrpe {


	private $command_name;
	private $command;

	/**
	 * @var db
	 */
	private $db;

	public function __construct(Container $container)
	{
		$this->db = $container['db'];
	}

	/**
	 * @param mixed $command_name
	 */
	public function setCommandName($command_name): void {
		$this->command_name = $command_name;
	}

	/**
	 * @param mixed $command
	 */
	public function setCommand($command): void {
		$this->command = $command;
	}

	public function save(server $server) {
		$ser_id = $server->getServerFromDb();
		return $this->db->query('INSERT INTO nrpe_to_server SET nrp_command_name =?,nrp_command=?,nrp_ser_id=?',[
			$this->getCommandName(),
			$this->getCommand(),
			$ser_id
		]);
	}

	/**
	 * @return mixed
	 */
	public function getCommandName() {
		return $this->command_name;
	}

	/**
	 * @return mixed
	 */
	public function getCommand() {
		return $this->command;
	}

	public function getall() {
		return $this->db->query('SELECT
										 *
									FROM crontabmanager.nrpe_to_server a
									INNER JOIN server s ON a.nrp_ser_id = s.ser_id');
	}

}