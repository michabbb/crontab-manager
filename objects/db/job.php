<?php
namespace exporter\objects\db;

use exporter\utils\utils;

class job implements job_interface {

    private $comment;
    private $comment_inactive;
    private $user;
    /**
     * @var server
     */
    private $server;

    /**
     * @return mixed
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getCommentInactive() {
        return $this->comment_inactive;
    }

    /**
     * @return mixed
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * @return boolean
     */
    public function isActive() : bool {
        return $this->active;
    }

    /**
     * @return jobGroup
     */
    public function getGroup() : jobGroup {
        return $this->group;
    }

    private $command;
    private $m;
    private $h;
    private $dom;
    private $mon;
    private $dow;
    /**
     * @var boolean
     */
    private $active;

    /**
     * @param boolean $active
     */
    public function setActive($active) : void {
        $this->active = $active;
    }
    /**
     * @var jobGroup
     */
    private $group;

    /**
     * @param jobGroup $group
     */
    public function setGroup(jobGroup $group) : void {
        $this->group = $group;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment) : void {
        $this->comment = $comment;
        utils::debug('setComment',$comment);
    }

    /**
     * @param mixed $comment_inactive
     */
    public function setCommentInactive($comment_inactive) : void {
        $this->comment_inactive = $comment_inactive;
        utils::debug('setCommentInactive',$comment_inactive);
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command) : void {
        $this->command = $command;
        utils::debug('setCommand',$command);
    }

    /**
     * @return array
     */
    public function getTime() : array {
        return [
            'm'   => $this->m,
            'h'   => $this->h,
            'dom' => $this->dom,
            'mon' => $this->mon,
            'dow' => $this->dow,
        ];
    }/** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param $m
     * @param $h
     * @param $dom
     * @param $mon
     * @param $dow
     */
    public function setTime($m, $h, $dom, $mon, $dow) : void {
        $this->m   = $m;
        $this->h   = $h;
        $this->dom = $dom;
        $this->mon = $mon;
        $this->dow = $dow;
        utils::debug('set m',$m);
        utils::debug('set h',$h);
        utils::debug('set dom',$dom);
        utils::debug('set mon',$mon);
        utils::debug('set dow',$dow);
    }

    /**
     * @return mixed
     */
    public function getServer() : server
    {
        return $this->server;
    }

    /**
     * @param mixed $server
     */
    public function setServer(server $server): void
    {
        $this->server = $server;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

}