<?php

if (!defined("_COMMON_")) {
    echo "stop";
    exit;
}
if (!defined("_CMP_")):
    define("_CMP_", 1);
    function cmp($a, $b)
    {
        return strcmp(strtoupper($a), strtoupper($b));
    }
endif;
$users = array();
$users2 = array();

$fp = fopen($who_in_chat_file, "r+b");
if (!$fp) {
    trigger_error("Could not open who.dat for writing. Please, check permissions", E_USER_ERROR);
}

if (!flock($fp, LOCK_EX)) {
    trigger_error("Could not LOCK who.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
}

$exists = 0;
$is_regist = 0;
$j = 0;
$hi = 0;
$from_this_ip = 1;
$canon_view = to_canon_nick($user_name);
$cu_array = array_fill(0, USER_TOTALFIELDS - 1, "");
while ($line = fgets($fp, 16384)) {
    if (strlen($line) < 7) {
        continue;
    }
    $user_array = explode("\t", trim($line), USER_TOTALFIELDS);

    $user_array[USER_INVISIBLE] = intval(trim($user_array[USER_INVISIBLE]));
    if ($user_array[USER_INVISIBLE] == 1) {
        $user_invisible = 1;
    } else {
        $user_invisible = 0;
    }

    if (strcmp($user_array[USER_CANONNICK], $canon_view) == 0) {
        if ($registered_user) {
            if ($current_user->check_browser) {
                if ($user_array[USER_BROWSERHASH] != $browser_hash) {
                    include($file_path."designes/".$design."/common_body_start.php");
                    echo "<p align=center>$w_security_error</p>";
                    include($file_path."designes/".$design."/common_body_end.php");
                    exit;
                }
            }

            if (!$current_user->registered) {
                $error_text = "$w_already_used<br><a href=\"index.php\">$w_try_again</a>";
                include($file_path."designes/".$design."/error_page.php");
                flock($fp, LOCK_UN);
                fclose($fp);
                exit;
            } else {
                $exists = $user_array[USER_REGID];
                $user_array[USER_SESSION] = $session;
                $user_array[USER_TIME] = my_time();
                $cu_array = $user_array;
            }
        } else {
            $user_array[USER_NICKNAME] = $user_name;
            $user_array[USER_SESSION] = $session;
            $user_array[USER_TIME] = my_time();
            $user_array[USER_GENDER] = $sex;
            $user_array[USER_AVATAR] = "";
            #check for small photo
            $tmp_name = "".floor($registered_user / 2000)."/".$registered_user.".gif";
            $tmp_name2 = "".floor($registered_user / 2000)."/".$registered_user.".jpg";
            if (file_exists($file_path."photos/$tmp_name")) {
                $user_array[USER_AVATAR] = $tmp_name;
            } elseif (file_exists($file_path."photos/$tmp_name2")) {
                $user_array[USER_AVATAR] = $tmp_name2;
            }
            $user_array[USER_REGID] = $registered_user;
            $user_array[USER_TAILID] = "0";
            $user_array[USER_IP] = $REMOTE_ADDR;
            $user_array[USER_STATUS] = 0;
            $user_array[USER_LASTSAYTIME] = time();
            $user_array[USER_ROOM] = $room_id;
            $user_array[USER_CANONNICK] = to_canon_nick($user_name);
            $user_array[USER_HTMLNICK] = $htmlnick;
            $user_array[USER_CHATTYPE] = $chat_type;
            $user_array[USER_LANG] = $user_lang;
            $user_array[USER_COOKIE] = $c_hash;
            $user_array[USER_CLASS] = $current_user->user_class;
            $user_array[USER_REDUCETRAFFIC] = ($registered_user) ? $current_user->reduce_traffic : 0;

            $user_array[USER_CLASS] = ($registered_user) ? $current_user->user_class : 0;
            $user_array[USER_CLANID] = ($registered_user) ? $current_user->clan_id : 0;
            $user_array[USER_CUSTOMCLASS] = ($registered_user) ? $current_user->custom_class : 0;

            $user_array[USER_REGISTERED] = ($registered_user) ? intval($current_user->registered) : 0;
            $user_array[USER_MEMBER] = ($registered_user) ? intval($current_user->is_member) : 0;

            //VOCLOVE
            if (defined('VOCLOVE_PATH')) {
                $user_array[USER_VOCLOVE_LOGGED] = 1;
            } else {
                $user_array[USER_CHAT_LOGGED] = 1;
            }

            if ($registered_user) {
                if (intval($current_user->plugin_info["silence_start"]) + intval(
                        $current_user->plugin_info["silence_time"]
                    ) > my_time()) {
                    $user_array[USER_SILENCE] = intval($current_user->plugin_info["silence_time"]);
                    $user_array[USER_SILENCE_START] = intval($current_user->plugin_info["silence_start"]);
                }
                if (intval($current_user->plugin_info["jail_start"]) + intval(
                        $current_user->plugin_info["jail_time"]
                    ) > my_time()) {
                    $user_array[USER_ROOM] = $jail_id;
                }
            }

            if (!in_array($design, $designes)) {
                $design = $default_design;
            }
            $user_array[USER_SKIN] = $design;
            $exists = $registered_user;
            $hi = 0;
            $cu_array = $user_array;
        }
    }
    if ($user_array[USER_TIME] > time() - $disconnect_time) {
        $users2[$j] = implode("\t", $user_array)."\n";
        if ($user_array[USER_IP] == $REMOTE_ADDR && strcmp($user_array[USER_CANONNICK], $canon_view) != 0) {
            $from_this_ip++;
        }
        $j++;
    } else {
        $user_array[USER_INVISIBLE] = intval(trim($user_array[USER_INVISIBLE]));

        if ($user_array[USER_NICKNAME] != "" and $user_array[USER_INVISIBLE] != 1) {
            $messages_to_show[] = array(
                MESG_TIME => my_time(),
                MESG_ROOM => $user_array[USER_ROOM],
                MESG_FROM => $rooms[$user_array[USER_ROOM]]["bot"],
                MESG_FROMWOTAGS => $rooms[$user_array[USER_ROOM]]["bot"],
                MESG_FROMSESSION => "",
                MESG_FROMID => 0,
                MESG_TO => "",
                MESG_TOSESSION => "",
                MESG_TOID => "",
                MESG_BODY => "<font color=\"$def_color\">".str_replace(
                        "~",
                        $user_array[USER_NICKNAME],
                        $sw_rob_idle
                    )."</font>"
            );
        }
    }
}
if (!is_array($users2)) {
    $users2 = array();
}
$too_many = (count($users2) >= $max_connect) ? true : false;
if (!$exists && !$too_many && ($from_this_ip <= $max_from_ip)) {
    $user_array = array_fill(0, USER_TOTALFIELDS - 1, "");
    $user_array[USER_NICKNAME] = $user_name;
    $user_array[USER_SESSION] = $session;
    $user_array[USER_TIME] = time();
    $user_array[USER_GENDER] = $sex;
    $user_array[USER_AVATAR] = "";
    #check for small photo
    $tmp_name = "".floor($registered_user / 2000)."/".$registered_user.".gif";
    $tmp_name2 = "".floor($registered_user / 2000)."/".$registered_user.".jpg";
    if (file_exists($file_path."photos/$tmp_name")) {
        $user_array[USER_AVATAR] = $tmp_name;
    } elseif (file_exists($file_path."photos/$tmp_name2")) {
        $user_array[USER_AVATAR] = $tmp_name2;
    }
    $user_array[USER_REGID] = $registered_user;
    $user_array[USER_TAILID] = "0";
    $user_array[USER_IP] = $REMOTE_ADDR;
    $user_array[USER_STATUS] = 0;
    $user_array[USER_LASTSAYTIME] = time();
    $user_array[USER_ROOM] = $room_id;
    $user_array[USER_CANONNICK] = to_canon_nick($user_name);
    $user_array[USER_HTMLNICK] = $htmlnick;
    $user_array[USER_CHATTYPE] = $chat_type;
    $user_array[USER_LANG] = $user_lang;
    $user_array[USER_IGNORLIST] = "";
    $user_array[USER_COOKIE] = $c_hash;
    $user_array[USER_BROWSERHASH] = $browser_hash;

    //DD patch
    if ($TryToBeInvisible) {
        $user_array[USER_INVISIBLE] = 1;
    } else {
        $user_array[USER_INVISIBLE] = 0;
    }
    //end DD

    $user_array[USER_CLASS] = ($registered_user) ? $current_user->user_class : 0;
    $user_array[USER_CLANID] = ($registered_user) ? $current_user->clan_id : 0;
    $user_array[USER_CUSTOMCLASS] = ($registered_user) ? $current_user->custom_class : 0;
    $user_array[USER_REDUCETRAFFIC] = ($registered_user) ? $current_user->reduce_traffic : 0;

    $user_array[USER_REGISTERED] = ($registered_user) ? intval($current_user->registered) : 0;
    $user_array[USER_MEMBER] = ($registered_user) ? intval($current_user->is_member) : 0;

    if ($registered_user) {
        if (intval($current_user->plugin_info["silence_start"]) + intval(
                $current_user->plugin_info["silence_time"]
            ) > my_time()) {
            $user_array[USER_SILENCE] = intval($current_user->plugin_info["silence_time"]);
            $user_array[USER_SILENCE_START] = intval($current_user->plugin_info["silence_start"]);
        }
        if (intval($current_user->plugin_info["jail_start"]) + intval(
                $current_user->plugin_info["jail_time"]
            ) > my_time()) {
            $user_array[USER_ROOM] = $jail_id;
        }
    }

    if ($registered_user) {
        if ($current_user->limit_ips != "") {
            $current_user->limit_ips = trim($current_user->limit_ips);
            $arr_ips = explode(";", $current_user->limit_ips);

            $IsIPFound = false;

            $REMOTE_ADDR = trim($REMOTE_ADDR);
            $proxy_arr = explode(":", $REMOTE_ADDR);
            $REMOTE_ADDR_PROXY = $proxy_arr[0];
            $REMOTE_ADDR = trim($REMOTE_ADDR);

            for ($i = 0; $i < count($arr_ips); $i++) {
                if (strpos($REMOTE_ADDR, $arr_ips[$i]) === true or
                    strpos($arr_ips[$i], $REMOTE_ADDR) === true or
                    $arr_ips[$i] == $REMOTE_ADDR or
                    strpos($REMOTE_ADDR_PROXY, $arr_ips[$i]) === true or
                    strpos($arr_ips[$i], $REMOTE_ADDR_PROXY) === true or
                    $arr_ips[$i] == $REMOTE_ADDR_PROXY
                ) {
                    $IsIPFound = true;
                }
            }

            if (!$IsIPFound) {
                include($file_path."designes/".$design."/common_body_start.php");
                echo "<p align=center>$w_security_error</p>";
                echo "<p align=center>$w_security_error_ip</p>";
                include($file_path."designes/".$design."/common_body_end.php");
                exit;
            }
        }
    }

    if (!in_array($design, $designes)) {
        $design = $default_design;
    }

    $user_array[USER_SKIN] = $design;
    $exists = $registered_user;
    $hi = 1;
    $users2[] = implode("\t", $user_array)."\n";
    $cu_array = $user_array;
}
$users2 = array_unique($users2);
usort($users2, "cmp");
$total = count($users2);

fseek($fp, 0);
fwrite($fp, implode("", $users2));
ftruncate($fp, ftell($fp));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
if (!$exists && $too_many) {
    $error_text = "$w_too_many<br><a href=\"index.php\">$w_try_again_later</a>";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}
if (!$exists && ($from_this_ip > $max_from_ip)) {
    $error_text = "$w_too_many_from_ip<br><a href=\"index.php\">$w_try_again_later</a>";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}