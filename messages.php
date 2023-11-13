<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");

if (!$exists)  {
	$error_text = "$w_no_user";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}
if (!in_array("reload",$chat_types)) {echo "not allowed"; exit;}

include($file_path."inc_form_message.php");
include($engine_path."messages_get_list.php");
$out_messages = array();
$already_showed = 0;
#actually, I can add possibility to set the number of messages to show into user profile
#but since this method (refreshing show) is not primary in the chat, 
#I don't want to spend my time for it :)
	$total_out = "";
	$total_messages = count($messages);
	//to get $last_id
	list($last_id, $to_out) = form_message(0,$messages[$total_messages-1], $ignored_users);
	for ($i=$total_messages-1;$i>=0;$i--) {
		list($unused, $to_out) = form_message(0,$messages[$i], $ignored_users);
		if ($to_out!="") {
			$already_showed++;
			$total_out = $to_out.$total_out;
		}
		if ($already_showed>29) break;
	}

$out_messages[] = $total_out;

include($file_path."designes/".$design."/messages.php");
?>