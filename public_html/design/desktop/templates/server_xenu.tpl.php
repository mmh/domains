<?php

$hardware = unserialize($server->hardware);
echo '<tr>
  <td>'.$server->name.'</td>
  <td>'.$server->ip.'</td>
  <td class="os '.strtolower( $server->os ).'">'.$server->os.'</td>
  <td>'.$server->os_release.'</td>
  <td>'.$server->kernel_release.'</td>
  <td>'.$server->arch.'</td>
  <td class="hardware cpu'. ( ( $hardware['cpucount'] ) ? '' : ' error' ) .'">'. ( ( $hardware['cpucount'] )?:'<span class="error">?</span>' ) .'</td>
  <td class="hardware memory'. ( (empty($hardware['memory'])) ? ' error' : '' ) .'">'. ( (empty($hardware['memory'])) ? '?' : $hardware['memory'] ) .'</td>
  <td><a class="loadDomains" href="/service/ajax/getDomains/json/?serverID='.$server->id.'"><img src="/design/desktop/images/domain_template.png" /></a></td>
  <td>'.$server->comment.'</td>
</tr>';

?>
