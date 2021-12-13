<?php

namespace framework;

class DBFactory
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $database;
    private $charset;

    private $mysqliObject;

    public function __construct($host, $port, $username, $password, $database, $charset)
    {
        $this->host         = $host;
        $this->port         = $port;
        $this->username     = $username;
        $this->password     = $password;
        $this->database     = $database;
        $this->charset      = $charset;
        $this->mysqliObject = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        $this->mysqliObject->set_charset($this->charset);
    }
}

