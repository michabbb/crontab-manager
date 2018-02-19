<?php
use exporter\config\config;
use exporter\db\db;
use Pimple\Container;



/**
 * Class DBTest
 */
/**
 * @group live
 */
class DBTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var Container
     */
    private $container;
    /**
     * @var array
     */
    private $result;
    /**
     * @var db
     */
    private $db;

    public function setUp() {
        $this->container = new Container();
        try {
            $config = new config();
        } catch (\Exception $e) {
            exceptionhandler::handler('Unable to init config',$e);
        }
        /** @noinspection PhpUndefinedVariableInspection */
        $this->container['config'] = $config;
        $this->db                  = new db($this->container);
        $this->result              = $this->db->connect();
    }

    public function testConnect(): void
    {
        $this->assertTrue($this->result['state']);
        $this->assertArrayNotHasKey('errno', $this->result);
        $this->assertArrayNotHasKey('error', $this->result);
        $this->assertArrayHasKey('server', $this->result);
        $this->assertArrayHasKey('server_info', $this->result['server']);
        $this->assertArrayHasKey('host_info', $this->result['server']);
        $this->assertArrayHasKey('server_version', $this->result['server']);
    }

    public function testSqlQuery(): void
    {
        /*
        $result = $this->db->query('SELECT * FROM server WHERE ser_id > ?',array(0));
        $result = $this->db->query('SELECT * FROM server WHERE ser_id > ? and ser_decr=?',array(0, 'test'));
        */
        $result = $this->db->query('SELECT * FROM server WHERE ser_id > 0');
        print_r($result);
        //$result = $this->db->query('UPDATE server SET ser_db_changed = NOW()');
    }

}