<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
//removing user from 'who's online'
unset($users);
$users = array();
$j=0;
$fp = fopen($who_in_chat_file, "r+b");
if(!$fp) trigger_error ("Could not open who.dat file. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
        trigger_error ("Could not LOCK who.dat file. Do you use Win 95/98/Me?", E_USER_ERROR);
fseek($fp,0);
while ($line = fgets($fp, 4096)) {
        $data  = explode("\t",trim($line));
        if (strcasecmp(trim($data[0]),$nameToBan)!=0) {
                $users[$j] = implode("\t",$data) . "\n";
                $j++;
        }
}
if (!is_array($users)) $users = array();
fseek($fp,0);
fwrite($fp,implode("",$users));
fflush($fp);
ftruncate($fp,ftell($fp));
flock($fp, LOCK_UN);
fclose($fp);
?>