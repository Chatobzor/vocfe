<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$users = array();
$ignored_list = array();
$fp = fopen($who_in_chat_file, "r+b");
if (!$fp) trigger_error("Could not open who.dat for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
	trigger_error("Could not LOCK who.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);

$user_array = array();
$cu_array = array_fill(0, USER_TOTALFIELDS, "");
while ($line = fgets($fp, 16384)) {
	if (strlen($line)<7) continue;
	$user_array  = explode("\t",trim($line), USER_TOTALFIELDS);
	if ($user_array[USER_SESSION] == $session) {
		$user_array[USER_TIME] = time();
		$ignored_list = explode(",",$user_array[USER_IGNORLIST]);
		$new_ignored_list = array();
		for ($i=0;$i<count($ignored_list);$i++)
			if ($remove_from_ignor != $ignored_list[$i])
				$new_ignored_list[] = $ignored_list[$i];
		$user_array[USER_IGNORLIST] = implode(",",$new_ignored_list);
		$cu_array = $user_array;
	}
	$users[] = implode("\t",$user_array) . "\n";
}
if (count($users)) usort($users, "cmp");
else $users = array();

fseek($fp,0);
fwrite($fp,implode("",$users));
fflush($fp);
ftruncate($fp, ftell($fp));
flock($fp, LOCK_UN);
fclose($fp);
?>