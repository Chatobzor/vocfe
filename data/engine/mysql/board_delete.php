<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
if (is_array($mess_to_del) and $is_regist) {
	for ($i=0;$i<count($mess_to_del);$i++)
		$mess_to_del[$i] = intval($mess_to_del[$i]);
	$list_of_ids = implode(",",$mess_to_del);
	$m_result = mysql_query("select count(*) from ".$mysql_table_prefix."board where user_id=$is_regist and id in($list_of_ids) and status=1" ) or die("database error<br>".mysql_error());
	list($was_new) = mysql_fetch_array($m_result, MYSQL_NUM);
	mysql_free_result($m_result);
	mysql_query("update ".$mysql_table_prefix."users set new_mails=new_mails-$was_new where id=$is_regist") or die("database error<br>".mysql_error());
	mysql_query("delete from ".$mysql_table_prefix."board where id in($list_of_ids)") or die("database error<br>".mysql_error());
}
?>