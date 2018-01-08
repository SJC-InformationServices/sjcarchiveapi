<?php
list($enta,$entb,$entc,$entd,$ente) = \R::dispense('entdef',5);

$attribdef = \R::dispense('attribdef');
$attribdef->name ="name";
$attribdef->label = strtoupper($entdef->name);
$attribdef->type = "text";
$attribdef->defaultvalue = "NULL";
$attribdef->options = array();
$attribdef->createdon = date("Y-m-d H:i:s");
$attribdef->updatedon = date("Y-m-d H:i:s");

$entties = [$enta,$entb,$entc,$entd,$ente];
foreach($entties as $e){
    $e->name = "seasons";
    $e->label = strtoupper($en->name);
    $e->createdon = date("Y-m-d H:i:s");
    $e->updatedon = date("Y-m-d H:i:s");
    $e->sharedAttribdefList[]  = $attribdef;
}





?>