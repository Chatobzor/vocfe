<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$canon_view = to_canon_nick($new_user_name);
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select * from ".$mysql_table_prefix."users where canon_nick='".addslashes($canon_view)."'");
if (mysql_num_rows($m_result)) {
	$error_text =  str_replace("~", $new_user_name, $w_already_registered)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
	include($file_path."designes/".$design."/error_page.php");
	mysql_free_result($m_result);
	exit;
}
mysql_free_result($m_result);
if (!isset($new_user_mail)) $new_user_mail = "";
if ($registration_mailconfirm) {
	$m_result = mysql_query("select count(*) from ".$mysql_table_prefix."users where registration_mail='".addslashes($new_user_mail)."'") or die("cannot check mails");
	if (mysql_result($m_result, 0, 0) >= $max_per_mail) {
		$error_text = $w_mail_used."<br>".str_replace("~", $max_per_mail, $w_max_per_mail)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
		include($file_path."designes/".$design."/error_page.php");
		exit;
	}
	mysql_free_result($m_result);
}

include($file_path."inc_user_class.php");
$user = new User;
$user->nickname = $new_user_name;
$user->password = $passwd1;
$user->show_group_1 = 1;
$user->show_group_2 = 1;
$user->registered_at = my_time();
$user->last_visit = my_time();

mysql_query("insert into ".$mysql_table_prefix."users (nick,        passwd,              user_class, canon_nick,                   user_info,                      new_mails, last_visit, registration_mail)".
						"values('".addslashes($new_user_name)."', '".addslashes($passwd1)."', 0, '".addslashes($canon_view)."', '".addslashes(serialize($user))."', 0, ".my_time().",'".addslashes($new_user_mail)."')") or die("database error: cannot add user information<br>".mysql_error());
$user_id = mysql_insert_id();
if(!@is_dir($file_path."photos/".floor($user_id/2000)))
	if (ini_get('safe_mode'))
		trigger_error("Your PHP works in SAFE MODE, please create directory chat/photos/".floor($t_id/2000),E_USER_ERROR);
	else
		mkdir($file_path."photos/".floor($user_id/2000),0777);
$out_message =  str_replace("~", $new_user_name, $w_succesfull_reg);
if (strtolower($user_name) == strtolower($new_user_name)) {
	$fields_to_update[0][0] = 5;
	$fields_to_update[0][1] = $user_id;
	include($engine_path."user_din_data_update.php");
	$out_message .= "<br><a href=\"user_info.php?session=$session\">$w_about_me</a>";
}
?>