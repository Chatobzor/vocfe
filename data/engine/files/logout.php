<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";

/*
function cmp($a, $b) {
        return strcmp(strtoupper($a), strtoupper($b));
} */

$users = array();

$fp = fopen($who_in_chat_file, "r+b");
if (!$fp) trigger_error("Could not open who.dat for writing. Please, check permissions", E_USER_ERROR);

if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK who.dat. Do you use Win 95/98/Me?", E_USER_WARNING);

$exists = 0;
$is_regist = 0;
$cu_array = array_fill(0, USER_TOTALFIELDS-1, "");
$messages_to_show = array();
while ($line = fgets($fp, 16384)) {
        if (strlen($line)<7) continue;
        $user_array  = explode("\t",trim($line), USER_TOTALFIELDS);

        //VOCLOVE
        if ($user_array[USER_SESSION] == $session) {           if(defined('VOCLOVE_PATH')) {              $user_array[USER_VOCLOVE_LOGGED] =  0;           }
           else {
              $user_array[USER_CHAT_LOGGED] =  0;           }
        }

        if ($user_array[USER_SESSION] == $session and !intval($user_array[USER_CHAT_LOGGED]) and !intval($user_array[USER_VOCLOVE_LOGGED])) {
                $user_name = $user_array[USER_NICKNAME];
                $user_array[USER_TIME]=time();
                $exists = 1;
                $is_regist = $user_array[USER_REGID];
                $user_ip = $user_array[USER_IP];
                $room_id = $user_array[USER_ROOM];
                if (!in_array($user_array[USER_SKIN], $designes)) $user_array[USER_SKIN] = $default_design;
                $design = $user_array[USER_SKIN];
                $current_design = $chat_url."designes/".$design."/";

                           $user_array[USER_INVISIBLE] = intval(trim($user_array[USER_INVISIBLE]));
                        if($user_array[USER_INVISIBLE] == 1) $user_invisible = 1;
                        else $user_invisible = 0;

                if ($user_array[USER_LANG] != $language) {
                        if (!in_array($user_array[USER_LANG], $allowed_langs)) $user_array[USER_LANG] = $language;
                        else { include_once($file_path."languages/".$user_array[USER_LANG].".php"); }
                        $user_lang = $user_array[USER_LANG];
                }
                //!!!!it's better to replace this variables to $cu_array[]; in all code.
                // now I left it for compability
                $cu_array = $user_array;
        }
        else {
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
}
if (count($users)) usort($users, "cmp");
else $users = array();
fseek($fp,0);
fwrite($fp,implode("",$users));
ftruncate($fp,ftell($fp));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
?>