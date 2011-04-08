<?php

/**
 * Returns all the domains that does not have an owner and is of type: name
 *
 * @return array Contains Domain Beans 
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function getUnrelatedMainDomains()
{
  $allDomains = R::find('domain');

  foreach ( $allDomains as $domain )
  {
    if ( $domain->type !== 'name' )
    {
      continue;
    } 

    $relations = array();
    $relations = R::related($domain,'owner');
    if ( empty($relations) )
    {
      $unrelatedDomains[] = $domain;
    }
  }

  return $unrelatedDomains;
}

?>
