<?php
list($enta,$entb,$entc,$entd,$ente) = \R::dispense('entdef',5);

$attribdef = \R::dispense('attribdef');
$attribdef->name ="dog";
$attribdef->label = strtoupper($attribdef->name);
$attribdef->type = "text";
$attribdef->defaultvalue = "NULL";
$attribdef->options = array();
$attribdef->createdon = date("Y-m-d H:i:s");
$attribdef->updatedon = date("Y-m-d H:i:s");

$entties = [$enta,$entb,$entc,$entd,$ente];
foreach($entties as $e){
    $e->name = "seasons";
    $e->label = strtoupper($e->name);
    $e->createdon = date("Y-m-d H:i:s");
    $e->updatedon = date("Y-m-d H:i:s");
    $e->sharedAttribdefList[]  = $attribdef;
    //$e->sharedEntdef[] = $entties;
    //$e->ownEntdef = [$enta,$entb];
}
\R::storeAll($entties);
$b = \R::dispenseAll('entdef');
$r = \R::exportAll($b,TRUE);
echo json_encode($r);



?>