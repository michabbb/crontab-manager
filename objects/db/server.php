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
        return $this->db->query('DELETE FROM server WHERE ser_ip=?',[$this->getIp()]);
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
     * @param mixed $descr
     */
    public function setDescr(string $descr): void
    {
        $this->descr = $descr;
    }

}