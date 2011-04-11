<div id="servers" class="page list">
<?php
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
      <th class="cpu">Cpu</th>
      <th class="memory">Memory</th>
      <th class="actions">Actions</th>
      <th class="comment">Comment</th>
    </tr>
  </thead>
  <tbody>';

    foreach ($xen0['xenu'] as $xenu) 
    {
      $hardware = unserialize($xenu->hardware);
      echo '<tr>
      <td>'.$xenu->name.'</td>
      <td>'.$xenu->ip.'</td>
      <td class="os '.strtolower( $xenu->os ).'">'.$xenu->os.'</td>
      <td>'.$xenu->os_release.'</td>
      <td>'.$xenu->kernel_release.'</td>
      <td>'.$xenu->arch.'</td>
      <td class="hardware cpu">'.$hardware['cpucount'] .' &times; '. $hardware['cpu'] .'</td>
      <td class="hardware memory">'. $hardware['memory'] .'</td>
      <td><a class="loadDomains" href="/service/ajax/getDomains/json/?serverID='.$xenu->id.'">Load domains</a></td>
      <td>'.$xenu->comment.'</td>
      </tr>';
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
