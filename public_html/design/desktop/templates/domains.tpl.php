<div id="domains" class="page">
<?php

foreach ( $accountToDomains as $account ) 
{
  echo '<h1>'.$account['owner']->name.'</h1>';
  echo '<ul>';
  foreach ( $account['domains'] as $domain ) 
  {
    echo '<li><a href="http://'.$domain->name.'">'.$domain->name."</a></li>";
  }
  echo '</ul>';
}

?>
</div>
