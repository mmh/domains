<div id="servers" class="page list">
<?php
use MiMViC as mvc;  
if ( count($servers_grouped) > 0 )
{
  foreach ($servers_grouped as $xen0) 
  {
    $hardware = unserialize($xen0['xen0']->hardware);

echo '<table class="tablesorter">
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
  <tbody>
    <tr class="xen0">
      <td>'.$xen0['xen0']->name.'</td>
      <td>'.$xen0['xen0']->ip.'</td>
      <td class="os '.strtolower( $xen0['xen0']->os ).'">'.$xen0['xen0']->os.'</td>
      <td>'.$xen0['xen0']->os_release.'</td>
      <td>'.$xen0['xen0']->kernel_release.'</td>
      <td>'.$xen0['xen0']->arch.'</td>
      <td class="hardware cpu'. ( ( $hardware['cpucount'] ) ? '' : ' error' ) .'">'. ( ( $hardware['cpucount'] )?:'<span class="error">?</span>' ) .'</td>
      <td class="hardware memory'. ( (empty($hardware['memory'])) ? ' error' : '' ) .'">'. ( (empty($hardware['memory'])) ? '?' : $hardware['memory'] ) .'</td>
      <td><a class="loadDomains" href="/service/ajax/getDomains/json/?serverID='.$xen0['xen0']->id.'"><img src="/design/desktop/images/domain_template.png" /></a></td>
      <td>'.$xen0['xen0']->comment.'</td>
    </tr>';

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
