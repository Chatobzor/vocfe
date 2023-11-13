<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$fp = fopen($who_in_chat_file, "r");
flock($fp, LOCK_EX);

$exists = 0;
while ($line = fgets($fp, 16384)) {
	$user_array  = explode("\t",trim($line));
	if ($user_array[USER_REGID] == $is_regist) {
		$exists = 1;
		break;
	}
}
flock($fp, LOCK_UN);
fclose($fp);
?>