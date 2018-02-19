<?php
namespace exporter;

use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;
use exporter\utils\utils;


class ssh {

    /**
     * @var SFTP
     */
    private $sftp;
    private $username;
    private $host;
    private $port;
    private $password;
    private $ssh_private_key_filename;

    /**
     * @var RSA
     */
    private $Key;

    /**
     * @var bool
     */
    private $debug;

    /**
     * ssh constructor.
     *
     * @param             $host
     * @param             $port
     * @param             $username
     * @param null        $password
     * @param string|bool $ssh_private_key_filename
     *
     * @throws \RuntimeException
     */
    public function __construct($host, $port, $username='', $password=null, $ssh_private_key_filename='') {
        if (!$username) {
            $this->username = exec('whoami');
            if (!$this->username) {
                throw new \RuntimeException('unable to get current user');
            }
        } else {
            $this->username = $username;
        }

        if (!$ssh_private_key_filename) {
            $ssh_private_key_filename     = $this->get_ssh_private_key();
            if ($ssh_private_key_filename===false) {
                throw new \RuntimeException('unable to find private key');
            }
        }

        $this->Key  = new RSA();
        if ($this->Key->loadKey(file_get_contents($ssh_private_key_filename))===false) {
            throw new \RuntimeException('unable to load key');
        }

        \define('NET_SSH2_LOGGING', 2);

        $this->sftp = new SFTP($host, $port);

        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->ssh_private_key_filename = $ssh_private_key_filename;

    }

    /**
     * @param string $user
     * @return bool|array
     */
    public function getCrontabFromRemoteServer(string $user)
    {
        [$exitcode,$crontab] = $this->exec('crontab -l -u '.$user);
        if ($exitcode===0) {
            return explode("\n",$crontab);
        }
        return false;
    }

    /**
     * @return bool|string
     */
    private function get_ssh_private_key()
    {
        $files = utils::find_all_files($_SERVER['HOME'] . '/.ssh/');
        foreach (array_keys($files) as $file) {
            if (\in_array($file, ['id_rsa', 'id_dsa'], true)) {
                return $_SERVER['HOME'] . '/.ssh/' . $file;
            }
        }

        return false;
    }

    public function delete($remote_filename): bool
    {
        return $this->sftp->delete($remote_filename);
    }

    public function file_exists($remote_filename): bool
    {
        return $this->sftp->file_exists($remote_filename);
    }

    /**
     *
     * @throws \RuntimeException
     */
    public function login(): void
    {
        if ($this->debug) {
            echo 'Use key: ' . $this->ssh_private_key_filename . "\n";
            echo 'Use Host: ' . $this->host . "\n";
            echo 'Use Port: ' . $this->port . "\n";
            echo 'Use User: ' . $this->username . "\n";
            echo 'Use Pwd : ' . $this->password . "\n";
        }
        if (!$this->sftp->login($this->username, $this->Key)) {
            throw new \RuntimeException('Login failed');
        }
    }

    public function disconnect(): void
    {
        $this->sftp->disconnect();
    }

    public function put($localfile,$remotefile): bool
    {
        return $this->sftp->put($remotefile, $localfile,1 );
    }

    public function exec($command): array
    {
        $return = $this->sftp->exec($command);
        return [$this->getExitStatus(),$return];
    }

    public function getErrors(): array
    {
        return $this->sftp->getSFTPErrors();
    }

    private function getExitStatus() {
        return $this->sftp->getExitStatus();
    }

    public function getLog(): string
    {
        return $this->sftp->getSFTPLog();
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug): void
    {
        $this->debug = $debug;
    }

}