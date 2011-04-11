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

/**
 * Groups all servers by type (xen0,xenu) 
 *
 * @return array 
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function getGroupedByType()
{
  $servers = R::find('server');
  $data = array();

  $groupedServers = array();

  foreach ($servers as $server) 
  {
    if ( is_null( $server->parent_id ) && $server->type == 'xen0' )
    {
      $groupedServers[ $server->id ]['xen0'] = $server;
    }
    if ( !is_null( $server->parent_id ) && $server->type == 'xenu' )
    {
      $groupedServers[ $server->parent_id ]['xenu'][] = $server;
    }
  }

  return $groupedServers;
}

/**
 * Returns all servers
 *
 * @return array
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function getAll()
{
  return R::find('server');
}
