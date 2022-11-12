<?php
/**
 * Created by VS-Code
 * User: Bernd Wagner
 * Date: 09.03.2019
 * Time: 07:00
 */

declare(strict_types=1);


namespace Sophokles\Database;

class dbconfig
{
    /** @var string host */
    protected string $host;

    /** @var string database */
    protected string $database;

    /** @var string user */
    protected string $user;

    /** @var string password */
    protected string $password;

    /** @var string port */
    protected string $port;

    /** @var string soket */
    protected string $socket;

    /** @var bool */
    protected bool $hardDelelte;

    public function __construct()
    {
        $this->host = '';
        $this->database = '';
        $this->user = '';
        $this->password = '';
        $this->port = '3306';
        $this->socket = '';
        $this->hardDelelte = false;
    }

    /*
    * @return string
    */
    public function getHost(): string
    {
        return $this->host;
    }

    /*
    * @return void
    */
    protected function setHost(string $host): void
    {
        $this->host = $host;
    }

    /*
    * @return string
    */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /*
    * @return void
    */
    protected function setDatabase(string $database): void
    {
        $this->database = $database;
    }


    /*
    * @return void
    */
    protected function setUser(string $user): void
    {
        $this->user = $user;
    }

    /*
    * @return string
    */
    public function getUser(): string
    {
        return $this->user;
    }

    /*
    * @return void
    */
    protected function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param bool $status
     * @return void
     */
    protected function setHardDelete(bool $status): void
    {
        $this->hardDelelte = $status;
    }

    /*
    * @return string
    */
    public function getPassword(): string
    {
        return $this->password;
    }


    /*
    * @return void
    */
    protected function setPort(string $port = '3306'): void
    {
        $this->port = $port;
    }


    /*
    * @return string
    */
    public function getPort(): string
    {
        return $this->port;
    }

    /*
    * @return void
    */
    protected function setSocket($socket): void
    {
        $this->socket = $socket;
    }

    /*
    * @return string
    */
    public function getSocket(): string
    {
        return $this->socket;
    }

    /**
     * @return bool
     */
    public function getHardDelete(): bool
    {
        return $this->hardDelelte;
    }

}



