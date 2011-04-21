<div id="servers" class="page list">
<?php
use MiMViC as mvc;  
if ( count($serversGrouped) > 0 )
{
  foreach ($serversGrouped as $type => $group) 
  {
    switch ($type) 
    {
      case 'virtual':
        foreach ($group as $xen0)
        {
          $data = array();
          $data['enabledFields'] = $enabledFields;
          echo '<table class="tablesorter">';
          mvc\render('design/desktop/templates/server_group_table_heading.tpl.php', $data);
          echo '<tbody>';

          $data['server'] = $xen0['xen0'];
          mvc\render('design/desktop/templates/server_table_row.tpl.php', $data);

          foreach ($xen0['xenu'] as $xenu) 
          {
            $data['server'] = $xenu;
            mvc\render('design/desktop/templates/server_table_row.tpl.php', $data);
          }

          echo '</tbody>
            </table>';
        }
        break;
      case 'physical':
          $data = array();
          $data['enabledFields'] = $enabledFields;
          echo '<table class="tablesorter">';
          mvc\render('design/desktop/templates/server_group_table_heading.tpl.php', $data);
          echo '<tbody>';

          $data['server'] = $group;
          mvc\render('design/desktop/templates/server_table_row.tpl.php', $data);

          echo '</tbody>
            </table>';
        break;
    }
  }
}
else
{
  echo 'Ingen servere registeret';
}
?>
</div>
