<?php
require "storedd_cfg.php";

$path = ltrim($_SERVER['REQUEST_URI'], '/');    // Trim leading slash(es)
$paths=explode("/",$path);
$basepath = $paths[0];
switch($basepath)
{
    case 'em':
    
    $api = new storedd\modules\manager($path,null);
    
    break;
    case 'attrib':
    $api = new storedd\modules\attribute($path,null);
    break;
    case 'api':
    $api = new storedd\modules\entities($path,null);
    
    break;
    
    default:
    header('HTTP/1.0 404 not found');    
    die;
    break;
}

echo $api->processAPI();
?>