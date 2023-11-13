<?php
require_once("inc_common.php");
set_variable("session");
set_variable("win_id");
set_variable("c_user_color");
set_variable("room_id");
$room_id = intval($room_id);
$REMOTE_ADDR = "";
include("get_IP.lib.php3");
//if (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) $REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
$REMOTE_ADDR = $IP;

set_variable("design");
if ($design == "") $design = $default_design;
else if (!in_array($design, $designes)) $design = $default_design;
$current_design = $chat_url."designes/".$design."/";

set_variable("user_lang");
set_variable("c_ulang");
if ($c_ulang != "" && $user_lang == "") $user_lang = $c_ulang;
if (!in_array($user_lang, $allowed_langs)) $user_lang = $language;
else { include_once($file_path."languages/".$user_lang.".php"); }

set_variable("room");
include($ld_engine_path."rooms_get_list.php");
if (!in_array($room, $room_ids))
        $room = intval($room_ids[0]);

//for the future:
//$user_lang = "en";
$fields_to_update = array();

//if user is already in the chat and just reload page or change the room:
if ($session != "") {
        include($engine_path."users_get_list.php");
        if (!$exists) {
                $error_text = "$w_no_user";
                include($file_path."designes/".$design."/error_page.php");
                exit;
        }

    include_once("inc_user_class.php");
    include($engine_path."users_get_object.php");
    if($current_user->user_class & ADM_BAN_MODERATORS) {
            if($current_user->show_ip != "") { $REMOTE_ADDR      = $current_user->show_ip;
                                       $current_user->IP = $current_user->show_ip;
                                     }
        }

    include("user_validate.php");

        $registered_user = $is_regist;
        //again, cause user has current roomdesign, not the new one
        $shower = "messages.php?session=$session";
        $chat_type = $user_chat_type;
        if (!in_array($chat_type, $chat_types)) $chat_type = $chat_types[0];
        if ($chat_type=="tail") $shower = "$daemon_url?$session";
        elseif ($chat_type=="reload") $shower = "messages.php?session=$session";
        elseif ($chat_type=="php_tail") $shower = "tail.php?session=$session";
        elseif ($chat_type=="js_tail") $shower = "js_frameset.php?session=$session";

        //mod_voc patch
        if(intval($daemon_type) == 2) $shower = $daemon_host."/?".$session;

        if($c_user_color== "") {$user_color=$default_color;}
                else $user_color = $c_user_color;
        $def_color = $registered_colors[$default_color][1];

        //$room_id == current user room
        //$room -- room where user want to go...

        RenderCopyrights();
        include($file_path."designes/".$design."/voc_popup.php");
        exit;
 }

function RenderCopyrights() {
global $file_path, $design;
if($enable_gzip) ob_start("ob_gzhandler");
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");
?>
<!--
/////////////////////////////////////////////////////
//                                                 //
//               Voodoo chat v. 0.90               //
//         (c) 1999-2004 by Vlad Vostrykh          //
//               http://vochat.com/                //
//                                                 //
//                QPL ver1 License                 //
//        See voc/LICENSE file for details         //
//                                                 //
//          because nobody reads licenses          //
//              I have to remind that              //
//      you're not allowed to modify/remove        //
//              any copyright notices              //
//                                                 //
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//                                                 //
//                  VOC++ v 1.0beta-0.9            //
//       St. Valentine Edition (Christmas SE)      //
//       (c) 2004 by DareDEVIL & EricDraven        //
//       (c) 2004 by CREATIFF Design Studio        //
//            http://vocplus.creatiff.com.ua/      //
//         e-mail: support@creatiff.com.ua         //
//                                                 //
//     original VOC engine and Voodoo Chat         //
//       (c) 1999-2004 by Vlad Vostryk             //
//            http://www.vochat.com/               //
//                                                 //
//                QPL ver1 License                 //
//        See voc/LICENSE file for details         //
//                                                 //
//          because nobody reads licenses          //
//              I have to remind that              //
//      you're not allowed to modify/remove        //
//              any copyright notices              //
//                                                 //
//     You are NOT ALLOWED to use this mod or      //
//              it's parts for                     //
//     ANY CHAT in Ivano-Frankivsk region, Ukraine.//
//     Especially for:                             //
//       - www.karna.if.ua                         //
//       - www.shelter.if.ua                       //
//       - www.chat.if.org.ua                      //
//       - www.sotka.if.ua                         //
//       - www.ifportal.net                        //
//       - www.rozvaga.if.ua                       //
/////////////////////////////////////////////////////
-->
<?php
}
?>