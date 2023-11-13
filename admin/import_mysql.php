<?php
      include("check_session.php");
      include("../inc_common.php");
      include("../inc_to_canon_nick.php");
      include("../inc_user_class.php");

      set_variable("import_mysql_server");
      set_variable("import_mysql_user");
      set_variable("import_mysql_password");
      set_variable("import_mysql_db");
      set_variable("import_mysql_table_prefix");

      define("C_DB_NAME", $import_mysql_db);
      define("C_DB_USER", $import_mysql_user);
      define("C_DB_PASS", $import_mysql_password);

      include("mysql.lib.php3");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<?php
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<blockquote><span class=head><font color=Black>
<?php
     $DbLink = new DB;
     $DbLink->setNewHost($import_mysql_server);

     $DbLink->query("SELECT id, nick,  passwd,  user_class,  canon_nick,  user_info,  last_visit,  registration_mail  FROM ".$import_mysql_table_prefix."users ORDER BY id ASC;");
         $MaxRecs = $DbLink->num_rows();

     $pass = $MaxRecs / 298;
          if($pass < 1) $pass = 1;

     echo "<script>parent.frames['wr'].reInitBar($pass, ".$MaxRecs.");</script>\n";

     $users = array();
     $users[] = "lalalalalala-dummy\n";

     flush();
     ob_end_flush();

     $current_user  = new User;

     $ai         = 0;
     $curr  = 0;

     for($i = 0; $i < $MaxRecs; $i++) {

                   echo " ";
                       if(intval($ai - $curr) >= intval($pass)) {
                        $curr = $ai;
                    echo "<script>parent.frames['wr'].incrCount();</script>\n";
                    ob_flush();
            }
            $ai++;

        list($id, $nick, $passwd, $user_class, $canon_nick, $user_info, $last_visit, $registration_mail) = $DbLink->next_record();
        $id                 = intval($id);
        $user_class         = intval($user_class);
        $last_visit         = intval($last_visit);

        $is_regist          = $id;

        $current_user       = unserialize($user_info);

        if (trim($current_user->chat_status) == "0") $current_user->chat_status = "";
        if (trim($current_user->clan_status) == "0") $current_user->clan_status = "";

        $current_user->registered = true;

        if(!@is_dir($data_path."users/".floor($is_regist/2000)))
                if (ini_get('safe_mode'))
                        trigger_error("Your PHP works in SAFE MODE, please create directory data/users/".floor($is_regist/2000),E_USER_ERROR);
                else
                        mkdir($data_path."users/".floor($is_regist/2000),0777);

                $fp = fopen ($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user", "wb");
                if (!$fp) trigger_error("Could not open users/".floor($is_regist/2000)."/".$is_regist.".user for writing. Please, check permissions", E_USER_ERROR);
                if (!flock($fp, LOCK_EX))
                        trigger_error("Could not LOCK file. Do you use Win 95/98/Me?", E_USER_WARNING);
                fwrite($fp,serialize($current_user));
                fflush($fp);
                flock($fp, LOCK_UN);
                fclose($fp);

        $t_id       = $id;
        $t_nickname = $current_user->nickname;
        $t_password = $current_user->password;
        $t_class    = $current_user->user_class;
        $t_canon    = to_canon_nick($t_nickname);
        $t_mail     = $current_user->email;

        // removing potential errors
                if(strlen(trim($t_nickname)) > 0) $users[] = "".$t_id."\t".$t_nickname."\t".$t_password."\t".$t_class."\t".$t_canon."\t".$t_mail."\n";
     }

    $fp = fopen($data_path."users.dat", "w+b");
        flock($fp, LOCK_EX);
        fwrite($fp,implode("",$users));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

    echo "<script>parent.frames['wr'].finish();</script>";
    echo "<script>alert('$adm_mysql_import: $MaxRecs OK')</script>";

    if(is_file($data_path."engine/files/guardian.php") and intval($vocplus_useguardian)) {
            echo "<script>parent.location.href='".$chat_url."admin/progress_frameset.php?session=$session&lang=$lang&operation=guardian';</script>";
    }
?>
</font></blockquote>
</td></tr></table></center>
</body>
</html>