<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

$fp_uLock = fopen($data_path."users/usersdat.lock", "w+b");
if($fp_uLock) {        if(!flock($fp_uLock, LOCK_EX)) die("Cannot lock ".$data_path."users/usersdat.lock -- please contact the Administrator."); ;
}
else die("Cannot create ".$data_path."users/usersdat.lock");

include($engine_path."users_get_list.php");

$users = "";
$t_id = 0;
$canon_view = to_canon_nick($new_user_name);
//if it's not from registration_mail;
if (!isset($new_user_mail)) $new_user_mail = "";
$already_on_mail = 0;
$fp = fopen($user_data_file, "a+");
flock($fp, LOCK_EX);
fseek($fp,0);
//for the first user -- to avoid empty line at the start or users.dat
$last_record = " \n";
$IsUserOnFile = false;
while($user = fgets($fp, 4096)) {
        $last_record = $user;
        $u_data = explode("\t",str_replace("\r","",str_replace("\n","",($user))));
        if (strcasecmp(trim($canon_view), trim($u_data[4])) == 0) {
             $IsUserOnFile = true;
             $t_id = intval($u_data[0]);
             break;
        }
        if (strcmp(strtolower($u_data[5]),strtolower($new_user_mail)) == 0)
                $already_on_mail++;
        if ($u_data[0]>$t_id)$t_id = $u_data[0];
        #$users .= implode("\t",$u_data) . "\n";
}
if ($registration_mailconfirm && ($already_on_mail >= $max_per_mail)) {
        $error_text = $w_mail_used."<br>".str_replace("~", $max_per_mail, $w_max_per_mail)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

$t_id=intval($t_id);

if($IsUserOnFile) {
   flock($fp, LOCK_UN);
   fclose($fp);

   include($file_path."inc_user_class.php");
   $current_user = new User;
   $current_user = unserialize(implode("",file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")));

   $is_regist = $t_id;

   if($current_user->registered) {
                $error_text = str_replace("~", $new_user_name." (".$u_data[4].")", $w_already_registered)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";

                            include($file_path."designes/".$design."/error_page.php");
                exit;
   }

   $User_UpdatePassword      = true;
   $current_user->registered = true;
   $current_user->password   = $passwd1;

   include($ld_engine_path."user_info_update.php");

   $out_message =  str_replace("~", $new_user_name, $w_succesfull_reg);

   if (strtolower(trim($user_name)) == strtolower(trim($new_user_name))) {
        $fields_to_update[0][0] = USER_REGISTERED;
        $fields_to_update[0][1] = 1;
        include($engine_path."user_din_data_update.php");
        $out_message .= "<br><a href=\"user_info.php?session=$session\">$w_about_me</a>";
   }

   include($file_path."designes/".$design."/common_body_start.php");
   echo  $out_message;
   include($file_path."designes/".$design."/common_body_end.php");

   exit;
}
else {
$t_id++;

//VOC++ Guardian
flock($fp, LOCK_UN);
fclose($fp);

require($ld_engine_path."guardian.php");

$guardRez = guardChat($t_id);
if(!$guardRez) {
                $error_text = "<b>VOC++ Guardian:</b> user database is corrupted. Please contact the chat administration.";
                include($file_path."designes/".$design."/error_page.php");
                flock($fp, LOCK_UN);
                fclose($fp);
                exit;
}
if($guardRez == 2) {
                $error_text = "<b>VOC++ Guardian:</b> Пожалуйста, попробуйте еще раз / Please Try Again.";
                include($file_path."designes/".$design."/error_page.php");
                flock($fp, LOCK_UN);
                fclose($fp);
                exit;
}


$fp = fopen($user_data_file, "a+");
flock($fp, LOCK_EX);

$u_data = array($t_id,$new_user_name,$passwd1,"user",$canon_view, $new_user_mail);
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
$user->registered = true;
$user->show_group_1 = 1;
$user->show_group_2 = 1;
$user->registered_at = my_time();
$user->last_visit = my_time();
$user->last_actiontime = my_time();

$new_user_sex = intval($new_user_sex);
if($new_user_sex < 0) $new_user_sex = 0;
if($new_user_sex > 2) $new_user_sex = 0;

$user->sex = $new_user_sex;

if($ref_id > 0) {
   if(file_exists($data_path."users/".floor($ref_id/2000)."/".$ref_id.".user")) {
      $old_reg   = $t_id;
      $is_regist = $ref_id;
      include($ld_engine_path."users_get_object.php");

      $user->reffered_by      = $is_regist;
      $user->reffered_by_nick = $current_user->nickname;

      $current_user->ref_arr[] = array("id" => $old_reg, "nick" => $user->nickname);

      include($ld_engine_path."user_info_update.php");

      $is_regist              =  $old_reg;
      $current_user           =  $user;
   }
}


if(!@is_dir($data_path."users/".floor($t_id/2000)))
        if (ini_get('safe_mode'))
                trigger_error("Your PHP works in SAFE MODE, please create directory data/users/".floor($t_id/2000),E_USER_ERROR);
        else
                mkdir($data_path."users/".floor($t_id/2000),0777);

$fp = fopen ($data_path."users/".floor($t_id/2000)."/".$t_id.".user", "w");
if (!$fp) trigger_error("Cannot open user-data file users/".floor($t_id/2000)."/".$t_id.".msg for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX)) trigger_error("Cannot LOCK user-data file. Do you use Win 95/98/Me?", E_USER_WARNING);
flock($fp, LOCK_EX);
fwrite($fp,serialize($user));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if(!@is_dir($data_path."board/".floor($t_id/2000)))
        if (ini_get('safe_mode'))
                trigger_error("Your PHP works in SAFE MODE, please create directory data/board/".floor($t_id/2000),E_USER_ERROR);
        else
                mkdir($data_path."board/".floor($t_id/2000),0777);
$fp = fopen($data_path."board/".floor($t_id/2000)."/".$t_id.".msg","w");
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
}

flock($fp_uLock, LOCK_UN);
fclose($fp_uLock);
?>