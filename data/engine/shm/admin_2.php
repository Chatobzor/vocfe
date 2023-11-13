<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
unset($users);
$users = array();
$old_users = array();
$a2_j=0;

$users_sh_id = shmop_open($shm_users_id, "c", 0644, 100000);
$users_sem_id = sem_get($shm_users_id);
sem_acquire($users_sem_id);
$var = shmop_read($users_sh_id, 0,shmop_size($users_sh_id));
$var = substr($var,0,strpos($var, "\0"));
if ($var != "") $old_users = explode("\n",$var);
if (!is_array($old_users)) $old_users = array();

for ($a2_i=0; $a2_i<count($old_users); $a2_i++) {
	$data = explode("\t", $old_users[$a2_i]);
	if (strcasecmp($data[0],$nameToBan)!=0)  {
		$users[$a2_j] = $old_users[$a2_i];
		$a2_j++;
	}
}
if (!is_array($users)) $users = array();
shmop_write($users_sh_id, implode("\n",$users)."\0",0);
sem_release($users_sem_id);
shmop_close($users_sh_id);
?>