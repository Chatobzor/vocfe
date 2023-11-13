<?php
require_once("inc_common.php");
include("events.php");

set_variable("user_name");
set_variable("chat_type");
set_variable("c_user_color");
set_variable("user_color");

if ($c_user_color!="") {
$c_user_color = intval($c_user_color);
if (($c_user_color < 0) or ($c_user_color >= count($registered_colors))) {$user_color=$default_color;}
else $user_color = $c_user_color;
}

$user_name = trim($user_name);
if(strcasecmp($user_name, $sw_usr_all_link) == 0 or
   strcasecmp($user_name, $sw_usr_adm_link) == 0 or
   strcasecmp($user_name, $sw_usr_boys_link) == 0 or
   strcasecmp($user_name, $sw_usr_girls_link) == 0 or
   strcasecmp($user_name, $sw_usr_they_link) == 0 or
   strcasecmp($user_name, $sw_usr_clan_link) == 0 or
   strcasecmp($user_name, $sw_usr_shaman_link) == 0) {
           exit;
   }

set_variable("password");
set_variable("room_id");
$room_id = intval($room_id);

$_tmp_rnd = false;
if ($c_hash == "") {
        $c_hash   = md5(uniqid(rand()));
        $_tmp_rnd = true;
}

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
            if($current_user->show_ip != "") {
              $REMOTE_ADDR      = $current_user->show_ip;
              $current_user->IP = $current_user->show_ip;
            }
        }

    include("user_validate.php");

    if(intval($ar_rooms[$room][ROOM_CLUBONLY]) == 1) {
       if(!$current_user->is_member) {
         $error_text ="$w_roz_not_allowed<br><a href=\"index.php\">$w_try_again</a>";
         include($file_path."designes/".$design."/error_page.php");
         exit;
       }
    }
    if($ar_rooms[$room][ROOM_PASSWORD] != "") {
      set_variable("room_password");
      if($room_password != $ar_rooms[$room][ROOM_PASSWORD]) {
        include($file_path."designes/".$design."/room_password_required.php");
        exit;
      }
    }

    if(intval($current_user->plugin_info["jail_start"]) + intval($current_user->plugin_info["jail_time"]) > my_time()) {
          $room  =  $jail_id;
    }

        $registered_user = $is_regist;
        //again, cause user has current roomdesign, not the new one
        $shower = "messages.php?session=$session";
        $chat_type = $user_chat_type;
        if (!in_array($chat_type, $chat_types)) $chat_type = $chat_types[0];
        if ($chat_type=="tail") $shower = "$daemon_url?$session";
        elseif ($chat_type=="reload") $shower = "messages.php?session=$session";
        elseif ($chat_type=="php_tail") $shower = "tail.php?session=$session";
        elseif ($chat_type=="js_tail") $shower = "js_writer.php?session=$session";
        if($c_user_color== "" and $user_color == "") {$user_color=$default_color;}
                else $user_color = $c_user_color;
        $def_color = $registered_colors[$default_color][1];

        if(intval($daemon_type) == 2) $shower = "$daemon_host/?$session";

        //$room_id == current user room
        //$room -- room where user want to go...
        if ($room_id != $room and $user_invisible != 1) {
                //somebody can jumping from one room to another and floods in such way. in this case enable flood_protection
                $flood_protection = 1;
                $w_rob_name = $rooms[$room_id]["bot"];
                $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                        MESG_ROOM=>$room_id,
                                                                        MESG_FROM=>$w_rob_name,
                                                                        MESG_FROMWOTAGS=>$w_rob_name,
                                                                        MESG_FROMSESSION=>"",
                                                                        MESG_FROMID=>0,
                                                                        MESG_TO=>"",
                                                                        MESG_TOSESSION=>"",
                                                                        MESG_TOID=>"",
                                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("*",$rooms[$room]["title"],str_replace("~", $user_name, $sw_goes_to_room))."</font>");
                $w_rob_name = $rooms[$room]["bot"];
                $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                        MESG_ROOM=>$room,
                                                                        MESG_FROM=>$w_rob_name,
                                                                        MESG_FROMWOTAGS=>$w_rob_name,
                                                                        MESG_FROMSESSION=>"",
                                                                        MESG_FROMID=>0,
                                                                        MESG_TO=>"",
                                                                        MESG_TOSESSION=>"",
                                                                        MESG_TOID=>"",
                                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("*",$rooms[$room_id]["title"],str_replace("~", $user_name, $sw_came_from_room))."</font>");
                if($cu_array[USER_CLASS] ==0 && $ar_rooms[$room][ROOM_PREMODER]==1) {
                        //khm... i have to output it:
                        $flood_protection = 0;
                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                        MESG_ROOM=>$room,
                                                                        MESG_FROM=>$w_rob_name,
                                                                        MESG_FROMWOTAGS=>$w_rob_name,
                                                                        MESG_FROMSESSION=>"",
                                                                        MESG_FROMID=>0,
                                                                        MESG_TO=>$user_name,
                                                                        MESG_TOSESSION=>$session,
                                                                        MESG_TOID=>$is_regist,
                                                                        MESG_BODY=>"<font color=\"$def_color\">".$w_premoder_room."</font>");
                }
                include($engine_path."messages_put.php");
        }
        $room_id = $room;
        $fields_to_update[0][0] = 10;
        $fields_to_update[0][1] = $room_id;
        include($engine_path."user_din_data_update.php");
    RenderCopyrights();
        include($file_path."designes/".$design."/voc.php");
        exit;
 }

//if user is trying to log in.
//DD - loggin in as invisible - first symbol must be "*"
$TryToBeInvisible = false;
if(strlen($user_name) > 0) {
        if(substr($user_name, 0, 1) == "*") {
       $user_name = substr($user_name, 1);
       $TryToBeInvisible = true;
    }
}

include("inc_user_class.php");
//end DD

setCookie("c_user_name", $user_name, time() + 2678400);
setCookie("c_chat_type", $chat_type, time() + 2678400);
setCookie("c_design", $design, time() + 2678400);
setCookie("c_hash", $c_hash, time() + 2678400);
setCookie("c_ulang", $user_lang, time() + 2678400);


include("inc_to_canon_nick.php");
#check for nickname;

if ((strlen($user_name)<$nick_min_length) or (strlen($user_name)>$nick_max_length)) {
        $error_text ="$w_incorrect_nick<br><a href=\"index.php\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if (ereg("[^".$nick_available_chars."]", $user_name)) {
        $error_text ="$w_incorrect_nick<br><a href=\"index.php\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if (strtolower($user_name) == strtolower(strip_tags($w_rob_name))) {
        $error_text ="$w_incorrect_nick<br><a href=\"index.php\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}


		 $canon_view    = to_canon_nick($user_name);
         $old_user_name = $user_name;
         include($engine_path.'users_get_list.php');
         $user_name = $old_user_name;

         $session = '';
         for($i = 0; $i < count($users); $i++) {
            $user_array  = explode("\t", trim($users[$i]), USER_TOTALFIELDS);
	        if (strcmp($user_array[USER_CANONNICK],$canon_view) == 0) {
	        	$session = $user_array[USER_SESSION];
	        	break;
	        }
	     }

	     if($session == '') $session = md5(uniqid(rand()));

$shower = "messages.php?session=$session";
if (!in_array($chat_type, $chat_types)) $chat_type = $chat_types[0];

if ($chat_type=="tail") $shower = "$daemon_url?$session";
elseif ($chat_type=="reload") $shower = "messages.php?session=$session";
elseif ($chat_type=="php_tail") $shower = "tail.php?session=$session";
elseif ($chat_type=="js_tail") $shower = "js_writer.php?session=$session";

if(intval($daemon_type) == 2) $shower = $daemon_host."/?".$session;
include($ld_engine_path."ban_check.php");

if (check_ban(array("un|".to_canon_nick($user_name), "ip|".$REMOTE_ADDR, "ch|".$c_hash, "bh|".$browser_hash, "sn|".substr($REMOTE_ADDR, 0 , strrpos($REMOTE_ADDR,".")), "sn|".substr($REMOTE_ADDR, 0 , strrpos($REMOTE_ADDR,":")) ))) {
        $error_text=$w_banned;
        include($file_path."designes/".$design."/error_page.php");
        exit;
}


#???????????
/*
if($c_user_color== "" and $user_color == "") {$user_color=$default_color;}
else if($c_user_color!="") $user_color = $c_user_color;
*/
$registered_user = 0;
$users = array();
$htmlnick = "";
include($ld_engine_path."voc_user_data.php");

if (!$registered_user) {
      if($club_mode) {
              header("Location: ".$chat_url."registration_form.php?design=$design&user_name=$user_name&new_user_sex=$new_user_sex&user_color=$user_color&room=$room");
              exit;
      }
      else {
            //automatically create a new profile
            $new_user_name = $user_name;
            $new_user_sex  = 1;
            if(!$impro_registration) include($ld_engine_path."registration_fake.php");
            else {

             set_variable("impro_user_code");
             set_variable("reg_word");
             set_variable("impro_id");

             if($reg_word == "true") {
                 include($ld_engine_path."impro.php");

                 if (!impro_check($impro_id, $impro_user_code)){
                      $error_text = $w_impro_incorrect_code."<br><a href=\"index.php?session=".$session."\">".$w_try_again."</a>";
                      include($file_path."designes/".$design."/error_page.php");
                      exit;
                 }
                 include($ld_engine_path."registration_fake.php");
             }
             else {
                  header("Location: ".$chat_url."registration_cell.php?design=$design&user_name=$user_name&new_user_sex=$new_user_sex&user_color=$user_color&room=$room");
                  exit;
             }
            }
      }
}

 if(intval($ar_rooms[$room][ROOM_CLUBONLY]) == 1) {
       if(!$current_user->is_member) {
         $error_text ="$w_roz_not_allowed<br><a href=\"index.php\">$w_try_again</a>";
         include($file_path."designes/".$design."/error_page.php");
         exit;
       }
 }
 if($ar_rooms[$room][ROOM_PASSWORD] != "") {
      set_variable("room_password");
      if($room_password != $ar_rooms[$room][ROOM_PASSWORD]) {
        include($file_path."designes/".$design."/room_password_required.php");
        exit;
      }
 }

//DD Levandovka fix :-)
if($current_user->user_class == 0 and $TryToBeInvisible) $TryToBeInvisible = false;

if($current_user->custom_class != 0) $user_custom_class = $current_user->custom_class;

//DD updating userinfo
$is_regist                                = $registered_user;
$current_user->IP                         = $IP;

if($current_user->user_class & ADM_BAN_MODERATORS) {
        if($current_user->show_ip != "") { $REMOTE_ADDR      = $current_user->show_ip;
                                       $current_user->IP = $current_user->show_ip;
                                     }
}

if(!intval($open_chat) and !($current_user->user_class & ADM_BAN_MODERATORS)) {
        $error_text=$w_roz_chat_closed;
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

if(disk_free_space($data_path) < 10*1024*1024 and is_file($data_path."engine/files/guardian.php") and intval($vocplus_useguardian)) {
   if($vocplus_guardian_dellogs) {
           include_once($data_path."engine/files/guardian.php");
              cleanUpOldLogs();
   }
}

if(disk_free_space($data_path) < 10*1024*1024 and !($current_user->user_class & ADM_BAN_MODERATORS)) {
        $error_text=$w_roz_chat_closed."<br><b>".$w_roz_out_of_space." ($admin_mail)</b>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

$current_user->browser_hash = $browser_hash;
$current_user->cookie_hash  = $c_hash;
$current_user->session      = $session;
if(isset($_SERVER["HTTP_USER_AGENT"])) $current_user->user_agent   = htmlspecialchars($_SERVER["HTTP_USER_AGENT"]);
else $current_user->user_agent   = htmlspecialchars($HTTP_SERVER_VARS['HTTP_USER_AGENT']);


include($ld_engine_path."user_info_update.php");


//updating the similar nicks table
$similars = array();
$fp1 = fopen($data_path."similar_nicks.tmp", "ab+");
if (!$fp1) trigger_error("Could not open ".$data_path."similar_nicks.tmp"." for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp1, LOCK_EX))
        trigger_error("Could not LOCK ".$data_path."similar_nicks.tmp"." file. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp1,0);
$isNickFoundInSimilars = false;
while ($data = fgets($fp1, 4096)) {
        $u_data  = explode("\t",str_replace("\r","",str_replace("\n","",$data)));

        $tov = 0;
        for($i = 0; $i < count($current_user->photo_voted); $i++) {
               $tov += $current_user->photo_voted_mark[$i];
        }

        if ($u_data[0] == $is_regist) {
                $u_data[2] = $current_user->password;
                $u_data[3] = $current_user->email;
                $u_data[4] = $current_user->IP;
                $u_data[5] = $current_user->browser_hash;
                $u_data[6] = $current_user->cookie_hash;
                $u_data[7] = $current_user->points;
                $u_data[8] = $current_user->online_time;
                $u_data[9] = $current_user->credits;
                $u_data[10] = $tov;
                $isNickFoundInSimilars = true;
        }
        $similars[] .= implode("\t",$u_data) . "\n";
}

if(!$isNickFoundInSimilars) {
    $similars[] = $is_regist."\t".$user_name."\t".$current_user->password."\t".$current_user->email."\t".$current_user->IP."\t".$current_user->browser_hash."\t".$current_user->cookie_hash."\t".$current_user->points."\t".$current_user->online_time."\t".$current_user->credits."\t".$tov."\n";
}

ftruncate($fp1,0);
fwrite($fp1,implode($similars,""));
fflush($fp1);
flock($fp1, LOCK_UN);
fclose($fp1);
unset($similars);
//end of updating the similar nicks table


//events
riseEvent(EVENT_LOGIN, $user_name, $user_class);
//

if($current_user->login_phrase != "") {
                $sw_rob_login = $current_user->login_phrase;
            $sw_rob_login = eregi_replace("#", "<a style='text-decoration: underline' style='{cursor: pointer}' onClick=\"javascript:parent.Whisper('~');\">~</a>", $sw_rob_login);
}

if($current_user->chat_status != "") {
   $sw_rob_login = str_replace("||", "<font color=#bf0d0d>".ucfirst($current_user->chat_status)."</font>", $sw_rob_login);
   }
   else $sw_rob_login = str_replace("||", "", $sw_rob_login);

$def_color = $registered_colors[$default_color][1];
$flood_protection = 0;
unset($messages_to_show);
$messages_to_show = array();
include($ld_engine_path."rooms_get_list.php");
//$room we get from request, room_id -- internal variable
$room_id = $room;
if (!in_array($room_id,$room_ids))$room_id=intval($room_ids[0]);
$w_rob_name = $rooms[$room_id]["bot"];

include($engine_path."voc.php");

if(!$TryToBeInvisible) {

if ($hi)
        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                MESG_ROOM=>$room_id,
                                                                MESG_FROM=>$w_rob_name,
                                                                MESG_FROMWOTAGS=>$w_rob_name,
                                                                MESG_FROMSESSION=>"",
                                                                MESG_FROMID=>0,
                                                                MESG_TO=>"",
                                                                MESG_TOSESSION=>"",
                                                                MESG_TOID=>"",
                                                                MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", $user_name, $sw_rob_login)."</font>");

if($cu_array[USER_CLASS] ==0 && $ar_rooms[$room_id][ROOM_PREMODER]==1)
        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                MESG_ROOM=>$room_id,
                                                                MESG_FROM=>$w_rob_name,
                                                                MESG_FROMWOTAGS=>$w_rob_name,
                                                                MESG_FROMSESSION=>"",
                                                                MESG_FROMID=>0,
                                                                MESG_TO=>$user_name,
                                                                MESG_TOSESSION=>$session,
                                                                MESG_TOID=>$is_regist,
                                                                MESG_BODY=>"<font color=\"$def_color\">".$w_premoder_room."</font>");

$toDay = date("j-n");
$birthDay = $bDay."-".$bMon;
#$birthDay = date("jn",mktime(0,0,0,$bDay , $bMon, date("Y")));
if ($toDay == $birthDay)
        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                MESG_ROOM=>$room_id,
                                                                MESG_FROM=>$w_rob_name,
                                                                MESG_FROMWOTAGS=>$w_rob_name,
                                                                MESG_FROMSESSION=>"",
                                                                MESG_FROMID=>0,
                                                                MESG_TO=>"",
                                                                MESG_TOSESSION=>"",
                                                                MESG_TOID=>"",
                                                                MESG_BODY=>"<font color=\"".$registered_colors[$highlighted_color][1]."\">".str_replace("~", $user_name, $sw_rob_hb)."</font>");
include($engine_path."messages_put.php");
}

if ($rooms[$room]["design"] != "")
        $design = $rooms[$room]["design"];
$current_design = $chat_url."designes/".$design."/";

RenderCopyrights();
include($file_path."designes/".$design."/voc.php");

function RenderCopyrights() {
global $file_path, $design, $w_title, $user_name;
$w_title = '['.$user_name.'] / '.$w_title;
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
//                VOC++ v 1.0-0.95.SPC             //
//              Business Special Edition           //
//       (c) 2004 by DareDEVIL & EricDraven        //
//     (c) 2004-2006 by CREATIFF Design Studio     //
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
//     ANY CHAT in Prekarpathian   region, Ukraine.//
/////////////////////////////////////////////////////
-->
<?php
}
?>