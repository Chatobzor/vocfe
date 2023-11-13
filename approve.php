<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");
include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

if ($current_user->user_class<1) {
	$error_text = "$w_no_admin_rights";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}
include_once($ld_engine_path."premoderation.php");
set_variable("op");
set_variable("id");
$id = intval($id);
$wait_mesg = array();
switch($op) {
	case "ap":
		$wait_mesg = premoder_approve($id);
	break;
	case "dec":
		$wait_mesg = premoder_decline($id);
	break;
	default:
		$wait_mesg = premoder_get();
}




$html_to_out = "Total:".count($wait_mesg)."  -- <a href=\"approve.php?session=".$session."\">refresh</a><br>";

$until = (count($wait_mesg)>10)?10:count($wait_mesg);
$html_to_out .= "<table border=\"1\"><tr><td>from</td><td>to</td><td>message</td><td>Appr</td><td>decl</td></tr>";
for ($i=0; $i<$until;$i++) {
	$html_to_out .= "<tr><td>".$wait_mesg[$i][MESG_FROMWOTAGS]."</td><td>".$wait_mesg[$i][MESG_TO]."</td>".
					"<td>".$wait_mesg[$i][MESG_BODY]."</td><td><a href=\"approve.php?op=ap&session=".$session."&id=".$wait_mesg[$i][MESG_ID]."\">Ap</a></td>".
					"<td><a href=\"approve.php?op=dec&session=".$session."&id=".$wait_mesg[$i][MESG_ID]."\">X</a></td></tr>\n";
}
$html_to_out .= "</table>";
include($file_path."designes/".$design."/output_page.php");
?>