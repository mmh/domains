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
      <th>Name</th>
      <th>IP</th>
      <th>OS</th>
      <th>OS release</th>
      <th>OS kernel</th>
      <th>Arch</th>
      <th>Cpu</th>
      <th>Memory</th>
      <th>Actions</th>
      <th>Comment</th>
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
      <td><a href="">Load domains</a></td>
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
