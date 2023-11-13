<?php

require_once("inc_common.php");
set_variable("photoss");

include($ld_engine_path."rooms_get_list.php");
include($engine_path."users_get_list.php");

if (!$exists) {
    $error_text = "$w_no_user";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}
//whoami?
$IsModer = 0;
if ($is_regist) {
    include("inc_user_class.php");
    include($ld_engine_path."users_get_object.php");
    if ($current_user->user_class > 0) {
        $IsModer = 1;
    } else {
        $IsModer = 0;
    }
}
//echo "IsModer=$IsModer";
$usr_test = array();

for ($kk = 0; $kk < count($room_ids); $kk++) {
    $in_room = array();
    for ($i = 0; $i < count($users); $i++) {
        $usr_test = explode("\t", $users[$i]);
        $who_nickname = $usr_test[USER_NICKNAME];
        $who_room = $usr_test[USER_ROOM];

        if ($who_room == $room_ids[$kk]) {
            $usr_test[USER_INVISIBLE] = intval(trim($usr_test[USER_INVISIBLE]));

            if ($usr_test[USER_INVISIBLE]) {
                if ($IsModer) {
                    $in_room[] = $who_nickname;
                }
            } else {
                $in_room[] = $who_nickname;
            }
        }
    }
    $rooms[$room_ids[$kk]]["in_room"] = $in_room;
}
include($file_path."designes/".$design."/rooms.php");