<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$board_messages = array();
if ($is_regist>0) {
	include_once($ld_engine_path."inc_connect.php");
	$m_result = mysql_query("select id, status, from_nick, from_uid, subject, at_date, body from ".$mysql_table_prefix."board where user_id=$is_regist") or die("database error: cannot retrieve mail-messages<br>".mysql_error());
	$i = 0;
	while ($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
		$board_messages[$i]["id"] = $row[0];
		$board_messages[$i]["status"] = $w_stat[$row[1]];
		$board_messages[$i]["from"] = $row[2];
		$board_messages[$i]["from_id"] = $row[3];
		$board_messages[$i]["subject"] = $row[4];
		if ($board_messages[$i]["subject"] == "") $board_messages[$i]["subject"] = $w_no_subject;
		$board_messages[$i]["date"] = date($w_date_format,$row[5]);
		$board_messages[$i]["body"] = $row[6];
		$i++;
	}
	mysql_free_result($m_result);
}
?>