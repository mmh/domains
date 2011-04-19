<?php

# FROM PUPPET, DONT EDIT

# will post an array with stats to $collectURL

$collectURL = 'http://domains.bellcom.dk/service/datacollector/';

$hostStats = array();
$facterOutput = trim( shell_exec('facter') );
$facterLines = explode("\n",$facterOutput);
foreach ($facterLines as $line)
{
  list($key,$value) = explode(' => ', $line);
  $hostStats[$key] = $value;
}

if ($hostStats['virtual'] == 'xen0') 
{
  $domUs = parseXenDomUs();
  $hostStats['domUs'] = $domUs;
}

$vhosts = parseVhosts();
if (!empty($vhosts[0])) 
{
  $hostStats['vhosts'] = $vhosts;
}

$hostStats['disk']['partitions'] = partitionInfo();
if ($hostStats['virtual'] == 'xen0') 
{
  $hostStats['disk']['physical'] = physicalDiskInfo();
}

if ($hostStats['operatingsystem'] == "Debian") 
{
  $hostStats['aptupdates'] = aptUpdates();
}

function aptUpdates()
{
  shell_exec("apt-get -qq update");
  $aptget = trim(shell_exec("/usr/bin/apt-get -q -y --allow-unauthenticated -s upgrade  |  /bin/grep ^Inst  | /usr/bin/cut -d\  -f2 | /usr/bin/sort"));
  return explode("\n",$aptget);
}

/**
 * Gets model info from disks if hdparm is installed (and disks are named /dev/sd[a-z])
 *
 * @return array
 * @author Henrik Farre <hf@bellcom.dk>
 **/
function physicalDiskInfo()
{
  $disks = array();
  if (is_executable('/sbin/hdparm'))
  {
    foreach ( glob('/dev/sd[a-z]') as $disk )
    {
      $output = trim(shell_exec('/sbin/hdparm -i '. $disk ));
      $lines  = explode("\n",$output);
      foreach ( $lines as $line )
      {
        $line = trim($line);
        if ( strpos($line,'Model=') !== false  )
        {
          parse_str( implode('&',explode(', ',$line)), $result );
          $disks[$disk] = $result;
          break; // only want Model info
        }
      }
    }
  }
  return $disks;
}

function partitionInfo()
{
  $df = trim(shell_exec("df -H | egrep -v '^Filesystem|Filsystem|tmpfs|cdrom|udev' | awk '{ print \"device=\" $1 \"&mountpoint=\" $6 \"&disktotal=\" $2 \"&diskfree=\" $4 \"&diskused=\" $5 }'"));
  $dfLines = explode("\n", $df);
  foreach ($dfLines as $line)
  {
    parse_str($line,$output);
    $diskinfo[] = $output;
  }
  return $diskinfo;
}

function parseXenDomUs() 
{
  $xmlist = trim(shell_exec('/usr/sbin/xm list | tail -n +3 | cut -d\  -f 1'));
  return explode("\n", $xmlist);
}

function parseVhosts() 
{
  $allvhosts = array();

  $sitesEnabled = glob( '/etc/apache2/sites-enabled/*' );
  foreach ( $sitesEnabled as $site ) 
  {
    if ( basename( $site ) == '000-default' ) 
    {
      continue;
    }

    $vhost = file( $site, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

    foreach ($vhost as $line) {
      # skip lines commented out (do the same for //?)
      if (preg_match('/^#/',trim($line))) 
      {
        continue;
      }
      $line = strtolower( trim( $line ) );

      if ( strpos($line,'servername') !== false ) 
      {
        if (!empty($vhostfile)) 
        { 
          $allvhosts[] = $vhostfile;
          $vhostfile = array();
        }
        $vhostfile = array('servername' => trim(strstr($line," ")));
      }
      elseif ( strpos($line,'serveralias') !== false ) 
      {
        $exploded = explode(" ",trim($line));
        for ($i = 1;$i < sizeof($exploded);$i++)
        {
          $vhostfile[] = $exploded[$i];
        }
      }
    }
  }
  $allvhosts[] = $vhostfile;
  return $allvhosts;
}

#print_r($hostStats);

$ch = curl_init();
$data = base64_encode( serialize( $hostStats ));
curl_setopt($ch, CURLOPT_URL, $collectURL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'data' => $data ));
curl_exec($ch);
