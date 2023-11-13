<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$old_users = array();

$users_sh_id = shmop_open($shm_users_id, "c", 0644, 100000);
$users_sem_id = sem_get($shm_users_id);
sem_acquire($users_sem_id);
$var = shmop_read($users_sh_id, 0,shmop_size($users_sh_id));
$var = substr($var,0,strpos($var, "\0"));
sem_release($users_sem_id);
shmop_close($users_sh_id);

if ($var != "") $old_users = explode("\n",$var);
if (!is_array($old_users)) $old_users = array();
$exists = 0;
for ($users_i=0; $users_i<count($old_users); $users_i++) {
	if ($old_users[$users_i] == "") {continue;}
	$data = explode("\t", $old_users[$users_i]);
	if ($user_array[USER_REGID] == $is_regist) {
		$exists = 1;
		break;
	}
}
?>