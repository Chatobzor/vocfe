<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
function check_ban($to_check) {
	if (!is_array($to_check)) return 0;
	$banned = 0;
	global $banlist_file;

	$users = array();
	$fp = fopen($banlist_file, "a+b");
	if (!$fp) trigger_error("Could not open banlist file for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp, LOCK_EX)) trigger_error("Could not LOCK banlist file. Do you use Win 95/98/Me?", E_USER_WARNING);
	fseek($fp,0);
	
	while ($line = fgets($fp, 4096)) {
		$data  = explode("\t",str_replace("\r","",str_replace("\n","",$line)));

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

		if ($until>time()) {
			$users[count($users)] = $who."\t".$moder."\t".$cause."\t".$until;
			for ($i=0;$i<count($to_check);$i++)
				if ((strcasecmp($to_check[$i], $who)==0)) $banned = 1;
		}
	}
	ftruncate($fp,0);
	if (!is_array($users)) $users = array();
	fwrite($fp,implode("\n",$users));
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
	
	return $banned;
}

function get_all_bans() {
	global $banlist_file;
	$banned_users = array();
	$fp = fopen($banlist_file, "rb");
	if (!$fp) trigger_error("Could not open banlist file! Please, check permissions", E_USER_ERROR);
	while ($line = fgets($fp, 4096)) {
		$data  = explode("\t",trim($line));

        if(strlen($data[1]) > 0 and intval($data[3] == 0)) {
			$banned_users[count($banned_users)] = array("who"=>$data[0], "moder"=>"", "cause"=>"", "until"=>$data[1]);
	    }
	    else $banned_users[count($banned_users)] = array("who"=>$data[0], "moder"=>$data[1], "cause"=>$data[2], "until"=>$data[3]);
	}
	fclose($fp);
	return $banned_users;
}

function unban($to_unban) {
	global $banlist_file;
	$users = array();
	$fp = fopen($banlist_file, "a+b");
	if (!$fp) trigger_error("Could not open banlist file for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp, LOCK_EX)) trigger_error("Could not LOCK banlist file. Do you use Win 95/98/Me?", E_USER_WARNING);
	fseek($fp,0);
	while ($line = fgets($fp, 4096)) {
		$data  = explode("\t",trim($line));

        $who   = $data[0];
	    $moder = $data[1];
	    $cause = $data[2];
   		$until = $data[3];

    	//compatibility with existing database
	    if(strlen($data[1]) > 0 and intval($data[3] == 0)){
	    	$moder = "";
	        $cause = "";
    	    $until = $data[1];
	    }

		if ($until>time() && $who != $to_unban) {
			$users[count($users)] = $who."\t".$moder."\t".$cause."\t".$until;
			if ((strcasecmp($who, $name)==0) or ($who==$ip)) $banned = 1;
		}
	}
	ftruncate($fp,0);
	if (!is_array($users)) $users = array();
	fwrite($fp,implode("\n",$users));
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>