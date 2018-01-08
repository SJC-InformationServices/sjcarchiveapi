<?php
require "storedd_cfg.php";
$lifeCycle = '';
$path = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
$paths=explode("/",$path);
$basepath = $paths[0];
try{
switch($basepath)
{
    case 'em':    
    $api = new storedd\modules\manager($path,null);    
    break;
    
    case 'attrib':
    $api = new storedd\modules\attributes($path,null);
    break;

    case 'api':
    R::freeze( true );
    $api = new storedd\modules\entities($path,null);
    break;
    
    case 'build':
    include "deploy.php";
    break;
    default:
    header('HTTP/1.0 404 not found');    
    die;
    break;
}
echo $api->processAPI();
echo $lifeCycle;
}catch(Exception $e){
    //TODO: Log Error
    header("HTTP/1.0 500 Internal Server Error - Fails to Process Request"); 
}
?>