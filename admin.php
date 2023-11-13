<?php
include("inc_common.php");
include($ld_engine_path."rooms_get_list.php");
include($engine_path."users_get_list.php");
$messages_to_show = array();
if (!$exists) {
	$error_text = "$w_no_user";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");
set_variable("cause");
set_variable("toBan");
set_variable("kill_time");
set_variable("action");

if ($current_user->user_class<1) {
	$error_text = "$w_no_admin_rights";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}
include($file_path."designes/".$design."/admin.php");
exit;
?>