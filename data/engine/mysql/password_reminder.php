<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
//preminder.dat's fields:
define("PR_UID", 0);
define("PR_NICK", 1);
define("PR_TIME", 2);
define("PR_CODE", 3);
define("PR_TOTALFIELDS", 4);


/**
 * param $look_for - nick in 'canonical' form
 * param $code - code to send
 * return - blank string if everything is ok, error message if something is wrong
**/
function save_code($look_for, $code) {
	global $mysql_table_prefix, $w_pr_already_sent, $w_search_no_found;
	mysql_query("delete from  ".$mysql_table_prefix."password_reminder where creation_time<".(time() - 86400));
	
	$m_result = mysql_query("select * from ".$mysql_table_prefix."password_reminder where nick='".addslashes($look_for)."'");
	if (mysql_num_rows($m_result)) {
		mysql_free_result($m_result);
		return array($w_pr_already_sent,"");
	}
	mysql_free_result($m_result);
	$m_result = mysql_query("select id, registration_mail from ".$mysql_table_prefix."users where canon_nick='".addslashes($look_for)."'");
	if (mysql_num_rows($m_result) == 0) {
		mysql_free_result ($m_result);
		return array(str_replace("~", $look_for, $w_search_no_found),"");
	}
	$uid = mysql_result($m_result, 0, 0);
	$user_mail = mysql_result($m_result, 0, 1);
	mysql_free_result($m_result);
	mysql_query("insert into ".$mysql_table_prefix."password_reminder  values(".$uid.",'".addslashes($look_for)."','".addslashes($code)."',".time().")") or die("cannot save code. ".mysql_error());
	return array("", $user_mail);
}

function update_password($new_password, $code) {
	global $mysql_table_prefix, $w_search_no_found,$user_data_file, $w_succ_updated;
	mysql_query("delete from  ".$mysql_table_prefix."password_reminder where creation_time<".(time() - 86400));
	$error = "";
	$m_result = mysql_query("select id, nick from  ".$mysql_table_prefix."password_reminder where code=".addslashes($code));
	if (mysql_num_rows($m_result)) {
		$is_valid = 1;
		$user_name = mysql_result($m_result, 0, 1);
		$user_id = mysql_result($m_result, 0, 2);
	}
	if ($is_valid) {
		//changing password here.
		$is_regist = $user_id;
		include("inc_user_class.php");
		include($ld_engine_path."users_get_object.php");
		$current_user->password = md5($new_password);
		include($ld_engine_path."user_info_update.php");
	} else $error = $w_pr_no_code;
	
	return $error;
}
