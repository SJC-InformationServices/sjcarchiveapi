<?php
require "libraries/redbean/rb.php";
$host = "sjc-content-archive-dev.cpi3jpipzm32.us-east-1.rds.amazonaws.com";
$db = "sjccontentarchive";
$user = 
$pass =
R::setup("mysql:host=$host;dbname=$db;'$user','$pass'");
?>