<?php

namespace exporter\parser;

use exporter\regex;


class parser
{

    private static $matches_keys = array('match', 'comment_inactive', 'm', 'h', 'dom', 'mon', 'dow', 'command');
    private static $matches_keys_comment = array('match', 'comment_inactive', 'm', 'h', 'dom', 'mon', 'dow', 'command', 'comment');
    private $arrayparsedcrontab = Array();

    public const JOB_WITHOUT_COMMENT = 1;
    public const JOB_WITH_COMMENT = 2;
    public const JOB_INACTIVE_WITH_COMMENT = 3;
    public const JOB_INACTIVE_WITHOUT_COMMENT = 4;

    /**
     * @return array
     */
    public function getArrayparsedcrontab(): array
    {
        return $this->arrayparsedcrontab;
    }

    /*public function getAllCrontabs()
    {
        $ssh = new ssh\ssh();
        $config = new config\config();
        $server = $config->getServers();
        foreach ($server['serverip'] as $serveriptotest) {
            $ssh->getCrontabFromRemoteServer($serveriptotest, 'root');
        }
//        print_r($this->arrayparsedcrontab);
    }*/

    /**
     * @param array $splitdata
     * @return array
     */
    public function getParsedCrontab(array $splitdata): array
    {
        $return = array();
        $group = 0;
        $x = 0;
        $groupcomment = '';
        //print_r($splitdata);
//        exit;

        foreach ($splitdata as $crontabline) {
            $commentinactive = '';
            $comment = '';
            //leere Zeile
            if ($crontabline === '') {
                $groupcomment = '';
                $group++;
                $x = 0;
                $return[$group] = array(
                    'groupcomment' => '',
                    'jobs'         => array()
                );
            } else {
                $parsedline = $this->parseLine($crontabline);
                //kein Ergebnis
                if ($parsedline['state'] === false) {
                    if ($crontabline[0] === '#') {
                        $groupcomment .= substr($crontabline, 1) . "\n";
                        if ($groupcomment === substr($crontabline, 1) . "\n") {
                            $return[$group]['groupcomment'] = substr($crontabline, 1) . "\n";
                        } else {
                            $return[$group]['groupcomment'] .= substr($crontabline, 1) . "\n";
                        }

                        $return[$group]['groupcomment'] = trim($return[$group]['groupcomment']);

                        //    $return[$group]['groupcomment'] .= substr($crontabline, 1) . "\n";
                    }
                } elseif ($parsedline['state'] === true) {

//                    echo "=====================================\n";
//                    echo "LINE: " . $crontabline . "\n";
//                    echo "GROUP: " . $group . "\n";
//                    echo "GROUPCOMMENT: " . $groupcomment . "\n";
//                    echo "COMMENT: " . $comment . "\n";
//                    echo "x: " . $x . "\n";
//                    echo "jobart:" . $parsedline['job'] . "\n";
//                    echo "=====================================\n";

                    $keystomatch = self::$matches_keys;

                    switch ($parsedline['job']) {
                        case self::JOB_INACTIVE_WITHOUT_COMMENT:
                            $keystomatch = self::$matches_keys;
                            $commentinactive = $parsedline['matches']['1'];
                            break;
                        case self::JOB_INACTIVE_WITH_COMMENT:
                            $keystomatch = self::$matches_keys_comment;
                            $commentinactive = $parsedline['matches']['1'];
                            $comment = $parsedline['matches']['8'];
                            break;
                        case self::JOB_WITH_COMMENT:
                            $keystomatch = self::$matches_keys_comment;
                            $comment = $parsedline['matches']['8'];
                            break;
                        case self::JOB_WITHOUT_COMMENT:
                            $keystomatch = self::$matches_keys;
                            break;
                    }

//                    print_r($parsedline);
                    $return[$group]['jobs'][] = array(
                        'comment'          => trim($comment),
                        'line'             => trim($crontabline),
                        'comment_inactive' => trim($commentinactive),
                        'matches'          => array_combine($keystomatch, $parsedline['matches'])
                    );
                    $x++;
                }
            }
        }

        return $this->removeEmptyElements($return);
    }


    /**
     * @param $line
     * @return array
     */
    public function parseLine($line): array
    {
        $regex = '/^(' . regex::$regexcominactive . ')?\s*(' . regex::$regexmin . ')\s+(' . regex::$regexhrs . ')\s+(' . regex::$regexdom . ')\s+(' . regex::$regexmon . ')\s+(' . regex::$regexdow . ')\s+(.[^#]+)' . regex::$regexcomeol . '?#*$/';
        if (preg_match($regex, $line, $matches)) {
            /*echo "=================\n";
            echo $regex . "\n";
            echo "=================\n";
            print_r($matches);*/
            if (\count($matches) === 9) {
                if ($matches['1'] !== '') {
                    //echo "inactive with comment\n";
                    $job = self::JOB_INACTIVE_WITH_COMMENT;
                } else {
                    //echo "command with comment\n";
                    $job = self::JOB_WITH_COMMENT;
                }
            } else {
                if ($matches['1'] !== '') {
                    //echo "inactive\n";
                    $job = self::JOB_INACTIVE_WITHOUT_COMMENT;
                } else {
                    //echo "command\n";
                    $job = self::JOB_WITHOUT_COMMENT;
                }

            }

            return array('state' => true, 'matches' => $matches, 'job' => $job);
        }

        return array('state' => false, 'job' => 'empty');
    }


    private function removeEmptyElements(array $array): array
    {
        $newArray = array();
        foreach ($array as $group) {
            if (\is_array($group) && array_key_exists('jobs', $group) && \count($group['jobs'])) {
                $newArray[] = $group;
            }
        }

        return $newArray;
    }

}