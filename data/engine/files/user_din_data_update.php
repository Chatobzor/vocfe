<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";

if(!defined("_CMP_")):
define("_CMP_",1);
function cmp($a, $b) {
	return strcmp(strtoupper($a), strtoupper($b));
}
endif;
$users = array();
$fp = fopen($who_in_chat_file, "r+b");
if (!$fp) trigger_error("Could not open who.dat for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
	trigger_error("Could not LOCK who.dat. Do you use Win 95/98/Me?", E_USER_WARNING);

$exists = 0;
$is_regist = 0;
$user_name = "";
$cu_array = array_fill(0, USER_TOTALFIELDS-1, "");
while ($line = fgets($fp, 16384)) {
	if (strlen($line)<7) continue;
	$user_array  = explode("\t",trim($line), USER_TOTALFIELDS);
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
	}
	$users[] = implode("\t",$user_array) . "\n";
}
if (count($users)) usort($users, "cmp");
else $users = array();

fseek($fp,0);
fwrite($fp,implode("",$users));
fflush($fp);
ftruncate($fp,ftell($fp));
flock($fp, LOCK_UN);
fclose($fp);
?>