<?php
include("check_session.php");
header("content-type: image/png");
include("../inc_common.php");
if (!isset($p)) $d_of = 0;
else if($p=="y") $d_of = -1;
$stat_file_prefix = date("Y-m-d",mktime(0,0,0,date("m"),date("d")+$d_of,date("Y")));
readfile($data_path."statistic/".$stat_file_prefix."_mess.png");
?>