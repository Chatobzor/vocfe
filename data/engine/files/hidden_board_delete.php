<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if (is_array($mess_to_del) and $is_regist) {
	$fp = fopen($data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg","a+b");
	if (!$fp) trigger_error("Could not open mail-file ".$data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp, LOCK_EX))
		trigger_error("Could not LOCK mail-file ".$data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg. Do you use Win 95/98/Me?", E_USER_WARNING);
	fseek($fp,0);
	$fs = filesize($data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg");
	$new_mes = str_replace("\t\n","",fgets($fp, 100));
	$to_write = "";
	while(!feof($fp))
		$board_content = str_replace("\r", "", fread($fp,$fs));

	foreach(explode("\t\n",$board_content) as $message) {
		if ($message!=""){
			list($t_id, $status, $from_nick, $from_id, $at_date, $subject, $body) = explode("\t",$message);
			if ($t_id)
				if (!in_array($t_id,$mess_to_del))
					$to_write .= "$t_id\t$status\t$from_nick\t$from_id\t$at_date\t$subject\t$body\t\n";
				else if ($status == 1) $new_mes--;
		}
	}

	ftruncate($fp,0);
	fwrite($fp,$new_mes."\t\n");
	fwrite($fp, $to_write);
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>