<a href="/servers/table/">View as table</a>
<div id="servers" class="page list">
<?php
if ( count($servers_grouped) > 0 )
{
  foreach ($servers_grouped as $xen0) 
  {
    echo '<div class="server xen0">';
    echo '<h1>'.$xen0['xen0']->name.'</h1>';
    $hardware = unserialize($xen0['xen0']->hardware);

    echo '<div class="info">';
    echo '<dl>';
    foreach ($hardware as $key => $value) 
    {
      echo '<dt>'.ucfirst( $key ).':</dt>
      <dd>'.$value.'</dd>';
    }
    echo '</dl>';
    echo '<br class="clr"/></div>';

    foreach ($xen0['xenu'] as $xenu) 
    {
      echo '<div class="server xenu">
        <h2>'.$xenu->name.'</h2><br class="clr"/>';
      $hardware = unserialize($xenu->hardware);

      echo '<div class="info">
        <h3>Software:</h3>
        <dl>
          <dt>OS:</dt>
          <dd>'. $xenu->os .'</dd>
          <dt>Arch:</dt>
          <dd>'. $xenu->arch .'</dd>
        </dl>
        <br class="clr"/>

        <h3>Hardware:</h3>
        <dl>';
      if (!empty($hardware))
      {
        foreach ($hardware as $key => $value) 
        {
          echo '<dt>'.ucfirst( $key ).':</dt>
            <dd>'.$value.'</dd>';
        }
        echo '</dl>';
      }
      echo '</div>';

      echo '<div class="domains">
          <h3>Domains:</h3>
          <a class="toggleDomains" href="/service/ajax/getDomains/json/?serverID='.$xenu->id.'">Load domains</a>
          <div class="container"></div>
          </div>
        <br class="clr"/></div>';
    }
    echo '</div>';
  }
}
else
{
  echo 'Ingen servere registeret';
}
?>
</div>
