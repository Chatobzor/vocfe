<?php

include($engine_path."messages_put.php");
$fields_to_update[0][0] = USER_INVISIBLE;
$fields_to_update[0][1] = 1;

include($ld_engine_path."rooms_get_list.php");
$w_rob_name = $rooms[$room_id]["bot"];

if ($current_user->logout_phrase != "") {
    $sw_rob_logout = "<font color=\"bf0d0d\"><b>".$current_user->logout_phrase."</b></font>";
    $sw_rob_logout = eregi_replace(
        "#",
        "<a style='text-decoration: underline' style='{cursor: pointer}' onClick=\"javascript:parent.Whisper('~');\">~</a>",
        $sw_rob_logout
    );
}
if ($current_user->chat_status != "") {
    $sw_rob_logout = str_replace(
        "||",
        "<font color=#bf0d0d>".ucfirst($current_user->chat_status)."</font>",
        $sw_rob_logout
    );
} else {
    $sw_rob_logout = str_replace("||", "", $sw_rob_logout);
}
$messages_to_show[] = array(
    MESG_TIME => my_time(),
    MESG_ROOM => $room_id,
    MESG_FROM => $w_rob_name,
    MESG_FROMWOTAGS => $w_rob_name,
    MESG_FROMSESSION => "",
    MESG_FROMID => 0,
    MESG_TO => "",
    MESG_TOSESSION => "",
    MESG_TOID => "",
    MESG_BODY => "<font color=\"$def_color\">".str_replace("~", $user_name, $sw_rob_logout)."</font>"
);
include($engine_path."messages_put.php");
include($engine_path."user_din_data_update.php");

$action_items[$action_id]['Quantity']--;
if ($action_items[$action_id]['Quantity'] <= 0) {
    unset($action_items[$action_id]);
}