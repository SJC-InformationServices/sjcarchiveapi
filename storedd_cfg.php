<?php
function require_all ($path) {
    
        foreach (glob($path.'*.php') as $filename){ 
            require_once $filename;
            
        }
    
    }

require_once "vendor/redbean/rb.php";
require_once "vendor/autoload.php";

$host = "sjc-content-archive-dev.cpi3jpipzm32.us-east-1.rds.amazonaws.com";
$db = "sjccontentarchive";
$user = "sjcArchiveWebApps";
$pass = "15BentonRoad!";
$rbsetup = \R::setup("mysql:host=$host;dbname=$db;",$user,$pass);
\R::setAutoResolve( TRUE );        //Recommended as of version 4.2
//\R::fancyDebug( TRUE );
require_all("/src/storedd/models");
define( 'REDBEAN_MODEL_PREFIX', '\\storedd\\models\\' );



?>