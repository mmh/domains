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

function parseXenDomUs() 
{
  $xmlist = trim(shell_exec('/usr/sbin/xm list | tail -n +3 | cut -d\  -f 1'));
  return explode("\n",$xmlist);
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
          array_push($allvhosts,$vhostfile);
          $vhostfile = array();
        }
        $vhostfile = array('servername' => trim(strstr($line," ")));
      }
      elseif ( strpos($line,'serveralias') !== false ) 
      {
        $exploded = explode(" ",trim($line));
        for ($i = 1;$i < sizeof($exploded);$i++)
        {
          array_push($vhostfile,$exploded[$i]);
        }
      }
    }
  }
  array_push($allvhosts,$vhostfile);
  return $allvhosts;
}

#print_r($hostStats);

$ch = curl_init();
$data = base64_encode( serialize( $hostStats ));
curl_setopt($ch, CURLOPT_URL, $collectURL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'data' => $data ));
curl_exec($ch);
