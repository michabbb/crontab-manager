<?php

namespace exporter\objects\db;

use exporter\db\db;
use exporter\utils\utils;
use Pimple\Container;

class jobGroup {

    private $comment;
    private $active;
    /**
     * @var db
     */
    private $db;

    /**
     * @var job[] $jobList
     */
    private $jobList;

    public function __construct(Container $container)
    {
        $this->db = $container['db'];
    }

    /**
     * @return mixed
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
        utils::debug('setComment',$comment);
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
        utils::debug('setActive',$active);
    }

    public function getJobList(): array
    {
        return $this->jobList;
    }

    /**
     * @param jobList $jobList
     */
    public function setJobList(jobList $jobList): void
    {
        $this->jobList = $jobList;
    }

    /**
     *
     * @throws \RuntimeException
     */
    public function save(): void
    {
        $crg_id = $this->getGroupCommentFromDb($this->comment);
        foreach ($this->jobList as $job) {
            $ser_id = $job->getServer()->getServerFromDb();
            $ret = $this->saveJob($job,$crg_id,$ser_id);
            if (!$ret['state']) {
                throw new \RuntimeException(print_r($ret,1));
            }
        }
    }

    /**
     * @param job $job
     * @param $crg_id
     * @param $ser_id
     * @return array
     */
    private function saveJob(job $job, $crg_id, $ser_id): array
    {
        return $this->db->query('INSERT INTO crontab_to_server SET 
                                cro_crg_id=?,
                                cro_ser_id=?,
                                cro_descr=?,
                                cro_user=?,
                                cro_m=?,
                                cro_h=?,
                                cro_dom=?,
                                cro_mon=?,
                                cro_dow=?,
                                cro_command=?
                                ',[
                                    $crg_id,
                                    $ser_id,
                                    $job->getComment(),
                                    $job->getUser(),
                                    $job->getTime()['m'],
                                    $job->getTime()['h'],
                                    $job->getTime()['dom'],
                                    $job->getTime()['mon'],
                                    $job->getTime()['dow'],
                                    $job->getCommand()
        ]);
    }

    public function deleteNotAssigned(): void
    {
        $this->db->query('DELETE c.* FROM crontab_groups c
                                LEFT OUTER JOIN crontab_to_server cts ON c.crg_id = cts.cro_crg_id
                                WHERE cts.cro_id IS NULL');
    }

    private function getGroupCommentFromDb($comment) {
        $ret = $this->db->query('SELECT * FROM crontab_groups WHERE crg_comment=?',[$comment]);
        if ($ret['numrows']) {
            return $ret['result'][0]['crg_id'];
        }
        $ret_ins = $this->db->query('INSERT INTO crontab_groups SET crg_comment=?',[$comment]);
        return $ret_ins['last_insert_id'];
    }

    public function getall() {
		return $this->db->query('SELECT
										 a.cro_active,a.cro_user,a.cro_m,a.cro_h,a.cro_dom,a.cro_mon,a.cro_dow,a.cro_command,cg.crg_comment,cg.crg_active,s.ser_ip,s.ser_descr
									FROM crontabmanager.crontab_to_server a
									INNER JOIN server s ON a.cro_ser_id = s.ser_id
									LEFT OUTER JOIN crontab_groups cg ON a.cro_crg_id = cg.crg_id');
	}

}