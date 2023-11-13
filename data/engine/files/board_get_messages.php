<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$board_messages = array();

$fp = fopen($data_path."board/".floor($is_regist/2000)."/".$is_regist.".msg","a+b");
if (!$fp) trigger_error("Could not open mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
                trigger_error("Could not LOCK mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg. Do you use Win 95/98/Me?", E_USER_WARNING);
rewind($fp);
fseek($fp,0);
$fs = filesize($data_path."board/".floor($is_regist/2000)."/".$is_regist.".msg");
//first string contains the number of new messages
$new_mes = fgets($fp, 100);
$i = 0;
while(!feof($fp))
        $board_content = str_replace("\r", "", fread($fp,$fs));

foreach(explode("\t\n",$board_content)as $message) {
        if ($message!=""){
                list($id, $status, $from_nick, $from_id, $at_date, $subject, $body) = explode("\t",$message);
                if ($id) {
                        if ($subject == "") $subject = $w_no_subject;
                        $board_messages[$i]["id"] = $id;
                        $board_messages[$i]["status"] = $w_stat[$status];
                        $board_messages[$i]["from"] = $from_nick;
                        $board_messages[$i]["from_id"] = $from_id;
                        if ($subject == "") $subject = $w_no_subject;
                        $board_messages[$i]["subject"] = $subject;
                        $board_messages[$i]["date"] = date($w_date_format,$at_date);
                        $board_messages[$i]["body"] = $body;
                        $i++;
                }
        }
}
if (!flock($fp, LOCK_UN));
fclose($fp);
?>