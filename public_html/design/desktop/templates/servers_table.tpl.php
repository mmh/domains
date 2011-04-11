<a href="/servers/grouped/">View as grouped</a> | <a href="/servers/list/">View as alt. list</a>

<div id="servers" class="page table">
<?php
if ( count($servers) > 0 )
{
  echo '<table class="tablesorter">
<thead>
  <tr>
    <th>Name</th>
    <th>IP</th>
    <th>Type</th>
    <th>OS</th>
    <th>Arch</th>
  </tr>
</thead>
<tbody>';
  foreach ($servers as $server) 
  {
    echo '<tr>
      <td>'.$server->name.'</td>
      <td>'.$server->ip.'</td>
      <td>'.$server->type.'</td>
      <td>'.$server->os.'</td>
      <td>'.$server->arch.'</td>
      </tr>';
  }
  echo '</tbody></table>';
}
else
{
  echo 'Ingen servere registeret';
}

?>
</div>
