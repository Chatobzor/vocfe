<?php
include("D:/Temp/xampp/htdocs/inc_common.php");
error_reporting(E_ALL);
include($file_path."inc_user_class.php");
include($file_path."events.php");
include($engine_path."admin_work.php");

@set_time_limit(0);

$current_user = new User;


$fp = fopen($user_data_file, "rb");
if(!$fp) exit;

flock($fp, LOCK_EX);
fseek($fp,0);

$user_ids = array();
$ii=0;

echo "Main cycle\n";
echo $user_data_file."<br>\n";


while ($data = fgets($fp, 4096)) {
        $user = trim($data);
        if (strlen($user)<5) continue;
        if (substr_count($user,"\t")<4) continue;
        list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user, 6);
        $t_id = intval($t_id);
        echo  $t_id.', ';

        fflush();
        ob_flush();

        if (is_file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")) {

       $current_user = unserialize(implode("",file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")));

           if($current_user->registered == true) {
                   if($current_user->last_visit < (my_time()-4*604800)) {
                      if($current_user->user_class   == 0
                         and $current_user->custom_class == 0
                         and ($current_user->points < 50000 or $current_user->online_time < 50000)) {
                          $user_ids[] = $t_id;
                         }
                   }

          } else {

                 if($current_user->last_visit < (my_time()-345600)) {
                       $user_ids[] = $t_id;
                 }
           }

        }
        else echo "No such file:".$data_path."users/".floor($t_id/2000)."/".$t_id.".user"."<br>\n";
        //debug
}
flock($fp, LOCK_UN);
fclose($fp);

echo "Done\n";
print_r($user_ids);
flush();
ob_flush();


users_delete($user_ids);
for ($i=0;$i<count($user_ids);$i++)  {
       echo "deleting: ".$user_ids[$i]."<br>";
       flush();
       ob_flush();

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

echo "<br>All Done!";
?>
