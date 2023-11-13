<?php

if (!defined("_COMMON_")) {echo "stop";exit;}
	$fp = fopen($data_path."clans.dat", "wb");

    if (!$fp) trigger_error("Could not open $data_path/clans.dat for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp, LOCK_EX))
		trigger_error("Could not write to $data_path/clans.dat! Do you use Win 95/98/Me?", E_USER_WARNING);
	fseek($fp,0);

    for($i = 0; $i < count($clans_list); $i++) {
    	if($clans_list[$i]["id"] > 0) fwrite($fp, $clans_list[$i]["id"]."\t".$clans_list[$i]["name"]."\n");
    }

if (!flock($fp, LOCK_UN));
fclose($fp);
?>