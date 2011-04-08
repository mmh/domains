<?php

require 'includes/redbean/rb.php';
R::setup('mysql:host=localhost;dbname=domains','domainmaster','d0m@1nm@s73r');

/*for ($i = 0; $i < 11; $i++) 
{
  $domain = R::dispense("domain");
  $domain->name = "sub".$i.".bellcom.dk";
  $id = R::store($domain);
}*/

//$server = R::dispense("server");
//$server->name = 'xen15';
//$hardware = array( 
  //'cpu' => 'Intel Core2 2.33Ghz',
  //'ram' => '2Gb',
//);

//$server->hardware = serialize($hardware);
//$id = R::store($server);

/*$server = R::dispense("server");
$server->name = 'rockhopper.dk';
$id = R::store($server);
 */

/*$dom0 = R::load('server',1);
$dom0->type = 'dom0';
$id = R::store($dom0);
 */
$domU = R::load('server',2);
$domain = R::load('domain',1);

R::associate( $domU, $domain );

?>
