<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

	$fp = fopen($data_path."clans.dat", "rb");
	if (!$fp) trigger_error("Could not open $data_path/clans.dat for reading. Please, check permissions", E_USER_ERROR);
	if (!flock($fp, LOCK_EX))
		trigger_error("Could not $data_path/clans.dat! Do you use Win 95/98/Me?", E_USER_WARNING);
	fseek($fp,0);

    if(isset($clans_list)) unset($clans_list);
    $clans_list = array();

    $i = 0;
    while ($line = fgets($fp, 16384)) {
        if (strlen($line)<7) continue;
        $clan_array  			= explode("\t",trim($line), CLAN_TOTALFIELDS);
        if(count($clan_array) < CLAN_TOTALFIELDS) continue;
        $clan_array[CLAN_ID] 	= intval(trim($clan_array[CLAN_ID]));
        $clan_array[CLAN_NAME] 	= trim($clan_array[CLAN_NAME]);
        if($clan_array[CLAN_ID] > 0 and strlen($clan_array[CLAN_NAME]) > 0) {
	        $clans_list[$i]["id"]	= $clan_array[CLAN_ID];
    	    $clans_list[$i]["name"]	= $clan_array[CLAN_NAME];
        }
        $i++;
    }

if (!flock($fp, LOCK_UN));
fclose($fp);
?>