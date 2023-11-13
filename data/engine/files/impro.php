<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

$impro_file = $data_path."impro.dat";

function impro_get_code($impro_id) {
	global $impro_file;
	$code = "error";
	$fp = fopen($impro_file, "rb");
	flock($fp, LOCK_EX);
	while ($line = fgets($fp, 4096)) {
		$data  = explode("\t",str_replace("\r","",str_replace("\n","",$line)));
		if ($data[1] == $impro_id) {
			$code = $data[2];
			break;
		}
	}
	flock($fp, LOCK_UN);
	fclose($fp);
	return $code;
}

function impro_save($impro_id, $impro_code) {
	global $impro_file;
	$to_write = array();
	$fp = fopen($impro_file,"ab+");
	if (!$fp)
		trigger_error("Cannot open impro.dat file, please check file permissions",E_USER_ERROR);
	flock($fp, LOCK_EX);
	fseek($fp,0);
	while ($line = fgets($fp, 4096)) {
		$line = str_replace("\r","",str_replace("\n","",$line));
		$data  = explode("\t",$line);
		//20 minutes
		if ($data[0] > time() -1200)
			$to_write[] = $line;
	}
	$to_write[] = time()."\t".$impro_id."\t".$impro_code;
	fseek($fp,0);
	ftruncate($fp,0);
	fwrite($fp, implode("\n",$to_write));
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
}


function impro_check($impro_id, $impro_code) {
	global $impro_file;
	$valid = 0;
	$to_write = array();
	$fp = fopen($impro_file,"ab+");
	flock($fp, LOCK_EX);
	fseek($fp,0);
	while ($line = fgets($fp, 4096)) {
		$line = str_replace("\r","",str_replace("\n","",$line));
		$data  = explode("\t",$line);
		if ($data[1] == $impro_id && $data[2] == $impro_code) 
			$valid = 1;
		else
			if ($data[0] > time() -1200)
				$to_write[] = $line;
	}
	fseek($fp,0);
	ftruncate($fp,0);
	fwrite($fp, implode("\n",$to_write));
	flock($fp, LOCK_UN);
	fclose($fp);
	return $valid;
}

?>