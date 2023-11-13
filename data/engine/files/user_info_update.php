<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$fp = fopen ($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user", "a+b");
if (!$fp) trigger_error("Could not open users/".floor($is_regist/2000)."/".$is_regist.".user for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK file. Do you use Win 95/98/Me?", E_USER_WARNING);

$ser_str = serialize($current_user);

if(is_object($current_user) and strlen($ser_str) > 100) {
   ftruncate($fp, 0);
   fwrite($fp, $ser_str);
   fflush($fp);
}
flock($fp, LOCK_UN);
fclose($fp);

if(isset($User_UpdatePassword)) {
if($User_UpdatePassword) {
$users = "";

$fp_uLock = fopen($data_path."users/usersdat.lock", "w+b");
if($fp_uLock) {        if(!flock($fp_uLock, LOCK_EX)) die("Cannot lock ".$data_path."users/usersdat.lock -- please contact the Administrator."); ;
}
else die("Cannot create ".$data_path."users/usersdat.lock");

$fp = fopen($user_data_file, "ab+");
if (!$fp) trigger_error("Could not open users.dat for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK users.dat file. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);
$ii=0;
while ($data = fgets($fp, 4096)) {
        $u_data  = explode("\t",str_replace("\r","",str_replace("\n","",$data)));
        if ($u_data[0] == $is_regist) {
                $u_data[2] = $current_user->password;
                $u_data[3] = $current_user->user_class;
        }
        $users .= implode("\t",$u_data) . "\n";
}

ftruncate($fp,0);
fwrite($fp,$users);
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
unset($users);
$info_message .= $w_succ_updated."<br>";

$fp_uLock = fopen($data_path."users/usersdat.lock", "w+b");
if($fp_uLock) {        if(!flock($fp_uLock, LOCK_EX)) die("Cannot lock ".$data_path."users/usersdat.lock -- please contact the Administrator."); ;
}
else die("Cannot create ".$data_path."users/usersdat.lock");
}

}
?>