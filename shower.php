<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");
include($engine_path."messages_get_list.php");
set_variable("design");
set_variable("c_design");
set_variable("room");
if ($c_design!="" and $design=="") $design = $c_design;
if (!in_array($design, $designes)) $design = $default_design;
include($ld_engine_path."rooms_get_list.php");
if (!in_array($room, $room_ids))
        $room = intval($room_ids[0]);

$room = intval($room_ids[0]);

set_variable("user_lang");
set_variable("c_ulang");
if ($c_ulang != "" && $user_lang == "") $user_lang = $c_ulang;
if (!in_array($user_lang, $allowed_langs)) $user_lang = $language;
else { include_once($file_path."languages/".$user_lang.".php"); }

$out_messages = "";
$already_showed = 0;
for ($i=$total_messages-1; $i>=0; $i--) {
        if ($already_showed>=$history_size) break;
        $mesg_array = explode ("\t", $messages[$i], MESG_TOTALFIELDS);
        if ($room == $mesg_array[MESG_ROOM]) {
            $message = strip_tags($mesg_array[MESG_BODY]);
            $message = str_replace("parent.voc_who_reload = 1;","","$message");
            $message = wordwrap($message, 75," ", 1);
            $to_out = "";
            if ($mesg_array[MESG_TO] == "") {
                    $to_out = str_replace("[HOURS]",date("H",$mesg_array[MESG_TIME]),$message_format);
                    $to_out = str_replace("[MIN]",date("i",$mesg_array[MESG_TIME]),$to_out);
                    $to_out = str_replace("[SEC]",date("s",$mesg_array[MESG_TIME]),$to_out);
                    $to_out = str_replace("[NICK]",strip_tags($mesg_array[MESG_FROMWOTAGS]),$to_out);
                    $to_out = str_replace("[NICK_WO_TAGS]",strip_tags($mesg_array[MESG_FROMWOTAGS]),$to_out);
                    $to_out = str_replace("[MESSAGE]",$message,$to_out);
            }
            if ($to_out != "") {
                    $out_messages = $to_out."<br>\n". $out_messages;
                    $already_showed++;
            }
            //if ($already_showed>9) break;
        }
}



$out_users = "";
$users_in_room = 0;
for ($i=0; $i<count($users); $i++) {
    $data = explode("\t", $users[$i]);
    if ($data[10] == $room) {
        if(intval(trim($data[USER_INVISIBLE])) != 1) {
            $out_users .= $data[0]."<br>\n";
                    $users_in_room++;
        }
    }
}

if(count($users) == 0 or $users_in_room == 0) {$out_users_header =  "$w_nobody_in\n";}
    else {
        $out_users_header = (count($room_ids)>1)? $w_in_room:$w_in_chat;
        $out_users_header .= ": <b>$users_in_room</b> ".w_people($users_in_room).".\n";
    }

include($file_path."designes/".$design."/shower.php");
?>