<?php
require_once "vendor/redbean/rb.php";
$host = "sjc-content-archive-dev.cpi3jpipzm32.us-east-1.rds.amazonaws.com";
$db = "sjccontentarchive";
$user = "sjcArchiveWebApps";
$pass = "15BentonRoad!";
R::setup("mysql:host=$host;dbname=$db;",$user,$pass);
R::setAutoResolve( TRUE );        //Recommended as of version 4.2
?>