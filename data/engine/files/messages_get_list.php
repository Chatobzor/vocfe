<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
unset($messages);
$messages = array();
$fp = fopen($messages_file, "rb");
if (!$fp) trigger_error("Could not messages.dat for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
        trigger_error("Could not LOCK messages.dat. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);

while($ttt=fgets($fp, 16384)) {
        if (strlen($ttt)<7) continue;
        $messages[] = trim($ttt);
}
flock($fp, LOCK_UN);
fclose($fp);
if (!is_array($messages)) $messages = array();
$total_messages = count($messages);

?>