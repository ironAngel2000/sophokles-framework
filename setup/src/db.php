<?php
namespace System\Config;

class db extends \Sophokles\Database\dbconfig
{
    public function __construct()
    {
        $this->setHost('[DBHOST]');
        $this->setDatabase('[DBDATABASE]');
        $this->setUser('[DBUSER]');
        $this->setPassword('[DBPW]');
        [DBPORT]
        [DBSOCKET]
    }

}
