<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
unset($messages);
$messages = array();
$msg_sh_id = shmop_open($shm_mess_id, "c", 0644, 100000);
$msg_sem_id = sem_get($shm_mess_id);
sem_acquire($msg_sem_id);
$var = shmop_read($msg_sh_id, 0,shmop_size($msg_sh_id));
$var = substr($var,0,strpos($var, "\0"));
sem_release($msg_sem_id);
shmop_close($msg_sh_id);

if ($var != "") $messages = explode("\n",$var);


if (!is_array($messages)) $messages = array();
$total_messages = count($messages);

?>