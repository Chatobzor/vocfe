<?php
require_once("inc_common.php");
include($ld_engine_path."password_reminder.php");
set_variable("look_for");
$html_to_out = "";

//searching
if ($look_for != "") {
	$key = md5(uniqid(time()));
	list($er, $user_mail) = save_code($look_for, $key);
	if ($er!="")
		$html_to_out .= "<b>".$er."</b><br><br>";
	else {
		if ($user_mail == "") 
			$html_to_out .= "<b>".$w_pr_noemail."</b><br><br>";
		else {
			if (mail($user_mail, $w_pr_title, 
				str_replace("*", $look_for, str_replace("~", $w_title, str_replace("#",$chat_url."change_password.php?key=".$key,$w_pr_mailtext))),
				"From: ".str_replace("\\@","@", $admin_mail)."\n".
				"Content-type: text/plain; ".(($charset!="") ? "charset=".$charset:"" )."\n".
				"Content-Transfer-Encoding: 8bit"
				 )) 
				$html_to_out .= "<b>".$w_regmail_sent."</b><br><br>";
			else $html_to_out .= "<b>".$er."</b><br><br>";
		}
	}
}

$html_to_out .= "<b>".$w_pr_title."</b>
<form method=\"post\" action=\"".$chat_url."password_reminder.php\">
".$w_enter_login_nick.": <input type=\"text\" name=\"look_for\" class=\"input\"><br>
<input type=\"submit\" class=\"input\" value=\"".$w_send."!\">
</form>";

require($file_path."designes/".$default_design."/output_page.php");
?>