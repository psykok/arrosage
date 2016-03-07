<?php
$time1 = "23:40";
$time2 = "120";
$time = date('H:i',strtotime($time1 . '+ '.$time2 .' minute'));
echo $time . "****" . $time1;
/*echo date("H:i:s", $time);

*/
?>
