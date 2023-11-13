<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if (!isset($error_text)) $error_text = "";
if(count($messages_to_show)) {
	$is_flood = 0;
	$messages = array();
	$newMessages = array();
	$old_messages = array();
	$msg_sh_id = shmop_open($shm_mess_id, "c", 0644, 100000);
	$msg_sem_id = sem_get($shm_mess_id);
	sem_acquire($msg_sem_id);
	$var = shmop_read($msg_sh_id, 0,shmop_size($msg_sh_id));
	$var = substr($var,0,strpos($var, "\0"));

	if ($var != "") $old_messages = explode("\n",$var);

	$last_id = 0;
	$total_messages = count($old_messages);

	$last_id = 0;
	for ($mes_count = 0;$mes_count<$total_messages;$mes_count++) {
		if (strlen($old_messages[$mes_count])<7) continue;
		list($test_last_id, $mess_stuff) = explode("\t",$old_messages[$mes_count],2);
		if (is_numeric($test_last_id)) {
			$messages[] = $old_messages[$mes_count];
			$last_id = $test_last_id+1;
		}
	}
	unset($old_messages);
	$total_messages = count($messages);

	if ($flood_protection) {
		$flood_start = (($total_messages - $flood_in_last)>0) ? ($total_messages-$flood_in_last):0;
		for ($i=$flood_start;$i<$total_messages;$i++) {
			if ($is_flood) break;
			$mess_array = explode("\t",$messages[$i],MESG_TOTALFIELDS);
			if (strcmp($mess_array[MESG_FROMWOTAGS],$messages_to_show[0][MESG_FROMWOTAGS])==0 || 
					strcmp($mess_array[MESG_FROMSESSION],$messages_to_show[0][MESG_FROMSESSION]) == 0) {
				if ($mess_array[MESG_TIME]+$flood_time > my_time()) {
					$is_flood = 1;
					$error = 1;
					$error_text .= $w_flood."<br>\n";
					break;
				}
				for ($j=0;$j<count($messages_to_show);$j++) {
					if ("===".strip_tags(str_replace("<img","",str_replace(" ","", $mess_array[MESG_BODY]))) == "===".strip_tags(str_replace("<img","",str_replace(" ","",$messages_to_show[$j][MESG_BODY])))) {
						$is_flood = 1;
						$error = 1;
						$error_text .= $w_flood."<br>\n";
						break;
					}
				}
			}
		}
	}
	if (!$is_flood) {
		for ($i=0;$i<count($messages_to_show);$i++) {
			$new_mess = $last_id;
			for($j=1;$j<MESG_TOTALFIELDS;$j++)
				$new_mess .= "\t".$messages_to_show[$i][$j];
			$messages[] = $new_mess;
			$last_id++;
		}
		if ($logging_messages) {
			include_once($data_path."engine/files/log_message.php"); 
			log_message();
		}
	}
	$total_messages = count($messages);
	$start_at = ($total_messages > 40)? ($total_messages-40) : 0;

	for ($mp_i=$start_at; $mp_i<$total_messages;$mp_i++)
		$newMessages[] = $messages[$mp_i];

	shmop_write($msg_sh_id, implode("\n",$newMessages)."\0",0);
	sem_release($msg_sem_id);
	shmop_close($msg_sh_id);
}
?>