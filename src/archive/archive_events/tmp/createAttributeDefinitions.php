<?php
function($endpoint,$method,$timing,$evobj,$archive){

$defaultargs = array("iskey"=>false,"index"=>false,"required"=>false,"dbtype"=>"text","dblength"=>NULL,"defaultvalue"=>NULL);	
$aadname = $archive->archivedb->real_escape_string($evobj['attributeDefinitions']);

$results = $evobj['results'][0]['aadattributes'];

$a = $evobj['args'][0];

if($args = json_decode($a[0],TRUE))
		{
			$archive->args[0]=$args;			
		}
		
		

$assetType = $args['assetType'];
$dbtype = $results['dbtype'];
$dblength = $results['dblength'];
$key = $results['iskey'];
$defaultValue = $args['defaultvalue'];

switch ($dbtype) 
{
	case 'text':
		$sql = "ALTER TABLE `archive_$assetType` ADD `$aadname` $dbtype CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL";
		break;
	case 'varchar':
		$sql = "ALTER TABLE `archive_$assetType` ADD `$aadname` $dbtype$dblength CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL";
		break;
	case 'int':
		$sql = "ALTER TABLE `archive_$assetType` ADD `$aadname` $dbtype$dblength CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL";
		break;
	case 'date':
		$sql = "ALTER TABLE `archive_$assetType` ADD `$aadname` $dbtype$dblength CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL";
		break;
	case 'json':
		$sql = "ALTER TABLE `archive_$assetType` ADD `$aadname` $dbtype";
	break;
}

$qry = $archive->archiveQryDb($sql);
return $qry;
}
?>