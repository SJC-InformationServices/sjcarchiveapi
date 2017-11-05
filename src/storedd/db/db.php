<?php

namespace storedd;
use \Mysqli;
use log;
use base;
class db{
    public $host;
    public $user;
    public $pass;
    public $db;   
    public $charset='utf8mb4';
    public $conn;

    public function __construct(Mysqli $conn=null,string $host,string $user,string $pass,string $db,string $charset)
    {
        
    }


}

?>