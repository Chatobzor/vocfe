<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");
set_variable("message_id");
set_variable("send_to");
set_variable("send_to_id");
set_variable("group");

$send_to_id = intval($send_to_id);
$message_id = intval($message_id);

session_start();

if (!$exists)  {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if(!$is_regist_complete) {
    $error_text = "<div align=center>$w_roz_only_for_club.</div>";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}
$u_ids = array();
$u_names = array();
$tmp_body = "";
$tmp_subject = "";

$info_message = "";

$group = intval($group);

if($message_id == "") {
        if ($group != 1 and $send_to != "") {
                $user_to_search = $send_to;
                include($ld_engine_path."users_search.php");
                if (!count($u_ids)) $info_message = "<b>".str_replace("~","&quot;<b>".htmlspecialchars($send_to)."</b>&quot;",$w_search_no_found)."</b><br>";
        }
        if ($send_to_id!="") {
                include("inc_user_class.php");
                $ttt = $is_regist;
                $is_regist = $send_to_id; #fake again :(
                include($ld_engine_path."users_get_object.php");
                $u_ids[] = $is_regist;
                $is_regist = $ttt;
                $u_names[] = $current_user->nickname;
        }
    if($group == 1) {
      if (($send_to == 0 and ($cu_array[USER_CLASS] & ADM_BAN_MODEATORS)) or
                $send_to == 1 or
            $send_to == 2 or
                (($cu_array[USER_CLASS] & ADM_BAN_MODERATORS) and $send_to == 3) or
                (($cu_array[USER_CLASS] & ADM_BAN_MODERATORS) and $send_to == 4) or
                (($cu_array[USER_CLASS] & ADM_BAN_MODERATORS) and $send_to == 5) or
                (($cu_array[USER_CLANID] > 0) and $send_to == 6)) {
                     unset($u_ids);
                 unset($u_names);

                 $u_ids   = array();
                 $u_names = array();

                 $u_ids[]         = $send_to;
                 if($send_to == 0) $u_names[] = $w_usr_all_link;
                 if($send_to == 1) $u_names[] = $w_usr_adm_link;
                 if($send_to == 2) $u_names[] = $w_usr_shaman_link;
                 if($send_to == 3) $u_names[] = $w_usr_boys_link;
                 if($send_to == 4) $u_names[] = $w_usr_girls_link;
                 if($send_to == 5) $u_names[] = $w_usr_they_link;
                 if($send_to == 6) $u_names[] = $w_usr_clan_link;
        }
     }
}
else {
        $board_operation = "reply";
        $id = $message_id;
        include($ld_engine_path."hidden_board_process_message.php");
        $u_ids[] = $board_message["from_id"];
        $u_names[] = $board_message["from"];
        $tmp_body =  str_replace("<br>","\n",$board_message["body"]);
        $tmp_body  = "\n\n_______ ".str_replace("~", $board_message["from"], $w_user_wrote)." ______\n$tmp_body";
        $tmp_subject = str_replace("\"","&quot;","Re: ".$board_message["subject"]);
}

include($file_path."designes/".$design."/board_send.php");
?>