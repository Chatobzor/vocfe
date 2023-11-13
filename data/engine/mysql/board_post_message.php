<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select concat(subject,body) from ".$mysql_table_prefix."board where user_id=$is_regist") or die("database error: cannot retrieve mail-messages<br>".mysql_error());
$total = 0;
while ($row = mysql_fetch_array($m_result, MYSQL_NUM))
	$total += count($row[0]);

$percentage = round($total / $max_mailbox_size *100);
mysql_free_result($m_result);

if ($max_mailbox_size>$total) {
	mysql_query("insert into ".$mysql_table_prefix."board (user_id, status, from_nick, from_uid, subject, body, at_date) values($send_to_id, 1, '".addslashes($user_name)."', $is_regist,'".addslashes($subject)."','".addslashes($message)."', ".my_time().")") or die("database error: cannot store new mail-message<br>".mysql_error());
	mysql_query("update ".$mysql_table_prefix."users set new_mails=new_mails+1 where id=$send_to_id") or die("database error: cannot store new mail-message<br>".mysql_error());;
	$info_message = $w_message_sended;
}
else $info_message = $w_message_error;
?>