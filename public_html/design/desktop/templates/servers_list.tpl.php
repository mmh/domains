<div id="servers" class="page list">
<?php
use MiMViC as mvc;  
if ( count($servers_grouped) > 0 )
{
  foreach ($servers_grouped as $xen0) 
  {
    $hardware = unserialize($xen0['xen0']->hardware);

echo '<strong>'. $xen0['xen0']->name .'</strong> | <img src="/design/desktop/images/hardware.png" alt="hardware" class="tooltip_trigger icon"/>
<div class="hardware tooltip">
<table>
  <tr>
    <td>
      <img src="/design/desktop/images/processor.png" alt="cpu" class="icon"/>
    </td>
    <td>'.$hardware['cpucount'] .' &times; '. $hardware['cpu'].'</td>
  </tr>
  <tr>
    <td>
      <img src="/design/desktop/images/ddr_memory.png" alt="memory" class="icon"/>
    </td>
    <td>'.$hardware['memory'].'</td>
  </tr>
</table>
</div>
<br/>

<table class="tablesorter xenu">
  <thead>
    <tr>
      <th class="name">Name</th>
      <th class="ip">IP</th>
      <th class="os">OS</th>
      <th class="os_release">OS release</th>
      <th class="os_kernel">OS kernel</th>
      <th class="arch">Arch</th>
      <th class="cpu_count">CPU Count</th>
      <th class="memory">Memory</th>
      <th class="actions">Actions</th>
      <th class="comment">Comment</th>
    </tr>
  </thead>
  <tbody>';

    foreach ($xen0['xenu'] as $xenu) 
    {
      $data['server'] = $xenu;
      mvc\render($designPath.'templates/server_xenu.tpl.php', $data);
    }

echo '</tbody>
</table>';
  }
}
else
{
  echo 'Ingen servere registeret';
}
?>
</div>
