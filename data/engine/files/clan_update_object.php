<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

if(!@is_dir($data_path."clans/".floor($is_regist_clan/2000)))
	if (ini_get('safe_mode'))
		trigger_error("Your PHP works in SAFE MODE, please create directory data/clans/".floor($is_regist_clan/2000),E_USER_ERROR);
	else
		mkdir($data_path."clans/".floor($is_regist_clan/2000),0777);


$fp = fopen ($data_path."clans/".floor($is_regist_clan/2000)."/".$is_regist_clan.".clan", "wb");
if (!$fp) trigger_error("Could not open clans/".floor($is_regist_clan/2000)."/".$is_regist_clan.".clan for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
	trigger_error("Could not LOCK file. Do you use Win 95/98/Me?", E_USER_WARNING);
fwrite($fp,serialize($current_clan));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
$clans_buf = "";

$fp = fopen($clans_data_file, "ab+");
if (!$fp) trigger_error("Could not open $clans_data_file for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
	trigger_error("Could not LOCK $clans_data_file file. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);
$ii=0;
while ($data = fgets($fp, 4096)) {
	$u_data  = explode("\t",str_replace("\r","",str_replace("\n","",$data)));

    $u_data[0] = intval(trim($u_data[0]));

	if ($u_data[0] == $is_regist_clan) {
		$u_data[1] = $current_clan->name;
	}
	$clans_buf .= implode("\t",$u_data)."\n";
}

ftruncate($fp,0);
fwrite($fp,$clans_buf);
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
unset($clans_buf);
$info_message .= $w_succ_updated."<br>";
?>