<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";

if(!defined("_CMP_")):
define("_CMP_",1);
function cmp($a, $b) {
        return strcmp(strtoupper($a), strtoupper($b));
}
endif;
if (!isset($rooms)) {
        include($ld_engine_path."rooms_get_list.php");
}


unset($messages_to_show);
$messages_to_show = array();
$def_color = $registered_colors[$default_color][1];
$orig_f_p = $flood_protection;
$flood_protection = 1;
$users = array();

$fp = fopen($who_in_chat_file, "r+b");
if (!$fp) trigger_error("Could not open who.dat for writing. Please, check permissions", E_USER_ERROR);
 if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK who.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
$exists = 0;
$is_regist = 0;
$user_name = "";
$ignored_users = array();
$ignored_list = array();
$room_id = 0;
$cu_array = array_fill(0, USER_TOTALFIELDS-1, "");

$user_silenced             = 0;
$user_silenced_start   = 0;

while ($line = fgets($fp, 16384)) {
        if (strlen($line)<7) continue;
        $user_array  = explode("\t",trim($line), USER_TOTALFIELDS);

        if ($user_array[USER_SESSION] == $session) {

                $user_name = $user_array[USER_NICKNAME];
                $user_array[USER_TIME] = my_time();
                $exists = 1;

                $user_array[USER_INVISIBLE] = intval(trim($user_array[USER_INVISIBLE]));
                $user_array[USER_CLANID] = intval(trim($user_array[USER_CLANID]));
                $user_array[USER_CUSTOMCLASS] = intval(trim($user_array[USER_CUSTOMCLASS]));

                if($user_array[USER_INVISIBLE] == 1) $user_invisible = 1;
                else $user_invisible = 0;

                $user_silenced                         = intval(trim($user_array[USER_SILENCE]));
                $user_silenced_start         = intval(trim($user_array[USER_SILENCE_START]));

                $is_regist = $user_array[USER_REGID];
                $tail_num = $user_array[USER_TAILID];
                $user_ip =$user_array[USER_IP];
                $room_id = $user_array[USER_ROOM];
                $user_status = $user_array[USER_STATUS];
                $user_chat_type = $user_array[USER_CHATTYPE];

                //Added by DD
                $user_last_say_time = $user_array[USER_LASTSAYTIME];
                $is_regist_complete = $user_array[USER_REGISTERED];
                $is_member          = $user_array[USER_MEMBER];

                if (!in_array($user_array[USER_SKIN], $designes)) $user_array[USER_SKIN] = $default_design;
                if ($rooms[$room_id]["design"] != "")
                        $user_array[USER_SKIN] = $rooms[$room_id]["design"];
                $design = $user_array[USER_SKIN];
                $current_design = $chat_url."designes/".$design."/";
                $ignored_list = explode(",",$user_array[USER_IGNORLIST]);
                for ($i=0;$i<count($ignored_list);$i++)
                        $ignored_users[strtolower($ignored_list[$i])] = 1;
                $cu_array = $user_array;
                if ($user_array[USER_LANG] != $language) {
                        if (!in_array($user_array[USER_LANG], $allowed_langs)) $user_array[USER_LANG] = $language;
                        else { include_once($file_path."languages/".$user_array[USER_LANG].".php"); }
                        $user_lang = $user_array[USER_LANG];
                }
        }
        if ($user_array[USER_TIME] > time()-$disconnect_time)
                $users[] = implode("\t",$user_array) . "\n";
        else {
                                           $user_array[USER_INVISIBLE] = intval(trim($user_array[USER_INVISIBLE]));

                                        if($user_array[USER_NICKNAME]!="" and $user_array[USER_INVISIBLE] != 1)
                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                MESG_ROOM=>$user_array[USER_ROOM],
                                                                                MESG_FROM=>$rooms[$user_array[USER_ROOM]]["bot"],
                                                                                MESG_FROMWOTAGS=>$rooms[$user_array[USER_ROOM]]["bot"],
                                                                                MESG_FROMSESSION=>"",
                                                                                MESG_FROMID=>0,
                                                                                MESG_TO=>"",
                                                                                MESG_TOSESSION=>"",
                                                                                MESG_TOID=>"",
                                                                                MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", $user_array[USER_NICKNAME], $sw_rob_idle)."</font>");
             }
}
if (count($users)) usort($users, "cmp");
else $users = array();
if (count($messages_to_show)) {
        include($engine_path."messages_put.php");
}
unset($messages_to_show);

fseek($fp,0);
fwrite($fp,implode("",$users));
ftruncate($fp,ftell($fp));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
$flood_protection = $orig_f_p;

?>