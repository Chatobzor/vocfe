<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
#mysql_query("delete from ".$mysql_table_prefix."banlist where until<".time()) or die("database error<br>".mysql_error());
$val_list = "";
if (is_array($to_ban))
	for ($i=0;$i<count($to_ban);$i++) {
		$val_list .= "('".addslashes($to_ban[$i])."', ".intval($sw_times[$kill_time]["value"] + my_time()).")";
		if ($i<count($to_ban)-1) $val_list .= ", ";
	}


mysql_query("insert into ".$mysql_table_prefix."banlist values". $val_list) or die("database error<br>".mysql_error());
include($engine_path."admin_2.php");
?>