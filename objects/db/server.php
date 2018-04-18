<?php
namespace exporter\objects\db;

use exporter\db\db;
use Pimple\Container;

class server {

    private $ip;
    private $descr;

    /**
     * @var db
     */
    private $db;

    public function __construct(Container $container)
    {
        $this->db = $container['db'];
    }

    public function delete() {
        return $this->db->query('DELETE FROM server WHERE ser_descr=?',[$this->getDescr()]);
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getDescr()
    {
        return $this->descr;
    }

	/**
	 * @return mixed
	 */
	public function getServerFromDb() {
		$ret = $this->db->query('SELECT * FROM server WHERE ser_descr=?',[$this->getDescr()]);
		if ($ret['numrows']) {
			return $ret['result'][0]['ser_id'];
		}
		$ret_ins = $this->db->query('INSERT INTO server SET ser_ip=?,ser_descr=?',[$this->getIp(),$this->getDescr()]);
		return $ret_ins['last_insert_id'];
	}

    /**
     * @param mixed $descr
     */
    public function setDescr(string $descr): void
    {
        $this->descr = $descr;
    }

    public function getall() {
    	return $this->db->query('SELECT * FROM server');
	}


}