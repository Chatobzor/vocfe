<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!defined("_CMP_")):
define("_CMP_",1);
function cmp($a, $b) {
	return strcmp(strtoupper($a), strtoupper($b));
}
endif;
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";

if (!isset($rooms)) {
	include($ld_engine_path."rooms_get_list.php");
}
unset($messages_to_show);
$messages_to_show = array();
$def_color = $registered_colors[$default_color][1];
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
$ignored_users = array();
$ignored_list = array();
$room_id = 0;
for ($users_i=0; $users_i<count($old_users); $users_i++) {
	if (strlen($old_users[$users_i])<7) {continue;}
	$user_array  = explode("\t",trim($old_users[$users_i]), USER_TOTALFIELDS);
	if ($user_array[USER_SESSION] == $session) {

		$user_name = $user_array[USER_NICKNAME];
		$user_array[USER_TIME] = time();
		$exists = 1;
		$is_regist = $user_array[USER_REGID];
		$tail_num = $user_array[USER_TAILID];
		$user_ip =$user_array[USER_IP];
		$room_id = $user_array[USER_ROOM];
		$user_status = $user_array[USER_STATUS];
		$user_chat_type = $user_array[USER_CHATTYPE];
		if (!in_array($user_array[USER_SKIN], $designes)) $user_array[USER_SKIN] = $default_design;
		if ($rooms[$room_id]["design"] != "")
			$user_array[USER_SKIN] = $rooms[$room_id]["design"];
		$design = $user_array[USER_SKIN];
		$current_design = $chat_url."designes/".$design."/";
		$ignored_list = explode(",",$user_array[USER_IGNORLIST]);
		for ($i=0;$i<count($ignored_list);$i++)
			$ignored_users[strtolower($ignored_list[$i])] = 1;
		$cu_array = $user_array;
		if ($user_array[USER_LANG] != $language) {
			if (!in_array($user_array[USER_LANG], $allowed_langs)) $user_array[USER_LANG] = $language;
			else { 
				include_once($file_path."languages/".$user_array[USER_LANG].".php"); 
				$registered_colors = $s_registered_colors;
				$default_color = $s_default_color;
				$highlighted_color = $s_highlighted_color;
			}
			$user_lang = $user_array[USER_LANG];
		}
		$old_users[$users_i] = implode("\t", $user_array);
	}
	if ($user_array[USER_TIME] > time()-$disconnect_time)
		$users[] = $old_users[$users_i];
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
if (count($users)) usort($users, "cmp");
else $users = array();
$orig_f_p = $flood_protection;
$flood_protection = 1;
include($engine_path."messages_put.php");
unset($messages_to_show);
shmop_write($users_sh_id, implode("\n",$users)."\0",0);
sem_release($users_sem_id);
shmop_close($users_sh_id);
$flood_protection = $orig_f_p;

?>