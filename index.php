<?php

require "storedd_cfg.php";
$lifeCycle = "";
try{
    echo "Begin";
$entdef = \R::dispense('entitydefinition');
$entdef->name = "Seasons";
$entid=\R::store($entdef);
$entdef =\R::load('entitydefinition',$entid);

echo $lifecycle;
echo "End";
}
catch(Exception $e){
 var_dump($e);
}


/*$bean = new entitydefinition();
try{
$bean->setname('Seasons');
$bean->commit();
}
catch(Exception $e){}
try{    
$bean->setname('Events');
$bean->commit();
}
catch(Exception $e){

}
/*$s = R::dispense('entdef');
$ev= R::dispense('entdef');
$pg= R::dispense('entdef');
$o= R::dispense('entdef');
$p= R::dispense('entdef');
$a= R::dispense('entdef');
$k= R::dispense('entdef');
$s->name = 'Seasons';
$ev->name = 'Events';
$pg->name = 'Pages';
$o->name = 'Offers';
$p->name = 'Products';
$a->name = 'Assets';
$k->name = 'Keywords';

$s->label = 'seasons';
$ev->label = 'events';
$pg->label = 'pages';
$o->label = 'offers';
$p->label = 'products';
$a->label = 'assets';
$k->label = 'keywords';
$s->options = JSON_ENCODE(array('stuff','more stuff'));

$s->sharedEntdefList = [$ev];
$ev->sharedEntdefList = [$pg,$o,$p,$a,$a];
$pg->sharedEntdefList = [$o,$p,$a,$k];
$o->sharedEntdefList = [$p,$a,$k];
$p->sharedEntdefList = [$a,$k];
$a->sharedEntdefList = [$k];

R::storeAll( [$s,$ev,$pg,$o,$p,$a,$k]);*/
?>