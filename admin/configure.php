<?php $configuration = 1;
require_once "check_session.php";
require_once "../inc_to_canon_nick.php";
define("_VOC_CONFIG_",1);
include("header.php");
?>
<table cellpadding="3"><tr><td>
<?
if (!isset($step)) $step = 0;
else $step = intval($step);
if (!isset($save_level)) $save_level = 0;
if ($step >0 ){
        if ($save_level > 1) { include "../inc_common.php";}
        include("configure/inc_cfg_save_voc.php");
}
error_reporting(E_ALL);
clearstatcache();
$error = 0;



switch ($step)
{
        case 2:
                echo "<h3><span style=\"{background-color:#666666;};\">&nbsp;<font color=#FFFFFF>$adm_step 3.</font>&nbsp;</span> $adm_engines</h3>";
                echo "<form method=\"post\" action=\"configure.php\">";
                include("configure/inc_cfg_engine.php");
                echo "<input type=\"hidden\" name=\"step\" value=\"\">";
        echo "<input type=hidden name=lang value=\"$lang\">";
                echo "<input type=\"hidden\" name=\"save_level\" value =\"3\">";
        echo "<a href=\"javascript:document.forms[0].step.value='1';document.forms[0].submit();\">&lt;--  <b>$adm_back</b></a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='2';document.forms[0].submit();\">-<b>$adm_test</b>-</a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='3';document.forms[0].submit();\"><b>$adm_next</b> --&gt;</a>";
                echo "</form>";
        break;
        case 3:
                echo "<h3><span style=\"{background-color:#666666;};\">&nbsp;<font color=#FFFFFF>$adm_step 4.</font>&nbsp;</span> $adm_options_and_lim</h3>";
                echo "<form method=\"post\" action=\"configure.php\">";
                include("configure/inc_cfg_limits.php");
                echo "<input type=\"hidden\" name=\"step\" value=\"\">";
        echo "<input type=hidden name=lang value=\"$lang\">";
                echo "<input type=\"hidden\" name=\"save_level\" value =\"4\">";
        echo "<a href=\"javascript:document.forms[0].step.value='2';document.forms[0].submit();\">&lt;--  <b>$adm_back</b></a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='3';document.forms[0].submit();\">-<b>$adm_test</b>-</a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='4';document.forms[0].submit();\"><b>$adm_next</b> --&gt;</a>";
                echo "</form>";
        break;
        case 4:
                echo "<h3><span style=\"{background-color:#666666;};\">&nbsp;<font color=#FFFFFF>$adm_step 5.</font>&nbsp;</span> $adm_user_access_lim</h3>";
                echo "<form method=\"post\" action=\"configure.php\">";
                include("configure/inc_cfg_access.php");
                echo "<input type=\"hidden\" name=\"step\" value=\"\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
                echo "<input type=\"hidden\" name=\"save_level\" value =\"5\">";
        echo "<a href=\"javascript:document.forms[0].step.value='3';document.forms[0].submit();\">&lt;--  <b>$adm_back</b></a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='4';document.forms[0].submit();\">-<b>$adm_test</b>-</a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='5';document.forms[0].submit();\"><b>$adm_next</b> --&gt;</a>";
                echo "</form>";
        break;
        case 5:
                echo "<h3><span style=\"{background-color:#666666;};\">&nbsp;<font color=#FFFFFF>$adm_step 6.</font>&nbsp;</span> $adm_add_features</h3>";
                echo "<form method=\"post\" action=\"configure.php\">";
                include("configure/inc_cfg_features.php");
                echo "<input type=\"hidden\" name=\"step\" value=\"\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
                echo "<input type=\"hidden\" name=\"save_level\" value =\"6\">";
        echo "<a href=\"javascript:document.forms[0].step.value='4';document.forms[0].submit();\">&lt;--  <b>$adm_back</b></a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='5';document.forms[0].submit();\">-<b>$adm_test</b>-</a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='6';document.forms[0].submit();\"><b>$adm_next</b> --&gt;</a>";
                echo "</form>";
        break;
        case 6:
                echo "<h3><span style=\"{background-color:#666666;};\">&nbsp;<font color=#FFFFFF>$adm_step 7.</font>&nbsp;</span> $adm_look_and_feel</h3>";
                echo "<form method=\"post\" action=\"configure.php\">";
                include("configure/inc_cfg_lookfeel.php");
                echo "<input type=\"hidden\" name=\"step\" value=\"\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
                echo "<input type=\"hidden\" name=\"save_level\" value =\"7\">";
        echo "<a href=\"javascript:document.forms[0].step.value='5';document.forms[0].submit();\">&lt;--  <b>$adm_back</b></a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='6';document.forms[0].submit();\">-<b>$adm_test</b>-</a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='7';document.forms[0].submit();\"><b>$adm_next</b> --&gt;</a>";
                echo "</form>";
        break;

        case 7:
                echo "<h3><span style=\"{background-color:#666666;};\">&nbsp;<font color=#FFFFFF>$adm_step 8.</font>&nbsp;</span> $adm_check_access</h3>";
                echo "<form method=\"post\" action=\"configure.php\">";
                echo "<input type=\"hidden\" name=\"session\" value=\"$session\">\n";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">\n";
                //if (!isset($file_path)) $conf_file_path = dirname($conf_inc_config)."/";
                eval(implode("",file($data_path."/voc.conf")));
                echo "$adm_checking_data ($data_path):";
                $files_list = array ("converts.dat","voc.conf");
                if ($long_life_data_engine == "files") {
                        $files_list[] = "banlist.dat";
                        $files_list[] = "users.dat";
                        $files_list[] = "rooms.dat";
                        $files_list[] = "robotspeak.dat";
            //VOC++ addon
                        $files_list[] = "clans.dat";
                        $files_list[] = "clear.dat";
                        $files_list[] = "shamans_list.tmp";
                        $files_list[] = "similar_nicks.tmp";
                        $files_list[] = "userlist.tmp";
                        $files_list[] = "transactions.dat";
                        $files_list[] = "items.dat";
                        $files_list[] = "items_types.dat";
                        $files_list[] = "users/guardian.dat";

                        if ($impro_registration)
                                $files_list[] = "impro.dat";
                        if ($registration_mailconfirm)
                                $files_list[] = "regmail.dat";
                }
                if ($engine == "files") {
                        $files_list[] = "messages.dat";
                        $files_list[] = "who.dat";
                }
                if ($mess_stat==1) $files_list[] = "mess_stat.dat";
                for ($i=0;$i<count($files_list);$i++) {
                        echo "<br>";
                        $real_name = realpath($data_path.$files_list[$i]);
                        $can_write = is_writeable($real_name);
                        $is_file = is_file($real_name);
                        if ($real_name == "") echo "<b><font color=\"red\" size=\"+1\">$adm_cannot_detect ".$data_path.$files_list[$i]."</font></b><br>";
                        if (!$is_file) { echo "<b><font color=\"red\" size=\"+1\">$adm_not_found: $real_name</font></b><br>";$error=1; }
                        else echo "<b>$adm_found: $real_name</b><br>";
                        if  ($can_write)
                                echo  "$adm_writeable, <b>Ok</b>";
                        else {
                                echo "<b><font color=\"red\" size=\"+1\">$adm_webserver</font></b>";
                                $error = 1;
                        }
                        echo "<br>";
                }
                echo "<hr>";
                echo "<b>$adm_checking_subdir:</b>";
                $files_list = array();
                if ($long_life_data_engine == "files");
                        $files_list = array($data_path."/"."board",$data_path."/"."users");
                if ($web_indicator)
                        $files_list[] = $data_path."statuses";
                if ($logging_messages or $logging_ban)
                        $files_list[] = $data_path."logs";

        $files_list[] = $file_path."photos";
        $files_list[] = $file_path."top20";
        //VOC++ addon
        $files_list[] = $data_path."clans";
        $files_list[] = $file_path."items";

        $files_list[] = $data_path."private-board";
        $files_list[] = $data_path."user-viewed";
        $files_list[] = $data_path."private-board/groups";
        $files_list[] = $data_path."user-board";

        $files_list[] = $data_path."moder-board";
        $files_list[] = $file_path."clans-avatar";
        $files_list[] = $file_path."clans-logos";
        //VOC++ Guardian Addon
        if(is_file($data_path."engine/files/guardian.php")) {
                $files_list[] = $data_path."users/backup";
        }

                for ($i=0;$i<count($files_list);$i++)
                {
                        echo "<br>";
                        $real_name =realpath($files_list[$i]);
                        $can_write = is_writeable($real_name);
                        $is_file = is_dir($real_name);
                        if ($real_name == "") echo "<b><font color=\"red\" size=\"+1\">$adm_cannot_detect ".$files_list[$i]."</font></b><br>";
                        if (!$is_file) {echo "<b><font color=\"red\" size=\"+1\">$adm_not_found: $real_name </font></b><br>";$error=1;} else echo "<b>$adm_found:</b>";
                        echo "$real_name<br>";
                        if  ($can_write){
                                echo  "$adm_writeable, <b>OK</b>";
                                if (ini_get('safe_mode')) echo "<br> - $adm_safe_mode_note";
                        }
                        else
                        {
                                echo "<b><font color=\"red\" size=\"+1\">$adm_cannot_write_di</font></b> $adm_webserver";
                                $error = 1;
                        }
                        echo "<br><br>";
                }
                //if ($long_life_data_engine == "mysql" or $engine == "mysql") {
                        //checking for tables and creating if neccessary.
                        if (!mysql_connect($mysql_server, $mysql_user, $mysql_password)) {
                                echo "<b>$adm_mysql_error</b><br>";
                                $error = 1;
                        }
                        else
                                if (!mysql_select_db($mysql_db)) {
                                        echo "<b>!!!! $adm_mysql_error_db $mysql_db!!!!</b><br>";
                                        $error = 1;
                                }
           //        }
                //VOC++ smileys
                    /*
 CREATE TABLE smileys (
uid int(7) unsigned default '0',
s_name char(50) default NULL,
url char(200) default NULL,
ord tinyint(3) unsigned NOT NULL default '9',
KEY uid (uid,ord)
) TYPE=MyISAM;
            */

        echo "<hr>";
                if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."smileys (uid int, s_name varchar(50), url varchar(255),ord int, KEY uid (uid,ord))"))
                                echo "$adm_table ".$mysql_table_prefix."<b>smileys</b> $adm_table_cr_or_ex.<br>";
                else echo "$adm_table ".$mysql_table_prefix."<b>smileys</b> $adm_table_not_creat!!!<br>";


                if ($long_life_data_engine == "mysql") {
                        if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."rooms(id int primary key auto_increment, name varchar(100), topic text, design varchar(100), bot_name varchar(100),creator varchar(30), allowed_users text,allow_pics int,premoder int,lastaction int)"))
                                echo "<br>$adm_table ".$mysql_table_prefix."<b>rooms</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>rooms</b> $adm_table_not_creat!!!<br>";
                        $m_result = mysql_query("show fields from ".$mysql_table_prefix."rooms");
                        if (mysql_num_rows($m_result) == 5) {
                                if (mysql_query("alter table ".$mysql_table_prefix."rooms add (creator varchar(30), allowed_users text, allow_pics int, premoder int, lastaction int)"))
                                        echo "$adm_table ".$mysql_table_prefix."<b>rooms</b> $adm_table_updated.<br>";
                                else echo "$adm_update_of_table ".$mysql_table_prefix."<b>rooms</b> $adm_failed!<br>";
                        }
                        $m_result = mysql_query("select count(*) from ".$mysql_table_prefix."rooms");
                        $total_rooms = mysql_result($m_result, 0, 0);
                        if (!$total_rooms) mysql_query ("insert into ".$mysql_table_prefix."rooms (name, topic,design, bot_name) values ('Welcome','Welcome to the Voodoo chat','','W_Bot')");
                        mysql_free_result($m_result);


                        //dropping old tables -- for update

                        mysql_query("drop table ".$mysql_table_prefix."banlist");
                        mysql_query("drop table ".$mysql_table_prefix."who");
                        mysql_query("drop table ".$mysql_table_prefix."messages");

                        if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."banlist(who varchar(35), until int)"))
                                echo "$adm_table ".$mysql_table_prefix."<b>banlist</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>banlist</b> $adm_table_not_creat!!!<br>";
                        if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."users(id int primary key auto_increment, nick varchar(30), passwd varchar(50), user_class smallint, canon_nick varchar(50), user_info text, new_mails smallint, last_visit int, , registration_mail varchar(100))"))
                                echo "$adm_table ".$mysql_table_prefix."<b>users</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>users</b> $adm_table_not_creat!!!<br>";
                        //if it's an update, add registration_mail field:
                        $m_result = mysql_query("show fields from ".$mysql_table_prefix."users");
                        if (mysql_num_rows($m_result) == 8) {
                                //old, without regmail:
                                if (mysql_query("alter table ".$mysql_table_prefix."users add registration_mail char(100)"))
                                        echo "$adm_table <b>".$mysql_table_prefix."users</b> $adm_table_updated.";
                                else echo "$adm_update_of_table <b>".$mysql_table_prefix."users</b> adm_failed!";
                        }


                        if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."board(id int primary key auto_increment, user_id int, status tinyint, from_nick varchar(30), from_uid int, subject varchar(255), body text, at_date int)"))
                                echo "$adm_table ".$mysql_table_prefix."<b>board</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>board</b> $adm_table_not_creat!!!<br>";
                        if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."robotspeak(phrase char(250), answer text, prob int)"))
                                echo "$adm_table ".$mysql_table_prefix."<b>robotspeak</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>robotspeak</b> $adm_table_not_creat!!!<br>";

                        //if ($impro_registration) {
                                if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."impro(time int, id varchar(32), code int)"))
                                        echo "$adm_table ".$mysql_table_prefix."<b>impro</b> $adm_table_cr_or_ex.<br>";
                                else echo "$adm_table ".$mysql_table_prefix."<b>impro</b> $adm_table_not_creat!!!<br>";
                        //}
                        //if ($registration_mailconfirm) {
                                if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."regmail(time int, nickname varchar(100), password varchar(100), email varchar(200), regkey varchar(32), canon_view varchar(100))"))
                                        echo "$adm_table ".$mysql_table_prefix."<b>regmail</b> $adm_table_cr_or_ex.<br>";
                                else echo "$adm_table ".$mysql_table_prefix."<b>regmail</b> $adm_table_not_creat!!!<br>";
                        //}
                        if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."password_reminder(userid int, nick char(30), code char(32), creation_time int)"))
                                echo "$adm_table ".$mysql_table_prefix."<b>password_reminder</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>password_reminder</b> $adm_table_not_creat!!!<br>";

                }
                if ($engine == "mysql") {
                        if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."who (user_name char(30), session char(32) not null, time int, sex tinyint, photo char(30), user_id int, tail_id int, remote_addr char(15), user_status int, last_action int, room int, ignor text, canon_nick char(60), chat_type char(10), user_lang char(10), htmlnick text,priv_tailid int,cookie char(32),browserhash char(32),user_class int,design char(20), unique (session), index(user_name, canon_nick))"))
                                echo "$adm_table ".$mysql_table_prefix."<b>who</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>who</b> $adm_table_not_creat!!!<br>";
                if (mysql_query("create table IF NOT EXISTS ".$mysql_table_prefix."messages (id int primary key auto_increment, room int, time int, fromnick text, fromwotags char(100),fromsession char(32), fromid int, fromavatar char(255), tonick char(32), tosession char(32),toid int,body text)"))
                                echo "$adm_table ".$mysql_table_prefix."<b>messages</b> $adm_table_cr_or_ex.<br>";
                        else echo "$adm_table ".$mysql_table_prefix."<b>messages</b> $adm_table_not_creat!!!<br>";
                }

                echo "<input type=\"hidden\" name=\"step\" value=\"\">";
                echo "<input type=\"hidden\" name=\"save_level\" value =\"8\">";
                echo "<br><br>";
        echo "<a href=\"javascript:document.forms[0].step.value='6';document.forms[0].submit();\">&lt;--  <b>$adm_back</b></a> | ";
                echo "<a href=\"javascript:document.forms[0].step.value='7';document.forms[0].submit();\">-<b>$adm_test</b>-</a> | ";

                if (!$error) echo "<a href=\"javascript:document.forms[0].step.value='8';document.forms[0].submit();\"><b>$adm_next</b> --&gt;</a>";;
                echo "</form>";
        break; //end of case 2



        case 8:
        //if it's not windows, and path is not started with X:/

        /*
         if ($file_path[2]!= ":") {
                        echo "<h3><span style=\"{background-color:#666666;};\">&nbsp;<font color=#FFFFFF>$adm_step 9.</font>&nbsp;</span> $adm_check_daemon</h3>";
                        echo "<form method=\"post\" action=\"configure.php\">";
                        echo "<input type=\"hidden\" name=\"session\" value=\"$session\">\n";
            echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">\n";

                        echo "$adm_checking_data:<p>";

            $real_name =realpath($data_path."/daemon/daemon");
                        $is_file = is_file($real_name);
                        if (!$is_file) { echo "<b><font color=\"red\" size=\"+1\">$adm_not_found:</font></b>";$error=1;} else echo "<b>$adm_found:</b>";
            echo "$real_name<br>";

                        $files_list = array("daemon.pid","daemon.log");
                        for ($i=0;$i<count($files_list);$i++) {
                                echo "<br>";
                                $real_name =realpath($data_path."/daemon/".$files_list[$i]);
                                $can_write = is_writeable($real_name);
                                $is_file = is_file($real_name);
                                if (!$is_file) { echo "<b><font color=\"red\" size=\"+1\">$adm_not_found:</font></b>";$error=1;} else echo "<b>$adm_found:</b>";
                                echo "$real_name<br>";
                                if  ($can_write) {
                                        echo  "$adm_writeable, <b>OK</b>";
                 }
                                else {
                                        echo "<b><font color=\"red\" size=\"+1\">$adm_webserver</font>";
                                        $error = 1;
                                }
                                echo "<br>";
                        }
                        echo "<br>";
            //Commented for VOC++
            // No perl daemon support for now

                        $real_name =realpath($data_path."/daemon/daemon.pl");
                        $can_write = is_writeable($real_name);
                        $is_file = is_file($real_name);
                        if (!$is_file) {echo "<b><font color=\"red\" size=\"+1\">$adm_not_found:</font></b>";$error=1;} else echo "<b>$adm_found:</b>";
                        echo "$real_name<br>";
                        if  (is_executable($real_name))
                                echo  "Executable, <b>Ok</b>";
                        else {
                                echo "<b><font color=\"red\" size=\"+1\">Cannot Execute this file</font></b>! Please, change file attributes (you can do it with your FTP-client)";
                                $error = 1;
                        }
                        echo "<hr>";

                        if (!ini_get('safe_mode')){
                                echo "Checking for neccessary Perl-modules (<b>PHP in the safe mode</b> couldn't determine it properly in most cases)<br>\n";

                                $need_modules = array("POSIX","IO::Socket","IO::Select","Socket","Fcntl","Time::localtime");
                                eval(implode("",file($data_path."/voc.conf")));
                                if ($long_life_data_engine == "mysql" or $engine == "mysql") $need_modules[] = "DBI";
                                for ($i=0;$i<count($need_modules);$i++) {
                                        echo "<br>".$need_modules[$i].":<br>";
                                        passthru("perl -e 'use ".$need_modules[$i].";' 2>&1",$res);
                                        if  (!$res) echo "<b>Ok</b>";
                                        else {
                                                echo "<br><b><font color=\"red\" size=\"+1\">FAILED!!!</font></b> you need to install this Perl-module!";
                                        }
                                        echo "<br>\n";
                                }
                        }else{echo "because you're using PHP in safe mode, I cannot check whether all neccessary perl libraries is installed or not. Try to start daemon from shell or from the daemon_admin.pl script<br><br>";}

                } */

                echo "<input type=\"hidden\" name=\"step\" value=\"\">";
                echo "<input type=\"hidden\" name=\"save_level\" value =\"9\">";
                echo "<br><br>";

        echo "<a href=\"javascript:document.forms[0].step.value='7';document.forms[0].submit();\">&lt;--  <b>$adm_back</b></a> | ";

                if (!$error)
                        echo "$adm_conf_success ".
                                " <a href=\"".$chat_url."\">$chat_url</a>.";
                echo "</form>";
        break;//end of case 3

}//end of switch
?>

</td></tr></table>
</body>
</html>