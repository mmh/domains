<?php
/*
* Looks up DNS A records for all domains, and updates dns_info if they aren't pointing at the server the are associated with
* TODO - punycode domains, better return messages, do a lookup on * domains and only warn thats its not right
*/


require dirname(__FILE__).'/../public_html/includes/redbean/rb.php';
include dirname(__FILE__).'/../public_html/config.php';

$dsn = 'mysql:host=localhost;dbname='.$dbname;
R::setup($dsn,$dbusername,$dbpassword);


$servers = R::find('server');

foreach ($servers as $server)
{
  $domains = R::related($server,'domain');
  foreach ($domains as $domain)
  {
    $status = cmp_ip($server->ip, $domain->name);
    {
      if ($status != 1) 
      {
        #echo $server->name."/".$domain->name." : ".$status."\n";
        $domain->dns_info = $status;
        R::store($domain);
      }
    }
  }
}

function cmp_ip($serverip, $domain)
{
  $serverip = substr(strrchr($serverip,'.'),1);
  if (strpos($domain,'*') !== false)
  {
    #return $domain ."\t Wont lookup * domains. Fix it :)\n"; 
    return "ERROR: * in domain";
  }
  if ($dns = gethostbynamel($domain))
  {
    foreach ($dns as $ip)
    {
      if (substr(strrchr($ip,'.'),1) == $serverip)
      {
        return TRUE;
      }
    }
    return "ERROR: points outside server to: ".$ip;
  }
  else
  {
    return "ERROR: no A record or domains doesnt exist";
  }
}
?>
