<?php
require_once("inc_common.php");
//setCookie("c_hash", $c_hash, time() + 2678400);
unset($messages_to_show);
$messages_to_show = array();
$def_color = $registered_colors[$default_color][1];
$flood_protection = 0;

include_once($engine_path."users_get_list.php");

if($exists) {
   $check_type = "logout";
   include("inc_user_class.php");
   include($ld_engine_path."users_get_object.php");
   include("user_validate.php");
        }

include($engine_path."logout.php");
include($ld_engine_path."rooms_get_list.php");

if ($exists and !$user_invisible) {

   include("inc_user_class.php");
   include($ld_engine_path."users_get_object.php");


    if($current_user->logout_phrase != "") {
            $sw_rob_logout = "<font color=\"bf0d0d\"><b>".$current_user->logout_phrase."</b></font>";
            $sw_rob_logout = eregi_replace("#", "<a style='text-decoration: underline' style='{cursor: pointer}' onClick=\"javascript:parent.Whisper('~');\">~</a>", $sw_rob_logout);
        }

    if($current_user->chat_status != "") {
             $sw_rob_logout = str_replace("||", "<font color=#bf0d0d>".ucfirst($current_user->chat_status)."</font>", $sw_rob_logout);
    }
    else $sw_rob_logout = str_replace("||", "", $sw_rob_logout);

        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                MESG_ROOM=>$cu_array[USER_ROOM],
                                                                MESG_FROM=>$rooms[$cu_array[USER_ROOM]]["bot"],
                                                                MESG_FROMWOTAGS=>$rooms[$cu_array[USER_ROOM]]["bot"],
                                                                MESG_FROMSESSION=>"",
                                                                MESG_FROMID=>0,
                                                                MESG_TO=>"",
                                                                MESG_TOSESSION=>"",
                                                                MESG_TOID=>"",
                                                                MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", $cu_array[USER_NICKNAME], $sw_rob_logout)."</font>");
                                }

include($engine_path."messages_put.php");
include($file_path."designes/".$design."/logout.php");
?>