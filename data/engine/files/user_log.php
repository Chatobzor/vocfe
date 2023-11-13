<?php

function WriteToUserLog($What, $UserRegID, $ModeratorNick) {
    global $is_regist;
    global $engine_path;
    global $data_path;
    global $max_mailbox_size;

    $tmp_is_regist = $is_regist;
    if(strlen(trim($What)) == 0) return;

    $UserRegID = intval(trim($UserRegID));
    if($UserRegID < 1) return;

    $moder_message                 = $What;
    $moder_user_name         = $ModeratorNick;
    $is_regist                         = $UserRegID;

    include($engine_path."moder_board_post_message.php");

        $is_regist = $tmp_is_regist;
}

?>