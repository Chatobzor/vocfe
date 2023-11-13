<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select new_mails from ".$mysql_table_prefix."users where id=$is_regist") or die("database error<br>".mysql_error());
if (mysql_num_rows($m_result))
	list($new_board_messages) = mysql_fetch_array($m_result, MYSQL_NUM);
if (!isset($new_board_messages)) $new_board_messages = "0";
mysql_free_result($m_result);

$m_result = mysql_query("select concat(subject,body) from ".$mysql_table_prefix."board where user_id=$is_regist") or die("database error: cannot retrieve mail-messages<br>".mysql_error());
$total = 0;
while ($row = mysql_fetch_array($m_result, MYSQL_NUM))
	$total += count($row[0]);

$percentage = round($total / $max_mailbox_size *100);
mysql_free_result($m_result);
?>