<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$fp = fopen($data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg","a+b");
if ($fp) {
if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK mail-file ".$data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);
$new_board_messages = fgets($fp, 100);

if ($new_board_messages == "") $new_board_messages = "0";

//если нет файла
//$num = intval($num);
if (!flock($fp, LOCK_UN));
fclose($fp);
$percentage = round(filesize ($data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg") / $max_mailbox_size *100);
}
//checking groups cache
set_variable('sendgroup');
if($sendgroup == 'getcacheword') {
  echo '<!-- 5ffdae4358e0a8a7e70ae75fb9b26aa1 -->';
}
//load already readed group contributions
if(!@is_dir($data_path."user-viewed/".floor($is_regist/2000)))
if (ini_get('safe_mode'))
                trigger_error("Your PHP works in SAFE MODE, please create directory ".$data_path."user-viewed/".floor($is_regist/2000),E_USER_ERROR);
else
        mkdir($data_path."user-viewed/".floor($is_regist/2000),0777);

unset($already_viewed);
$fp                     = fopen($data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view","a+b");
$i = 0;

$already_viewed = array();

if($fp) {
    if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK mail-file ".$data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);

   while (!feof($fp)) {
           $buffer = fgets($fp, 4096);

       if(strlen(trim($buffer)) > 0) {
               list($t_group_id, $t_msg_id) = explode("\t", $buffer);
           $t_group_id                  = intval($t_group_id);
               $t_msg_id                = intval($t_msg_id);
           $already_viewed[$i]  = array("group_id" => $t_group_id, "msg_id" => $t_msg_id);
           $i++;
       }
   }

   if (!flock($fp, LOCK_UN));
   fclose($fp);
}


function proceed_messages_group_file($file, $group_id) {
global $already_viewed;
$fp = fopen($file, "a+b");
$new_mess = 0;

if($fp) {
    if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK mail-file $file. Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);

    //supress fake 'new'
    $buffer = fgets($fp, 4096);

    while (!feof($fp)) {
           $buffer = fgets($fp, 4096);
       if(strlen(trim($buffer)) > 0) {
               list($t_id, $status, $from_nick, $from_id, $at_date, $subject, $body) = explode("\t", $buffer);
           $t_id                        = intval($t_id);

               $isNew = true;
           for($i = 0; $i < count($already_viewed); $i++) {
                   if($already_viewed[$i]["group_id"] == $group_id and $already_viewed[$i]["msg_id"] == $t_id) {
                    $isNew = false; break;
                        }
                }

               if($isNew) { $new_mess++;  }
       }
   }

    if (!flock($fp, LOCK_UN));
    fclose($fp);
}

return($new_mess);
}


$group_new = 0;
//for ALL
$temp_new = proceed_messages_group_file($data_path."private-board/groups/all.msg", 0);
if($temp_new) $group_new += $temp_new;
//adminz
if($cu_array[USER_CLASS] > 0) {
        $temp_new = proceed_messages_group_file($data_path."private-board/groups/adminz.msg", 1);
        if($temp_new) $group_new += $temp_new;
}

//shamanz
if($cu_array[USER_CUSTOMCLASS] == CST_PRIEST) {
   $temp_new = proceed_messages_group_file($data_path."private-board/groups/shamanz.msg", 2);
   if($temp_new) $group_new += $temp_new;
}
//clanz
if($cu_array[USER_CLANID] > 0) {
   $temp_new = proceed_messages_group_file($data_path."private-board/groups/clan_".$cu_array[USER_CLANID].".msg", 6);
   if($temp_new) $group_new += $temp_new;
}

//genderz
if($cu_array[USER_GENDER] == GENDER_BOY) {
   $temp_new = proceed_messages_group_file($data_path."private-board/groups/boyz.msg", 3);
   if($temp_new) $group_new += $temp_new;
}
else if ($cu_array[USER_GENDER] == GENDER_GIRL) {
   $temp_new = proceed_messages_group_file($data_path."private-board/groups/girlz.msg", 4);
   if($temp_new) $group_new += $temp_new;
}
else if ($cu_array[USER_GENDER] == GENDER_THEY) {
   $temp_new = proceed_messages_group_file($data_path."private-board/groups/they.msg", 5);
   if($temp_new) $group_new += $temp_new;
}

$new_board_messages += intval($group_new);
?>