<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$registered_user = 0;
$canon_view = to_canon_nick($user_name);
$fp = fopen($user_data_file, "rb");
if(!$fp) exit;

flock($fp, LOCK_EX);
fseek($fp,0);

$ii=0;
while ($data = fgets($fp, 4096)) {
        $user = trim($data);
        if (strlen($user)<5) continue;
        if (substr_count($user,"\t")<4) continue;
        list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user, 6);
        if ($canon_view == $t_canon) {
            $user_name = trim($t_nickname);
                $registered_user = $t_id;
                if (!isset($password)) {$password = "";}
                $user_password = (strlen($t_password) == 32) ? md5($password):$password;
                break;
        }
}

flock($fp, LOCK_UN);
fclose($fp);

$passSalt = md5($password);
$passSalt = $md5_salt.$passSalt;
$passSalt = md5($passSalt);

$sex = -1;
$bDay = 0;
$bMon = 0;
if ($registered_user) {
        $current_user       = unserialize(implode("",file($data_path."users/".floor($registered_user/2000)."/".$registered_user.".user")));

        if ($current_user->registered){
                if($t_password != $user_password and $t_password != $passSalt) {
                        include($file_path."designes/".$design."/voc_password_required.php");
                        flock($fp, LOCK_UN);
                        fclose($fp);
                        exit;
                }
        }
        $is_regist_complete = $current_user->registered;
        $is_member          = $current_user->is_member;

        $bDay = $current_user->b_day;
        $bMon = $current_user->b_month;
        $sex = $current_user->sex;
        $current_user->last_visit = my_time();
        $htmlnick = $current_user->htmlnick;
        $fp = fopen ($data_path."users/".floor($registered_user/2000)."/".$registered_user.".user", "w");
        flock($fp, LOCK_EX);
        fwrite($fp,serialize($current_user));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
}

?>