<a href="#" id="showFieldSelector">Select Displayed fields</a>
<div id="fieldSelector" class="hidden">
<?php
$avaliableLi = '';
$enabledLi   = '';
$enabledFieldKeys = array_keys($enabledFields);

foreach ( $avaliableFields as $key => $prettyName )
{
  if (in_array($key,$enabledFieldKeys))
  {
    $enabledLi .= '<li id="field='.$key.'">'.$prettyName.'</li>';
  }
  else
  {
    $avaliableLi .= '<li id="field='.$key.'">'.$prettyName.'</li>';
  }
}
?>
  <div class="sortableListContainer first">
    <h2>Enabled fields</h2>
    <ul class="connectedSortable" id="enabledFields">
    <?php
      echo $enabledLi;
    ?>
    </ul>
  </div>
  <div class="sortableListContainer last">
    <h2>Avaliable fields</h2>
    <ul class="connectedSortable" id="avaliableFields">
    <?php
      echo $avaliableLi;
    ?>
    </ul>
  </div>
</div>
