<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

function premoder_approve($mesg_id) {
	global $data_path, $engine_path, $mess_stat, $messages_file, $flood_protection, $logging_messages, $flood_in_last, $flood_time;
	$mesg_list = array();
	$fp_mes = fopen($data_path."premoder.dat", "r+b");
	if (!$fp_mes) trigger_error("Could not messages.dat for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp_mes, LOCK_EX))
		trigger_error("Could not LOCK messages.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
		
	$messages_to_show = array();
	while($ttt=fgets($fp_mes, 16384)) {
		if (strlen($ttt)<7) continue;
		$ttt = trim($ttt);
		$mess_array = explode("\t",$ttt,MESG_TOTALFIELDS);
		if ($mess_array[MESG_TIME] > my_time()-7200){
			if ($mess_array[MESG_ID] == $mesg_id) {
				
				$messages_to_show[0] = $mess_array;
				$messages_to_show[0][MESG_ID] = 0;
			} else 
				$mesg_list[] = $mess_array;
		}
	}
	fseek($fp_mes, 0);
	$total_messages = count($mesg_list);
	for ($i=0; $i<$total_messages;$i++)
		fwrite($fp_mes, implode("\t",$mesg_list[$i])."\n");
	fflush($fp_mes);
	ftruncate($fp_mes,ftell($fp_mes));
	flock($fp_mes, LOCK_UN);
	fclose($fp_mes);
	if (count($messages_to_show) >0) {
		$error = "";
		$whisper = $mess_array[MESG_TO];
		include($engine_path."messages_put.php");
		if ($mess_stat == 1 && !$error) {
			$fp = fopen($data_path."mess_stat.dat", "a+");
			flock($fp, LOCK_EX);
			fseek($fp,0);
			$normal_messages = intval(str_replace("\n","",@fgets($fp,1024)));
			$private_messages = intval(str_replace("\n","",@fgets($fp,1024)));
			if ($whisper)$private_messages++;
				else $normal_messages++;
			ftruncate($fp,0);
			fwrite($fp,$normal_messages."\n".$private_messages);
			fflush($fp);
			flock($fp, LOCK_UN);
			fclose($fp);
		}
	}
	
	return $mesg_list;
}

function premoder_decline($mesg_id) {
	global $data_path;
	$mesg_list = array();
	$fp_mes = fopen($data_path."premoder.dat", "r+b");
	if (!$fp_mes) trigger_error("Could not messages.dat for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp_mes, LOCK_EX))
		trigger_error("Could not LOCK messages.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
		
	while($ttt=fgets($fp_mes, 16384)) {
		if (strlen($ttt)<7) continue;
		$ttt = trim($ttt);
		$mess_array = explode("\t",$ttt,MESG_TOTALFIELDS);
		if ($mess_array[MESG_TIME] > my_time()-7200 && $mess_array[MESG_ID] != $mesg_id)
			$mesg_list[] = $mess_array;
	}
	fseek($fp_mes, 0);
	$total_messages = count($mesg_list);
	for ($i=0; $i<$total_messages;$i++)
		fwrite($fp_mes, implode("\t",$mesg_list[$i])."\n");
	fflush($fp_mes);
	ftruncate($fp_mes,ftell($fp_mes));
	flock($fp_mes, LOCK_UN);
	fclose($fp_mes);
	return $mesg_list;
}

function premoder_get() {
	global $data_path;
	$mesg_list = array();
	$fp_mes = fopen($data_path."premoder.dat", "rb");
	if (!$fp_mes) trigger_error("Could not messages.dat for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp_mes, LOCK_EX))
		trigger_error("Could not LOCK messages.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
		
	//not rewriting here -- saving some cpu-time :)
	while($ttt=fgets($fp_mes, 16384)) {
		if (strlen($ttt)<7) continue;
		$ttt = trim($ttt);
		$mess_array = explode("\t",$ttt,MESG_TOTALFIELDS);
		if ($mess_array[MESG_TIME] > my_time()-7200)
			$mesg_list[] = $mess_array;
	}
	flock($fp_mes, LOCK_UN);
	fclose($fp_mes);
	return $mesg_list;

}

function premoder_add($messages_to_show) {
	if (is_array($messages_to_show) && count($messages_to_show) > 0){
		global $data_path;
		$fp_mes = fopen($data_path."premoder.dat", "r+b");
		if (!$fp_mes) trigger_error("Could not messages.dat for writing. Please, check permissions", E_USER_ERROR);
		if (!flock($fp_mes, LOCK_EX))
			trigger_error("Could not LOCK messages.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
	
		$last_id = 0;
		$messages = array();
		$already_sent = 0;
		while($ttt=fgets($fp_mes, 16384)) {
			if (strlen($ttt)<7) continue;
			$ttt = trim($ttt);
			$mess_array = explode("\t",$ttt,MESG_TOTALFIELDS);
			//2hours should be always enough for chat with VIP persons
			if ($mess_array[MESG_TIME] > my_time()-7200) {
				if ($mess_array[MESG_ID] >$last_id)
					$last_id = $mess_array[MESG_ID];
				//the message from user is always the first
				if (strcmp(	strip_tags(str_replace("<img","",str_replace(" ","", $mess_array[MESG_BODY]))), 
							strip_tags(str_replace("<img","",str_replace(" ","",$messages_to_show[0][MESG_BODY]))) ) == 0) 
					$already_sent = 1;
				$messages[] = $ttt;
			}
		}
		for ($i = 0; $i<count($messages_to_show);$i++) {
			
			$new_mess = ++$last_id;
			for($j=1;$j<MESG_TOTALFIELDS;$j++)
				$new_mess .= "\t".$messages_to_show[$i][$j];
			$messages[] = $new_mess;
		}
		$total_messages = count($messages);
		fseek($fp_mes, 0);
		for ($i=0; $i<$total_messages;$i++)
			fwrite($fp_mes, $messages[$i]."\n");
		fflush($fp_mes);
		ftruncate($fp_mes,ftell($fp_mes));
		flock($fp_mes, LOCK_UN);
		fclose($fp_mes);
	}
}

?>