<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$fp = fopen($data_path."board/".floor($is_regist/2000)."/".$is_regist.".msg","a+b");
if (!$fp) trigger_error("Could not open mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX)) 
	trigger_error("Could not LOCK mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);
$new_board_messages = fgets($fp, 100);

if ($new_board_messages == "") $new_board_messages = "0";
//если нет файла
//$num = intval($num);
if (!flock($fp, LOCK_UN));
fclose($fp);
$percentage = round(filesize ($data_path."board/".floor($is_regist/2000)."/".$is_regist.".msg") / $max_mailbox_size *100);

?>