<?php
/*
* Finds domains that exists on more than 1 server
*/

$config = require 'config.php';
require 'includes/redbean/rb.php';

$dsn = 'mysql:host='.$config->dbHost.';dbname='.$config->dbName;
R::setup($dsn,$config->dbUsername,$config->dbPassword);

$domains = R::find('domain','is_active = 1');

foreach ($domains as $domain)
{
  $related = R::related($domain,'server');
  if (sizeof($related) > 1) 
  {
    echo "\n".$domain->name." exists on:\n";
    foreach ($related as $server)
    {
      echo $server->name."\n";
    }
  }
}

?>
