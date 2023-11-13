<?php include("check_session.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<?php
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<blockquote><span class=head><font color=Black>
<?php
include("../inc_common.php");
include("../inc_to_canon_nick.php");
include("../inc_user_class.php");

    $dir_array  =  glob($data_path."users/*", GLOB_ONLYDIR);

    $total_files                 = 0;

    for($i = 0; $i < count($dir_array); $i++) {
        if(isset($files_array)) unset($files_array);
                $files_array = glob($dir_array[$i]."/*.user");
        $total_files = $total_files + count($files_array);
    }

    $pass = $total_files / 298;
    if($pass < 1) $pass = 1;

    echo "<script>parent.frames['wr'].reInitBar($pass, ".$total_files.");</script>\n";

    flush();
    ob_end_flush();

    $ai = 0;
    $users  = array();
    $users[]= "0\tdummystr\n";
    $users_arr = array();

    $clans                  = array();
    $clans_exist    = array();

    $a = 0;

           for($i = 0; $i < count($dir_array); $i++) {
                   if(isset($files_array)) unset($files_array);
        $files_array = glob($dir_array[$i]."/*.user");

            for($j = 0; $j < count($files_array); $j++) {
                echo " ";
                       if(intval($ai - $curr) >= intval($pass)) {
                        $curr = $ai;
                    echo "<script>parent.frames['wr'].incrCount();</script>\n";
                    ob_flush();
            }
            $ai++;

            $full_name = $files_array[$j];
            $full_name = str_replace($dir_array[$i]."/", "", $full_name);
            $full_name = str_replace(".user", "", $full_name);

            $is_regist = intval($full_name);

            include($data_path."engine/files/users_get_object.php");

            $t_id                 = $is_regist;
            $t_nickname = $current_user->nickname;
            $t_password        = $current_user->password;
            $t_class        = $current_user->user_class;
            $t_canon         = to_canon_nick($t_nickname);
            $t_mail                = $current_user->email;

            if($current_user->clan_id > 0 and strlen(trim($t_nickname)) > 0) {

               $is_regist_clan = $current_user->clan_id;

               $IsClanExist = false;
               for($a = 0; $a < count($clans_exist); $a++) {
                               if($clans_exist[$a] == $is_regist_clan) { $IsClanExist = true; break; }
               }

               if(!$IsClanExist) {
                       if(!is_file($data_path."clans/".floor($is_regist_clan/2000)."/".$is_regist_clan.".clan")) {
                                                $current_user->clan_id = 0;
                            include($data_path."engine/files/user_info_update.php");
                   }
                   else {
                               $clans_exist[] = $current_user->clan_id;
                               $clans[$a][] = array("nick" => $t_nickname, "id" => $is_regist);
                       }
               }
               else $clans[$a][] = array("nick" => $t_nickname, "id" => $is_regist);
            }

               // removing potential errors
               if(strlen(trim($t_nickname)) > 0) $users_arr[$t_id] =  $t_id."\t".$t_nickname."\t".$t_password."\t".$t_class."\t".$t_canon."\t".$t_mail."\n";
        }
        }

        ksort( $users_arr, SORT_NUMERIC);
        
         while (list($key, $val) = each($users_arr)) {
              $users[] = $val;
        }

        $fp = fopen($user_data_file, "w+b");
        flock($fp, LOCK_EX);
        fwrite($fp,implode("",$users));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

    //scanning clans
    $clans_list = array();
    $current_clan = new Clan;

    for($i = 0; $i < count($clans_exist); $i++) {
            $is_regist_clan = $clans_exist[$i];
        $current_clan = unserialize(implode("",file($data_path."clans/".floor($is_regist_clan/2000)."/".$is_regist_clan.".clan")));
        $clans_list[] = array("id" => $is_regist_clan, "name" => $current_clan->name);

        unset($current_clan->members);
        $current_clan->members = array();

        for($j=0; $j < count($clans[$i]); $j++) {
           $current_clan->members[] = array("nick" =>$clans[$i][$j]["nick"], "id" => $clans[$i][$j]["id"]);
        }
        include($data_path."engine/files/clan_info_update.php");
    }

    include($data_path."engine/files/clans_update_list.php");

    echo "<script>parent.frames['wr'].finish();</script>";


    if(is_file($data_path."engine/files/guardian.php") and intval($vocplus_useguardian)) {
            echo "<script>parent.location.href='".$chat_url."admin/progress_frameset.php?session=$session&lang=$lang&operation=guardian';</script>";
    }

if (!defined("_CMP_MID_")):
define("_CMP_MID_", 1);
function cmp_mid($a, $b)
{
   if (intval($a["id"]) == intval($b["id"])) {
       return 0;
   }
   return (intval($a["id"]) < intval($b["id"])) ? -1 : 1;
}
endif;
?>
</font></blockquote>
</td></tr></table></center>
</body>
</html>