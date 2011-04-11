<?php

use MiMViC as mvc;  

class dataCollectorHandler implements mvc\ActionHandler
{
  public function exec($params)
  {
    $data = unserialize( base64_decode( $_POST['data'] ) );

    $server = R::findOne("server", "name=? ", array($data['hostname']));

    if ( empty($server) )
    {
      $server = R::dispense('server');
      $server->created = mktime();
    }
    $server->updated = mktime();
    $server->name = $data['hostname'];
    $server->ip = $data['ipaddress'];
    $hardware = array(
      'memory' => $data['memorysize'],
      'cpucount' => $data['processorcount'],
      'cpu' => $data['processor0'],
      );
    $server->kernel_release = $data['kernelrelease'];
    $server->os = $data['lsbdistid'];
    $server->os_release = $data['lsbdistrelease'];
    $server->arch = $data['hardwaremodel'];
    $server->hardware = serialize($hardware);
    $server->type = $data['virtual'];
    $server->comment = '';
    $serverID = R::store($server);

    if ( $server->type === 'xen0' && isset($data['domUs']) && !empty($data['domUs']) )
    {
      foreach ($data['domUs'] as $domUName) 
      {
        $domU = array();
        $result = array();

        $domU = R::findOne("server", "name=?", array($domUName));
        if ( empty($domU) )
        {
          $domU = R::dispense('server');
          $domU->name = $domUName;
          $domU->created = mktime();
          $domU->updated = mktime();
          $domU->type = 'xenu';
          R::attach($server,$domU);
          $domUID = R::store($domU);
        }
        else
        {
          $domU->updated = mktime();
          R::attach($server,$domU); // server is parent of domU
        }
      }
    }

    // Handle domains

    if ( isset($data['vhosts']) )
    {
      foreach ($data['vhosts'] as $domains) 
      {
        $updateTimestamp = mktime();
        $vhostGroupKey = trim( $domains['servername'] );
        foreach ($domains as $key => $domainName) 
        {
          $domainName = trim( $domainName );

          if ( empty($domainName) )
          {
            continue;
          }

          $domain = array();
          $domain = R::findOne("domain", "name=? ", array( $domainName  ));

          if ( empty($domain) )
          {
            $domain = R::dispense('domain'); 
            $domain->created = mktime();
          }
          $domain->updated         = $updateTimestamp;
          $domain->name            = $domainName;
          $domain->vhost_group_key = $vhostGroupKey;
          $domain->is_active       = true;
          $domain->type            = 'alias';

          if ( $key === 'servername' )
          {
            $domain->type = 'name';
          }

          $domainID = R::store($domain);
          R::associate($server,$domain);
        }

        // set is_active to false for those domains in the current vhost which has not been updated
        $notUpdatedDomains = R::find("domain","updated != ? AND vhost_group_key = ?", array( $updateTimestamp, $vhostGroupKey ));
        $domain = array();
        foreach ($notUpdatedDomains as $domain) 
        {
          $domain->is_active = false; 
          $domainID = R::store($domain);
        }
      }
    }
    echo 'ok';
  }
}


?>
