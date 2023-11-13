<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
function cmp($a, $b) {
	return strcmp(strtoupper($a), strtoupper($b));
}
$users = array();
$users2 = array();
$users_sh_id = shmop_open($shm_users_id, "c", 0644, 100000);
$users_sem_id = sem_get($shm_users_id);
sem_acquire($users_sem_id);
$var = shmop_read($users_sh_id, 0,shmop_size($users_sh_id));
$var = substr($var,0,strpos($var, "\0"));
if ($var != "") $users = explode("\n",$var);
if (!isset($users)) $users = array();
$exists = 0;
$is_regist = 0;
$j = 0;
$hi = 0;
$from_this_ip = 1;
$canon_view = to_canon_nick($user_name);
for ($voc_i=0; $voc_i<count($users); $voc_i++) {
	if (strlen($users[$voc_i])<7) continue;
	$user_array  = explode("\t",trim($users[$voc_i]), USER_TOTALFIELDS);
	if (strcmp($user_array[USER_CANONNICK],$canon_view) == 0)
		if (!$registered_user) {
			$error_text = "$w_already_used<br><a href=\"index.php\">$w_try_again</a>";
			include($file_path."designes/".$design."/error_page.php");
			flock($fp, LOCK_UN);
			fclose($fp);
			exit;
		}
		else  {
			$user_array[USER_NICKNAME] = $user_name;
			$user_array[USER_SESSION] = $session;
			$user_array[USER_TIME] = time();
			$user_array[USER_GENDER] = $sex;
			$user_array[USER_AVATAR] = "";
			#check for small photo
			$tmp_name = "" . floor($registered_user/2000) . "/" . $registered_user . ".gif";
			$tmp_name2 = "" .  floor($registered_user/2000) . "/" . $registered_user . ".jpg";
			if (file_exists($file_path."photos/$tmp_name")) 
				$user_array[USER_AVATAR] = $tmp_name;
			elseif(file_exists($file_path."photos/$tmp_name2")) 
				$user_array[USER_AVATAR] = $tmp_name2;
			$user_array[USER_REGID] = $registered_user;
			$user_array[USER_TAILID] = "0";
			$user_array[USER_IP] = $REMOTE_ADDR;
			$user_array[USER_STATUS] = 0;
			$user_array[USER_LASTSAYTIME] = my_time();
			$user_array[USER_ROOM] = $room_id;
			$user_array[USER_CANONNICK] = to_canon_nick($user_name);
			$user_array[USER_HTMLNICK] = $htmlnick;
			$user_array[USER_CHATTYPE] = $chat_type;
			$user_array[USER_LANG] = $user_lang;
			$user_array[USER_COOKIE] = $c_hash;
			$user_array[USER_CLASS] = $current_user->user_class;
			$user_array[USER_BROWSERHASH] = $browser_hash;
			if (!in_array($design, $designes)) $design = $default_design;
			$user_array[USER_SKIN] = $design;
			$exists = $registered_user;
			$hi = 0;
			$cu_array = $user_array;
			$users[$voc_i] = implode("\t",$user_array);
		}
	if ($user_array[USER_TIME] > time()-$disconnect_time)  {
		$users2[$j] = $users[$voc_i];
		if ($user_array[USER_IP] == $REMOTE_ADDR && strcmp($user_array[USER_CANONNICK],$canon_view) != 0)
			$from_this_ip++;
		$j++;
	}
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
if (!is_array($users2)) $users2 = array();
$too_many = (count($users2)>=$max_connect) ? true:false;
if (!$exists && !$too_many && ($from_this_ip<=$max_from_ip)) {
	$user_array = array_fill(0,USER_TOTALFIELDS-1,"");
	$user_array[USER_NICKNAME] = $user_name;
	$user_array[USER_SESSION] = $session;
	$user_array[USER_TIME] = time();
	$user_array[USER_GENDER] = $sex;
	$user_array[USER_AVATAR] = "";
	#check for small photo
	$tmp_name = "" . floor($registered_user/2000) . "/" . $registered_user . ".gif";
	$tmp_name2 = "" .  floor($registered_user/2000) . "/" . $registered_user . ".jpg";
	if (file_exists($file_path."photos/$tmp_name")) 
		$user_array[USER_AVATAR] = $tmp_name;
	elseif(file_exists($file_path."photos/$tmp_name2")) 
		$user_array[USER_AVATAR] = $tmp_name2;
	$user_array[USER_REGID] = $registered_user;
	$user_array[USER_TAILID] = "0";
	$user_array[USER_IP] = $REMOTE_ADDR;
	$user_array[USER_STATUS] = 0;
	$user_array[USER_LASTSAYTIME] = my_time();
	$user_array[USER_ROOM] = $room_id;
	$user_array[USER_CANONNICK] = to_canon_nick($user_name);
	$user_array[USER_HTMLNICK] = $htmlnick;
	$user_array[USER_CHATTYPE] = $chat_type;
	$user_array[USER_LANG] = $user_lang;
	$user_array[USER_IGNORLIST] = "";
	$user_array[USER_COOKIE] = $c_hash;
	$user_array[USER_BROWSERHASH] = $browser_hash;
	$user_array[USER_CLASS] = ($registered_user) ? $current_user->user_class : 0;
	if (!in_array($design, $designes)) $design = $default_design;
	$user_array[USER_SKIN] = $design;
	$exists = $registered_user;
	$hi = 1;
	$cu_array = $user_array;
	$users2[] = implode("\t", $user_array);
}
$users2 = array_unique($users2);
usort($users2, "cmp");
$total = count($users2);
if (!is_array($users2)) $users2 = array();
else {array_unique($users2);usort($users2, "cmp");}
shmop_write($users_sh_id, implode("\n",$users2)."\0",0);
sem_release($users_sem_id);
shmop_close($users_sh_id);
if (!$exists && $too_many) {
	$error_text = "$w_too_many<br><a href=\"index.php\">$w_try_again_later</a>";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}
if (!$exists && ($from_this_ip>$max_from_ip)) {
	$error_text = "$w_too_many_from_ip<br><a href=\"index.php\">$w_try_again_later</a>";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}

?>