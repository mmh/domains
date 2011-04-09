<?php

# FROM PUPPET, DONT EDIT

# will post an array with stats to $collectURL

$collectURL = 'http://domains.bellcom.dk/service/datacollector/';

$systemInfo = array();
$facterOutput = trim( shell_exec('facter') );
$facterLines = explode("\n",$facterOutput);
foreach ($facterLines as $line)
{
  list($key,$value) = explode(' => ', $line);
  $systemInfo[$key] = $value;
}

$statsarray['hostname']      = $systemInfo['hostname'];
$statsarray['ip']            = $systemInfo['ipaddress'];
$statsarray['memory']        = $systemInfo['memorysize'];
$statsarray['arch']          = $systemInfo['hardwaremodel'];
$statsarray['debianversion'] = $systemInfo['lsbdistdescription'];
$statsarray['virtual']       = $systemInfo['virtual'];

if ($systemInfo['virtual'] == 'xen0') 
{
  $domUs = parseXenDomUs();
  $statsarray['domUs'] = $domUs;
}

$vhosts = parseVhosts();
if (!empty($vhosts[0])) 
{
  $statsarray['vhosts'] = $vhosts;
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

#print_r($statsarray);

$ch = curl_init();
$data = base64_encode( serialize( $statsarray ));
curl_setopt($ch, CURLOPT_URL, $collectURL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'data' => $data ));
curl_exec($ch);
