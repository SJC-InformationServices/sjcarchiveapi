<?php
include "../archive_config_inc.php";
include "alpha_colors.php";

$path = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
$name = 'alpha_colors';

$path = substr($path,strpos($path,$name));
try{
	$API = new archive($path, null,$archiveconfig);
    echo $API->processAPI();
	
} 
catch (Exception $e){
	  echo json_encode(Array('error' => $e->getMessage()));
	}
	


?>