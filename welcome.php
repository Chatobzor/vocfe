<?php

require_once("inc_common.php");
set_variable("design");
set_variable("c_design");
set_variable("c_user_name");
set_variable("c_chat_type");

$c_user_name = str_replace("\"", "&quot;", $c_user_name);

if ($c_design != "" and $design == "") {
    $design = $c_design;
}
if (!in_array($design, $designes)) {
    $design = $default_design;
}

set_variable("user_lang");
set_variable("c_ulang");
if ($c_ulang != "" && $user_lang == "") {
    $user_lang = $c_ulang;
}
if (!in_array($user_lang, $allowed_langs)) {
    $user_lang = $language;
} else {
    include_once($file_path."languages/".$user_lang.".php");
}

if (in_array($c_chat_type, $chat_types)) {
    $chat_type = $c_chat_type;
} else {
    $chat_type = $chat_types[0];
}


set_variable("room");
include($ld_engine_path."rooms_get_list.php");
if (!in_array($room, $room_ids)) {
    $room = $room_ids[0];
}

for ($i = 0; $i < count($room_ids); $i++) {
    $rooms[$room_ids[$i]]["users"] = 0;
}

include($engine_path."users_get_list.php");
for ($i = 0; $i < count($users); $i++) {
    $user_array = explode("\t", $users[$i], USER_TOTALFIELDS);
    $rooms[$user_array[USER_ROOM]]["users"]++;
}

include($file_path."designes/".$design."/welcome.php");