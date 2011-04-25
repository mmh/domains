<?php
echo '<thead>
<tr>';

foreach ( $enabledFields as $key => $value )
{
  echo '<th class="'.$key.'">'.$value .'</th>';
}

  echo '</tr>
  </thead>';
