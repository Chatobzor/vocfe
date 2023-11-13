<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}


//saving area :)
@eval(implode("",file($data_path."voc.conf")));
$write_to_conf = 0;
switch ($save_level) {
        case 1:
                if (!isset($conf_file_path)) $conf_file_path = dirname($conf_inc_config)."/";
                $data_path = $conf_data_path;
                echo $adm_saving_data." <b>inc_common.php<b> ... ";
                $config_lines = file($conf_inc_config);
                $fp = fopen($conf_inc_config,"wb");
                flock($fp, LOCK_EX);
                //in win replace c:\path with c:/path
                if ($data_path[1] == ":")
                        $data_path = str_replace("\\","/",$data_path);
                for ($i=0;$i<count($config_lines);$i++)
                        if (strpos($config_lines[$i],"=")) {
                                list($param,$value) = split("=",$config_lines[$i]);
                                if (trim($param) == "\$data_path") $config_lines[$i] = "\$data_path = \"".$data_path."\";\n";
                        }
                fwrite($fp, implode("",$config_lines));
                fflush($fp);
                flock($fp, LOCK_UN);
                fclose($fp);
                echo "<b>OK</b><hr>";
                eval(implode("",file($data_path."voc.conf")));
                $file_path = $conf_file_path;
                //in win replace c:\path with c:/path
                if ($file_path[1] == ":")
                        $file_path = str_replace("\\","/",$file_path);
                $write_to_conf = 1;
        break;
        case 2:
                //now it's automatically defined at step 1, from inc_common.php path
                //set_variable("file_path");
                //$file_path = trim($file_path);
                set_variable("chat_url");
                set_variable("images_url");
                set_variable("daemon_type");
                $daemon_type = intval($daemon_type);
                set_variable("daemon_host");
                set_variable("daemon_port");
                set_variable("daemon_listen");
                set_variable("modvoc_socket");
                   set_variable("locale");
                $write_to_conf = 1;
        break;
        case 3:
                set_variable("shm_mess_id");
                set_variable("shm_users_id");
                set_variable("engine");
                if ($engine == "") $engine = "files";
                set_variable("long_life_data_engine");
                set_variable("mysql_server");
                set_variable("mysql_user");
                set_variable("mysql_password");
                set_variable("mysql_db");
                set_variable("mysql_db");
                set_variable("mysql_table_prefix");

                $chat_types = array();
                for ($j = 0; $j<4; $j++)
                        if (isset($HTTP_POST_VARS["chat_types"][$j])) $chat_types[$j] = $HTTP_POST_VARS["chat_types"][$j];

                $write_to_conf = 1;
        break;
        case 4:
                set_variable("open_chat");
                set_variable("enable_gzip");
                set_variable("enable_reiting");
                set_variable("admin_mail");
                //for perl, otherwise it fails on old versions
                $admin_mail = str_replace("@","\\@",$admin_mail);
                set_variable("max_mailbox_size");
                set_variable("disconnect_time");
                set_variable("history_size");
                set_variable("history_size_shower");
                set_variable("max_photo_size");
                set_variable("max_photo_width");
                set_variable("max_photo_height");
                set_variable("max_avatar_size");
                set_variable("max_avatar_width");
                set_variable("max_avatar_height");
                set_variable("max_connect");
                set_variable("max_from_ip");
                set_variable("max_cap_letters");
                set_variable("time_offset");
                $time_offset = intval($time_offset)*3600;
                set_variable("max_images");
                set_variable("flood_protection");
                set_variable("flood_time");
                set_variable("flood_in_last");

                $open_chat = ($open_chat != "") ?1:0;
                $enable_gzip = ($enable_gzip != "") ?1:0;
                $enable_reiting = ($enable_reiting != "") ?1:0;

                set_variable("md5_salt");
                $md5_salt = trim($md5_salt);

                $write_to_conf = 1;
        break;
        case 5:
                set_variable("nick_min_length");
                set_variable("nick_max_length");
                set_variable("nick_available_chars");
                set_variable("current_to_canon");
                set_variable("club_mode");
                set_variable("impro_registration");
                set_variable("registration_mailconfirm");
                set_variable("max_per_mail");
                $write_to_conf = 1;
        break;
        case 6:
                set_variable("mess_stat");
                set_variable("web_indicator");
                set_variable("logging_messages");
                set_variable("logging_ban");
                set_variable("allow_pics");
                set_variable("allow_priv_pics");
                set_variable("pics_maxsize");
                set_variable("pics_maxh");
                set_variable("pics_maxw");
        //VOC++
        set_variable("vocplus_useguardian");
        set_variable("vocplus_guardian_dellogs");

                $logging_ban = ($logging_ban != "") ?1:0;
                $logging_messages = ($logging_messages != "") ?1:0;
        $vocplus_useguardian = ($vocplus_useguardian != "") ?1:0;
        $vocplus_guardian_dellogs = ($vocplus_guardian_dellogs != "") ?1:0;
                // it's dropdown with int, i'm setting intval to it when writing $allow_pics = ($allow_pics != "") ?1:0;
                $pics_maxsize = intval($pics_maxsize);
                $pics_maxw = intval($pics_maxw);
                $pics_maxh = intval($pics_maxh);
                $write_to_conf = 1;
        break;
        case 7:
                $designes = array();
                $allowed_langs = array();
                for ($j = 0; $j<50; $j++)
                        if (isset($HTTP_POST_VARS["designes"][$j])) $designes[] = $HTTP_POST_VARS["designes"][$j];
                for ($j = 0; $j<50; $j++)
                        if (isset($HTTP_POST_VARS["allowed_langs"][$j])) $allowed_langs[] = $HTTP_POST_VARS["allowed_langs"][$j];

                set_variable("language");
                set_variable("charset");
                set_variable("default_design");
                set_variable("keep_whisper");
                set_variable("message_format");
                set_variable("message_fromme");
                set_variable("private_message");
                set_variable("private_message_fromme");
                set_variable("private_hidden");
                set_variable("nick_highlight_before");
                set_variable("nick_highlight_after");
                set_variable("str_w_n_before");
                set_variable("str_w_n_after");
                set_variable("colorize_nicks");
                set_variable("priv_frame");
                set_variable("allow_multiply");
                set_variable("enabled_b_style");
                set_variable("enabled_i_style");
                set_variable("enabled_u_style");

                if (!is_array($designes) || count($designes) == 0) {
                        $designes = array();
                        $designes[0] = $default_design;
                }
                if (!is_array($allowed_langs) || count($allowed_langs) == 0) {
                        $allowed_langs = array();
                        $allowed_langs[0] = $language;
                }
                if (!in_array($language, $allowed_langs))
                        $allowed_langs[] = $language;
                $priv_frame = ($priv_frame != "") ?1:0;
                $enabled_b_style = ($enabled_b_style != "") ?1:0;
                $enabled_i_style = ($enabled_i_style != "") ?1:0;
                $enabled_u_style = ($enabled_u_style != "") ?1:0;
        $allow_multiply = ($allow_multiply != "") ?1:0;

                $write_to_conf = 1;
        break;
}
if ($write_to_conf) {
        echo $adm_saving_data." <b>voc.conf</b> ... ";
        $fp = fopen($data_path."/voc.conf","wb");
        flock($fp, LOCK_EX);
        $to_save = "#Config file for the Voodoo chat\n#generated automatically by the chat/admin/configure.php script\n#Please, DON'T edit this file by hand.\n\n";
        if (!isset($engine)) $engine = "files";
        if ($engine == "") $engine = "files";
        $to_save .= "\$web_indicator = ".intval($web_indicator).";\n";
        $to_save .= "\$colorize_nicks = ".intval($colorize_nicks).";\n";
        $to_save .= "\$max_from_ip = ".intval($max_from_ip).";\n";
        $to_save .= "\$history_size = ".intval($history_size).";\n";
        $to_save .= "\$history_size_shower = ".intval($history_size_shower).";\n";
        $to_save .= "\$max_photo_size = ".intval($max_photo_size).";\n";
        $to_save .= "\$max_photo_width = ".intval($max_photo_width).";\n";
        $to_save .= "\$max_photo_height = ".intval($max_photo_height).";\n";
        $to_save .= "\$max_avatar_size = ".intval($max_avatar_size).";\n";
        $to_save .= "\$max_avatar_width = ".intval($max_avatar_width).";\n";
        $to_save .= "\$max_avatar_height = ".intval($max_avatar_height).";\n";
        $to_save .= "\$current_to_canon = \"$current_to_canon\";\n";
        //khm... looks like i do it second time:
        $to_save .= "\$enabled_b_style = ". (($enabled_b_style != "") ?1:0) .";\n";
        $to_save .= "\$enabled_i_style = ". (($enabled_i_style != "") ?1:0) .";\n";
        $to_save .= "\$enabled_u_style = ". (($enabled_u_style != "") ?1:0).";\n";
        $to_save .= "\$priv_frame = ". (($priv_frame != "") ?1:0).";\n";
        $to_save .= "\$logging_messages = ". intval($logging_messages) .";\n";
        $to_save .= "\$logging_ban = ". intval($logging_ban).";\n";
        $to_save .= "\$allow_pics = ". intval($allow_pics).";\n";
        $to_save .= "\$allow_priv_pics = ". intval($allow_priv_pics).";\n";
        $to_save .= "\$pics_maxsize = ". intval($pics_maxsize).";\n";
        $to_save .= "\$pics_maxw = ". intval($pics_maxw).";\n";
        $to_save .= "\$pics_maxh = ". intval($pics_maxh).";\n";
        $to_save .= "\$nick_min_length = ".intval($nick_min_length).";\n";
        $to_save .= "\$nick_max_length = ".intval($nick_max_length).";\n";
        $to_save .= "\$nick_available_chars = \"".str_replace("\"","\\\"",$nick_available_chars)."\";\n";
        $to_save .= "\$club_mode = ". intval($club_mode) .";\n";
        $to_save .= "\$impro_registration = ". intval($impro_registration) .";\n";
        $to_save .= "\$registration_mailconfirm = ". intval($registration_mailconfirm) .";\n";
        $to_save .= "\$keep_whisper = ". intval($keep_whisper).";\n";
        $to_save .= "\$max_images = ". intval($max_images).";\n";
        $to_save .= "\$max_mailbox_size = ". intval($max_mailbox_size).";\n";
        $to_save .= "\$disconnect_time = ". intval($disconnect_time).";\n\n";
        $to_save .= "\$admin_mail = \"".str_replace("\"","\\\"",$admin_mail)."\";\n\n";
        $to_save .= "\$message_format = \"".str_replace("\"","\\\"",$message_format)."\";\n";
        $to_save .= "\$message_fromme = \"".str_replace("\"","\\\"",$message_fromme)."\";\n";
        $to_save .= "\$private_message = \"".str_replace("\"","\\\"",$private_message)."\";\n";
        $to_save .= "\$private_message_fromme = \"".str_replace("\"","\\\"",$private_message_fromme)."\";\n";
        $to_save .= "\$private_hidden = \"".str_replace("\"","\\\"",$private_hidden)."\";\n";
        $to_save .= "\$nick_highlight_before = \"".str_replace("\"","\\\"",$nick_highlight_before)."\";\n";
        $to_save .= "\$nick_highlight_after = \"".str_replace("\"","\\\"",$nick_highlight_after)."\";\n";
        $to_save .= "\$str_w_n_before = \"".str_replace("\"","\\\"",$str_w_n_before)."\";\n";
        $to_save .= "\$str_w_n_after = \"".str_replace("\"","\\\"",$str_w_n_after)."\";\n";
        $to_save .= "\$engine = \"".$engine."\";\n";
        $to_save .= "\$long_life_data_engine = \"".$long_life_data_engine."\";\n\n";
        $to_save .= "\$chat_url = \"".str_replace("\"","\\\"",$chat_url)."\";\n";
    //VOC++
    $to_save .= "\$images_url = \"".str_replace("\"","\\\"",$images_url)."\";\n";
    $to_save .= "\$vocplus_useguardian = \"1\";\n";
        $to_save .= "\$vocplus_guardian_dellogs = \"".str_replace("\"","\\\"",$vocplus_guardian_dellogs)."\";\n";
    $to_save .= "\$open_chat = \"".str_replace("\"","\\\"",$open_chat)."\";\n";
    $to_save .= "\$enable_reiting = \"".str_replace("\"","\\\"",$enable_reiting)."\";\n";
    $to_save .= "\$allow_multiply = \"".str_replace("\"","\\\"",$allow_multiply)."\";\n";
    $to_save .= "\$enable_gzip = \"".str_replace("\"","\\\"",$enable_gzip)."\";\n";

       $to_save .= "\$md5_salt = \"".str_replace("\"","\\\"",$md5_salt)."\";\n";

    $to_save .= "\$daemon_host = \"".str_replace("\"","\\\"",$daemon_host)."\";\n";
        $to_save .= "\$daemon_port = ".intval($daemon_port).";\n";
        $to_save .= "\$daemon_listen = \"".str_replace("\"","\\\"",$daemon_listen)."\";\n";
        $to_save .= "\$daemon_type = ".intval($daemon_type).";\n";
    $to_save .= "\$locale = \"".str_replace("\"", "\\\"", $locale)."\";\n";
        $to_save .= "\$modvoc_socket = \"".str_replace("\"", "\\\"", $modvoc_socket)."\";\n";
        $to_save .= "\$max_connect = ".intval($max_connect).";\n";
        $to_save .= "\$file_path = \"".str_replace("\"","\\\"",$file_path)."\";\n";
        for($j=0;$j<count($designes);$j++)
                $to_save .= "\$designes[$j] = \"".$designes[$j]."\";\n";
        if (!in_array($default_design, $designes)) $default_design = $designes[0];
        $to_save .= "\$default_design = \"".$default_design."\";\n";
        for($j=0;$j<count($chat_types);$j++)
                $to_save .= "\$chat_types[$j] = \"".$chat_types[$j]."\";\n";
        $to_save .= "#shared memory\n";
        $to_save .= "\$shm_mess_id = ".intval($shm_mess_id).";\n";
        $to_save .= "\$shm_users_id = ".intval($shm_users_id).";\n";
        $to_save .= "#MySQL\n";
        $to_save .= "\$mysql_server = \"". str_replace("\"","\\\"",$mysql_server)."\";\n";
        $to_save .= "\$mysql_user = \"". str_replace("\"","\\\"",$mysql_user)."\";\n";
        $to_save .= "\$mysql_password = \"". str_replace("\"","\\\"",$mysql_password)."\";\n";
        $to_save .= "\$mysql_db = \"". str_replace("\"","\\\"",$mysql_db)."\";\n";
        $to_save .= "\$mysql_table_prefix = \"". str_replace("\"","\\\"",$mysql_table_prefix)."\";\n";
        $to_save .= "#flood protection settings\n";
        $to_save .= "\$flood_protection = ".intval($flood_protection).";\n";
        $to_save .= "\$flood_time = ".intval($flood_time).";\n";
        $to_save .= "\$flood_in_last = ".intval($flood_in_last).";\n";
        $to_save .= "\$mess_stat = ".intval($mess_stat).";\n";
        $to_save .= "\$time_offset = ".intval($time_offset).";\n";
        $to_save .= "\$max_cap_letters = ".intval($max_cap_letters).";\n";
        $to_save .= "\$max_per_mail = ".intval($max_per_mail).";\n";
        $to_save .= "\$charset = \"".str_replace("\"","\\\"",$charset)."\";\n";
        for($j=0;$j<count($allowed_langs);$j++)
                $to_save .= "\$allowed_langs[$j] = \"".$allowed_langs[$j]."\";\n";
        $to_save .= "\$language = \"".str_replace("\"","\\\"",$language)."\";\n";
        fwrite($fp, $to_save);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        echo "OK<br>\n";
        echo "$adm_reloading_conf ... ";
        eval(implode("",file($data_path."voc.conf")));
        echo "OK<hr>\n";
}

?>