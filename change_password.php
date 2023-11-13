<?php
require_once("inc_common.php");
set_variable("key");
set_variable("op");
$html_to_out = "";
$error = 0;
if ($op =="change") {
	set_variable("password");
	set_variable("password2");
	if (strlen($password <1)) {
		$html_to_out .= "<b>".$w_incorrect_password."</b><br><br>";
		$error = 1;
	}else if (strcmp($password,$password2)!=0) {
		$html_to_out .= "<b>".$w_password_mismatch."</b><br><br>";
		$error = 1;
	} else {
		include($ld_engine_path."password_reminder.php");
		$er = update_password($password, $key);
		if ($er == "") {
			$html_to_out .= "<b>".$w_pas_changed."</b><br><br><a href=\"".$chat_url."\">".$w_login_button."</a>";
			require($file_path."designes/".$default_design."/output_page.php");
			exit();
		} else $html_to_out .= "<b>".$er."</b><br><br>";
	}
	
}

$html_to_out .= "<b>".$w_pr_title."</b>
	<form method=\"post\" action=\"".$chat_url."change_password.php\">
	<input type=\"hidden\" name=\"key\" value=\"".$key."\">
	<input type=\"hidden\" name=\"op\" value=\"change\">
	<table>
	<tr><td>".$w_new_password.": </td><td><input type=\"password\" name=\"password\" class=\"input\"></td></tr>
	<tr><td>".$w_confirm_password.": </td><td><input type=\"password\" name=\"password2\" class=\"input\"></td></tr>
	<tr><td></td><td><input type=\"submit\" value=\"".$w_update."\" class=\"input\"></td></tr>
	</table>
	</form>";

require($file_path."designes/".$default_design."/output_page.php");
?>