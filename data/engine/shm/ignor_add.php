<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$users = array();
$ignored_list = array();
$old_users = array();

$users_sh_id = shmop_open($shm_users_id, "c", 0644, 100000);
$users_sem_id = sem_get($shm_users_id);
sem_acquire($users_sem_id);
$var = shmop_read($users_sh_id, 0,shmop_size($users_sh_id));
$var = substr($var,0,strpos($var, "\0"));
if ($var != "") $old_users = explode("\n",$var);
if (!is_array($old_users)) $old_users = array();

for ($ia_i=0; $ia_i<count($old_users); $ia_i++) {
	if (strlen($old_users[$ia_i])<7) continue;
	$user_array  = explode("\t",trim($old_users[$ia_i]), USER_TOTALFIELDS);
	if ($user_array[USER_SESSION] == $session) {
		$user_array[USER_TIME] = time();
		$ignored_list = explode(",",$user_array[USER_IGNORLIST]);
		if (!in_array($add_to_ignor,$ignored_list) && count($ignored_list) < 50)
			$ignored_list[count($ignored_list)] = $add_to_ignor;
		$user_array[USER_IGNORLIST] = implode(",",$ignored_list);
		$cu_array = $user_array;
		$old_users[$ia_i] = implode("\t",$user_array);
	}
	$users[] = $old_users[$ia_i];
}
if (!is_array($users)) $users = array();
shmop_write($users_sh_id, implode("\n",$users)."\0",0);
sem_release($users_sem_id);
shmop_close($users_sh_id);
?>