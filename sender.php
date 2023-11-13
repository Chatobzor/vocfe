<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");
include($engine_path."rooms_get_list.php");
include($file_path."inc_badwords_filter.php");
include("events.php");

set_variable("mesg");
set_variable("whisper");
set_variable("user_color");
set_variable("update_status");
set_variable("style_b");
set_variable("style_i");
set_variable("style_u");
set_variable("IsPublic");
set_variable("banType");
set_variable("custom_style");
set_variable("act");
set_variable("translit");

define("ADMINZ_PRIVATE", $sw_usr_adm_link);
define("BOYS_PRIVATE", $sw_usr_boys_link);
define("GIRLS_PRIVATE", $sw_usr_girls_link);
define("THEY_PRIVATE", $sw_usr_they_link);
define("ALL_PRIVATE", $sw_usr_all_link);
define("SHAMAN_PRIVATE", $sw_usr_shaman_link);
define("CLAN_PRIVATE", $sw_usr_clan_link);

$messages_to_show = array();
//anti-bot fix: trying to stop automatic submission

if ($REQUEST_METHOD == GET) exit();
if ($_SERVER["REQUEST_METHOD"] == "GET") exit();
if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "GET") exit();
if ($_SERVER["REQUEST_METHOD"] == GET) exit();
if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == GET) exit();
if (!$browser_hash) exit();


// \076 \074 fix
//$mesg = ereg_replace("([\\]|[/])[0-9]{1,3}", "", $mesg);
//$mesg = ereg_replace("([\\]|[/])(x|X)[0-9]{1,3}", "", $mesg);
$mesg = trim(str_replace("**n", "", $mesg));
$mesg = str_replace("[MESSAGE]", "", $mesg);
$mesg = eregi_replace("[a-fA-F0-9]{32}", "", $mesg);

//end of anti-bot fix
if (!$exists) {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}


$test_ref  = $current_design."sender_visible.php?session=".$session;
$test_ref2 = $current_design."sender_visible.php?&opcode=popup&session=".$session;

if($_SERVER['HTTP_REFERER'] != "") {
   if((strpos(strtolower($_SERVER['HTTP_REFERER']), strtolower($test_ref)) === FALSE and strtolower($_SERVER['HTTP_REFERER']) != strtolower($test_ref))
      and (strpos(strtolower($_SERVER['HTTP_REFERER']), strtolower($test_ref2)) === FALSE and strtolower($_SERVER['HTTP_REFERER']) != strtolower($test_ref2))
   ) {
     //bot or hacker.
     exit;
   }
}

//command-bar check
/*******************************/
function my_array_unique($somearray){
   $tmparr = array_unique($somearray);
   $i=0;
   foreach ($tmparr as $v) {
       $newarr[$i] = $v;
       $i++;
   }
   return $newarr;
}
/********************************/

//translit
if($translit and function_exists("translit_".strtolower(trim($cu_array[USER_LANG]))) > 0) $mesg = call_user_func("translit_".strtolower(trim($cu_array[USER_LANG])), $mesg);

$whisper_arr = explode(", ", $whisper);
$whisper          = $whisper_arr[0];
$whisper_arr = my_array_unique($whisper_arr);

if(trim($banType) != "") {
    include("adm_cmd.php");
    //fake needed for design's sender.php
        $mesg = "bla";
        include($file_path."designes/".$design."/sender.php");
    exit;
}
if(trim($act) != "") {

    if($act == "filter_on") {
                $fup_val = 1;
    }
        else $fup_val = 0;

        $fields_to_update[0][0] = USER_FILTER;
        $fields_to_update[0][1] = $fup_val;
        include($engine_path."user_din_data_update.php");

    //fake needed for design's sender.php
        $mesg = "bla";
        include($file_path."designes/".$design."/sender.php");
    exit;
}
//end

//1 apr

if($mesg == "") exit;

$IsCommon = 0;
// $whisper != CLAN_PRIVATE
//multi public adressee fix by DD

$whisper = trim($whisper);

if( $whisper == ALL_PRIVATE           or
    $whisper == CLAN_PRIVATE          or
    $whisper == SHAMAN_PRIVATE        or
    $whisper == BOYS_PRIVATE          or
    $whisper == GIRLS_PRIVATE         or
    $whisper == ADMINZ_PRIVATE        or
    $whisper == THEY_PRIVATE) {

    if($whisper == ALL_PRIVATE and !($cu_array[USER_CLASS] & ADM_VIEW_PRIVATE)) {
         $IsCommon = 1;
         $mesg = implode(", ", $whisper_arr)."> ".$mesg;
         $whisper = "";
    }else if($cu_array[USER_CLASS] < 1) {
          if( $whisper != ADMINZ_PRIVATE and
              $whisper != SHAMAN_PRIVATE and
              $whisper != CLAN_PRIVATE   and
              !($whisper == CLAN_PRIVATE and intval($cu_array[USER_CLANID]) == 0)) {
                    $IsCommon = 1;
                    $mesg = implode(", ", $whisper_arr)."> ".$mesg;
                    $whisper = "";
          }
    }
}
$IsPublic = intval($IsPublic);

//Plugin management
if($IsPublic == 1 and strlen($whisper) > 0) riseEvent(EVENT_DO_PUBLIC_MESSAGE, implode(", ", $whisper_arr), $mesg);
else riseEvent(EVENT_DO_PRIVATE_MESSAGE, $whisper, $mesg);

if($IsPublic == 1 and strlen($whisper) > 0) {
    $mesg = implode(", ", $whisper_arr)."> ".$mesg;
    $whisper = "";
}

//DD silence control
if($user_silenced > 1) {
        if(my_time() < ($user_silenced_start + $user_silenced)) {
                            $flood_protection = 0;
                            $replStr = intval((intval($user_silenced_start) + intval($user_silenced))) - my_time();
                            $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                        MESG_ROOM=>$room_id,
                                                        MESG_FROM=>$sw_usr_adm_link,
                                                        MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                        MESG_FROMSESSION=>"",
                                                        MESG_FROMID=>0,
                                                        MESG_TO=>$user_name,
                                                        MESG_TOSESSION=>$session,
                                                        MESG_TOID=>$is_regist,
                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", $replStr, $w_roz_silence_remind)."</font>");
                                           include($engine_path."messages_put.php");
        exit;
    }
    else {
              $fields_to_update[0][0] = USER_SILENCE;
              $fields_to_update[0][1] = 0;
              $fields_to_update[1][0] = USER_SILENCE_START;
              $fields_to_update[1][1] = 0;
              include($engine_path."user_din_data_update.php");
    }
}
//end of DD silence control
// reiting engine
$whisper = trim($whisper);

$custom_style = intval(trim($custom_style));


if($is_regist and (strlen($whisper) == 0 or $custom_style == 1)) {
    include_once("inc_user_class.php");
    include($ld_engine_path."users_get_object.php");

    $check_type = "common_message";
    include("user_validate.php");

   if(strlen($whisper) == 0 and strlen(trim($mesg)) > 0) {

      if(intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"]) > my_time()) {
         $MsgToPass = $w_user_chaos;
         $MsgToPass = str_replace("~", date("d.m.Y H:i:s",intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"])), $MsgToPass);

         ?>
            <script language="JavaScript" type="text/javascript"> alert('<?php echo htmlspecialchars($MsgToPass);?>');</script>
        <?php
        exit;
     }

     if($is_regist_complete) {
      if(intval($rooms[$room_id]["points"]) and !intval($rooms[$room_id]["jail"])) {
       $user_last_say_time = intval(trim($current_user->last_actiontime));
       if((my_time() - $user_last_say_time) < REITING_TIME_LIMIT*60) {

          if($current_user->points < 0) $current_user->points = $current_user->points*-1;

          if(((my_time() - $user_last_say_time) < REITING_TIME_LIMIT*60) > 0) {
                $current_user->points       = intval($current_user->points);
                $current_user->online_time  = intval($current_user->online_time);

                if(!$current_user->online_time) {
                    if($current_user->points) $current_user->online_time = $current_user->points;
                }

                $pointsToAdd = (my_time() - $user_last_say_time);

                if($current_user->rewards) {
                   if($current_user->rewards == 2) $pointsToAdd = intval($pointsToAdd*1.3);
                   if($current_user->rewards >= 3) $pointsToAdd = intval($pointsToAdd*2);
                }
                else if($current_user->damneds) {
                   if($current_user->damneds == 1) $pointsToAdd = intval($pointsToAdd*0.5);
                   if($current_user->damneds == 2) $pointsToAdd = intval($pointsToAdd*0.3);
                   if($current_user->damneds >= 3) $pointsToAdd = 0;
                }
                $current_user->points      += $pointsToAdd;
                $current_user->online_time += $pointsToAdd;
          }
       }
      }
     }

   }

    if(intval($cu_array[USER_INVISIBLE]) == 0) $current_user->last_actiontime = my_time();

    //include($engine_path."users_get_list.php");
    $MustPay = false;

    if($current_user->reffered_by > 0 and $current_user->ref_payment_done == false ) {
        require("tarrifs.php");
        if($current_user->points >= $tarrifs["ref_bounty_points"]) {
           $current_user->ref_payment_done = true;
           $MustPay = true;
        }

    }

   include($ld_engine_path."user_info_update.php");

   if($MustPay) {
     $old_user       = $current_user;
     $old_user_id    = $is_regist;
     $old_user_name = $user_name;

     $is_regist      = $current_user->reffered_by;
     include($ld_engine_path."users_get_object.php");

     $oldMoney = $current_user->credits;
     $current_user->credits += $tarrifs["ref_bounty"];

     include_once($data_path."engine/files/user_log.php");

     $MsgToPass = $sw_adm_reffered_payment;
     $MsgToPass = str_replace("#", $tarrifs["ref_bounty"], $MsgToPass);
     $MsgToPass = str_replace("~", $old_user->nickname, $MsgToPass);
     $MsgToPass = str_replace("$", $oldMoney, $MsgToPass);
     $MsgToPass = str_replace("%", $current_user->credits, $MsgToPass);

     WriteToUserLog($MsgToPass, $is_regist, "");
     include($ld_engine_path."user_info_update.php");

     $group      = 0;
     $send_to_id = $is_regist;
     $subject    = $sw_adm_reffered_subject;
     $message    = $MsgToPass;
     $user_name  = "";

     include($ld_engine_path."hidden_board_post_message.php");

     $current_user = $old_user;
     $is_regist    = $old_user_id;
     $user_name    = $old_user_name;
   }

    if($custom_style == 1) {
        $style_start = $current_user->style_start;
        $style_end = $current_user->style_end;
        $cpuLen = strlen($mesg) * 10;
    }
}

if (isset($update_status))
        if($update_status!="") {
                $update_status = intval($update_status);
                $fields_to_update[0][0] = 8;
                $fields_to_update[0][1] = $update_status;
                include($engine_path."user_din_data_update.php");
                header("location: session.php?session=$session&".time());
                exit;
        }


//functions
function addFaces($mesg) {
        global $total_pics, $max_images, $chat_url;
        $l = 0;
        $temp = "";
        for ($i = 0; $i < strlen($mesg); $i++) {
                $oi = $i;
                $ok = 1;
                if (($mesg[$i] == ':' || $mesg[$i] == '=')  and !(substr($mesg,$i-3,4)=="&lt;" or substr($mesg,$i-3,4)=="&gt;" or (substr($mesg,$i-5,1)=="&" &&$mesg[$i] == ';' ) )) {
                        $alt = "";
                        $brows = "normal";
                        if ($i >= 4) {
                                if ( substr($mesg,$i-4,4) == '&gt;') {
                                        $brows = "mad";
                                        $alt = "&gt;"; /*Orig: $alt = "]";*/
                                }
                                elseif ( substr($mesg,$i-4,4) == '&lt;') {
                                        $brows = "upset";
                                        $alt = "&lt;"; /*Orig = $alt="[";*/
                                }
                        }
                        $prefix = "";
                        if ($mesg[$i] == ';') {
                                $prefix = "wink-";
                                $alt .= ";";
                        }
                        else { $alt .= ":"; }
                        $i++;
                        if ($mesg[$i] == '^' || $mesg[$i] == '-' || $mesg[$i] == '\'') {
                                $i++;  $alt.="-"; }
                        $mouth = "";
                        if ($mesg[$i] == ')' || $mesg[$i] == 'D' || $mesg[$i] == ']') {
                                $mouth = "smile"; $alt .= ")"; }
                        elseif ($mesg[$i] == '(') {
                                $mouth ="frown"; $alt .= "(";}
                        elseif ($mesg[$i] == '|') {
                                $mouth = "shy"; $alt .= "|";}
                        elseif ($mesg[$i] == 'P' || $mesg[$i] == 'p' || $mesg[$i] == 'Ð' || $mesg[$i] == 'ð') {
                                $mouth = "tongue"; $alt .= "P";}
                        elseif ($mesg[$i] == 'O' || $mesg[$i] == 'o' || $mesg[$i] == 'Î' || $mesg[$i] == 'î') {
                                $mouth = "amazed"; $alt .= "o";}
                        if ($total_pics<$max_images) {
                                if (strlen($mouth) != 0) {
                                        $ok = 0;
                                        $face = $prefix . $mouth . "-" . $brows;
                                        $face = "<img src=\"".$chat_url."faces/$face.gif\" alt=\"$alt\" width=16 height=16>";

                                        /*              $temp[$l] = '\0'; */
                                        if (strcmp($brows, "normal") != 0) {
                                                $l = strlen ($temp) -4;
                                                $temp = substr($temp,0,$l);
                                        }
                                        $temp .= $face;
                                        $total_pics++;
                                        $l = strlen($temp);
                                }
                                else {
                                        $i = $oi;
                                        $ok = 1;
                                }
                        }
                        else {$ok = 1;$i = $oi;$l = strlen($temp);}
                }
                if ($ok == 1) {
                        $temp .= $mesg[$i];
                        $l++;
                }
        }
        return $temp;
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
        return $str2;
}

function check_uppercase($mesg) {
        global $max_cap_letters;
        if ($max_cap_letters) {
                $l = strlen($mesg);
                $t_u = 0;
                for ($i=0;$i<$l;$i++) {
                        $ch = substr($mesg,$i,1);
                        if ($ch == strtoupper($ch) && $ch != strtolower($ch))
                        $t_u++;
                }
                if ($t_u > $max_cap_letters) $mesg = strtolower($mesg);
        }
        return $mesg;
}

function mesg2html($m_text) {
        global $numOfImgPhrases,$mesg,$total_pics,$SmTbl,$max_images;
        //$m_text = addFaces(wordwrap($m_text, 75," ", 1));
        if ( substr_count($mesg,"**n") <7)
                $m_text = str_replace("**n","<br>",$m_text);
        $m_text = str_replace("<br><br>","<br>", $m_text);
        for ($j=0; $j<$numOfImgPhrases; $j++) {
                $total_pics += substr_count($mesg,$SmTbl[$j]["name"]);
                //backward

                 if ($max_images >= $total_pics)
                     $m_text = str_replace($SmTbl[$j]["name"], $SmTbl[$j]["link"], $m_text);
                else $m_text = str_replace($SmTbl[$j]["name"], "", $m_text);
        }
        return $m_text;
}

function cmpLen($a, $b) {
        return strlen($b["name"]) - strlen($a["name"]);
}
//end of functions


if ($user_color=="") {$user_color=$default_color;}
$user_color = intval($user_color);
if (($user_color < 0) or ($user_color >= count($registered_colors))) {$user_color=$default_color;}
SetCookie("c_user_color", $user_color, time() + 2678400);

//additional styles
if($style_b != "") $style_b = "1";
else $style_b = "";

if($style_i != "") $style_i = "1";
else $style_i = "";

if($style_u != "") $style_u = "1";
else $style_u = "";

SetCookie("c_style_b", $style_b, time() + 2678400);
SetCookie("c_style_i", $style_i, time() + 2678400);
SetCookie("c_style_u", $style_u, time() + 2678400);


$error_text = "";
$error = 0;
$total_pics = 0;

if (!isset($mesg)){$mesg = "";}
$mesg = str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ", $mesg)));




if ($mesg !="") {
        $to_nick = "";
        $to_id = 0;
        $to_session = "";
        if ($whisper != "") {
                for($i=0; $i<sizeof($users); $i++) {
                        $data = explode("\t", $users[$i]);
                        if ($data[0] == $whisper) {
                                $to_nick = $data[0];
                                $to_id = $data[5];
                                $to_session = $data[1];
                        }
                }
                        //DD patch
                if ($to_nick == "") {
                        if(strcasecmp(ADMINZ_PRIVATE, $whisper) == 0 or
                           strcasecmp(BOYS_PRIVATE, $whisper) == 0 or
                           strcasecmp(GIRLS_PRIVATE, $whisper) == 0 or
                           strcasecmp(ALL_PRIVATE, $whisper) == 0 or
                           strcasecmp(THEY_PRIVATE, $whisper) == 0 or
                           strcasecmp(SHAMAN_PRIVATE, $whisper) == 0 or
                           (strcasecmp(CLAN_PRIVATE, $whisper) == 0 and $cu_array[USER_CLANID] > 0)) {
                                $to_nick = $whisper;
                                $to_id = 0;
                                $to_session = 0;
                                $error = 0;
                        }
                        else {

                        riseEvent(EVENT_POST_MESSAGE, $whisper, $mesg);

                        if($to_nick == "") {

                              // private posting for offline user
                              $user_to_search = $whisper;
                              $u_ids = array();
                              $u_names = array();
                              include($ld_engine_path."users_search.php");

                              $to_id = 0;

                              if (count($u_ids)) {
                                  for($i = 0; $i < count($u_ids); $i++) {
                                         if(strcasecmp(trim($u_names[$i]), $user_to_search) == 0){
                                               $to_nick = $u_names[$i];
                                               $to_id   = $u_ids[$i];
                                               $to_session = 0;
                                               $error = 0;
                                               break;
                                         }
                                  }

                                  if(!$to_id) {
                                     $error_text .= $w_whisper_out."<br>\n";
                                     $error = 1;
                                  }
                              }
                              else {
                                   $error_text .= $w_whisper_out."<br>\n";
                                   $error = 1;
                              }
                          // end of if($to_nick == "")
                          }

                        }
                }
                //end of DD patch

        }

    if(isset($SmTbl)) unset($SmTbl);
        $converts = file($converts_file);
        $numOfImgPhrases = count($converts);
        for ($i=0;$i<$numOfImgPhrases;$i++) {
            if (strpos($converts[$i],"\t")) {
                list ($temp_imgPhrase,$temp_imgURL) = explode("\t",trim($converts[$i]));
                $SmTbl[] = array("name" => $temp_imgPhrase,
                                 "link" => $temp_imgURL);
                 //backward
                $SmTbl[] = array("name" => substr($temp_imgPhrase, 0, strlen($temp_imgPhrase)-1),
                                 "link" => $temp_imgURL);
            }
       }
       $numOfImgPhrases = count($SmTbl);

       require_once "smiles.php";
       usort($SmTbl, "cmpLen");

         //now it's in inc_common.php
        //if (get_magic_quotes_gpc()) $mesg = stripslashes($mesg);

        if (strlen($mesg)>512) {
                $error_text .= $w_too_long."<br>\n";
                $error = 1;
        }

        if (!$error) {
                $mesg = " ".$mesg;
                $mesg = check_uppercase($mesg);
                $mesg = replace_badwords($mesg);
                 // /074/076 fix

                $mesg = htmlspecialchars($mesg);

                $mesg = addURLS($mesg);
				riseEvent(EVENT_POSTSMILEYS_MESSAGE, $whisper, $mesg);
                $mesg_parts = array();
                $mesg_parts = split("<",$mesg);
                for ($i=0;$i<count($mesg_parts);$i++) {
                        if ($i%2) {
                                list ($m_tag, $m_text) = split(">",$mesg_parts[$i]);
                                $mesg_parts[$i] = $m_tag.">".mesg2html($m_text);
                        }
                        else {
                                $mesg_parts[$i] = mesg2html($mesg_parts[$i]);
                        }
                }
                $mesg = implode("<",$mesg_parts);

                $mesg_prefix = "";
                $mesg_postfix = "";

                if($style_b != "" && $enabled_b_style) {
                        $mesg_prefix .= "<b>";
                        $mesg_postfix = "</b>".$mesg_postfix;
                }
                if($style_i != "" && $enabled_i_style) {
                        $mesg_prefix .= "<i>";
                        $mesg_postfix = "</i>".$mesg_postfix;
                }
                if($style_u != "" && $enabled_u_style) {
                        $mesg_prefix .= "<u>";
                        $mesg_postfix = "</u>".$mesg_postfix;
                }

       /*if($IsCommon) {
                        $mesg_prefix .= "<span class='hs'>";
                        $mesg_postfix = "</span>".$mesg_postfix;
                }
        */

                $t_color = $registered_colors[$user_color][1];
                $def_color = $registered_colors[$default_color][1];

        $mesg = str_replace("/me", "", $mesg);
        $style_start = str_replace("#", $cpuLen, $style_start);
		riseEvent(EVENT_HTML_MESSAGE, $whisper, $mesg);


                if (strpos($mesg, "/me ") == 1)
                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$room_id,
                                                    MESG_FROM=>"",
                                                    MESG_FROMWOTAGS=>$user_name,
                                                    MESG_CLANID=>$cu_array[USER_CLANID],
                                                    MESG_FROMSESSION=>$session,
                                                    MESG_FROMAVATAR=>$cu_array[USER_AVATAR],
                                                    MESG_FROMID=>$is_regist,
                                                    MESG_TO=>"",
                                                    MESG_TOSESSION=>"",
                                                    MESG_TOID=>0,
                                                    MESG_BODY=>"<font color=\"$def_color\">" .$mesg_prefix. $user_name." ". trim(substr($mesg, 5)) .$mesg_postfix. "</font>");
                else
                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$room_id,
                                                    MESG_FROM=>(strlen($cu_array[USER_HTMLNICK])>0)?
                                                        $cu_array[USER_HTMLNICK] :
                                                        (($colorize_nicks) ?
                                                                "<font color=\"$t_color\">$user_name</font>":
                                                                $user_name
                                                        ),
                                                    MESG_FROMWOTAGS=>$user_name,
                                                    MESG_CLANID=>$cu_array[USER_CLANID],
                                                    MESG_FROMSESSION=>$session,
                                                    MESG_FROMAVATAR=>$cu_array[USER_AVATAR],
                                                    MESG_FROMID=>$is_regist,
                                                    MESG_TO=>$to_nick,
                                                    MESG_TOSESSION=>$to_session,
                                                    MESG_TOID=>$to_id,
                                                    MESG_BODY=>"<font color=\"$t_color\">".$mesg_prefix.$style_start.trim($mesg).$style_end.$mesg_postfix."</font>");
                $to_robot = strip_tags($mesg);
                $w_rob_name = $rooms[$room_id]["bot"];
                if (!$whisper) {
                    include($ld_engine_path."robot_get_answers.php");
                    include ("quiz.php");
                }

                if ($ar_rooms[$room_id][ROOM_PREMODER] && $cu_array[USER_CLASS] == 0 ){
                        //waiting for approval from moderators
                        //khm.not sure what to use -- 'main engine' or 'long life data engine'
                        //put it into 'long data' now, because it might cause problems with shm -- i.e. not enough block size
                        //it also has to be stored for a long time
                        include_once($ld_engine_path."premoderation.php");
                        premoder_add($messages_to_show);
                        unset($messages_to_show);
                }else{
                        include($engine_path."messages_put.php");
                        if ($mess_stat == 1 && !$error) {
                            $fp = fopen($data_path."mess_stat.dat", "a+");
                            flock($fp, LOCK_EX);
                            fseek($fp,0);
                            $normal_messages = intval(str_replace("\n","",@fgets($fp,1024)));
                            $private_messages = intval(str_replace("\n","",@fgets($fp,1024)));
                            if ($whisper)$private_messages++;
                                    else $normal_messages++;
                            ftruncate($fp,0);
                            fwrite($fp,$normal_messages."\n".$private_messages);
                            fflush($fp);
                            flock($fp, LOCK_UN);
                            fclose($fp);
                        }
                }
        }
}

$out_users = array();
$who_j = 0;
for ($i=0;$i<count($users);$i++) {
        $user_array = explode("\t",$users[$i]);
        if ($user_array[USER_ROOM] == $cu_array[USER_ROOM]) {
            $out_users[$who_j]["nickname"] = $user_array[USER_NICKNAME];
            $out_users[$who_j]["sex"] = $user_array[USER_GENDER];
            $out_users[$who_j]["small_photo"] = $user_array[USER_AVATAR];
            $out_users[$who_j]["user_id"] = $user_array[USER_REGID];
            $out_users[$who_j]["status"] = $user_array[USER_STATUS];
            $who_j++;
        }
}
$total_users = count($out_users);

include($file_path."designes/".$design."/sender.php");
?>