<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$user_board_messages = array();

$fp = fopen($data_path."user-board/".floor($is_regist/2000)."/".$is_regist.".contrib","rb");
if ($fp) {
if (!flock($fp, LOCK_EX))
		trigger_error("Could not LOCK ".$data_path."user-board/".floor($is_regist/2000)."/".$is_regist.".contrib. Do you use Win 95/98/Me?", E_USER_WARNING);
rewind($fp);
fseek($fp,0);
$fs = filesize($data_path."user-board/".floor($is_regist/2000)."/".$is_regist.".contrib");
//first string contains the number of new messages
$new_mes = fgets($fp, 100);
$i = 0;
while(!feof($fp))
	$board_content = str_replace("\r", "", fread($fp,$fs));

foreach(explode("\t\n",$board_content)as $message) {
	if ($message!=""){
		list($id, $from_nick, $at_date, $body) = explode("\t",$message);

        if($BoardMode != "pro") {
    	    if ($id and $from_nick == $cu_array[USER_NICKNAME]) {
				if ($subject == "") $subject = $w_no_subject;
				$user_board_messages[$i]["id"] = $id;
				$user_board_messages[$i]["from"] = $from_nick;
				$user_board_messages[$i]["date"] = date($w_date_format,$at_date);
				$user_board_messages[$i]["body"] = $body;
				$i++;
			}
        }
        else {
        	if($cu_array[USER_CLASS] & ADM_VIEW_PRIVATE) {
               if ($id) {
                    $user_board_messages[$i]["id"] = $id;
					$user_board_messages[$i]["from"] = $from_nick;
					$user_board_messages[$i]["date"] = date($w_date_format,$at_date);
					$user_board_messages[$i]["body"] = $body;
					$i++;
               }
            }
        }
	}
}
if (!flock($fp, LOCK_UN));
fclose($fp);
}
?>