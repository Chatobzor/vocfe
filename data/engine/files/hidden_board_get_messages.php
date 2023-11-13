<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$board_messages = array();

unset($already_viewed);
$fp        = fopen($data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view","a+b");
$i  = 0;

$already_viewed = array();

if($fp) {
    if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK mail-file ".$data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);

   while (!feof($fp)) {
           $buffer = fgets($fp, 4096);

       if(strlen(trim($buffer)) > 0) {
               list($t_group_id, $t_msg_id) = explode("\t", $buffer);
               $t_group_id                          = intval($t_group_id);
               $t_msg_id                    = intval($t_msg_id);
                  $already_viewed[$i]  = array("group_id" => $t_group_id, "msg_id" => $t_msg_id);
           $i++;
       }
   }

   if (!flock($fp, LOCK_UN));
   fclose($fp);
}



proceed_file($data_path."private-board/groups/all.msg", 5, 0);
if($cu_array[USER_CLASS] > 0)  proceed_file($data_path."private-board/groups/adminz.msg", 1, 1);
if($cu_array[USER_CUSTOMCLASS] == CST_PRIEST) proceed_file($data_path."private-board/groups/shamanz.msg", 2, 2);
if($cu_array[USER_CLANID] > 0) proceed_file($data_path."private-board/groups/clan_".$cu_array[USER_CLANID].".msg", 3, 6);
//genderz
if($cu_array[USER_GENDER] == GENDER_BOY) { $board_file = "boyz.msg"; $group_id = 3; }
else if ($cu_array[USER_GENDER] == GENDER_GIRL){ $board_file = "girlz.msg"; $group_id = 4; }
else if ($cu_array[USER_GENDER] == GENDER_THEY or intval($cu_array[USER_GENDER]) == 0 or intval($cu_array[USER_GENDER]) == -1) { $board_file = "they.msg"; $group_id = 5; }
if($board_file == "") { $board_file = "they.msg"; $group_id = 5; }
proceed_file($data_path."private-board/groups/$board_file", 4, $group_id);
//user messages
proceed_file($data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg", 0, -1);

function proceed_file($file, $type, $t_group_id) {
global  $board_messages, $is_regist, $data_path, $w_date_format, $already_viewed, $w_stat, $w_no_subject;

if ($is_regist >0) {

        if($type == 0) {
            if(!@is_dir($data_path."private-board/".floor($is_regist/2000)))
                if (ini_get('safe_mode'))
                        trigger_error("Your PHP works in SAFE MODE, please create directory ".$data_path."private-board/".floor($is_regist/2000),E_USER_ERROR);
                else
                        mkdir($data_path."private-board/".floor($is_regist/2000),0777);
    }

        $fp = fopen($file,"a+b");
        if (!$fp)  trigger_error("Could not open mail-file $file for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                        trigger_error("Could not LOCK mail-file $file. Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);
        $fs = filesize($file);
        //first string contains the number of new messages
        $new_mes = fgets($fp, 100);
        $i = 0;
        while(!feof($fp))
                $board_content = str_replace("\r", "", fread($fp,$fs));

        foreach(explode("\t\n",$board_content)as $message) {
                if ($message!=""){
                        list($id, $status, $from_nick, $from_id, $at_date, $subject, $body) = explode("\t",$message);
                        if ($id) {
                    if($t_group_id > -1) {

                   $isNew = true;
                               for($j = 0; $j < count($already_viewed); $j++) {
                                           if($already_viewed[$j]["group_id"] == $t_group_id and $already_viewed[$j]["msg_id"] == $id) {
                                                   $isNew = false; break;
                                           }
                                       }

                               if($isNew) {
                     if ($subject == "") $subject = $w_no_subject;
                                        $board_messages[$type][$i]["id"] = $id;
                                        //$board_messages[$type][$i]["status"] = "[".$status."]";//$w_stat[$status];
                                        $board_messages[$type][$i]["from"] = $from_nick;
                                        $board_messages[$type][$i]["from_id"] = $from_id;
                                        if ($subject == "") $subject = $w_no_subject;
                                        $board_messages[$type][$i]["subject"] = $subject;
                                        $board_messages[$type][$i]["date"] = date($w_date_format,$at_date);
                                        $board_messages[$type][$i]["body"] = $body;
                        $i++;
                   }
                }
                else {
                        if ($subject == "") $subject = $w_no_subject;
                                        $board_messages[$type][$i]["id"] = $id;
                                        $board_messages[$type][$i]["status"] = $w_stat[intval($status)];
                                        $board_messages[$type][$i]["from"] = $from_nick;
                                        $board_messages[$type][$i]["from_id"] = $from_id;
                                        if ($subject == "") $subject = $w_no_subject;
                                        $board_messages[$type][$i]["subject"] = $subject;
                                        $board_messages[$type][$i]["date"] = date($w_date_format,$at_date);
                                        $board_messages[$type][$i]["body"] = $body;
                        $i++;
                }
                        }
                }
        }
        if (!flock($fp, LOCK_UN));
        fclose($fp);
}

}
?>