<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

function engine_total_users() {
	global $user_data_file;
	$fp = fopen($user_data_file, "rb");
	//flock($fp, LOCK_EX);
	fseek($fp,0);
	$total_users = 0;
	while ($line = fgets($fp, 16384)) {
		if (strlen($line)<7) continue;
		$total_users++;
	}
	//flock($fp,LOCK_UN);
	fclose($fp);
	return $total_users;
}


function engine_last_registered() {
	global $user_data_file,$file_path;
	$last_users = array();
	$fp = fopen($user_data_file, "rb");
	fseek($fp,0);
	$loaded_users = array();
	$num_of_loaded = 0;
	while ($data = fgets($fp, 4096)) {
		if ($num_of_loaded<15) { $loaded_users[$num_of_loaded] = $data;$num_of_loaded++;}
		else {
			for ($i=0;$i<14;$i++) $loaded_users[$i] = $loaded_users[$i+1];
			$loaded_users[14] = $data;
		}
	}
	
	fclose($fp);
	for ($i=$num_of_loaded-1;$i>=0;$i--) {
		list($t_id, $t_nickname, $t_password, $t_class) = explode("\t",$loaded_users[$i]);
		$pic_name = "".floor($t_id/2000)."/".$t_id . ".big.gif";
		if (!@file_exists($file_path."photos/$pic_name")) $pic_name="";
		if ($pic_name == "") {
			$pic_name = "".floor($t_id/2000)."/".$t_id . ".big.jpg";
			if (!@file_exists($file_path."photos/$pic_name")) $pic_name="";
		}
		$photo = ($pic_name!="") ? true:false;
		$last_users[$num_of_loaded - $i -1] = array("nickname"=>$t_nickname,"photo"=>$photo,"id"=>$t_id);
	}
	return $last_users;
}
?>