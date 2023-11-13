<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
unset($users);
$users = array();
$fp = fopen($banlist_file, "r+b");
if(!$fp) trigger_error ("Could not open banlist file. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
	trigger_error ("Could not LOCK banlist file. Do you use Win 95/98/Me?", E_USER_ERROR);
fseek($fp,0);
while ($line = fgets($fp, 4096)) {
	$data  = explode("\t", trim($line));
	$who   = $data[0];
    $moder = $data[1];
    $cause = $data[2];
   	$until = $data[3];

    //compatibility with existing database
    if(strlen($data[1]) > 0 and intval($data[3] == 0)) {
    	$moder = "";
        $cause = "";
        $until = $data[1];
    }

	if ($until>time())
		$users[count($users)] = $who."\t".$moder."\t".$cause."\t".$until;
}
if (is_array($to_ban)) {
	for ($i=0;$i<count($to_ban);$i++)
        if(isset($kill_time) and $kill_time > -1) {
			$users[count($users)] = $to_ban[$i]."\t".($w_times[$kill_time]["value"] + time());
        }
        else $users[count($users)] = $to_ban[$i]."\t".(intval($mesg)*60 + time());
}
fseek($fp,0);
if (!is_array($users)) $users = array();
fwrite($fp,implode("\n",$users));
fflush($fp);
ftruncate($fp, ftell($fp));
flock($fp, LOCK_UN);
fclose($fp);

include($engine_path."admin_2.php");
?>