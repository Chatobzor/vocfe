<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
function users_delete($user_ids) {
	global $mysql_table_prefix;
	for ($i=0;$i<count($user_ids);$i++)
		$user_ids[$i] = intval($user_ids[$i]);
	mysql_query("delete from ".$mysql_table_prefix."users where id in (".implode(",",$user_ids).")") or die("cannot delete users. database error<br>".mysql_error());
}
?>