<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if ($is_regist) {
	include_once($ld_engine_path."inc_connect.php");
	$m_result = mysql_query("select user_info from ".$mysql_table_prefix."users where id=$is_regist") or die("database error: cannot retrieve user info<br>".mysql_error());
	if (mysql_num_rows($m_result)) {
		list($current_user_text) = mysql_fetch_array($m_result, MYSQL_NUM);
		$current_user = unserialize($current_user_text);
		#backward compatibility
		if ("--".$current_user->user_class == "--admin") $current_user->user_class = ADM_BAN;
		mysql_free_result($m_result);
	} else {
		$error_text = str_replace("~","",$w_search_no_found);
		include($file_path."designes/".$design."/error_page.php");
		mysql_free_result($m_result);
		exit;
	}
}
?>