<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$ttt = str_replace("\\*","([".$nick_available_chars."]+)",quotemeta($user_to_search));

$fp = fopen($user_data_file, "r");
flock($fp, LOCK_EX);
fseek($fp,0);
$ii=0;
while ($data = fgets($fp, 4096)) {
	$user = str_replace("\r","",str_replace("\n","",$data));
	list($t_id, $t_nickname, $t_password, $t_class,$t_canon) = explode("\t",$user);
	if (eregi($ttt,$t_nickname)) {
		$u_ids[] = $t_id;
		$u_names[] = $t_nickname;
	}
}
flock($fp, LOCK_UN);
fclose($fp);
?>