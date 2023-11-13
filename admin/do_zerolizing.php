<?php include("check_session.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<?php
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<blockquote><span class=head><font color=Black>
<?php
$dummy = " ";
for($i = 0; $i < 128; $i++) $dummy .= "&nbsp;\n";
$curr  = 0;

@set_time_limit(0);

include("../inc_common.php");

set_variable("clear_points");
if(intval($clear_points)) $clear_points = 1;
else $clear_points = 0;

set_variable("clear_points_all");
if(intval($clear_points_all)) $clear_points_all = 1;
else $clear_points_all = 0;

set_variable("clear_credits");
if(intval($clear_credits)) $clear_credits = 1;
else $clear_credits = 0;

set_variable("clear_photo_reiting");
if(intval($clear_photo_reiting)) $clear_photo_reiting = 1;
else $clear_photo_reiting = 0;


include("../inc_user_class.php");
if ($long_life_data_engine == "mysql") {
        include_once($ld_engine_path."inc_connect.php");
}
include("../inc_to_canon_nick.php");
if ($long_life_data_engine == "files") {
    $users = array();
    $users = file($user_data_file);
    $pass = count($users) / 298;
    if($pass < 1) $pass = 1;

    echo "<script>parent.frames['wr'].reInitBar($pass, ".count($users).");</script>\n";

    $shamans_list = array();

    flush();
    ob_end_flush();

    $fp_x = fopen($data_path."similar_nicks.tmp", "wb");
    if (!$fp_x) trigger_error("Could not open ".$data_path."similar_nicks.tmp"." for writing. Please, check permissions", E_USER_ERROR);
    if (!flock($fp_x, LOCK_EX))
        trigger_error("Could not LOCK ".$data_path."similar_nicks.tmp file. Do you use Win 95/98/Me?", E_USER_WARNING);

        $prDone = 0;

        for ($i=0; $i<count($users);$i++)
        {
        echo " ";
               if(intval($i - $curr) >= intval($pass)) {
                    $curr = $i;
                    //echo $dummy;
                    echo "<script>parent.frames['wr'].incrCount();</script>\n";
                    ob_flush();
                }

                $user = str_replace("\n","",$users[$i]);
                list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user);
                $t_id = intval(trim($t_id));

                if($t_id < 1) continue;

                if(file_exists($data_path."users/".floor($t_id/2000)."/".$t_id.".user")) {
                        $current_user = unserialize(implode("",file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")));

                        if($clear_points) $current_user->points = 0;
                        if($clear_points_all) $current_user->online_time = 0;
                        if($clear_credits) $current_user->credits = 0;

                        if($clear_photo_reiting) {
                        	 $current_user->photo_reiting = 0;
                        	 $current_user->photo_voted   = array();
                        	 $current_user->photo_voted_mark = array();
                        }


                        $is_regist = $t_id;

                        include($ld_engine_path."user_info_update.php");


                        if($fp_x) {
                                $tov = 0;
                                $prDone++;
                                fwrite($fp_x, $t_id."\t".$t_nickname."\t".$t_password."\t".$current_user->email."\t".$current_user->IP."\t".$current_user->browser_hash."\t".$current_user->cookie_hash."\t".$current_user->points."\t".$current_user->online_time."\t".$current_user->credits."\t".$tov."\n");
                        }
               }

      }
    fflush($fp_x);
    flock($fp_x, LOCK_UN);
    fclose($fp_x);
    echo "<script>parent.frames['wr'].finish();</script>";
    echo "<script>alert('$adm_gen_similar_table_ok! ( $prDone /".count($users).")');</script>";
} else if ($long_life_data_engine == "mysql") {
        $m_result = mysql_query("select id, nick from ".$mysql_table_prefix."users") or die("database error<br>".mysql_error());
        while($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
                $canon_nick = addslashes(to_canon_nick($row[1]));
                mysql_query("update ".$mysql_table_prefix."users set canon_nick='".$canon_nick."' where id=".$row[0]) or die("database error<br>".mysql_error());
        }
        mysql_free_result($m_result);
}
?>
</font></blockquote>
</td></tr></table></center>
</body>
</html>