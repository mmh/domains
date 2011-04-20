<?php

use MiMViC as mvc;  

class dataCollectorHandler implements mvc\ActionHandler
{
  public function exec($params)
  {
    $data = unserialize( base64_decode( $_POST['data'] ) );

    if ( $data['hostname'] == null || empty( $data['hostname'] ) )
    {
      die('Missing hostname');
    }

    $server = R::findOne("server", "name = ? ", array($data['hostname']));

    if ( empty($server) )
    {
      $server = R::dispense('server');
      $server->created = mktime();
    }
    $server->updated = mktime();
    $server->name    = $data['hostname'];
    $server->ip      = $data['ipaddress'];

    $hardware = array(
      'memory'   => $data['memorysize'],
      'cpucount' => $data['processorcount'],
      'cpu'      => $data['processor0'],
      );

    if ( isset($data['disk']['partitions']) )
    {
      $hardware['partitions'] = $data['disk']['partitions'];
    }

    $server->kernel_release = $data['kernelrelease'];
    $server->os             = $data['lsbdistid'];
    $server->os_release     = $data['lsbdistrelease'];
    $server->arch           = $data['hardwaremodel'];
    $server->hardware       = serialize($hardware);
    $server->type           = $data['virtual'];
    $server->comment        = $server->comment; // keep existing comment - should be dropped when schema is frozen
    $serverID = R::store($server);

    if ( isset($data['disk']['physical']) )
    {
      foreach ( $data['disk']['physical'] as $disk )
      {
        $drive = R::findOne("harddrive", "serial_no=?", array($disk['SerialNo']));
        if ( empty($drive) )
        {
          $drive = R::dispense('harddrive');
          $drive->created     = mktime();
          $drive->updated     = mktime();
          $drive->is_active   = true;

          $brand = ( isset($disk['brand']) ? $disk['brand'] : 'Unknown' );

          // Try an educated guess
          if ( $brand == 'Unknown' )
          {
            $found = false;
            if ( !$found && substr($disk['Model'], 0, 2) == 'ST')
            {
              $brand = 'Seagate';
              $found = true;
            }
            if ( !$found && substr($disk['Model'], 0, 2) == 'IC')
            {
              $brand = 'IBM';
              $found = true;
            }
            if ( !$found && substr($disk['Model'], 0, 3) == 'WDC')
            {
              $brand = 'Western Digital';
              $found = true;
            }
            if ( !$found && substr($disk['Model'], 0, 7) == 'TOSHIBA')
            {
              $brand = 'Toshiba';
              $found = true;
            }
            if ( !$found && substr($disk['Model'], 0, 7) == 'SAMSUNG')
            {
              $brand = 'Samsung';
              $found = true;
            }
            if ( !$found && substr($disk['Model'], 0, 6) == 'MAXTOR')
            {
              $brand = 'Maxtor';
              $found = true;
            }
          }

          $drive->brand       = $brand;
          $drive->model       = $disk['Model'];
          $drive->serial_no   = $disk['SerialNo'];
          $drive->fw_revision = $disk['FwRev'];
        }
        else
        {
          $drive->updated = mktime();
        }

        R::store($drive);
        R::associate($server,$drive);
      }
    }

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
          $domain = R::findOne("domain", "name = ? and server_id = ? ", array( $domainName, $serverID ));

          if ( empty($domain) )
          {
            $domain = R::dispense('domain'); 
            $domain->created = mktime();
          }
          $domain->updated         = $updateTimestamp;
          $domain->name            = $domainName;
          $domain->vhost_group_key = $vhostGroupKey;
          $domain->is_active       = true;
          $domain->server_id       = $serverID; // a domain can exist on multiple servers
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
  }
}
