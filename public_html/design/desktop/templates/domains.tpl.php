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

foreach ( $accountToDomains as $account ) 
{
  echo '<tr>';
  foreach ( $account['domains'] as $domain ) 
  {
    if ( $domain->type != 'name' )
    {
      continue;
    }
    echo '<td><a href="http://'.$domain->name.'">'.$domain->name."</a></td>";
  }
  echo '<td>'.$account['owner']->name.'</td>
    </tr>';
}

?>
    </tbody>
  </table>
</div>
