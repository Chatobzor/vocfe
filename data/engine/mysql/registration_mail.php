<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
function regmail_add($new_user_name, $password, $new_user_mail, $regkey) {
	global $data_path, $file_path, $mysql_table_prefix, 
		$w_already_registered, $session, $w_try_again, $design, $w_title, $current_design, 
		$max_per_mail, $w_max_per_mail, $w_mail_used;
	$canon_view = to_canon_nick($new_user_name);
	$m_result = mysql_query("select * from ".$mysql_table_prefix."users where canon_nick='".addslashes($canon_view)."'");
	if (mysql_num_rows($m_result)) {
		$error_text =  str_replace("~", $new_user_name, $w_already_registered)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
		include($file_path."designes/".$design."/error_page.php");
		mysql_free_result($m_result);
		exit;
	}
	mysql_free_result($m_result);
	$m_result = mysql_query("select * from ".$mysql_table_prefix."regmail where canon_view='".addslashes($canon_view)."'");
	if (mysql_num_rows($m_result)) {
		$error_text =  str_replace("~", $new_user_name, $w_already_registered)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
		include($file_path."designes/".$design."/error_page.php");
		mysql_free_result($m_result);
		exit;
	}
	mysql_free_result($m_result);
	$m_result = mysql_query("select count(*) from ".$mysql_table_prefix."users where lower(registration_mail)='".addslashes(strtolower($new_user_mail))."'");
	$already_on_mail = mysql_result($m_result, 0, 0);
	mysql_free_result($m_result);
	$m_result = mysql_query("select count(*) from ".$mysql_table_prefix."regmail where lower(email)='".addslashes(strtolower($new_user_mail))."'");
	$already_on_mail += mysql_result($m_result, 0, 0);
	mysql_free_result($m_result);
	if ($already_on_mail >= $max_per_mail) {
		$error_text = $w_mail_used."<br>".str_replace("~", $max_per_mail, $w_max_per_mail)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
		include($file_path."designes/".$design."/error_page.php");
		exit;
	}
	mysql_query("insert into ".$mysql_table_prefix."regmail values (".
		time().",\"".addslashes($new_user_name)."\",".
		"\"".addslashes($password)."\",".
		"\"".addslashes($new_user_mail)."\",".
		"\"".addslashes($regkey)."\",".
		"\"".addslashes($canon_view)."\"".
	")") or die("cannot save registration key:".mysql_error());
}

function regmail_activate($regkey) {
	global $data_path, $ld_engine_path, $mysql_table_prefix, 
		$file_path, $w_succesfull_reg, $w_already_registered, $w_try_again, $session, $design, $w_title, $current_design;
	$activated = 0;
	mysql_query("delete from ".$mysql_table_prefix."regmail where time<".(time()-86400));
	$mra_result = mysql_query("select * from ".$mysql_table_prefix."regmail where regkey=\"".addslashes($regkey)."\"");
	if (mysql_num_rows($mra_result)) {
		$new_user_name = mysql_result($mra_result, 0, 1);
		$passwd1 = mysql_result($mra_result, 0, 2);
		//for reg_add -- user_name is nickname if the user in the chat.
		$user_name = "";
		$user->registration_mail = mysql_result($mra_result, 0, 3);
		$new_user_mail = $user->registration_mail;
		include($ld_engine_path."registration_add.php");
		mysql_query("update ".$mysql_table_prefix."users set user_info=\"".addslashes(serialize($user))."\" where id=".intval($user_id));
		$activated = 1;
	}
	mysql_free_result($mra_result);
	mysql_query("delete from ".$mysql_table_prefix."regmail where regkey=\"".addslashes($regkey)."\"");
	return $activated;
}

?>