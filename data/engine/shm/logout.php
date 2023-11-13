<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if($session=="") $session = "0";
if(!is_string($session)) $session = "0";

function cmp($a, $b) {
	return strcmp(strtoupper($a), strtoupper($b));
}

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

for ($logout_i=0; $logout_i<count($old_users); $logout_i++) {
	if (strlen($old_users[$logout_i])<7) continue;
	$user_array  = explode("\t",trim($old_users[$logout_i]), USER_TOTALFIELDS);
	if ($user_array[USER_SESSION] == $session) {
		$user_name = $user_array[USER_NICKNAME];
		$user_array[USER_TIME]=time();
		$exists = 1;
		$is_regist = $user_array[USER_REGID];
		$user_ip = $user_array[USER_IP];
		$room_id = $user_array[USER_ROOM];
		if (!in_array($user_array[USER_SKIN], $designes)) $user_array[USER_SKIN] = $default_design;
		$design = $user_array[USER_SKIN];
		$current_design = $chat_url."designes/".$design."/";
		if ($user_array[USER_LANG] != $language) {
			if (!in_array($user_array[USER_LANG], $allowed_langs)) $user_array[USER_LANG] = $language;
			else { include_once($file_path."languages/".$user_array[USER_LANG].".php"); }
			$user_lang = $user_array[USER_LANG];
		}
		//!!!!it's better to replace this variables to $cu_array[]; in all code.
		// now I left it for compability
		$cu_array = $user_array;
		$old_users[$logout_i] = implode("\t", $user_array);
	}
	else {
		if ($user_array[USER_TIME] > time()-$disconnect_time)
			$users[] = implode("\t",$data);
		else if($user_array[USER_NICKNAME]!="")
			$messages_to_show[] = array(MESG_TIME=>my_time(), 
										MESG_ROOM=>$user_array[USER_ROOM],  
										MESG_FROM=>$rooms[$user_array[USER_ROOM]]["bot"], 
										MESG_FROMWOTAGS=>$rooms[$user_array[USER_ROOM]]["bot"],
										MESG_FROMSESSION=>"",
										MESG_FROMID=>0,
										MESG_FROMAVATAR=>"",
										MESG_TO=>"", 
										MESG_TOSESSION=>"",
										MESG_TOID=>"",
										MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", $user_array[USER_NICKNAME], $sw_rob_idle)."</font>");
	}
}
if (count($users)) usort($users, "cmp");
else $users = array();
shmop_write($users_sh_id, implode("\n",$users)."\0",0);
sem_release($users_sem_id);
shmop_close($users_sh_id);
?>