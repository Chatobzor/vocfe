<?php
define("_TAIL_",1);
define ("CLEAR_TIME", 0);
define ("CLEAR_NICKNAME", 1);
define ("CLEAR_AUTHOR",        2);
define ("CLEAR_TOTAL",        3);

$clr_arr = array();

require_once("inc_common.php");
include($engine_path."users_get_list.php");
include($file_path."inc_form_message.php");
@set_time_limit(0);
if (!$exists)  {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if (!in_array("php_tail",$chat_types)) {echo "not allowed"; exit;}

include($engine_path."messages_get_list.php");
$out_messages = array();

echo str_replace("[CHARSET]", (($charset == "")?"":"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$charset."\">"),
                                         str_replace("[CHAT_URL]", $chat_url,
                                                str_replace("[SKIN]", $design,
                                                                 str_replace("[TOPIC]",$rooms[$room_id]["topic"],implode("",file($file_path."designes/".$design."/daemon_html_header.html")))
                                                )
                                        )
                                );
flush();
$start_tail_num = $cu_array[USER_TAILID]+1;
$fields_to_update = array();
$fields_to_update[0][0] = USER_TAILID;
$fields_to_update[0][1] = $start_tail_num;
include($engine_path."user_din_data_update.php");

$already_showed = 0;
$total_out = "";
$total_messages = count($messages);
//to get $last_id
list($last_id, $to_out) = form_message(0,$messages[$total_messages-1], $ignored_users);
for ($i=$total_messages-1;$i>=0;$i--) {
        if ($already_showed>=$history_size) break;
        list($unused, $to_out) = form_message(0,$messages[$i], $ignored_users);
        if ($to_out!="") {
                $already_showed++;
                $total_out = $to_out.$total_out;
        }
}
        if(strlen($total_out) > 0) echo "<script>".$total_out."\nparent.ping();</script>";
        else echo " ";
flush();

$abort_counter = 0;
while(1) {
        $abort_counter++;
        sleep(2);
        include($engine_path."users_get_list.php");
        if (!$exists) {
                echo  "$w_no_user";
                exit;
        }
        if ($cu_array[USER_TAILID] != $start_tail_num) {
                echo $w_only_one_tail;
                exit;
        }
        include($engine_path."messages_get_list.php");
        list($last_id,$to_out) = show_messages($last_id, $messages,$ignored_users);
        if ($to_out != "") {
                $abort_counter = 0;
                echo "<script>".$to_out."\n parent.ping();</script>\n";
                flush();
        }
        if ($abort_counter>5)  {
                echo " ";
                $abort_counter = 0;
                flush();
        }

}


function show_messages($last_id, $messages, $ignored_users,$start_at = 0) {
        $total_messages = count($messages);
        $total_out = "";
        for ($i=$start_at;$i<$total_messages;$i++) {
                list($message_id, $to_out) = form_message($last_id, $messages[$i],$ignored_users);
                $total_out .= $to_out;
        }
        return array($message_id,$total_out);
}
?>