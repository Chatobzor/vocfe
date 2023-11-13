<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$moder_board_messages = array();

$fp = fopen($data_path."moder-board/".floor($is_regist/2000)."/".$is_regist.".mod","rb");
if ($fp) {
if (!flock($fp, LOCK_EX))
		trigger_error("Could not LOCK moder-board/".floor($is_regist/2000)."/".$is_regist.".mod. Do you use Win 95/98/Me?", E_USER_WARNING);
rewind($fp);
fseek($fp,0);
$fs = filesize($data_path."moder-board/".floor($is_regist/2000)."/".$is_regist.".mod");
//first string contains the number of new messages
$new_mes = fgets($fp, 100);
$i = 0;
while(!feof($fp))
	$board_content = str_replace("\r", "", fread($fp,$fs));

foreach(explode("\t\n",$board_content)as $message) {
	if ($message!=""){
		list($id, $from_nick, $at_date, $body) = explode("\t",$message);
		if ($id) {
			if ($subject == "") $subject = $w_no_subject;
			$moder_board_messages[$i]["id"] = $id;
			$moder_board_messages[$i]["from"] = $from_nick;
			$moder_board_messages[$i]["date"] = date($w_date_format,$at_date);
			$moder_board_messages[$i]["body"] = $body;
			$i++;
		}
	}
}
if (!flock($fp, LOCK_UN));
fclose($fp);
}
?>