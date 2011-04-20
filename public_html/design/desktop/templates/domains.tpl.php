<div id="domains" class="page list">

  <table class="tablesorter">
    <thead>
      <tr>
        <th>Name</th>
        <th>Owner</th>
      </tr>
    </thead>
    <tbody>

<?php
use MiMViC as mvc;  

foreach ( $accountToDomains as $account ) 
{
  foreach ( $account['domains'] as $domain ) 
  {
    if ( $domain->type != 'name' )
    {
      continue;
    }
    echo '<tr>
      <td><a href="http://'.$domain->name.'">'.$domain->name.'</a></td><td><a href="'.sprintf( mvc\retrieve('config')->sugarAccountUrl,  $account['owner']->account_id ) .'">'.$account['owner']->name.'</a></td>
    </tr>';
  }
}

?>
    </tbody>
  </table>
</div>
