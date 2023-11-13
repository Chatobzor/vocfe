<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

$fp_uLock = fopen($data_path."users/usersdat.lock", "w+b");
if($fp_uLock) {	if(!flock($fp_uLock, LOCK_EX)) die("Cannot lock ".$data_path."users/usersdat.lock -- please contact the Administrator."); ;
}
else die("Cannot create ".$data_path."users/usersdat.lock");

set_variable("new_user_sex");
$new_user_sex = intval($new_user_sex);

if($new_user_sex < 0 or $new_user_sex > 2) $new_user_sex = 0;

$users = "";
$t_id = 0;
$canon_view = to_canon_nick($new_user_name);
$fp = fopen($user_data_file, "a+");

flock($fp, LOCK_EX);
fseek($fp,0);

//for the first user -- to avoid empty line at the start or users.dat
$last_record = " \n";
while($user = fgets($fp, 4096)) {
        $last_record = $user;
        $u_data = explode("\t",str_replace("\r","",str_replace("\n","",($user))));

        if (trim($canon_view) == trim($u_data[4])) {
                $error_text = str_replace("~", $new_user_name." (".$u_data[4].")", $w_already_registered)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
                include($file_path."designes/".$design."/error_page.php");
                flock($fp, LOCK_UN);
                fclose($fp);
                exit;
        }

        if ($u_data[0]>$t_id) $t_id = $u_data[0];
        #$users .= implode("\t",$u_data) . "\n";
}

$t_id=intval($t_id);
$t_id++;

//VOC++ Guardian
flock($fp, LOCK_UN);
fclose($fp);

require($ld_engine_path."guardian.php");

$guardRez = guardChat($t_id);
if(!$guardRez) {
                $error_text = "<b>VOC++ Guardian:</b> user database is corrupted. Please contact the chat administration.";
                include($file_path."designes/".$design."/error_page.php");
                exit;
}
if($guardRez == 2) {
                $error_text = "<b>VOC++ Guardian:</b> Пожалуйста, попробуйте еще раз / Please Try Again.";
                include($file_path."designes/".$design."/error_page.php");
                exit;
 }


$fp = fopen($user_data_file, "a+");
flock($fp, LOCK_EX);

if(!$registration_mailconfirm) $passwd1 = md5(" ");

$u_data = array($t_id,$new_user_name,$passwd1,"user", $canon_view, $new_user_mail);
$users = (substr($last_record,-1) == "\n" ) ? implode("\t",$u_data) : "\n".implode("\t",$u_data);
//$users[count($users)] = "".$t_id."\t".$new_user_name."\t".$passwd1."\tuser\t".$canon_view."\n";
#ftruncate($fp,0);

  $fp_wal = fopen($data_path."users/wal.dat", "ab");
  if (!$fp_wal) {
          AddToGuardianLog("WAL: не могу открыть ".$data_path."users/wal.dat"." для записи. Пожалуйста, проверьте привилегии.");
          trigger_error("WAL: не могу открыть ".$data_path."users/wal.dat"." для записи. Пожалуйста, проверьте привилегии.", E_USER_ERROR);
          exit;
  }
  if (!flock($fp_wal, LOCK_EX)) {
        AddToGuardianLog("WAL: Не могу использовать ".$data_path."users/wal.dat монопольно (flock error).");
        trigger_error("WAL: Не могу использовать ".$data_path."users/wal.dat монопольно (flock error).", E_USER_ERROR);
        exit;
  }
   fwrite($fp_wal, date("H:i:s d-m-Y")."\t".USERSDAT_WRITE_START."\tUSERSDAT_WRITE_START\n");

   if(fwrite($fp,$users) === FALSE) {
     AddToGuardianLog("Не могу записать данные в ".$data_path."users.dat"." Пожалуйста, проверьте привилегии.");
     trigger_error("Не могу записать данные в ".$data_path."users.dat"." Пожалуйста, проверьте привилегии.", E_USER_ERROR);
     exit;
   }

fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
unset($users);

// backup user file after
        if(copy($data_path."users.dat", $data_path."users/users.dat") === FALSE) {
           AddToGuardianLog("Не могу скопировать ".$data_path."users.dat"." в ".$data_path."users/users.dat Пожалуйста, проверьте привилегии.");
        }

        fwrite($fp_wal, date("H:i:s d-m-Y")."\t".USERSDAT_WRITE_END."\tUSERSDAT_WRITE_END\n");
        fwrite($fp_wal, date("H:i:s d-m-Y")."\t".WAL_END."\tWAL_END\n");
        flock($fp_wal, LOCK_UN);
        fclose($fp_wal);

include($file_path."inc_user_class.php");
$user = new User;
$user->nickname = $new_user_name;
$user->password = $passwd1;
$user->email    = $new_user_mail;
$user->show_group_1 = 1;
$user->show_group_2 = 1;
$user->registered_at = my_time();
$user->last_visit = my_time();
$user->sex = $new_user_sex;

$current_user   =  new User;
$current_user   =  $user;

$is_regist          = $t_id;
$registered_user    = $t_id;
$is_regist_complete = false;
$is_member          = false;
$exists             = 1;
$sex                = $new_user_sex;

if(!@is_dir($data_path."users/".floor($t_id/2000)))
        if (ini_get('safe_mode'))
                echo ("Your PHP works in SAFE MODE, please create directory data/users/".floor($t_id/2000));
        else
                mkdir($data_path."users/".floor($t_id/2000),0777);

include($ld_engine_path."user_info_update.php");

if(!@is_dir($data_path."board/".floor($t_id/2000)))
        if (ini_get('safe_mode'))
                trigger_error("Your PHP works in SAFE MODE, please create directory data/board/".floor($t_id/2000),E_USER_ERROR);
        else
                mkdir($data_path."board/".floor($t_id/2000),0777);
$fp = fopen($data_path."board/".floor($t_id/2000)."/".$t_id.".msg","w+b");
if (!$fp) trigger_error("Cannot open mail-file board/".floor($t_id/2000)."/".$t_id.".msg for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX)) trigger_error("Cannot LOCK mail-file. Do you use Win 95/98/Me?", E_USER_WARNING);
fwrite($fp,"0\t\n");
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if(!@is_dir($file_path."photos/".floor($t_id/2000)))
        if (ini_get('safe_mode'))
                trigger_error("Your PHP works in SAFE MODE, please create directory chat/photos/".floor($t_id/2000),E_USER_ERROR);
        else
                mkdir($file_path."photos/".floor($t_id/2000),0777);

$out_message =  str_replace("~", $new_user_name, $w_succesfull_reg);

flock($fp_uLock, LOCK_UN);
fclose($fp_uLock);
?>