<?php
function($endpoint,$method,$timing,$evobj,$archive){
$aatname = $archive->archivedb->real_escape_string($evobj['assetType']);

$sql = "create table if not exists `archive_$aatname` (`aatid` int(100) NOT NULL AUTO_INCREMENT, 
PRIMARY KEY(`aatid`),
constraint `aatname` FOREIGN KEY(`aatid`) 
REFERENCES `aat` (`aatid`) on delete cascade on update cascade) 
ENGINE=InnoDB default charset=utf8 collate=utf8_unicode_ci";

$qry = $archive->archiveQryDb($sql);
return $qry;
}
?>