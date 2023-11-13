<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$board_message = array();

$fp = fopen($data_path."board/".floor($is_regist/2000)."/".$is_regist.".msg","a+b");
if (!$fp) trigger_error("Could not open mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
	trigger_error("Could not LOCK mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);
$fs = filesize($data_path."board/".floor($is_regist/2000)."/".$is_regist.".msg");
#first string contains the number of new messages
$new_mes = str_replace("\t\n","",fgets($fp, 100));
#probably it's needed for windows version:
$new_mes = str_replace("\r","",$new_mes);
$to_write = "";
while(!feof($fp))
	$board_content = str_replace("\r", "", fread($fp,$fs));

foreach(explode("\t\n",$board_content)as $message) {
	if ($message!=""){
		list($t_id, $status, $from_nick, $from_id, $at_date, $subject, $body) = explode("\t",$message);
		if ($t_id==$id) {
			if ($status == 1) {$status = 0; $new_mes--;}
			if ($board_operation == "reply") $status = 2;
			if ($subject == "") $subject = $w_no_subject;
			$board_message["id"] = $id;
			$board_message["status"] = $w_stat[$status];
			$board_message["from"] = $from_nick;
			$board_message["from_id"] = $from_id;
			$board_message["subject"] = $subject;
			$board_message["date"] = date($w_date_format,$at_date);
			$board_message["body"] = $body;
		}
		if ($t_id) $to_write .= "$t_id\t$status\t$from_nick\t$from_id\t$at_date\t$subject\t$body\t\n";
	}
}
ftruncate($fp,0);
fwrite($fp,$new_mes."\t\n");
fwrite($fp, $to_write);
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
?>