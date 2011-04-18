<?php

$hardware = unserialize($server->hardware);
echo '<tr class="'.$server->type.'">
  <td class="name">'.$server->name.'</td>
  <td>'.$server->ip.'</td>
  <td class="os '.strtolower( $server->os ).'">'.$server->os.'</td>
  <td>'.$server->os_release.'</td>
  <td>'.$server->kernel_release.'</td>
  <td>'.$server->arch.'</td>
  <td class="hardware cpu'. ( ( $hardware['cpucount'] ) ? '' : ' error' ) .'">'. ( ( $hardware['cpucount'] )?:'<span class="error">?</span>' ) .'</td>
  <td class="hardware memory'. ( (empty($hardware['memory'])) ? ' error' : '' ) .'">'. ( (empty($hardware['memory'])) ? '?' : $hardware['memory'] ) .'</td>
  <td class="actions">';

$domains = array() ;
$domains = R::related( $server, 'domain' );
if ( !empty($domains) )
{
  echo '<a class="ajaxRequest" href="/service/ajax/getDomains/json/?serverID='.$server->id.'"><img src="/design/desktop/images/domain_template.png" /></a>';
}

echo '</td>
  <td><a class="ajaxRequest" href="/service/ajax/editServerComment/json/?serverID='.$server->id.'"><img src="/design/desktop/images/pencil';
  if ( empty( $server->comment ) )
  {
    echo '_add';
  }
echo '.png" alt="Edit comment" class="icon"/></a>'.( !empty( $server->comment ) ? $server->comment : '').'</td>
</tr>';
