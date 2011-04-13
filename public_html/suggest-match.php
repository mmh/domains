<?php
/*
Suggests matches between domains and accounts
TODO: fix f.eks. co.uk domæner. Vi antager at alt efter sidste . er tld. 
*/

$config = require 'config.php';

require 'includes/redbean/rb.php';

$dsn = 'mysql:host='.$config->dbHost.';dbname='.$config->dbName;
R::setup($dsn,$config->dbUsername,$config->dbPassword);
$domains = R::getCol('select name from domain');

require 'includes/classes/class.sugarCrmConnector.php';
try
{
  $sugar = sugarCrmConnector::getInstance();
  $sugar->connect($config->sugarLogin, $config->sugarPassword);

  $accounts = array();

  $results = $sugar->getEntryList( 'Accounts', "accounts.account_type = 'Customer'", 'name', 0, array('name') );
  foreach ($results->entry_list as $result)
  {
    #$accounts[] = (object) array( 'id' => $result->id, 'label' => $result->name_value_list[0]->value, 'value' => $result->name_value_list[0]->value );
    $accounts[] = array('id' => $result->id, 'value' => $result->name_value_list[0]->value);
  }
}
catch(Exception $e)
{
  print_r($e);
}

find_suggestions($domains,$accounts);

function domainToAccount ($domainname, $account, $account_id) {
  $owner = R::findOne('owner', 'account_id=?',array( $account_id ));
  if ( $owner === false )
  {
    $owner = R::dispense("owner");
    $owner->name = $account;
    $owner->account_id = $account_id;
    $id = R::store($owner);
  }
  $domain = R::findOne( 'domain', 'name=?',array($domainname));
  #$domain = R::load( 'domain', $id );
  R::associate( $owner, $domain );

  $otherDomainsDefinedInVhost = R::find("domain","vhost_group_key = ?",array($domain->vhost_group_key));
  foreach ($otherDomainsDefinedInVhost as $otherDomain)
  {
    if ( $otherDomain->type === 'name' )
    {
      continue;
    }
    R::associate( $owner, $otherDomain );
  }
}

function find_suggestions($domains,$accounts) 
{
  foreach ($domains as $domain)
  {
    $explodeddomain = explode('.',$domain);
    $explodedsize = sizeof($explodeddomain);
    if ($explodedsize <= 1)
    {
      echo $domain." is too short and stupid. Find and fix it :)\n";
      continue;
    }
    else if ($explodeddomain[$explodedsize-2] == "bellcom") 
    {
      if ($explodedsize == 2)
      {
        continue; # ignore domains like bellcom.dk, bellcom.info
      }
      else
      {
        $shortdomain = $explodeddomain[$explodedsize-3];
      }
    }
    else
    {
     $shortdomain = $explodeddomain[sizeof($explodeddomain)-2];
    }
    foreach ($accounts as $account) 
    {
      $account_words = explode(' ',$account['value']);
      foreach ($account_words as $account_word)
      {
        if (strlen($account_word) > 3) 
        {
          if (strcasecmp($shortdomain,$account_word) == 0)
          {
            echo "Adding match... Domain: ".$domain." Account: ".$account['value']."\n";
            domainToAccount($domain, $account['value'], $account['id']);
          }
        }
      }
    }
  }
}
