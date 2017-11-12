<?php

namespace storeMe;
use \Doctrine\DBAL;
class base
{
    private $dbname = '';
    private $dbuser = '';
    private $dbpass = '';
    private $dbhost = '';
    private $dbdriver = '';
    public $db = '';
    private $dbcharset = '';

    public function __construct($configs)
    {
        foreach($configs as $k =>$v){
			$this->$k=$v;
		}
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'dbname' => $this->dbname,
            'user' => $this->dbuser,
            'password' => $this->dbpass,
            'host' => $this->dbhost,
            'driver' => $this->dbdriver,
            'charset' => $this->dbcharset
        );
        $this->db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    }

    
}
?>