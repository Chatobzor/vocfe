<?php
require_once("inc_common.php");
set_variable("look_for");
#to determine design:
include($engine_path."users_get_list.php");
$u_ids = array();
$u_names = array();
$tmp_body = "";
$tmp_subject = "";

$info_message = "";

if($look_for!="") {
	$user_to_search = $look_for;
	include($ld_engine_path."users_search.php");
	if (!count($u_ids)) $info_message = "<b>".str_replace("~","&quot;<b>".htmlspecialchars($look_for)."</b>&quot;",$w_search_no_found)."</b><br>";
}
include($file_path."designes/".$design."/users.php");
?>