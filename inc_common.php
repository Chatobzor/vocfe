<?php
if (!defined("_COMMON_")):
    define("_COMMON_", 1);

    if (!defined("_TAIL_")) {
        ob_start();
    }

    define("FILE_PATH", __DIR__.'/');
    define("DATA_PATH", __DIR__.'/data/');
    define("CORE_PATH", __DIR__.'/core/');
    define('ADMIN_PATH', __DIR__.'/admin/');
    define('MVOC_PATH', __DIR__.'/mvoc/');
    define('DEFAULT_CHARSET', 'utf-8');
    define('VERSION', file_get_contents(DATA_PATH . 'version.dat'));

    $data_path = DATA_PATH;
    $file_path = FILE_PATH;

    require_once CORE_PATH . "php_migration.php";


//DD rozmova skin specific definitions
    $a_silence_id = 9;
//end of DD DD rozmova skin specific definitions

//available fonts
    $fonts_arr = [];
    $fonts_arr = array(
        "Verdana, Tahoma, Arial",
        "Georgia, Book Antiqua, Garamond, Helvetica, Arial"
    );
    $font_sizes_arr = array();
    $fonts_sizes_arr = array(
        "75",
        "80",
        "90",
        "100",
        "120",
        "130",
        "150"
    );

    error_reporting(1);
    ini_set("log_errors", 1);
    ini_set("display_errors", 1);
    $debug = 0;

    function my_err_h($errno, $errstr, $errfile, $errline)
    {
        global $debug;
        switch ($errno) {
            case E_NOTICE:
                if ($debug) {
                    echo "<b>NOTICE</b>: ".htmlspecialchars($errstr).",  at line ".$errline." in the file ".str_replace(
                            dirname(dirname($errfile))."/",
                            "",
                            $errfile
                        )."<br>";
                } else {
                    return 0;
                }
            case E_WARNING:
                if ($debug) {
                    echo "<b>WARNING</b>: ".htmlspecialchars(
                            $errstr
                        ).",  at line ".$errline." in the file ".str_replace(
                            dirname(dirname($errfile))."/",
                            "",
                            $errfile
                        )."<br>";
                } else {
                    return 0;
                }
                break;
            case E_USER_ERROR:
                echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=".DEFAULT_CHARSET."\">\n";
                echo "<center><table border=\"0\"width=\"80%\" cellpadding=\"6\" cellspacing=\"0\">";
                echo "<tr><td bgcolor=\"red\" ><span style=\"color:white; font-weight: bold; font-size:18px; font-family: Arial, Verdana\">VOC++ -- Fatal error</span></td></tr>";

                echo "<tr bgcolor=\"#dddddd\"><td><span style=\"color:black; font-size:14px; font-family: Arial, Verdana\">We got the error <br><b>".htmlspecialchars(
                        $errstr
                    )."</b><br>at line <b>".$errline."</b>";
                echo " in the file <b>".str_replace(dirname(dirname($errfile)), "", $errfile);
                echo "</span></td></tr>";
                echo "<tr bgcolor=\"#dddddd\"><td><span style=\"color:black; font-size:12px; font-family: Arial, Verdana\">";
                echo "Find help at <a href=\"http://vocplus.creatiff.com.ua/\">VOC++ homepage</a>";
                echo "</td></tr>";
                echo "</table></center>";
                exit;
                break;
            case E_USER_WARNING:
                echo "<center><table border=\"0\"width=\"80%\" cellpadding=\"6\" cellspacing=\"0\">";
                echo "<tr><td bgcolor=\"red\" ><span style=\"color:white; font-weight: bold; font-size:18px; font-family: Arial, Verdana\">VOC++ -- error</span></td></tr>";

                echo "<tr bgcolor=\"#dddddd\"><td><span style=\"color:black; font-size:14px; font-family: Arial, Verdana\">We got the error <br><b>".htmlspecialchars(
                        $errstr
                    )."</b><br>at line <b>".$errline."</b>";
                echo " in the file <b>".str_replace(dirname(dirname($errfile)), "", $errfile);
                echo "</span></td></tr>";
                echo "</table></center>";
                break;
        }
        return 0;
    }

    set_error_handler("my_err_h");

    ini_set('register_globals', 'off');

    if (!($conf_content = implode("", file(DATA_PATH."voc.conf")))) {
        trigger_error("Cannot open voc.conf file, please check your DATA PATH parameter", E_USER_ERROR);
    }
    eval($conf_content);

//images empty fix
    if (trim($images_url) == "") {
        $images_url = $chat_url;
    }


    function my_time()
    {
        global $time_offset;
        return time() + $time_offset;
    }

    setlocale(LC_ALL, $locale);

    if ($_SERVER['HTTP_HOST'] != "") {
        $chat_url = strtolower($chat_url);
        $_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
        if (strpos($chat_url, $_SERVER['HTTP_HOST']) === false) {
            //script called for another domain
            $test_url = $chat_url;
            $test_url = str_replace("http://", "", $test_url);

            for ($i = 0; $i < strlen($test_url); $i++) {
                if (substr($test_url, $i, 1) == "/") {
                    break;
                }
            }

            if ($i < strlen($test_url)) {
                $test_url = substr($test_url, 0, $i);
                $chat_url = eregi_replace($test_url, $_SERVER['HTTP_HOST'], $chat_url);
                $daemon_host = eregi_replace($test_url, $_SERVER['HTTP_HOST'], $daemon_host);
            }
        }
    }

#for Register_gloabls = off
    function set_variable($variable_name)
    {
        global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $$variable_name;

        $$variable_name = "";
        //GPC order :)
        if (isset($HTTP_GET_VARS[$variable_name])) {
            $$variable_name = $HTTP_GET_VARS[$variable_name];
        }
        if (isset($HTTP_POST_VARS[$variable_name])) {
            $$variable_name = $HTTP_POST_VARS[$variable_name];
        }
        if (substr($variable_name, 0, 2) == "c_" && isset($HTTP_COOKIE_VARS[$variable_name])) {
            $$variable_name = $HTTP_COOKIE_VARS[$variable_name];
        }
        //i don't use string-arrays in forms, only in the admin-zone, but there it's just design &lang names
        //which is normally doesn't have ' or " etc
        if (is_string($$variable_name)) {
            $$variable_name = str_replace("\0", "", $$variable_name);
            $$variable_name = str_replace("\t", " ", $$variable_name);
        }
    }

    set_variable("session");
    $session = preg_replace("/[^a-fA-F0-9]/", "", $session);
    set_variable("design");

    set_variable("c_hash");

//will set it in the file which doesn't output anything before inc_common.php
//setCookie("c_hash", $c_hash, time() + 2678400);

    $browser_t = "";
    if (isset($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
        $browser_t .= $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
    }
    if (isset($HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_t .= $HTTP_SERVER_VARS['HTTP_ACCEPT_LANGUAGE'];
    }
    if (isset($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'])) {
        $browser_t .= $HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'];
    }
    $browser_hash = 0;
    for ($i = 0; $i < strlen($browser_t); $i++) {
        $browser_hash += ord($browser_t[$i]);
    }

#determening the current design
    if ($design == "") {
        $design = $default_design;
    } else {
        if (!in_array($design, $designes)) {
            $design = $default_design;
        }
    }
    $current_design = $chat_url."designes/".$design."/";


#setting necessary variables
    $daemon_url = $daemon_host.":".$daemon_port."/";
    $engine_path = DATA_PATH."engine/".$engine."/";
    $ld_engine_path = DATA_PATH."engine/".$long_life_data_engine."/";

    $user_data_file = DATA_PATH."users.dat";
    $who_in_chat_file = DATA_PATH."who.dat";
    $messages_file = DATA_PATH."messages.dat";
    $converts_file = DATA_PATH."converts.dat";
    $robotspeak_file = DATA_PATH."robotspeak.dat";
    $banlist_file = DATA_PATH."banlist.dat";
    $rooms_list_file = DATA_PATH."rooms.dat";
//DD addon
    $clans_data_file = DATA_PATH."clans.dat";

    define('MAX_CLANMEMBERS', 20);

#user statuses
    define('ONLINE', 0);
    define('DISCONNECTED', 1);
    define('AWAY', 2);
    define('NA', 4);
    define('DND', 8);
    define('PRIVATE', 16);

#genders
    define("GENDER_BOY", 1);
    define("GENDER_GIRL", 2);
    define("GENDER_THEY", 3);

#admin rights
    define('ADM_BAN', 1);
    define('ADM_IP_BAN', 2);
    define('ADM_VIEW_IP', 4);
    define('ADM_UN_BAN', 8);
    define('ADM_BAN_MODERATORS', 16);
    define('ADM_CHANGE_TOPIC', 32);
    define('ADM_CREATE_ROOMS', 64);
    define('ADM_EDIT_USERS', 128);
    define('ADM_BAN_BY_BROWSERHASH', 256);
    define('ADM_BAN_BY_SUBNET', 512);
    define('ADM_VIEW_PRIVATE', 1024);
    $total_admin_levels = 11;
    define("_VIP_", -84);
//DD addon
    define('CST_PRIEST', 1);

    define('REITING_TIME_LIMIT', 3);
    define('PRIEST_BAN_LIMIT', 999999999);
    define('MODER_LOG_LIMIT', 4);
//clans
    define('CLN_ADDUSER', 1);
    define('CLN_DELETEUSER', 2);
    define('CLN_EDIT', 4);
    define('CLN_EDITUSER', 8);
    define('CLN_LEVELS', 4);
    define('CLAN_ID', 0);
    define('CLAN_NAME', 1);
    define('CLAN_TOTALFIELDS', 2);

//constans for users and messages lists
    define("USER_NICKNAME", 0);
    define("USER_SESSION", 1);
    define("USER_TIME", 2);
    define("USER_GENDER", 3);
    define("USER_AVATAR", 4);
    define("USER_REGID", 5);
    define("USER_TAILID", 6);
    define("USER_IP", 7);
    define("USER_STATUS", 8);
    define("USER_LASTSAYTIME", 9);
    define("USER_ROOM", 10);
    define("USER_IGNORLIST", 11);
    define("USER_CANONNICK", 12);
    define("USER_CHATTYPE", 13);
    define("USER_LANG", 14);
//new
    define("USER_HTMLNICK", 15);
    define("USER_PRIVTAILID", 16);
    define("USER_COOKIE", 17);
    define("USER_BROWSERHASH", 18);
    define("USER_CLASS", 19);
//old again :)
    define("USER_SKIN", 20);
//DD patch
    define("USER_INVISIBLE", 21);
    define("USER_SILENCE", 22);
    define("USER_SILENCE_START", 23);
    define("USER_FILTER", 24);
    define("USER_CUSTOMCLASS", 25);
    define("USER_CLANID", 26);
    define("USER_REDUCETRAFFIC", 27);
    define("USER_REGISTERED", 28);
    define("USER_MEMBER", 29);
    define("USER_SHMID", 30);
    define("USER_TOTALFIELDS", 31);
//end DD patch

    define("MESG_ID", 0);
    define("MESG_ROOM", 1);
    define("MESG_TIME", 2);
    define("MESG_FROM", 3);
    define("MESG_FROMWOTAGS", 4);
    define("MESG_FROMSESSION", 5);
    define("MESG_FROMID", 6);
    define("MESG_FROMAVATAR", 7);
    define("MESG_TO", 8);
    define("MESG_TOSESSION", 9);
    define("MESG_TOID", 10);
    define("MESG_BODY", 11);
    define("MESG_CLANID", 12);
    define("MESG_TOTALFIELDS", 13);

    define("ROOM_ID", 0);
    define("ROOM_TITLE", 1);
    define("ROOM_TOPIC", 2);
    define("ROOM_DESIGN", 3);
    define("ROOM_BOT", 4);
    define("ROOM_CREATOR", 5);//for private rooms
    define("ROOM_ALLOWEDUSERS", 6);//for private rooms
    define("ROOM_ALLOWPICS", 7);//to send pics.
    define("ROOM_PREMODER", 8);//message must be approved by moderator
    define("ROOM_LASTACTION", 9);
// VOC++ BE
    define("ROOM_CLUBONLY", 10);
    define("ROOM_PASSWORD", 11);
    define("ROOM_JAIL", 12);
    define("ROOM_POINTS", 13);
    define("ROOM_TOTALFIELDS", 14);

    define("MAX_PHOTO_REITING", 10);
    define("PASS_CHANGE_TIME", 14 * 24 * 3600);

    function my_sem_pick($sem_id)
    {
        global $semaphor_acquired, $semaphor_acquired_ids;
        global $time_start, $_debug_array, $_debug_first;

        if (($b_id = array_search($sem_id, $semaphor_acquired)) === false) {
            $users_sem_id = sem_get($sem_id, 1, 0777 | IPC_CREAT, 1);
            if (!sem_acquire($users_sem_id)) {
                trigger_error("Can't create semaphore, maybe sysvsem.so not loaded!");
            } else {
                $semaphor_acquired[count($semaphor_acquired)] = $sem_id;
                $semaphor_acquired_ids[count($semaphor_acquired_ids)] = $users_sem_id;
                return $users_sem_id;
            }
        } else {
            $semaphor_acquired[count($semaphor_acquired)] = $sem_id;
            $semaphor_acquired_ids[count($semaphor_acquired_ids)] = $semaphor_acquired_ids[$b_id];
            return $semaphor_acquired_ids[$b_id];
        }
    }

    function my_sem_clear($sem_id)
    {
        global $semaphor_acquired, $semaphor_acquired_ids;
        global $time_start, $_debug_array, $_debug_first;
        if (($b_id = array_search($sem_id, $semaphor_acquired)) === false) {
            return;
        } else {
            $_rem_sid = $semaphor_acquired_ids[$b_id];

            $semaphor_acquired_ids = my_sem_array_trim($semaphor_acquired_ids, $b_id);
            $semaphor_acquired = my_sem_array_trim($semaphor_acquired, $b_id);
            if (($b_id = array_search($sem_id, $semaphor_acquired, true)) === false) {
                sem_release($_rem_sid);
            }
        }
    }

    function my_sem_array_trim($array, $index)
    {
        $_ret = array();
        if (is_array($array)) {
            unset ($array[$index]);
            @reset($array);
            while (list($id, $value) = @each($array)) {
                if (!empty($value) and $value != '') {
                    $_ret[] = $value;
                }
            }
            return $_ret;
        } else {
            return false;
        }
    }

    function my_sem_get_res($sem_id)
    {
        global $semaphor_acquired, $semaphor_acquired_ids;

        if (($b_id = array_search($sem_id, $semaphor_acquired)) === false) {
            return;
        } else {
            return $semaphor_acquired_ids[$b_id];
        }
        return false;
    }

#loading language pack
//require beacuse I need 'fatal error' if i cannot find the file
    if (!defined("_VOC_CONFIG_")) {
        require_once(FILE_PATH."languages/".$language.".php");

        //System messages -- for case user selected not default language
        //?$w_whisper_to
        //the same name, but with 's' before
        $sw_rob_login = $w_rob_login;
        $sw_rob_hb = $w_rob_hb;
        $sw_rob_logout = $w_rob_logout;
        $sw_rob_idle = $w_rob_idle;
        $sw_goes_to_room = $w_goes_to_room;
        $sw_came_from_room = $w_came_from_room;
        $sw_set_topic_text = $w_set_topic_text;
        $sw_alert_text = $w_alert_text;
        $sw_kill_text = $w_kill_text;

        //DD addon
        $sw_roz_silence_msg = $w_roz_silence_msg;
        $sw_roz_silenced_adm = $w_roz_silenced_adm;
        $sw_roz_ban_adm = $w_roz_ban_adm;
        $sw_roz_clear_pub_adm = $w_roz_clear_pub_adm;

        $sw_usr_all_link = $w_usr_all_link;
        $sw_usr_adm_link = $w_usr_adm_link;
        $sw_usr_boys_link = $w_usr_boys_link;
        $sw_usr_girls_link = $w_usr_girls_link;
        $sw_usr_they_link = $w_usr_they_link;
        $sw_usr_clan_link = $w_usr_clan_link;
        $sw_usr_shaman_link = $w_usr_shaman_link;

        $sw_roz_announce_stat = $w_roz_announce_stat;
        $sw_roz_warning_stat = $w_roz_warning_stat;

        $sw_roz_damn_mess = $w_roz_damn_mess;
        $sw_roz_undamn_mess = $w_roz_undamn_mess;
        $sw_roz_rew_mess = $w_roz_rew_mess;
        $sw_roz_damn_mess_adm = $w_roz_damn_mess_adm;
        $sw_roz_undamn_mess_adm = $w_roz_undamn_mess_adm;
        $sw_roz_rew_mess_adm = $w_roz_rew_mess_adm;
        $sw_roz_quaked_msg = $w_roz_quaked_msg;
        $sw_roz_shaman_alert = $w_roz_shaman_alert;
        $sw_roz_clan_common_entr = $w_roz_clan_common_entr;
        $sw_roz_clan_common_exit = $w_roz_clan_common_exit;

        $sw_roz_just_married = $w_roz_just_married;
        $sw_roz_no_married = $w_roz_no_married;
        $sw_roz_just_married_adm = $w_roz_just_married_adm;
        $sw_roz_no_married_adm = $w_roz_no_married_adm;

        $sw_adm_reason = $w_admin_reason;
        $sw_roz_moderator = $w_roz_moderator;

        $sw_banned = $w_banned;
        $sw_jailed = $w_jailed;

        $sw_roz_jailed_adm = $w_roz_jailed_adm;
        $sw_jail_text = $w_jail_text;

        $sw_adm_user_add_clan = $w_adm_user_add_clan;
        $sw_adm_user_del_clan = $w_adm_user_del_clan;
        $sw_adm_user_exchange = $w_adm_user_exchange;
        $sw_adm_user_buy = $w_adm_user_buy;
        $sw_adm_user_present = $w_adm_user_present;
        $sw_adm_user_transfer = $w_adm_user_transfer;
        $sw_adm_user_present_from = $w_adm_user_present_from;
        $sw_adm_user_transfer_from = $w_adm_user_transfer_from;
        $sw_adm_user_item_used = $w_adm_user_item_used;
        $sw_adm_user_item_used_on = $w_adm_user_item_used_on;
        $sw_adm_user_item_removed = $w_adm_user_item_removed;
        $sw_adm_user_item_returned = $w_adm_user_item_returned;
        $sw_adm_money_transfer_from = $w_adm_money_transfer_from;
        $sw_adm_money_transfer = $w_adm_money_transfer;
        $sw_adm_clan_penalty = $w_adm_clan_penalty;
        $sw_adm_clan_rew = $w_adm_clan_rew;

        $sw_mod_remove_photo_adm = $w_mod_remove_photo_adm;
        $sw_mod_remove_photo_user = $w_mod_remove_photo_user;
        $sw_mod_remove_photo_subj = $w_mod_remove_photo_subj;

        $sw_adm_reffered_subject = $w_adm_reffered_subject;
        $sw_adm_reffered_payment = $w_adm_reffered_payment;

        $sw_user_chaos = $w_user_chaos;
        $sw_adm_chaos_put = $w_adm_chaos_put;
        $sw_adm_chaos_adm = $w_adm_chaos_adm;

        //DD BUGFIX for system time (ban-actions):
        $sw_times = array();
        for ($i = 0; $i < count($w_times); $i++) {
            $sw_times[$i]["name"] = $w_times[$i]["name"];
            $sw_times[$i]["value"] = $w_times[$i]["value"];
        }
        $sw_roz_user_status = array();
        for ($i = 0; $i < count($w_roz_user_status); $i++) {
            $sw_roz_user_status[$i]["points"] = $w_roz_user_status[$i]["points"];
            $sw_roz_user_status[$i]["status"] = $w_roz_user_status[$i]["status"];
        }
    }
//check for current language and call w_people_* from corresponding lang-file
    function w_people()
    {
        global $language, $user_lang;
        $call_lang = $language;
        if (!isset($user_lang)) {
            $user_lang = "";
        }
        if ($user_lang != "") {
            $call_lang = $user_lang;
        }
        return call_user_func("w_people_".$call_lang, "");
    }

    function fixup_contributions($what)
    {
        $fix_arr = explode(" ", $what);
        $rez_arr = array();

        for ($i = 0; $i < count($fix_arr); $i++) {
            $fix_arr[$i] = trim($fix_arr[$i]);
            if (strlen($fix_arr[$i]) > 25) {
                $fix_arr[$i] = substr($fix_arr[$i], 0, 25);
            }
            if (strlen($fix_arr[$i]) > 0) {
                $rez_arr[] = $fix_arr[$i];
            }
        }
        return implode(" ", $rez_arr);
    }

    function dd()
    {
        $bugtrace = debug_backtrace();

        echo '<pre><b style="color:#701e22">--- '.$bugtrace[0]['file'].' Line: '.$bugtrace[0]['line']." ---</b></pre>";
        foreach (func_get_args() as $arg) {
            echo '<pre>';
            var_dump($arg);
            echo '</pre>';
        }
        exit;
    }

endif;