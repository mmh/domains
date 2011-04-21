<?php
echo '<thead>
<tr>';

foreach ( $enabledFields as $key => $value )
{
  echo '<th class="'.$key.'">'.$key .'</th>';
}

  echo '</tr>
  </thead>';
