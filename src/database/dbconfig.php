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
    protected $host;

    /** @var string database */
    protected $database;
    
    /** @var string user */
    protected $user;
    
    /** @var string password */
    protected $password;
    
    /** @var string port */
    protected $port;
    
    /** @var string soket */
    protected $socket;
    
    public function __construct()
    {
        $this->host = '';
        $this->database = '';
        $this->user = '';
        $this->password = '';
        $this->port = '3306';
        $this->socket = '';
    }

    /*
    * @return string
    */
    public function getHost()
    {
        return $this->host;
    }

    /*
    * @return void
    */
    protected function setHost(string $host)
    {
        $this->host = $host;
    }

    /*
    * @return string
    */
    public function getDatabase()
    {
        return $this->database;
    }

    /*
    * @return void
    */
    protected function setDatabase(string $database)
    {
        $this->database = $database;
    }


    /*
    * @return void
    */
    protected function setUser(string $user)
    {
        $this->user = $user;
    }

    /*
    * @return string
    */
    public function getUser()
    {
        return $this->user;
    }

    /*
    * @return void
    */
    protected function setPassword(string $password)
    {
        $this->password = $password;
    }

    /*
    * @return string
    */
    public function getPassword()
    {
        return $this->password;
    }


    /*
    * @return void
    */
    protected function setPort(string $port='3306')
    {
        $this->port = $port;
    }


    /*
    * @return string
    */
    public function getPort()
    {
        return $this->port;
    }

    /*
    * @return void
    */
    protected function setSocket($socket)
    {
        $this->socket = $socket;
    }

    /*
    * @return string
    */
    public function getSocket()
    {
        return $this->socket;
    }

}



