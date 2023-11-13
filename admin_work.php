<?php
require_once("inc_common.php");
include($ld_engine_path."rooms_get_list.php");
include($engine_path."users_get_list.php");

include($file_path."tarrifs.php");

if (!$exists) {
    exit;
}

include_once($data_path."engine/files/user_log.php");

include("events.php");

@set_time_limit(0);

$messages_to_show = array();
if (!$exists) {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

function array_trim ( $array, $index ) {
   if (is_array ( $array ) ) {
     unset ( $array[$index] );
     array_unshift ( $array, array_shift ( $array ) );
     return $array;
     }
   else {
     return false;
     }
}

function addURLS($str)
{
        global $chat_url;
        $str2 = $str;
        if (function_exists('preg_replace')){
            $str2 = preg_replace("/(?<!<a href=\")(?<!\")(?<!\">)((http|https|ftp):\/\/[\w?=&.\/-~#-_]+)/e",
                                        "'<a href=\"".$chat_url."go.php?url='.urlencode('\\1').'\" target=\"_blank\">\\1</a>'",
                                        $str);
            $str2 = preg_replace("/((?<!<a href=\"mailto:)(?<!\">)(?<=(>|\s))[\w_-]+@[\w_.-]+[\w]+)/","<a href=\"mailto:\\1\">\\1</a>",$str2);
        }
        if($str != $str2) $str2 = str_replace("&amp;","&", $str2);
        return $str2;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

$check_type = "admin_work";
include("user_validate.php");

set_variable("op");
if(strlen(trim($op)) == 0) $op = "ban";
if ($current_user->user_class<1 and $op != "marry" and $op != "do_marriage" and $op != "do_un_marriage") {
        $error_text = "$w_no_admin_rights";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if ($op == "marry" or $op != "do_marriage" or $op != "do_un_marriage") {
    if (!($current_user->custom_class & CST_PRIEST) and $current_user->user_class<1) {
                $error_text = "$w_no_admin_rights";
                include($file_path."designes/".$design."/error_page.php");
                exit;
        }
}
if (($current_user->user_class & ADM_BAN) && $op == "") $op = "ban";

switch ($op) {
        case "similar":
        if (!($current_user->user_class & ADM_BAN)) {
                        $error_text = "$w_no_admin_rights";
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
        }
                   $html_to_out = "<form method=\"post\" action=\"admin_work.php\" target=\"voc_admin_work\">".
                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                "<input type=\"hidden\" name=\"op\" value=\"do_similar\">".
                "<input type=\"hidden\" name=\"room_to_change\" value=\"$room_id\">".
                "$w_roz_similar_search : <select name=user_to_search class=input>\n";

    $marrSelect = "";
        for ($i=0; $i<count($users); $i++){
                               $data = explode("\t", $users[$i]);
                        $name = $data[0];
                                $marrSelect .= "<option value=\"".$data[0]."\">".$name."</option>\n";
        }
        $html_to_out .= $marrSelect;
        $html_to_out .= "</select>&nbsp;&nbsp;$w_roz_who:<input type=text class=input name=user_to_search2><br>\n";
    $html_to_out .= $w_roz_similar_ref.":<br>";
    $html_to_out .= "<input type=checkbox value=1 name=check_sim_ip checked> IP;<br>";
    $html_to_out .= "<input type=checkbox value=1 name=check_sim_hash checked> $w_roz_browser_id;<br>";
    $html_to_out .= "<input type=checkbox value=1 name=check_sim_email> E-Mail;<br>";
    $html_to_out .= "<input type=checkbox value=1 name=check_sim_cookie> Cookie;<br>";
    $html_to_out .= "<input type=checkbox value=1 name=check_sim_pass_hash> $w_password.<br>";

        $html_to_out .=  "<input type=\"submit\" value=\"OK\" class=\"input_button\">".
                                          "</form>";
    break;
    case "do_similar":
    if (!($current_user->user_class & ADM_BAN)) {
                        $error_text = "$w_no_admin_rights";
                        include($file_path."designes/".$design."/error_page.php");
                       exit;
        }
    set_variable("user_to_search");
    set_variable("user_to_search2");

    if(trim($user_to_search2) != "") $user_to_search = $user_to_search2;

    $html_to_out = "";

    set_variable("check_sim_ip");
    set_variable("check_sim_hash");
    set_variable("check_sim_email");
    set_variable("check_sim_pass_hash");
    set_variable("check_sim_cookie");

    $u_ids = array();
    $u_names = array();

    ob_start();
    include($file_path."designes/".$design."/common_body_start.php");

    include($ld_engine_path."users_search.php");

        if (count($u_ids)) {

            for($i = 0; $i < count($u_ids); $i++) {
                    if(strnatcasecmp($u_names[$i], $user_to_search) == 0){
                   //user found, ok, now we can do anything else
                    $user_reg_id = $u_ids[$i];
                    $user_nick         = $u_names[$i];

                    $orig_user = $is_regist;
                    $is_regist = $user_reg_id;
                    include("inc_user_class.php");
                    include($ld_engine_path."users_get_object.php");
                    $is_regist = $orig_user;

                    //fill the current_user
                    $similar_IP                         = $current_user->IP;
                                    $similar_browser_hash        = $current_user->browser_hash;
                                    $similar_email                         = $current_user->email;
                                        $similar_pass_hash                = $current_user->password;
                    $similar_cookie                        = $current_user->cookie_hash;
                    //

                    //online users
                    // obtainig ip and browser hash of the original user
                    include($engine_path."users_get_list.php");
                                   for ($j=0; $j<count($users); $j++){
                                                $data = explode("\t", $users[$j]);
                                                if(strnatcasecmp($user_nick, $data[USER_NICKNAME]) == 0) {
                                $user_s_ip                                    = $data[USER_IP];
                            $user_browserhash       = $data[USER_BROWSERHASH];
                            break;
                        }
                                        }

                    if(isset($users_sim_ip)) unset($users_sim_ip);
                    if(isset($users_sim_hash)) unset($users_sim_hash);
                    if(isset($users_sim_ip_hash)) unset($users_sim_ip_hash);

                    $users_sim_ip                 = array();
                    $users_sim_hash         = array();
                    $users_sim_ip_hash        = array();

                    for ($j=0; $j<count($users); $j++){
                                                $data = explode("\t", $users[$j]);
                                                if($data[USER_IP] == $user_s_ip) {
                                                        $users_sim_ip[] = $data[USER_NICKNAME];
                        }
                                                   if($data[USER_BROWSERHASH] == $user_browserhash) {
                                                        $users_sim_hash[] = $data[USER_NICKNAME];
                        }

                                                   if($data[USER_BROWSERHASH] == $user_browserhash and $data[USER_IP] == $user_s_ip) {
                                                        $users_sim_ip_hash[] = $data[USER_NICKNAME];
                        }
                                        }
                    echo "<h4>$w_roz_similar - $w_roz_similar_online:</h4>";
                    echo "<h5>$user_nick ($user_s_ip, $user_browserhash)</h5>";
                    echo "<h6>$w_roz_similar_hash_ip (".count($users_sim_ip_hash)."):</h6>";

                    for($j = 0; $j < count($users_sim_ip_hash); $j++) {
                       echo $users_sim_ip_hash[$j]."<br>";
                    }

                    echo "<h6>$w_roz_similar_hash (".count($users_sim_hash)."):</h6>";
                    for($j = 0; $j < count($users_sim_hash); $j++) {
                       echo $users_sim_hash[$j]."<br>";
                    }

                    echo "<h6>$w_roz_similar_ip (".count($users_sim_ip)."):</h6>";
                    for($j = 0; $j < count($users_sim_ip); $j++) {
                       echo $users_sim_ip[$j]."<br>";
                    }

                    //end of online users
                    break;
                    //end of
                }
            }


            if($user_nick == "") {
                    $error_text = $w_search_no_found;
                    $error_text = str_replace("~", "<b>!!!".$user_to_search."</b>", $error_text);
                                include($file_path."designes/".$design."/error_page.php");
                                exit;
            }
            else {

                  if($check_sim_ip or $check_sim_hash or $check_sim_email or  $check_sim_pass_hash or  $check_sim_cookie) {
                       ob_flush();
                       echo "<h4>$w_roz_userdb</h4>";
                       echo "<table width=90%><tr><td>Searching:</td><td align=LEFT>";
                       include($ld_engine_path."users_similar.php");
                       echo "</td></tr></table>";

                        for ($j=0; $j<count($similar_rez); $j++){
                            $html_to_out .=  $similar_rez[$j]."<br>";
                        }
                            }
            }
            ob_end_flush();

    }
    else {
                    $error_text = $w_search_no_found;
            $error_text = str_replace("~", "<b>".$user_to_search."</b>", $error_text);
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
    }

    break;
        case "ban":
                if (!($current_user->user_class & ADM_BAN)) {
                        $error_text = "$w_no_admin_rights";
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
        //DD starts
                $html_to_out = "<form method=\"post\" action=\"admin_work.php\">".
                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                "<input type=\"hidden\" name=\"op\" value=\"do_announce\">".
                "<input type=\"hidden\" name=\"room_to_change\" value=\"$room_id\">".
                "$w_roz_announce: <input type=\"text\" name=\"announce\" class=\"input\" size=50>&nbsp;".
                "<input type=\"submit\" value=\"OK\" class=\"input_button\">".
                "</form>";
                //DD ends

                $html_to_out .=  $w_select_nick.": <form method=\"post\" action=\"admin_work.php\">".
                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                "<input type=\"hidden\" name=\"op\" value=\"do_ban\">".
                                                "<table border=\"0\">";
                $allowed_to_view_ip = $current_user->user_class & ADM_VIEW_IP;
                if(count($room_ids)>1){
                        for($kk=0;$kk<count($room_ids);$kk++){
                                $html_to_out .= "<tr><td colspan=\"2\">$w_in_room <b>".$rooms[$room_ids[$kk]]["title"]."</b></td></tr>\n";
                                for ($i=0; $i<count($users); $i++){
                                        $data = explode("\t", $users[$i]);
                                        if ($data[10] == $room_ids[$kk]) {
                                                if(intval($data[USER_INIVISIBLE]) == 1 and (intval($data[USER_CLASS]) & ADM_BAN_MODERATORS) and !(intval($cu_array[USER_CLASS]) & ADM_BAN_MODERATORS)) continue;
                                                $name = $data[0];
                                                $ip = ($allowed_to_view_ip) ? ", ".str_replace("\n","",$data[7]): "";
                                                $html_to_out .= "<tr><td><input type=\"radio\" name=\"toBan\" value=\"$name\"></td><td>$name$ip</td></tr>\n";
                                        }
                                }
                        }
                }else{
                        for ($i=0; $i<count($users); $i++){
                                $data = explode("\t", $users[$i]);
                                $name = $data[0];
                                $ip = ($allowed_to_view_ip) ? ", ".str_replace("\n","",$data[7]): "";
                                $html_to_out .= "<tr><td><input type=\"radio\" name=\"toBan\" value=\"$name\"></td><td>$name$ip</td></tr>\n";
                        }
                }
                $html_to_out .= "</table><br>".$w_admin_action.": <select name=\"action\" class=\"input\">".
                                                "<option value=1>$w_admin_alert</option>".
                        "<option value=$a_silence_id>$w_roz_silence</option>".
                                                "<option value=2>$w_admin_kill</option>";
                if ($current_user->user_class & ADM_IP_BAN) $html_to_out .= "<option value=3>$w_admin_ip_kill</option>";
                if ($current_user->user_class & ADM_BAN_BY_BROWSERHASH) $html_to_out .= "<option value=4>$w_admin_browserhash_kill</option>";
                if ($current_user->user_class & ADM_BAN_BY_SUBNET) $html_to_out .= "<option value=5>$w_admin_subnet_kill</option>";
                $html_to_out .= "</select><br>".$w_kill_time.": <select name=\"kill_time\" class=\"input\">";
                for ($i=0;$i<count($sw_times);$i++)
                        $html_to_out .= "<option value=\"$i\">".$w_times[$i]["name"]."</option>";
                $html_to_out .= "</select><br>".$w_admin_reason.": <input type=\"text\" name=\"cause\" class=\"input\">".
                                                "<br><input type=\"submit\" value=\"$w_admin_ban\" class=\"input_button\">".
                                                "</form>";
        break;//end of ban
    case "do_announce":
    set_variable("announce");
           if (!($current_user->user_class & ADM_BAN)) {
         $error_text = "$w_no_admin_rights";
         include($file_path."designes/".$design."/error_page.php");
         exit;
   }
   set_variable("room_to_change");
   $room_to_change = intval($room_to_change);
   if(strlen(trim($announce)) > 0) {
       $announce = trim($announce);
       $announce = addURLS($announce);

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
                           MESG_BODY=>"<span class=ha><font color=\"$def_color\"><b>".$announce."</b></font></span>");

      $MsgToPass = $sw_roz_announce_stat;
      $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);

      $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$room_id,
                                                                                                        MESG_FROM=>$sw_usr_adm_link,
                                                                                                        MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>$sw_usr_adm_link,
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>0,
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

   include($engine_path."messages_put.php");
   header("location: admin_work.php?session=$session&op=ban");
   exit;
    }
    else {
       header("location: admin_work.php?session=$session&op=ban");
      exit;
    }

        break;
        case "do_ban":
                set_variable("cause");
                set_variable("toBan");
                set_variable("kill_time");
                set_variable("action");
                if (!($current_user->user_class & ADM_BAN)) {
                        $error_text = "$w_no_admin_rights";
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }

                $flood_protection = 0;
                $def_color = $registered_colors[$default_color][1];
                if($toBan !="") {
                        //for compatible with old engine
                        $nameToBan = $toBan;
            $sil_author = $current_user->nickname;
                        #list($nameToBan, $ipToBan) = explode(":", $toBan);
                        for ($i=0;$i<count($users);$i++) {
                                //list($ban_nickname, $unused, $unused, $unused, $unused, $ban_user_id, $unused, $ban_user_ip, $unused, $unused, $ban_room,$unused, $ban_canon_n, $unused, $unused, $unused)
                                //        = explode("\t", $users[$i]);
                                $banuser_array = explode("\t", $users[$i], USER_TOTALFIELDS);
                                if (strcmp($banuser_array[USER_NICKNAME], $toBan) == 0) {
                                        $tmp_admin_rights = $current_user->user_class;
                                        if ($banuser_array[USER_REGID]) {
                                                //fake is_regist to load user-data
                                                $is_regist = $banuser_array[USER_REGID];
                                                include($ld_engine_path."users_get_object.php");
                                                //current user now contains user to ban
                                                if (($current_user->user_class > 0) && !($tmp_admin_rights & ADM_BAN_MODERATORS)){
                                                        $error_text = "$w_adm_cannot_ban_mod";
                                                        include($file_path."designes/".$design."/error_page.php");
                                                        exit;
                                                }
                                        }
                                        $cause = htmlspecialchars($cause);

                    //DD silence patch
                    if($action == $a_silence_id) {
                        $old_session = $session;

                        $session = $banuser_array[USER_SESSION];
                        $fields_to_update[0][0] = USER_SILENCE;
                                                $fields_to_update[0][1] = $sw_times[intval($kill_time)]["value"];
                        $fields_to_update[1][0] = USER_SILENCE_START;
                                                $fields_to_update[1][1] = my_time();
                                                include($engine_path."user_din_data_update.php");

                        $session = $old_session;
                                                   $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$banuser_array[USER_ROOM],
                                                                                                        MESG_FROM=>$sw_usr_adm_link,
                                                                                                        MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>$banuser_array[USER_NICKNAME],
                                                                                                        MESG_TOSESSION=>$banuser_array[USER_SESSION],
                                                                                                        MESG_TOID=>$banuser_array[USER_REGID],
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", $sw_times[intval($kill_time)]["value"]/60, $sw_roz_silence_msg)."</font>");

                                       $MsgToPass = $sw_roz_silenced_adm;
                                                       $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
                                                       $MsgToPass = str_replace("#", $sw_times[intval($kill_time)]["value"]/60, $MsgToPass);
                                                   $MsgToPass = str_replace("*", $sil_author, $MsgToPass);

                                                            $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$banuser_array[USER_ROOM],
                                                                                                        MESG_FROM=>$sw_usr_adm_link,
                                                                                                        MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>$sw_usr_adm_link,
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>0,
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

                                        //to user's private log
                                                                            WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
                                                                            // to moder's private log
                                                                            WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");

                          break;
                    }
                    //end DD silence patch

                                        if ($action == 1) {
                                                $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$banuser_array[USER_ROOM],
                                                                                                        MESG_FROM=>"",
                                                                                                        MESG_FROMWOTAGS=>"",
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>"",
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>"",
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("#",$cause,str_replace("*", $cu_array[USER_NICKNAME],str_replace("~", $toBan,$sw_alert_text)))."</font>");

                        $MsgToPass = $sw_roz_warning_stat;
                                            $MsgToPass = str_replace("*", $cu_array[USER_NICKNAME], $MsgToPass);
                                                  $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);

                                       $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$banuser_array[USER_ROOM],
                                                                                                        MESG_FROM=>$sw_usr_adm_link,
                                                                                                        MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>$sw_usr_adm_link,
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>0,
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");
                       //to user's private log
                                            WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
                                            // to moder's private log
                                            WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");
                                        }
                                        $to_ban = array();
                                        if ($action >1) {
                                                $to_ban[0] = "un|".$banuser_array[USER_CANONNICK]."\t".$sil_author."\t".$cause;;
                                                $to_ban[1] = "ch|".$banuser_array[USER_COOKIE]."\t".$sil_author."\t".$cause;;
                        $LBanType = "COOKIE";
                                                $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$banuser_array[USER_ROOM],
                                                                                                        MESG_FROM=>"",
                                                                                                        MESG_FROMWOTAGS=>"",
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>"",
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>"",
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("$",$sw_times[$kill_time]["name"],str_replace("#",$cause,str_replace("*", $cu_array[USER_NICKNAME],str_replace("~", $toBan,$sw_kill_text))))."</font>");

                                                if ($logging_ban) {
                                                        include_once($data_path."engine/files/log_message.php");
                                                        log_ban($cu_array[USER_NICKNAME],
                                                                        $banuser_array[USER_CANONNICK],
                                                                        $banuser_array[USER_IP],
                                                                        $banuser_array[USER_ROOM], $cause);
                                                }
                                                if ($action == 3 && ($tmp_admin_rights & ADM_IP_BAN)) {
                                                        $LBanType = "IP";
                                                        $to_ban[] = "ip|".$banuser_array[USER_IP]."\t".$sil_author."\t".$cause;;}
                                                if ($action == 4 && ($tmp_admin_rights & ADM_BAN_BY_BROWSERHASH)) {
                                                        $LBanType = "HASH";
                                                        $to_ban[] = "bh|".$banuser_array[USER_BROWSERHASH]."\t".$sil_author."\t".$cause;
                                                        //not good, but I don't know a better way
                                                        $to_ban[] = "ip|".$banuser_array[USER_IP]."\t".$sil_author."\t".$cause;
                                                }
                                                if ($action == 5 && ($tmp_admin_rights & ADM_BAN_BY_SUBNET)){
                                                                $LBanType = "NETWORK";
                                                                $to_ban[] = "sn|".substr($banuser_array[USER_IP], 0 , strrpos($banuser_array[USER_IP],"."))."\t".$sil_author."\t".$cause; ;
                            }
                                                if (count($to_ban)>0) {
                                                     $MsgToPass = $sw_roz_ban_adm;
                                                     $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
                                                     $MsgToPass = str_replace("#", $sw_times[intval($kill_time)]["value"]/60, $MsgToPass);
                                                     $MsgToPass = str_replace("*", $cu_array[USER_NICKNAME], $MsgToPass);
                                                     $MsgToPass = str_replace("@", $LBanType, $MsgToPass);

                                                     $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$banuser_array[USER_ROOM],
                                                                                                        MESG_FROM=>$sw_usr_adm_link,
                                                                                                        MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>$sw_usr_adm_link,
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>0,
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

                                                    include($engine_path."messages_put.php");
                                                        include($ld_engine_path."admin.php");
                            //to user's private log
                                                    WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
                                                    // to moder's private log
                                                    WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");
                                                }
                                        }
                                        break;
                                }
                        }
                        include($engine_path."messages_put.php");
                }
                header("location: admin_work.php?session=$session&op=ban");
                exit;
        break;//end of do ban
        case "do_marriage":
            if (!($current_user->user_class & ADM_EDIT_USERS) and !($current_user->custom_class & CST_PRIEST)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
        $PriestName = $current_user->nickname;
                set_variable("marrWho");
                set_variable("marrWith");

        if($marrWho != "" and $marrWith != "" and $marrWho != $marrWith) {
                   $marrWho         = intval(trim($marrWho));
                   $marrWith         = intval(trim($marrWith));

                   //previously married?
                   $is_regist = $marrWho;
                   include("inc_user_class.php");
                   include($ld_engine_path."users_get_object.php");

                   if($current_user->married_with != "") {
                           $error_text = "<b>".$current_user->nickname."</b> already has been married with <b>".$current_user->married_with."</b>!";
                                include($file_path."designes/".$design."/error_page.php");
                                exit;
                   }
                   if(!$current_user->registered) {
                           $error_text = "<div align=center>$w_roz_not_in_club</div>";
                           include($file_path."designes/".$design."/error_page.php");
                           exit;
                   }
                   if($current_user->credits < $tarrifs["marry"]) {
                           $error_text = "<div align=center><b>$current_user->nickname:</b> $w_no_money</div>";
                           include($file_path."designes/".$design."/error_page.php");
                           exit;
                   }

                   $JustMarried1 = $current_user->nickname;
                   $is_regist = $marrWith;
                   include($ld_engine_path."users_get_object.php");

                   if($current_user->married_with != "") {
                           $error_text = "<b>".$current_user->nickname."</b> already has been married with <b>".$current_user->married_with."</b>!";
                            include($file_path."designes/".$design."/error_page.php");
                            exit;
                   }
                   if(!$current_user->registered) {
                           $error_text = "<div align=center>$w_roz_not_in_club</div>";
                           include($file_path."designes/".$design."/error_page.php");
                           exit;
                   }
                   if($current_user->credits < $tarrifs["marry"]) {
                           $error_text = "<div align=center><b>$current_user->nickname:</b> $w_no_money</div>";
                           include($file_path."designes/".$design."/error_page.php");
                           exit;
                   }


                   $JustMarried2 = $current_user->nickname;

                   $is_regist = $marrWho;
                   include($ld_engine_path."users_get_object.php");
                   $current_user->married_with = $JustMarried2;
                   $current_user->credits -= $tarrifs["marry"];
                   include($ld_engine_path."user_info_update.php");

                   $is_regist = $marrWith;
                   include($ld_engine_path."users_get_object.php");
                   $current_user->married_with = $JustMarried1;
                   $current_user->credits -= $tarrifs["marry"];
                   include($ld_engine_path."user_info_update.php");

                   $html_to_out .= $info_message;
                   $MsgToPass = $sw_roz_just_married;
                   $MsgToPass = str_replace("~", $JustMarried1, $MsgToPass);
                   $MsgToPass = str_replace("#", $JustMarried2, $MsgToPass);

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
                                               MESG_BODY=>"<span class=ha><font color=\"$def_color\"><b>".$MsgToPass."</b></font></span>");

                                               $MsgToPass = $sw_roz_just_married_adm;
                                               $MsgToPass = str_replace("~", $JustMarried1, $MsgToPass);
                                               $MsgToPass = str_replace("#", $JustMarried2, $MsgToPass);
                                               $MsgToPass = str_replace("*", $PriestName, $MsgToPass);

                   $messages_to_show[] = array(MESG_TIME=>my_time(),
                                               MESG_ROOM=>$banuser_array[USER_ROOM],
                                               MESG_FROM=>$sw_usr_adm_link,
                                               MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                               MESG_FROMSESSION=>"",
                                               MESG_FROMID=>0,
                                               MESG_TO=>$sw_usr_adm_link,
                                               MESG_TOSESSION=>"",
                                               MESG_TOID=>0,
                                               MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

                              include($engine_path."messages_put.php");
                              //to user's private log
                              WriteToUserLog($MsgToPass, $marrWho, "");
                              WriteToUserLog($MsgToPass, $marrWith, "");
                              // to moder's private log
                              WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");

        }
        else {
                  $error_text = "Users not found in database or You are trying to marriage the same person";
                  include($file_path."designes/".$design."/error_page.php");
                  exit;
        }

    break;
    case "marry":
        if (!($current_user->user_class & ADM_EDIT_USERS) and !($current_user->custom_class & CST_PRIEST)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
        }
    $html_to_out = "<form method=\"post\" action=\"admin_work.php\">".
                                        "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                        "<input type=\"hidden\" name=\"op\" value=\"do_marriage\">\n".
                    "<b>$w_roz_marry_pan:</b><br>".
                    "$w_roz_marry_who : <select name=marrWho class=input>\n";
    $marrSelect = "";
        for ($i=0; $i<count($users); $i++){
                               $data = explode("\t", $users[$i]);
                        if($data[USER_ROOM] ==  $room_id) {
                        if(intval($data[USER_INVISIBLE]) and ($current_user->custom_class & CST_PRIEST)) {}
                    else {
                            $name = $data[0];
                            $marrSelect .= "<option value=\"".$data[USER_REGID]."\">".$name."</option>\n";
                    }
                }
        }
        $html_to_out .= $marrSelect;
        $html_to_out .= "</select>\n";
        $html_to_out .= "$w_roz_marry_with : <select name=marrWith class=input>\n";
        $html_to_out .= $marrSelect;
        $html_to_out .= "</select>\n";
        $html_to_out .= "<input type=submit value=\"OK\" class=input_button>";
        $html_to_out .= "</form>";
        $html_to_out .= "<form method=\"post\" action=\"admin_work.php\">".
                                        "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                        "<input type=\"hidden\" name=\"op\" value=\"do_un_marriage\">\n".
                                        "<b>$w_roz_unmarry_pan:</b><br>".
                                        "$w_roz_marry_who : <select name=marrWho class=input>\n";
           $html_to_out .= $marrSelect;
           $html_to_out .= "</select>\n";
    $html_to_out .= "<input type=submit value=\"OK\" class=input_button>";
    $html_to_out .= "</form>";
    break;

    case "do_un_marriage":
    set_variable("marrWho");
    if (!($current_user->user_class & ADM_EDIT_USERS) and !($current_user->custom_class & CST_PRIEST)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
        }

    $html_to_out = "";

    $PriestName = $current_user->nickname;

    $is_regist = $marrWho;
    include("inc_user_class.php");
    include($ld_engine_path."users_get_object.php");
    if($current_user->married_with == "") {
                   $error_text = "<b>".$current_user->nickname."</b> is not married yet!";
                include($file_path."designes/".$design."/error_page.php");
                exit;
    }
    if(!$current_user->registered) {
            $error_text = "<div align=center>$w_roz_not_in_club</div>";
            include($file_path."designes/".$design."/error_page.php");
            exit;
    }


   if($current_user->credits < $tarrifs["umarry"]) {
            $error_text = "<div align=center><b>$current_user->nickname:</b> $w_no_money</div>";
            include($file_path."designes/".$design."/error_page.php");
            exit;
   }

    $user_to_search = $current_user->married_with;
    $Nick1 = $current_user->nickname;
        //unmarrying first one
    $current_user->married_with = "";
    $current_user->credits -= $tarrifs["umarry"];
    include($ld_engine_path."user_info_update.php");

    // try to locate and unmarry the second user
    $u_ids = array();
    include($ld_engine_path."users_search.php");
        $html_to_out = "";
        if (count($u_ids)) {
            $IsFound = 0;
            for($j = 0; $j < count($u_ids); $j++) {
                    if(strnatcasecmp(trim($u_names[$j]), $user_to_search) == 0){

                    $is_regist = $u_ids[$j];
                    include("inc_user_class.php");
                    include($ld_engine_path."users_get_object.php");

                   if(!$current_user->registered) {
                     $error_text = "<div align=center>$w_roz_not_in_club</div>";
                     include($file_path."designes/".$design."/error_page.php");
                     exit;
                   }

                   if($current_user->credits < $tarrifs["unmarry"]) {
                     $error_text = "<div align=center><b>$current_user->nickname:</b> $w_no_money</div>";
                     include($file_path."designes/".$design."/error_page.php");
                     exit;
                   }


                   $Nick2 = $current_user->nickname;
                   $current_user->married_with = "";
                   $current_user->credits -= $tarrifs["umarry"];
                   include($ld_engine_path."user_info_update.php");

                   $MsgToPass = $sw_roz_no_married;
                   $MsgToPass = str_replace("~", $Nick1, $MsgToPass);
                   $MsgToPass = str_replace("#", $Nick2, $MsgToPass);

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
                                               MESG_BODY=>"<span class=ha><font color=\"$def_color\"><b>".$MsgToPass."</b></font></span>");

                   $MsgToPass = $sw_roz_no_married_adm;
                   $MsgToPass = str_replace("~", $Nick1, $MsgToPass);
                   $MsgToPass = str_replace("#", $Nick2, $MsgToPass);
                   $MsgToPass = str_replace("*", $PriestName, $MsgToPass);

                   $messages_to_show[] = array(MESG_TIME=>my_time(),
                                               MESG_ROOM=>$banuser_array[USER_ROOM],
                                               MESG_FROM=>$sw_usr_adm_link,
                                               MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                               MESG_FROMSESSION=>"",
                                               MESG_FROMID=>0,
                                               MESG_TO=>$sw_usr_adm_link,
                                               MESG_TOSESSION=>"",
                                               MESG_TOID=>0,
                                               MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

                   include($engine_path."messages_put.php");
                   //to user's private log
                   WriteToUserLog($MsgToPass, $marrWho, "");
                   WriteToUserLog($MsgToPass, $is_regist, "");
                   // to moder's private log
                   WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");

                   $IsFound = 1;
                   break;
                }
            }
            if(!$IsFound)  $html_to_out .= str_replace("~","&quot;<b>".$user_to_search."</b>&quot;",$w_search_no_found);


        } else        $html_to_out .= str_replace("~","&quot;<b>".$user_to_search."</b>&quot;",$w_search_no_found);

    $html_to_out = $MsgToPass;

    break;


        case "unban":
                if (!($current_user->user_class & ADM_UN_BAN)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $html_to_out = "<p align=center><b>".UCFirst($w_adm_banned_now)."</b></p><table align=CENTER border=\"1\"><tr><td align=center><b>$w_adm_nick_or_ip</b></td><td align=center><b>$w_roz_moderator</b></td><td align=center><b>".UCFirst($w_admin_reason)."</b></td><td align=center colspan=2><b>$w_adm_ban_until</b></td></tr>";
                include_once($ld_engine_path."ban_check.php");
                $banned_users = get_all_bans();
                for ($i=0;$i<count($banned_users);$i++) {

                if($banned_users[$i]["moder"] == "") $banned_users[$i]["moder"] = "&nbsp;";
                   if($banned_users[$i]["cause"] == "") $banned_users[$i]["cause"] = "&nbsp;";

                        $html_to_out .= "<tr><td>".$banned_users[$i]["who"]."</td><td>".$banned_users[$i]["moder"]."</td><td>".$banned_users[$i]["cause"]."</td><td>".date("d M y H:i:s",intval(intval($banned_users[$i]["until"])+ $time_offset*3600))."</td><td><a href=\"admin_work.php?session=$session&op=do_unban&to_unban=".urlencode($banned_users[$i]["who"])."\">$w_adm_unban</td></tr>";
                }
                $html_to_out .="</table>";
        break;
        case "do_unban":
                if (!($current_user->user_class & ADM_UN_BAN)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                include_once($ld_engine_path."ban_check.php");
                set_variable("to_unban");
                unban(urldecode($to_unban));
                if ($logging_ban) {include_once($data_path."engine/files/log_message.php"); log_unban($user_name, $to_unban);}
                header("location: admin_work.php?session=$session&op=unban");
                exit;
        break;
        case "topic":
                if (!($current_user->user_class & ADM_CHANGE_TOPIC)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $html_to_out =  "<form method=\"post\" action=\"admin_work.php\">".
                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                "<input type=\"hidden\" name=\"op\" value=\"change_topic\">".
                                                "<input type=\"hidden\" name=\"room_to_change\" value=\"$room_id\">".
                                                "$w_topic: <input type=\"text\" name=\"topic\" value=\"".str_replace("\"","&quot;",$rooms[$room_id]["topic"])."\" class=\"input\"><br>".
                                                "<input type=\"submit\" value=\"$w_update\" class=\"input_button\"></form>";
        break;
        case "change_topic":
                if (!($current_user->user_class & ADM_CHANGE_TOPIC)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("room_to_change");
                $room_to_change = intval($room_to_change);
                set_variable("topic");
                $room_to_change = intval($room_to_change);
                include_once($ld_engine_path."rooms_operations.php");
                $new_topic = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$topic))));
                $new_topic = addURLS($new_topic);
                change_topic($room_to_change, $new_topic);
                $messages_to_show = array();
                $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                        MESG_ROOM=>$room_to_change,
                                                                        MESG_FROM=>"",
                                                                        MESG_FROMWOTAGS=>"",
                                                                        MESG_FROMSESSION=>"",
                                                                        MESG_FROMID=>0,
                                                                        MESG_TO=>"",
                                                                        MESG_TOSESSION=>"",
                                                                        MESG_TOID=>"",
                                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("#",$new_topic,str_replace("*", "", $sw_set_topic_text))."</font>");

                include($engine_path."messages_put.php");
                header("location: admin_work.php?session=$session&op=topic");
                exit;
        break;
        case "rooms":
                if (!($current_user->user_class & ADM_CREATE_ROOMS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $html_to_out =         $w_list_of_rooms.":<table border=\"1\">".
                                                "<tr><td>$w_room_name</td><td>$w_topic</td><td></td></tr>";

                for ($i=0;$i<count($room_ids);$i++) {
                        $html_to_out .= "<tr><td>".$rooms[$room_ids[$i]]["title"]."</td><td>".$rooms[$room_ids[$i]]["topic"]."</td>".
                                                        "<td><a href=\"admin_work.php?session=$session&op=edit_room&room_to_change=".$room_ids[$i]."\" class=\"jsnavi\">$w_edit</a> | ".
                                                        "<a href=\"admin_work.php?session=$session&op=delete_room&room_to_change=".$room_ids[$i]."\" class=\"jsnavi\">$w_delete</a></td></tr>";
                }
                $html_to_out .= "</table><br><br><a href=\"admin_work.php?session=$session&op=add_room_form\" class=\"jsnavi\">$w_adm_add_room</a>";
        break;
        case "delete_room":
                if (!($current_user->user_class & ADM_CREATE_ROOMS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("room_to_change");
                $room_to_change = intval($room_to_change);
                include_once($ld_engine_path."rooms_operations.php");
                room_delete($room_to_change);
                header("location: admin_work.php?session=$session&op=rooms");
                exit;
        break;


        case "add_room_form":
                if (!($current_user->user_class & ADM_CREATE_ROOMS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $html_to_out = "<form method=\"post\" action=\"admin_work.php\">".
                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                "<input type=\"hidden\" name=\"op\" value=\"add_room\">".
                                                "<table border=\"0\">".
                                                "<tr><td>$w_room_name:</td><td><input type=\"text\" name=\"room_name\" class=\"input\" size=\"20\"></td></tr>".
                                                "<tr><td>$w_topic:</td><td><input type=\"text\" name=\"room_topic\" class=\"input\" size=\"50\"></td></tr>".
                                                "<tr><td>$w_adm_room_design:</td><td><select name=\"room_design\" class=\"input\">";
                $handle = opendir($file_path."/designes/");
                while (false !== ($tmp_file = readdir($handle)))
                        if ($tmp_file!="." and $tmp_file != "..")
                                $all_designes[] = $tmp_file;
                closedir($handle);
                $html_to_out .= "<option value=\"\">None</option>\n";
                for ($des_i = 0; $des_i<count($all_designes);$des_i++)
                        $html_to_out .= "<option value=\"".$all_designes[$des_i]."\">".$all_designes[$des_i]."</option>\n";

                $html_to_out .= "</select></td></tr>".
                                                "<tr><td>$w_bot_name:</td><td><input type=\"text\" name=\"room_bot\" value=\"".str_replace("\"","&quot;",$w_rob_name)."\" class=\"input\" size=\"20\"></td></tr>".
                                                "<tr><td colspan=\"2\"><input type=\"submit\" value=\"$w_update\" class=\"input_button\"></td></tr>".
                                                "</table></form>";
        break;
        case "edit_room":
                set_variable("room_to_change");
                $room_to_change = intval($room_to_change);
                if (!($current_user->user_class & ADM_CREATE_ROOMS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $html_to_out = "";
                for ($i=0;$i<count($room_ids);$i++)
                        if ($room_ids[$i] == $room_to_change) {
                                $html_to_out .= "<form method=\"post\" action=\"admin_work.php\">".
                                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                                "<input type=\"hidden\" name=\"op\" value=\"update_room\">".
                                                                "<input type=\"hidden\" name=\"room_to_change\" value=\"".$room_ids[$i]."\">".
                                                                "<table border=\"0\">".
                                                                "<tr><td>$w_room_name:</td><td><input type=\"text\" name=\"room_name\" value=\"".str_replace("\"","&quot;",$rooms[$room_ids[$i]]["title"])."\" class=\"input\" size=\"20\"></td></tr>".
                                                                "<tr><td>$w_topic:</td><td><input type=\"text\" name=\"room_topic\" value=\"".str_replace("\"","&quot;",$rooms[$room_ids[$i]]["topic"])."\" class=\"input\" size=\"50\"></td></tr>".
                                                                "<tr><td>$w_adm_room_design:</td><td><select name=\"room_design\" class=\"input\">";
                                $handle = opendir($file_path."/designes/");
                                while (false !== ($tmp_file = readdir($handle)))
                                        if ($tmp_file!="." and $tmp_file != "..")
                                                $all_designes[] = $tmp_file;
                                closedir($handle);
                                $html_to_out .= "<option value=\"\">None</option>\n";
                                for ($des_i = 0; $des_i<count($all_designes);$des_i++) {
                                        $html_to_out .= "<option value=\"".$all_designes[$des_i]."\"";
                                        if ($all_designes[$des_i] == $rooms[$room_ids[$i]]["design"]) $html_to_out .= " selected";
                                        $html_to_out .= ">".$all_designes[$des_i]."</option>\n";
                                }

                                $html_to_out .= "</select></td></tr>".
                                                                "<tr><td>$w_bot_name:</td><td><input type=\"text\" name=\"room_bot\" value=\"".str_replace("\"","&quot;",$rooms[$room_ids[$i]]["bot"])."\" class=\"input\" size=\"20\"></td></tr>".
                                                                "<tr><td colspan=\"2\"><input type=\"submit\" value=\"$w_update\" class=\"input_button\"></td></tr>".
                                                                "</table></form>";
                                break;
                        }
        break;

    case "common":
    if (!($current_user->user_class & ADM_BAN)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
        $html_to_out =  "<form action=\"admin_work.php\" method=POST>".
                               "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                        "<input type=\"hidden\" name=\"op\" value=\"view_common_log\">".
                                    "$w_roz_who: <input type=text class=\"input\" name=txtCommonNick width=20 size=20> =&gt; <select class=\"input\" name=selCommonDate>\n";

        if (is_dir($data_path."logs")) {
           $dl=opendir($data_path."logs");
                   if ($dl) {
                       while($name = readdir($dl)) {
                                   if (!is_dir($name)) {
                    if(strpos($name, "private") === FALSE and strnatcasecmp(trim($name), "to_remove") != 0 )
                    {
                     if(strpos($name, "pvt") === FALSE) {
                             $name_to_see = eregi_replace(".log", "", $name);
                             $html_to_out .= "<option value=\"$name\">$name_to_see</option>";
                         }
                    }
                               }
                           }
               closedir($dl);
       }
        }

    $html_to_out .= "</select><br>";

    $html_to_out .= "$w_roz_room_for: <select name=selRoomFor class=input>\n";
    for($j=0; $j < count($room_ids); $j++)
                   $html_to_out .= "<option value=\"room $j\">".$rooms[$j]["title"]."</option>";
    $html_to_out .= "</select><br>";


    $html_to_out .= "<b>$w_roz_from:</b><br>";

    $html_to_out .= "$w_roz_hours: <select class=\"input\" name=selHoursStart>";
    for($j=0; $j < 24; $j++) $html_to_out .= "<option value=\"$j\">$j</option>";
    $html_to_out .= "</select>";

    $html_to_out .= " $w_roz_minutes: <select class=\"input\" name=selMinutesStart>";
    for($j=0; $j < 60; $j++) $html_to_out .= "<option value=\"$j\">$j</option>";
    $html_to_out .= "</select>";

    $html_to_out .= " $w_roz_seconds: <select class=\"input\" name=selSecondsStart>";
    for($j=0; $j < 60; $j++) $html_to_out .= "<option value=\"$j\">$j</option>";
    $html_to_out .= "</select><br><b>$w_roz_till:</b><br>";

    $html_to_out .= "$w_roz_hours: <select class=\"input\" name=selHoursEnd>";
    for($j=0; $j < 24; $j++) $html_to_out .= "<option value=\"$j\">$j</option>";
    $html_to_out .= "</select>";

    $html_to_out .= " $w_roz_minutes: <select class=\"input\" name=selMinutesEnd>";
    for($j=0; $j < 60; $j++) $html_to_out .= "<option value=\"$j\">$j</option>";
    $html_to_out .= "</select>";

    $html_to_out .= " $w_roz_seconds: <select class=\"input\" name=selSecondsEnd>";
    for($j=0; $j < 60; $j++) $html_to_out .= "<option value=\"$j\">$j</option>";
    $html_to_out .= "</select><br>";

    $html_to_out .= " <input class=\"input_button\" type=submit value=OK></form>";
    break;

    case "view_common_log":
    if (!($current_user->user_class & ADM_BAN)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
        }
    set_variable("selCommonDate");

     //possible hijack fixup
    if(!eregi("^[0-9]{4}-[0-9]{2}-[0-9]{2}\.log$", $selCommonDate)) exit;

    set_variable("txtCommonNick");

    set_variable("selRoomFor");

    set_variable("selHoursStart");
    set_variable("selMinutesStart");
    set_variable("selSecondsStart");

    $selHoursStart                 = intval(trim($selHoursStart));
    $selMinutesStart         = intval(trim($selMinutesStart));
    $selSecondsStart         = intval(trim($selSecondsStart));

    set_variable("selHoursEnd");
    set_variable("selMinutesEnd");
    set_variable("selSecondsEnd");

    $selHoursEnd       = intval(trim($selHoursEnd));
    $selMinutesEnd     = intval(trim($selMinutesEnd));
    $selSecondsEnd     = intval(trim($selSecondsEnd));

   $html_to_out =  "";
   $StartDate   = mktime($selHoursStart, $selMinutesStart, $selSecondsStart);
   $EndDate     = mktime($selHoursEnd, $selMinutesEnd, $selSecondsEnd);

    if(isset($MsgLog)) unset($MsgLog);
        $fp = fopen($data_path."logs/".$selCommonDate, "rb");
    if (!$fp)  $html_to_out .= "Could not $selCommonDate for reading. Please, check permissions";

    if (!flock($fp, LOCK_EX))
                trigger_error("Could not LOCK $selCommonDate. Do you use Win 95/98/Me?", E_USER_WARNING);

    fseek($fp,0);

    if($EndDate < $StartDate) {
       $EndDate = mktime(23, 59, 59);
    }

        while($ttt=fgets($fp, 16384)) {
                if (strlen($ttt)<7) { $html_to_out .= "invalid ttt!"; continue; }
                $MsgLog = explode("\t",trim($ttt));
        if(count($MsgLog) < 5) { $html_to_out .= "invalid parameter-list!"; continue; }
        $TimeToTest = trim(strip_tags($MsgLog[0]));
        $NickToTest = trim(strip_tags($MsgLog[3]));
        $RoomToTest = trim(strip_tags($MsgLog[4]));

        if($RoomToTest != $selRoomFor) continue;

        if($EndDate > $StartDate) {
                if(isset($TimeVal)) unset($TimeVal);
            $TimeVal = explode(":", $TimeToTest);
            if(count($TimeVal) < 3) continue;
            $MsgTime = mktime($TimeVal[0], $TimeVal[1], $TimeVal[2]);

            if($MsgTime < $StartDate or $MsgTime > $EndDate) continue;

            if(strlen(trim($txtCommonNick)) > 0) {
                      if(strnatcasecmp($NickToTest, $txtCommonNick) == 0) {
                                $html_to_out .= "<small>".$MsgLog[0]."</small> <b><u>".$MsgLog[3]."</u>:</b> ".$MsgLog[5]."<br>";
                    continue;
                        }
                continue;
            }
            else {
              $html_to_out .= "<small>".$MsgLog[0]."</small> <b><u>".$MsgLog[3]."</u>:</b> ".$MsgLog[5]."<br>";
              continue;
            }
         //end of date check
        }

           if(strlen(trim($txtCommonNick)) > 0) {
                      if(strnatcasecmp($NickToTest, $txtCommonNick) == 0) {
                                $html_to_out .= "<small>".$MsgLog[0]."</small> <b><u>".$MsgLog[3]."</u>:</b> ".$MsgLog[5]."<br>";
                    continue;
                        }
                else continue;
            }
            else {
              $html_to_out .= "<small>".$MsgLog[0]."</small> <b><u>".$MsgLog[3]."</u>:</b> ".$MsgLog[5]."<br>";
            }

        }
        flock($fp, LOCK_UN);
        fclose($fp);
    break;

    case "private":
    if (!($current_user->user_class & ADM_VIEW_PRIVATE)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
        $html_to_out =  "<form action=\"admin_work.php\" method=POST>".
                               "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                        "<input type=\"hidden\" name=\"op\" value=\"view_log\">".
                                    "$w_roz_who: <input type=text class=\"input\" name=txtNick width=20 size=20> =&gt; <select class=\"input\" name=selDate>\n";

        if (is_dir($data_path."logs")) {
           $dl=opendir($data_path."logs");
                   if ($dl) {
                       while($name = readdir($dl)) {
                                   if (!is_dir($name)) {
                    if(strpos($name, "private") === FALSE) {}
                    else {
                     $name_to_see = eregi_replace("-private.log", "", $name);
                     $html_to_out .= "<option value=\"$name\">$name_to_see</option>";
                     }
                               }
                           }
               closedir($dl);
       }
        }
    $html_to_out .= "</select> <input class=\"input_button\" type=submit value=OK></form>";
    break;

    case "view_log":
    if (!($current_user->user_class & ADM_VIEW_PRIVATE)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
    set_variable("selDate");
    set_variable("txtNick");

    //possible hijack fixup
    if(!eregi("^[0-9]{4}-[0-9]{2}-[0-9]{2}-private\.log$", $selDate)) exit;


    $txtNick = trim($txtNick);

    $fp_log = fopen($data_path."logs/pvt-access", "a+b");
    if($fp_log) {
            fwrite($fp_log, date("d.m.Y H:i:s", time()+$time_offset)."\t".$current_user->nickname."\t".trim(strip_tags($selDate))."\t".trim(strip_tags($txtNick))."\n");
        fclose($fp);
    }

    if(isset($MsgLog)) unset($MsgLog);
        $fp = fopen($data_path."logs/".$selDate, "rb");
    if (!$fp) trigger_error("Could not lock ".$data_path."logs/".$selDate." for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                trigger_error("Could not LOCK messages.dat. Do you use Win 95/98/Me?", E_USER_WARNING);

    fseek($fp,0);

        while($ttt=fgets($fp, 16384)) {
                if (strlen($ttt)<7) continue;
                $MsgLog = explode("\t",trim($ttt));
        if(count($MsgLog) < 5) continue;
        $NickToTest = trim(strip_tags($MsgLog[2]));
        if(strnatcasecmp($NickToTest, $txtNick) == 0 or strnatcasecmp(trim($MsgLog[3]), $txtNick) == 0) {
                $html_to_out .= "<small>".$MsgLog[0]."</small>[<b>".$MsgLog[2]." -&gt; ".$MsgLog[3]."</b>] ".$MsgLog[4]."<br>";
        }
        }
        flock($fp, LOCK_UN);
        fclose($fp);

    break;

        case "update_room":
                if (!($current_user->user_class & ADM_CREATE_ROOMS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("room_to_change");
                $room_to_change = intval($room_to_change);
                set_variable("room_name");
                $room_name = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_name))));
                set_variable("room_topic");
                $room_topic = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_topic))));
                set_variable("room_design");
                $room_design = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_design))));
                set_variable("room_bot");
                $room_bot = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_bot))));
                include_once($ld_engine_path."rooms_operations.php");
                room_update($room_to_change,$room_name,$room_topic,$room_design,$room_bot);
                header("location: admin_work.php?session=$session&op=edit_room&room_to_change=$room_to_change");
                exit;
        break;
        case "add_room":
                if (!($current_user->user_class & ADM_CREATE_ROOMS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("room_name");
                $room_name = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_name))));
                set_variable("room_topic");
                $room_topic = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_topic))));
                set_variable("room_design");
                $room_design = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_design))));
                set_variable("room_bot");
                $room_bot = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$room_bot))));
                include_once($ld_engine_path."rooms_operations.php");
                room_add($room_name,$room_topic,$room_design,$room_bot);
                header("location: admin_work.php?session=$session&op=rooms");
                exit;
        break;
        case "user":
                if (!($current_user->user_class & ADM_EDIT_USERS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $html_to_out = "<form method=\"post\" action=\"admin_work.php\">".
                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                "<input type=\"hidden\" name=\"op\" value=\"user_search\">".
                                                "<table border=\"0\"><tr><td valign=\"middle\">".
                                                $w_enter_nick.": <input type=\"text\" name=\"user_to_search\" class=\"input\"> </td>".
                                                "<td valign=\"middle\">".
                                                "<input type=\"submit\" value=\"".$w_search_button."\" class=\"input_button\">".
                                                "</td></tr></table>\n</form>";
        break;
        case "user_search":
                if (!($current_user->user_class & ADM_EDIT_USERS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("user_to_search");
                $u_ids = array();
                include($ld_engine_path."users_search.php");
                $html_to_out = "";
                if (count($u_ids)) {
                        $html_to_out .= "$w_search_results<br>";
                        for ($i=0;$i<count($u_ids);$i++)
                                $html_to_out .= "<a href=\"admin_work.php?op=user_edit&user_id=".$u_ids[$i]."&session=$session\">".htmlspecialchars($u_names[$i])."</a>".
                                                                " -- <a href=\"javascript:if (confirm('".$w_sure_user_delete."')) {document.location.href='admin_work.php?op=user_delete&user_id=".$u_ids[$i]."&session=$session';}\">".$w_delete."</a><br>\n";
                } else
                        $html_to_out .= str_replace("~","&quot;<b>".$user_to_search."</b>&quot;",$w_search_no_found);
        break;
        case "user_delete":
                if (!($current_user->user_class & ADM_EDIT_USERS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("user_id");
                $user_ids = array();
                $user_ids[0] = $user_id;
                include_once($ld_engine_path."admin_work.php");
                users_delete($user_ids);
                for ($i=0;$i<count($user_ids);$i++) {
                        @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".big.jpg");
                        @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".big.jpeg");
                        @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".big.gif");
                        @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".jpg");
                        @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".gif");
                        @unlink($data_path."board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".msg");
                        @unlink($data_path."user-board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".contrib");
                        @unlink($data_path."private-board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".msg");
                        @unlink($data_path."user-privates/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".msg");
                        @unlink($data_path."moder-board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".mod");
                        @unlink($data_path."user-viewed/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".view");
                        @unlink($data_path."users/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".user");
                }
                $html_to_out = $w_user_deleted;
        break;

        case "user_edit":
                if (!($current_user->user_class & ADM_EDIT_USERS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                if($current_user->user_class & ADM_EDIT_USERS) $IsEditReiting = true;
                else $IsEditReiting = false;

                set_variable("user_id");
                $is_regist = $user_id;
                include("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");
                $html_to_out = "<form method=\"post\" action=\"admin_work.php\">".
                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                "<input type=\"hidden\" name=\"op\" value=\"user_update\">".
                                                "<input type=\"hidden\" name=\"user_id\" value=\"$user_id\">".
                                                "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">".
                                        "<tr><td>$w_select_nick:</td><td align=left><input type=\"text\" size=\"15\" name=\"nickname\" value=\"".$current_user->nickname."\" class=\"input\"></td></tr>".
                                        ($IsEditReiting ? "<tr><td>$w_roz_reiting:</td><td align=left><input type=\"text\" size=\"15\" name=\"reiting\" value=\"".$current_user->points."\" class=\"input\"></td></tr>":"").
                        "<tr><td>$w_roz_marr_man_yes:</td><td align=left><input type=\"text\" size=\"15\" name=\"married_with\" value=\"".$current_user->married_with."\" class=\"input\"></td></tr>".
                        "<tr><td>$w_roz_damneds:</td><td align=left><input type=\"text\" size=\"15\" name=\"damneds\" value=\"".$current_user->damneds."\" class=\"input\"></td></tr>".
                        "<tr><td>$w_roz_reward:</td><td align=left><input type=\"text\" size=\"15\" name=\"rewards\" value=\"".$current_user->rewards."\" class=\"input\"></td></tr>".
                        "<tr><td colspan=\"2\"><input type=checkbox name=\"showGroup1\"".
                                                (($current_user->show_group_1 == 1) ? " checked":"").
                                                "> ".$w_show_data."<table border=\"0\">".
                                                "<tr><td>".$w_surname.": </td><td><input type=\"text\" size=\"15\" name=\"surname\" value=\"".$current_user->surname."\" class=\"input\"></td></tr>".
                                                "<tr><td>".$w_name.": </td><td><input type=\"text\" size=\"15\" name=\"firstname\" value=\"".$current_user->firstname."\" class=\"input\"></td></tr>";

                        //
                     if($current_user->registered) {

                        $html_to_out .= "<tr><td>".$w_full_access.": </td><td><input type=checkbox value=\"1\" name=\"full_access\" class=\"input\"";
                        if($current_user->is_member) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "<tr><td>".$w_membered_by.": </td><td><b>".$current_user->membered_by."</b></td></tr>";

                        if($IsEditReiting) {
                          $html_to_out .= "<tr><td>".$w_roz_priest.": </td><td><input type=checkbox value=\"1\" name=\"is_priest\" class=\"input\" ";
                            if($current_user->user_class == 0) {
                               if($current_user->custom_class & CST_PRIEST) $html_to_out .= "checked";
                               $html_to_out .= "></td></tr>";
                            }
                          $html_to_out .= "<tr><td>$w_money:</td><td align=left><input type=\"text\" size=\"15\" name=\"money\" value=\"".$current_user->credits."\" class=\"input\"></td></tr>";
                        }
                     }
                         // ip
                         $html_to_out .= "<tr><td colspan=\"2\">&nbsp;$w_limit_by_ip_only: <input size=\"80\" type=text class=\"input\" name=\"limit_ips\" value=\"".$current_user->limit_ips."\"></td></tr>";
                         $html_to_out .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
                        //clans
                        $html_to_out .= "<tr><td>".$w_roz_clan.": </td><td><select name=\"clan\" class=\"input\"><option value=\"\">--</option>";
                        include($ld_engine_path."clans_get_list.php");

                        for ($i=0; $i<count($clans_list); $i++) {
                                                         $html_to_out .= "<option value=\"".$clans_list[$i]["id"]."\"";
                                                        if ($clans_list[$i]["id"] == $current_user->clan_id) $html_to_out .= " selected";
                                                        $html_to_out .= ">".$clans_list[$i]["name"]."</option>\n";
                                                }
                        $html_to_out .= "</select></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_status.": </td><td><input type=\"text\" size=\"15\" name=\"clan_status\" value=\"".$current_user->clan_status."\" class=\"input\"></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_add_user.": </td><td><input type=checkbox name=\"clan_add_user\" class=\"input\"";
                        if($current_user->clan_class & CLN_ADDUSER) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_delete_user.": </td><td><input type=checkbox name=\"clan_delete_user\" class=\"input\"";
                        if($current_user->clan_class & CLN_DELETEUSER) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_edit.": </td><td><input type=checkbox name=\"clan_edit\" class=\"input\"";
                        if($current_user->clan_class & CLN_EDIT) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_edit_user.": </td><td><input type=checkbox name=\"clan_edit_user\" class=\"input\"";
                        if($current_user->clan_class & CLN_EDITUSER) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                                                $html_to_out .= "<tr><td>".$w_birthday.": </td><td><select name=\"day\" class=\"input\"><option value=\"\">--</option>";
                for ($i=1; $i<32; $i++) {
                        $html_to_out .= "<option";
                        if ($i == $current_user->b_day) $html_to_out .= " selected";
                        $html_to_out .= ">$i\n";
                }
                $html_to_out .= "</select> / <select name=\"month\" class=\"input\"><option value=\"\">--</option>";
                for ($i=1; $i<13; $i++) {
                        $html_to_out .= "<option";
                        if ($i == $current_user->b_month) $html_to_out .= " selected";
                        $html_to_out .= ">$i\n";
                }
                $html_to_out .= "</select> / <select name=\"year\" class=\"input\"><option value=\"\">--</option>";
                for ($i=1950; $i<2001; $i++) {
                        $html_to_out .= "<option";
                        if ($i == $current_user->b_year) $html_to_out .= " selected";
                        $html_to_out .= ">$i\n";
                }
                $html_to_out .= "</select></td></tr>".
                                                "<tr><td>".$w_city.": </td><td><input type=\"text\" name=\"city\" size=\"10\" value=\"".$current_user->city."\" class=\"input\"></td></tr>".
                                                "<tr><td>".$w_gender.": </td><td><select name=\"sex\" class=\"input\">";
                $sex = $current_user->sex;
                $html_to_out .= "<option value=0";
                if ($sex == 0) $html_to_out .= " selected";
                $html_to_out .= ">$w_unknown</option>\n<option value=1";
                if ($sex == 1) $html_to_out .= " selected";
                $html_to_out .= ">$w_male</option>\n<option value=2";
                if ($sex == 2) $html_to_out .= " selected";
                $html_to_out .= ">$w_female</option>\n</select></td></tr>\n";
                //$html_to_out .= "<tr><td>$w_small_photo: </td><td>";
                $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.gif";
                if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
                if ($pic_name == "") {
                        $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpg";
                        if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
                }
                   if ($pic_name == "") {
                        $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpeg";
                        if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
                }
                $big_picture = $pic_name;
                $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".gif";
                if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
                if ($pic_name == "") {
                        $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".jpg";
                        if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
                }
                $small_picture = $pic_name;
        /*
                if ($small_picture != "")
                        $html_to_out .= "<img src=\"photos/$small_picture\"><br><input type=\"checkbox\" name=\"sm_del\">$w_check_for_delete<br>";
        */
                $html_to_out .= "<tr><td>$w_big_photo: </td><td>";
                if ($big_picture != "") {
                        $html_to_out .= "<img src=\"photos/$big_picture\"><br>";
                        $html_to_out .= "<input type=\"checkbox\" name=\"big_del\">$w_check_for_delete<br>";
                }
                $html_to_out .= "<tr><td>".$w_addit_info.": </td><td>".
                                                "<textarea name=\"comments\" rows=\"10\" cols=\"30\" class=\"input\">".str_replace("<br>","\n",$current_user->about).
                                                "</textarea></td></tr>".
                                                "</table></td></tr>".
                                                "<tr><td>&nbsp;</td></tr>".
                                                "<tr><td>".
                                                "<input type=checkbox name=\"showGroup2\"".
                                                (($current_user->show_group_2 == 1) ? " checked":"").
                                                "> ".$w_show_data."<br>".
                                                "<table border=\"0\">".
                                                "<tr><td>".$w_email.": </td><td><input type=\"text\" size=\"15\" name=\"email\" value=\"".$current_user->email."\" class=\"input\"></td></tr>".
                                                "<tr><td>".$w_homepage.": </td><td><input type=\"text\" size=\"15\" name=\"url\" value=\"".$current_user->url."\" class=\"input\"></td></tr>".
                                                "<tr><td>".$w_icq.": </td><td><input type=\"text\" size=\"15\" name=\"icquin\" value=\"".$current_user->icquin."\" class=\"input\"></td></tr>".
                                                "</table></td></tr><tr><td>&nbsp;</td></tr>";
                if($web_indicator){
                        $html_to_out .= "<tr><td><table><tr><td>".
                                                        "<input type=\"checkbox\" name=\"enable_web_indicator\"".
                                                        (($current_user->enable_web_indicator) ? " checked":"").
                                                        "> ".$w_web_indicator."</td></tr>".
                                                        "<tr><td>".str_replace("~","<img src=\"".$chat_url."user_status.php?".$is_regist."\" border=\"0\" alt=\"chat status\">",$w_web_indicator_code)."</td></tr>".
                                                        "<tr><td align=\"center\"><textarea class=\"input\" rows=\"5\" cols=\"40\">".
                                                        "<a href=\"".$chat_url."\" target=\"_blank\"><img src=\"".$chat_url."user_status.php?".$is_regist."\" border=\"0\" alt=\"chat status\"></a>".
                                                        "</textarea></td></tr>".
                                                        "</table></td></tr>".
                                                        "<tr><td>&nbsp;</td></tr>";
                }
                $html_to_out .= "<tr><td><table border=\"0\">".
                                                "<tr><td colspan=\"2\">".$w_if_wanna_change_password."</td></tr>".
                                                "<tr><td>".$w_new_password.": </td><td><input type=\"password\" name=\"passwd1\" class=\"input\"></td></tr>".
                                                "<tr><td>".$w_confirm_password.": </td><td><input type=\"password\" name=\"passwd2\" class=\"input\"></td></tr>".
                                                "</table></td></tr>".
                                                "<tr><td>&nbsp;</td></tr>".
                                                "</table><br>".
                                                "<input type=\"submit\" value=\"".$w_update."\" class=\"input_button\">".
                                                "</form>";
        break;
        case "user_update":
                if (!($current_user->user_class & ADM_EDIT_USERS)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }

                if($current_user->user_class & ADM_EDIT_USERS) $IsEditReiting = true;
                else $IsEditReiting = false;

                set_variable("user_id");
        set_variable("nickname");
        $nickname = trim($nickname);
                $is_regist = $user_id;
                set_variable("surname");
        set_variable("married_with");
                set_variable("firstname");
                set_variable("city");
                set_variable("comments");
                set_variable("email");
                set_variable("url");
                set_variable("icquin");
                set_variable("day");
                set_variable("month");
                set_variable("year");
                set_variable("sex");
                set_variable("passwd1");
                set_variable("passwd2");
                set_variable("showGroup1");
                set_variable("showGroup2");
                set_variable("sm_del");
                set_variable("reiting");
                set_variable("big_del");
                set_variable("enable_web_indicator");
                set_variable("damneds");
                set_variable("rewards");
                set_variable("full_access");

                set_variable("limit_ips");



              $full_access = intval($full_access);


         $reiting = intval($reiting);
         if($reiting < 0) $reiting = 0;

        //clan-specific
        set_variable("clan");
        include($ld_engine_path."clans_get_list.php");

        $clan = intval(trim($clan));
        $IsClanFound = false;

        for($i = 0; $i < count($clans_list); $i++) {
                if($clans_list[$i]["id"] == $clan) { $IsClanFound = true; break;}
        }
        if(!$IsClanFound) $clan = 0;

        set_variable("clan_add_user");
        set_variable("clan_delete_user");
        set_variable("clan_edit");
        set_variable("clan_edit_user");
        set_variable("clan_status");

        if ($clan == 0) {
           $clan_add_user                 = "";
           $clan_delete_user         = "";
           $clan_edit                         = "";
           $clan_edit_user                 = "";
        }

        $clan_level = 0;
        $info_message = "";
                $html_to_out = "";

        if(strnatcasecmp(trim($clan_add_user), "on") == 0) $clan_level += pow(2, 0);
        if(strnatcasecmp(trim($clan_delete_user), "on") == 0) $clan_level += pow(2,1);
        if(strnatcasecmp(trim($clan_edit), "on") == 0) $clan_level += pow(2,2);
        if(strnatcasecmp(trim($clan_edit_user), "on") == 0) $clan_level += pow(2,3);

                include("inc_user_class.php");
        $is_regist = $user_id;
        include($ld_engine_path."users_get_object.php");

       $current_clan = new Clan;
        //clan
        if($clan != $current_user->clan_id) {
           if($current_user->clan_id > 0) {
                           $is_regist_clan = intval($current_user->clan_id);
                           include($ld_engine_path."clan_get_object.php");

                           $IsUserFound = false;

                           for($i=0; $i < count($current_clan->members); $i++) {
                                 if($current_clan->members[$i]["id"] == $is_regist or strnatcasecmp($current_clan->members[$i]["nick"], $current_user->nickname) == 0) {
                                   $IsUserFound = true;
                                   break;
                                 }
                           }

                   if($IsUserFound) {
                                   $current_clan->members = array_trim($current_clan->members, $i);
                                   include($ld_engine_path."clan_update_object.php");
                   }
          }
        }

        include($ld_engine_path."users_get_object.php");

        $current_user->clan_id                 = $clan;

        $current_user->clan_class         = $clan_level;
        $current_user->clan_status         = htmlspecialchars(trim($clan_status));

        $is_regist_clan = $current_user->clan_id;

        if($is_regist_clan > 0) {
           include($ld_engine_path."clan_get_object.php");

           $IsUserFound = false;

           for($i=0; $i < count($current_clan->members); $i++) {
                        if($current_clan->members[$i]["id"] == $is_regist) {
                   $IsUserFound = true;
                   break;
                }
           }
           if(!$IsUserFound) {
               $idx = count($current_clan->members);
                   $current_clan->members[$idx]["id"]   = $is_regist;
                   $current_clan->members[$idx]["nick"] = $current_user->nickname;
               include($ld_engine_path."clan_update_object.php");
           }
        }

        if($nickname != "" and !ereg("[^".$nick_available_chars."]", $nickname)) {
                $current_user->nickname = $nickname;
        }
        $current_user->married_with = htmlspecialchars($married_with);

        $damneds = intval(trim($damneds));
        if($damneds < 0) $damneds = 0;
        if($damneds > 3) $damneds = 3;

        if($current_user->damneds != $damneds) {
                     $fp = fopen($data_path."users/damneds.log", "a+b");
                     if($fp) {
                        fwrite($fp,date("H:i:s d-m-Y", my_time())."\t".$user_name."\t".$current_user->nickname."\t".$damneds."\n");
                        fclose($fp);
                     }
        }
        $current_user->damneds = $damneds;

        $rewards = intval(trim($rewards));
        if($rewards < 0) $rewards = 0;
        if($rewards > 27) $rewards = 27;

        if($current_user->rewards != $rewards) {
                     $fp = fopen($data_path."users/rewards.log", "a+b");
                     if($fp) {
                        fwrite($fp,date("H:i:s d-m-Y", my_time())."\t".$user_name."\t".$current_user->nickname."\t".$rewards."\n");
                        fclose($fp);
                     }
        }
        $current_user->rewards = $rewards;

                $passwd1 = str_replace("\t","",$passwd1);
                if ( (!$passwd1) or ($passwd1 != $passwd2) )
                        $html_to_out .= "$w_pas_not_changed.<br>\n";
                else {
                        $html_to_out .= "$w_pas_changed.<br>\n";

                        if($md5_salt != "") {
                           $passSalt = md5($passwd1);
                           $passSalt = $md5_salt.$passSalt;
                           $passSalt = md5($passSalt);
                           $passwd1  = $passSalt;
                        }
                        else $passwd1 = md5($passwd1);

                        $current_user->password = $passwd1;
                }
                if ($showGroup1 == "on") $current_user->show_group_1=1; else $current_user->show_group_1 = 0;
                if ($showGroup2 == "on") $current_user->show_group_2=1; else $current_user->show_group_2=0;
                $current_user->enable_web_indicator = ($enable_web_indicator == "on") ? 1 : 0;

                $pic_name = "" . $is_regist . ".big.gif";
                if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
                if ($pic_name == "")
                {
                        $pic_name = "" . $is_regist . ".big.jpg";
                        if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
                }
        if ($pic_name == "")
                {
                        $pic_name = "" . $is_regist . ".big.jpeg";
                        if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
                }
                $big_picture = $pic_name;
                $pic_name = "" . $is_regist . ".gif";
                if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
                if ($pic_name == "")
                {
                        $pic_name = "". $is_regist . ".jpg";
                        if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
                }
                $small_picture = $pic_name;


                if ($big_del == "on") {
                        if ($big_picture != "") {
                            @unlink($file_path."photos/".floor($is_regist/2000)."/".$big_picture);
                            $current_user->photo_voted   =  array();
                            $current_user->photo_voted_mark  =  array();
                            $current_user->photo_reiting =  0;
                        }
                }
                if ($sm_del == "on") {
                        if ($small_picture != "") @unlink($file_path."photos/".floor($is_regist/2000)."/".$small_picture);
                }

                $current_user->surname = htmlspecialchars($surname);
                $current_user->firstname = htmlspecialchars($firstname);
                $current_user->city = htmlspecialchars($city);
                $current_user->about = htmlspecialchars($comments);
                $current_user->about = str_replace("\n","<br>", $current_user->about);
                $current_user->email = htmlspecialchars($email);
                $current_user->url = htmlspecialchars($url);
                $current_user->icquin = htmlspecialchars($icquin);
                $current_user->b_day = intval($day);
                $current_user->b_month = intval($month);
                $current_user->b_year = intval($year);
                $current_user->sex = intval($sex);
                if($IsEditReiting) $current_user->points = intval($reiting);

                $limit_ips = trim($limit_ips);
                $arr_ips   = explode(";", $limit_ips);
                $good_ips  = array();

                for($i = 0; $i < count($arr_ips); $i++) {
                   $test_ip = $arr_ips[$i];
                   $test_ip = trim($test_ip);
                   //may be subnetwork, delimited with :
                   $test_ip_arr = explode(":", $test_ip);

                   $good_ip = "";
                   if(eregi("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $test_ip_arr[0])) {
                       $good_ip = $test_ip_arr[0];
                       if(eregi("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $test_ip_arr[1])) $good_ip = $good_ip.":".$test_ip_arr[1];
                   }
                   else $good_ip = "";
                   if($good_ip != "") $good_ips[] = $good_ip;
                }

                $good_ips = array_unique($good_ips);
                $current_user->limit_ips = implode(";", $good_ips);


                if($full_access == 1) {
                  if($current_user->registered) {
                    $current_user->is_member = true;
                    if($current_user->membered_by == "") $current_user->membered_by = $user_name;
                  }
                }
                else {
                  $current_user->is_member = false;
                  $current_user->membered_by = "";
                }

                if($IsEditReiting) {
                  set_variable("money");
                  $money = intval($money);
                  if($money < 0) $money = 0;

                  if($current_user->credits != $money) {
                     $fp = fopen($data_path."users/money.log", "a+b");
                     if($fp) {
                        fwrite($fp,date("H:i:s d-m-Y", my_time())."\t".$user_name."\t".$current_user->nickname."\t".$money."\n");
                        fclose($fp);
                     }
                  }
                  $current_user->credits = $money;

                  set_variable("is_priest");
                  $is_priest = intval($is_priest);
                  if($current_user->user_class == 0) {
                      if($is_priest) $current_user->custom_class = CST_PRIEST;
                      else $current_user->custom_class = 0;
                     }
                }

                $User_UpdatePassword = true;
                include($ld_engine_path."user_info_update.php");

                riseEvent(EVENT_EDT_USER, $current_user->nickname, $current_user->user_class);

                $html_to_out .= $info_message;
        break;
}
include($file_path."designes/".$design."/output_page.php");
?>