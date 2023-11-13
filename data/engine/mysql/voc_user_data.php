<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$registered_user = 0;
$canon_view = to_canon_nick($user_name);
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select * from ".$mysql_table_prefix."users where canon_nick='$canon_view'");
$sex = -1;
$bDay = 0;
$bMon = 0;
if (mysql_num_rows($m_result)){
	$row = mysql_fetch_array($m_result, MYSQL_NUM);
	$registered_user = $row[0];
	if (!isset($password)) {$password = "";}
	$user_password = (strlen($row[2]) == 32) ? md5($password):$password;
	if ($row[2] != $user_password) {
		include($file_path."designes/".$design."/voc_password_required.php");
		mysql_free_result($m_result);
		exit;
	}
	$current_user = unserialize($row[5]);
	$bDay = $current_user->b_day;
	$bMon = $current_user->b_month;
	$sex = $current_user->sex;
	$current_user->last_visit = my_time();
	$htmlnick = $current_user->htmlnick;
	mysql_query("update ".$mysql_table_prefix."users set last_visit=".my_time().", user_info='".addslashes(serialize($current_user))."' where id=$registered_user");
}
mysql_free_result($m_result);
?>