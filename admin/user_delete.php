<?php
include("check_session.php");
include("../inc_common.php");
include("../events.php");
include("header.php");
if (isset($user_ids))
{
        include($ld_engine_path."admin_work.php");
        users_delete($user_ids);
        for ($i=0;$i<count($user_ids);$i++)
        {
                echo "removing files for user id:".$user_ids[$i]."<br>";
                @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".big.jpg");
                 @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".big.jpeg");
                @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".big.gif");
                @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".jpg");
                @unlink($file_path."photos/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".gif");
                @unlink($data_path."board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".msg");
                @unlink($data_path."users/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".user");
        //VOC++
        @unlink($data_path."user-board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".contrib");
        @unlink($data_path."private-board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".msg");
        @unlink($data_path."moder-board/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".mod");
        @unlink($data_path."user-viewed/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".view");
        @unlink($data_path."user-privates/".floor($user_ids[$i]/2000)."/".$user_ids[$i].".msg");
        }
        //re-calibrating Guardian
        if(is_file($data_path."engine/files/guardian.php") and $vocplus_useguardian) {
                if($long_life_data_engine == "files") {
                    if(isset($users)) unset($users);
                $users = array();
                                $users = file($user_data_file);

                $MaxID = 0;
                                for ($i=0; $i<count($users);$i++){
                        $user = str_replace("\n","",$users[$i]);
                                        list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user);
                                // removing potential errors
                                        if(strlen(trim($t_nickname)) > 0) $users[$i] = "".$t_id."\t".$t_nickname."\t".$t_password."\t".$t_class."\t".$t_canon."\t".$t_mail."\n";
                                if($t_id > $MaxID) $MaxID = $t_id;
                                }

                                $fp = fopen($user_data_file, "w+b");
                                flock($fp, LOCK_EX);
                                fwrite($fp,implode("",$users));
                                fflush($fp);
                                flock($fp, LOCK_UN);
                                fclose($fp);

                $fp = fopen($data_path."users/guardian.dat", "w+b");
                                if($fp) {
                                    fwrite($fp, $MaxID);
                                fclose($fp);
                                copy($data_path."users.dat", $data_path."users/users.dat");
                               }
            }
        }
}

?>
<center><span class=head>User(s) has been deleted!</center>
</body></html>