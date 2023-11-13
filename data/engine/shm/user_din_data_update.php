<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if($session == "") $session = "0";
if(!is_string($session)) $session = "0";

if(!defined("_CMP_")):
define("_CMP_",1);
function cmp($a, $b) {
	return strcmp(strtoupper($a), strtoupper($b));
}
endif;
$users = array();
$old_users = array();

$users_sh_id = shmop_open($shm_users_id, "c", 0644, 100000);
$users_sem_id = sem_get($shm_users_id);
sem_acquire($users_sem_id);
$var = shmop_read($users_sh_id, 0,shmop_size($users_sh_id));
$var = substr($var,0,strpos($var, "\0"));
if ($var != "") $old_users = explode("\n",$var);
if (!is_array($old_users)) $old_users = array();

$exists = 0;
$is_regist = 0;
$user_name = "";
for ($uddu_i=0; $uddu_i<count($old_users); $uddu_i++) {
	if (strlen($old_users[$uddu_i])<7) continue;
	$user_array  = explode("\t",trim($old_users[$uddu_i]), USER_TOTALFIELDS);
	if ($user_array[USER_SESSION] == $session) {
		for($j=0;$j<count($fields_to_update);$j++)
			$user_array[$fields_to_update[$j][0]] = $fields_to_update[$j][1];
		$user_name = $user_array[USER_NICKNAME];
		$user_array[USER_TIME] = time();
		$exists = 1;
		$is_regist = $user_array[USER_REGID];
		$tail_num = $user_array[USER_TAILID];
		$user_ip = $user_array[USER_IP];
		$room_id = $user_array[USER_ROOM];
		if (!in_array($user_array[USER_SKIN], $designes)) $user_array[USER_SKIN] = $default_design;
		if ($rooms[$room_id]["design"] != "")
			$user_array[USER_SKIN] = $rooms[$room_id]["design"];
		$design = $user_array[USER_SKIN];
		$current_design = $chat_url."designes/".$design."/";
		$cu_array = $user_array;
		$old_users[$uddu_i] = implode("\t",$user_array);
	}
	$users[] = $old_users[$uddu_i];
}
if (count($users)) usort($users, "cmp");
else $users = array();
if (is_array($users)) usort($users, "cmp");
else $users = array();
shmop_write($users_sh_id, implode("\n",$users)."\0",0);
sem_release($users_sem_id);
shmop_close($users_sh_id);
?>