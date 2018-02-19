<?php
namespace exporter\db;

use exporter\utils\utils;
use Pimple\Container;

class db implements db_interface {

    /**
     * @var \mysqli
     */
    private $link;
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function begin(): void
    {
        $this->link->begin_transaction();
    }

    public function commit(): void
    {
        $this->link->commit();
    }

    public function rollback(): void
    {
        $this->link->rollback();
    }

    /**
     * @return array
     */
    public function connect() : array {
        $this->link = new \mysqli(
            $this->container['config']['db']['host'],
            $this->container['config']['db']['user'],
            $this->container['config']['db']['pwd'],
            $this->container['config']['db']['db'],
            $this->container['config']['db']['port']
        );
        if ($this->link->connect_errno) {
            return array(
                    'state' => false,
                    'errno' => $this->link->connect_errno,
                    'error' => $this->link->connect_error
            );
        }

        $this->link->set_charset($this->container['config']['db']['charset']);

        return array(
            'state'  => true,
            'server' => array(
                'server_info'    => $this->link->server_info,
                'server_version' => $this->link->server_version,
                'stat'           => $this->link->stat(),
                'host_info'      => $this->link->host_info
            )
        );
    }

    /**
     * @param $sql
     * @param array $params
     * @return array
     */
    public function query($sql, array $params = array()) : array {

        $state           = false;
        $error           = null;
        $result          = array();
        $affected_rows   = 0;
        $numrows         = 0;
        $paramsWithTypes = array();
        $bind_names = array();

        $stmt = $this->link->prepare($sql);
        if (!$stmt) {
            return array(
                'state'         => $state,
                'numrows'       => $numrows,
                'result'        => $result,
                'affected_rows' => $affected_rows,
                'error'         => $this->link->error,
                'sql'           => $sql,
                'params'        => $params
            );
        }

        if ($params) {
            foreach ($params as $i => $value) {
                //TODO handle false
                $paramType               = utils::getParamType($value);
                $paramsWithTypes[$i] = ['value' => $value, 'paramtype' => $paramType];
            }
            //print_r($paramsWithTypes);
            if(\count($paramsWithTypes)) {
                $bind_names[] = implode('',array_column($paramsWithTypes,'paramtype'));
                //print_r($bind_names);
                foreach ($paramsWithTypes as $i => $paramtype) {
                    $bind_name = 'bind' . $i;
                    $$bind_name = $paramtype['value'];
                    $bind_names[] = &$$bind_name;
                }
                //print_r($bind_names);
                \call_user_func_array(array($stmt, 'bind_param'),$bind_names);
            }
        }
        if (!$stmt->execute()) {
            $error = $stmt->error;
        } else {

            $state         = true;
            $numrows       = 0;
            $affected_rows = ($stmt->affected_rows>0) ? $stmt->affected_rows : 0;
            $meta          = $stmt->result_metadata();
            $fields        = array();

            if ($meta) {
                while ($field = $meta->fetch_field()) {
                    $var          = $field->name;
                    $$var         = null;
                    $fields[$var] = &$$var;
                }

                \call_user_func_array(array($stmt, 'bind_result'), $fields);

                $i = 0;
                while ($stmt->fetch()) {
                    $numrows++;
                    $result[$i] = array();
                    foreach ($fields as $k => $v) {
                        $result[$i][$k] = $v;
                    }
                    $i++;
                }
            }

            $stmt->free_result();
        }
        return array(
            'state'         => $state,
            'numrows'       => $numrows,
            'result'        => $result,
            'affected_rows' => $affected_rows,
            'error'         => $error,
            'sql'           => $sql,
            'params'        => $paramsWithTypes,
            'last_insert_id'=> $this->link->insert_id
        );
    }

    /**
     *
     */
    public function disconnect() : void {
        $this->link->close();
    }

}
