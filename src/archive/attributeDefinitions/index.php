<?php
include "../archive_config_inc.php";
include "attributeDefinitions.php";

$path = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
$name = 'attributeDefinitions';

$path = substr($path,strpos($path,$name));
try{
	$API = new archive($path, null,$archiveconfig);
    echo $API->processAPI();
} 
catch (Exception $e){
	  echo json_encode(Array('error' => $e->getMessage()));
	}
	


?>