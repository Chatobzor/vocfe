<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$board_message = array();
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select * from ".$mysql_table_prefix."board where user_id=$is_regist and id=".intval($id)) or die("database error: cannot retrieve mail-message<br>".mysql_error());;
if (mysql_num_rows($m_result)) {
	$row =  mysql_fetch_array($m_result, MYSQL_NUM);
	$board_message["id"] = $row[0];
	$status = $row[2];
	$board_message["status"] = $w_stat[$row[2]];
	$board_message["from"] = $row[3];
	$board_message["from_id"] = $row[4];
	$board_message["subject"] = $row[5];
	$board_message["date"] = date($w_date_format,$row[7]);
	$board_message["body"] = $row[6];
	if ($board_message["subject"] == "")$board_message["subject"] = $w_no_subject;
}
mysql_free_result($m_result);
if ($status == 1) {
	mysql_query("update ".$mysql_table_prefix."users set new_mails=new_mails-1 where id=$is_regist") or die("database error<br>".mysql_error());
	if ($board_operation != "reply")
		mysql_query("update ".$mysql_table_prefix."board set status=0 where user_id=$is_regist and id=".intval($id)) or die("database error<br>".mysql_error());
}
if ($board_operation == "reply" and $status!=2 ) 
	mysql_query("update ".$mysql_table_prefix."board set status=2 where user_id=$is_regist and id=".intval($id)) or die("database error<br>".mysql_error());
?>