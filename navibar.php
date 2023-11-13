<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");

if (!$exists)  {
	$error_text = "$w_no_user";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}
if ($is_regist) {
	include("inc_user_class.php");
	include($ld_engine_path."users_get_object.php");
	#fake for navi bar.
	if ($current_user->user_class>0) $current_user->user_class = "admin";
	else $current_user->user_class = "user";
}
include($file_path."designes/".$design."/navibar.php");
?>