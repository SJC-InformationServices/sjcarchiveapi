<?php

namespace storedd;
use \Mysqli;
use log;
use base;
class db{
    private $host;
    private $user;
    private $pass;
    private $db;   
    private $charset='utf8mb4';
    protected $conn;

    public function __construct(Mysqli $conn=null,string $host,string $user,string $pass,string $db,string $charset)
    {
     if(!is_null($conn))
     {
         $this->conn = $conn;
     }   
     else{
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db = $db;
        $charset = $charset;
        $dbconn = new mysqli($this->host, $this->user, $this->pass,$this->db);
        $dbconn->select_db($this->db);
        $dbconn->set_charset($this->charset);
        $dbconn->autocommit(true);
        $dbconn->query("SET SESSION group_concat_max_len=100000");
        $this->conn = $dbconn;
        R::setup("mysql:host=$host;dbname=$db;'$user','$pass'");
     }
    }
}

?>