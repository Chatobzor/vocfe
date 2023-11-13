<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$board_message = array();
$group = intval($group);
//user messages
$group_id = -1;
if($group == 0) $board_file = $data_path."private-board/".floor($is_regist/2000)."/".$is_regist.".msg";
else if ($group == 5) { $board_file = $data_path."private-board/groups/all.msg"; $group_id = 0; }
else if($group == 1 and $cu_array[USER_CLASS] > 0) { $board_file = $data_path."private-board/groups/adminz.msg"; $group_id = 1; }
else if($group == 2 and $cu_array[USER_CUSTOMCLASS] == CST_PRIEST){ $board_file = $data_path."private-board/groups/shamanz.msg";  $group_id = 2; }
else if($group == 3 and $cu_array[USER_CLANID] > 0) { $board_file = $data_path."private-board/groups/clan_".$cu_array[USER_CLANID].".msg"; $group_id = 6; }
//genderz
else if($group == 4 and $cu_array[USER_GENDER] == GENDER_BOY)  { $board_file =  $data_path."private-board/groups/boyz.msg"; $group_id = 3; }
else if($group == 4 and $cu_array[USER_GENDER] == GENDER_GIRL) { $board_file =  $data_path."private-board/groups/girlz.msg"; $group_id = 4; }
else if($group == 4 and $cu_array[USER_GENDER] == GENDER_THEY) { $board_file =  $data_path."private-board/groups/they.msg"; $group_id = 5; }


//if it is a group message, write a note that this message was read by user
if($group_id > -1 and $group_id < 7) {
    if(!@is_dir($data_path."user-viewed/".floor($is_regist/2000)))
	if (ini_get('safe_mode'))
		trigger_error("Your PHP works in SAFE MODE, please create directory ".$data_path."user-viewed/".floor($is_regist/2000),E_USER_ERROR);
	else
		mkdir($data_path."user-viewed/".floor($is_regist/2000),0777);

$fp	= fopen($data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view","a+b");

$i = 0;
$IsAlreadyViewed = 0;
if($fp) {
    if (!flock($fp, LOCK_EX))
	trigger_error("Could not LOCK mail-file ".$data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view. Do you use Win 95/98/Me?", E_USER_WARNING);
	fseek($fp,0);

   while (!feof($fp)) {
	   $buffer = fgets($fp, 4096);
       list($t_group_id, $t_msg_id) = explode("\t", $buffer);
       $t_group_id 	 		= intval($t_group_id);
       $t_msg_id	    	= intval($t_msg_id);
       if($t_group_id == $group_id and $t_msg_id == $id) { $IsAlreadyViewed = 1; break; }
   }

   if (!flock($fp, LOCK_UN));
   fclose($fp);
 }
}


$fp = fopen($board_file,"a+b");
if (!$fp) trigger_error("Could not open mail-file $board_file for writing. Please, check permissions", E_USER_ERROR);
if (!flock($fp, LOCK_EX))
	trigger_error("Could not LOCK mail-file $board_file. Do you use Win 95/98/Me?", E_USER_WARNING);
fseek($fp,0);
$fs = filesize($board_file);

#first string contains the number of new messages
$new_mes = str_replace("\t\n","",fgets($fp, 100));
#probably it's needed for windows version:
$new_mes = str_replace("\r","",$new_mes);
$to_write = "";
while(!feof($fp))
	$board_content = str_replace("\r", "", fread($fp,$fs));

foreach(explode("\t\n",$board_content)as $message) {
	if ($message!=""){
		list($t_id, $status, $from_nick, $from_id, $at_date, $subject, $body) = explode("\t",$message);
		if ($t_id==$id) {
			if ($status == 1) {$status = 0; $new_mes--;}
			if ($board_operation == "reply") $status = 2;
			if ($subject == "") $subject = $w_no_subject;
			$board_message["id"] = $id;
			$board_message["status"] = $w_stat[$status];
			$board_message["from"] = $from_nick;
			$board_message["from_id"] = $from_id;
			$board_message["subject"] = $subject;
			$board_message["date"] = date($w_date_format,$at_date);
			$board_message["body"] = $body;

            //echo "$IsAlreadyViewed, $group_id, $id";
            if(!$IsAlreadyViewed and $group_id > -1 and $group_id < 7) {

            $fs = filesize($data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view");
			$new_mes = "$group_id\t$id\n";

           	$fp_log	= fopen($data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view","a+b");

                if($fp_log) {
				    if (!flock($fp_log, LOCK_EX))
					trigger_error("Could not LOCK mail-file ".$data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view. Do you use Win 95/98/Me?", E_USER_WARNING);

                    $all_other =  fread ($fp_log,filesize($data_path."user-viewed/".floor($is_regist/2000)."/".$is_regist.".view"));

				    if($fs > $max_mailbox_size) {
				       $arr_pieces 	= explode("\n", $all_other);
				       $nLen 		= strlen($new_mes);
				       $nbytes		= 0;
		       		   $all_other	= "";

				       for($i = 0; $i < count($arr_pieces); $i++) {
				            if($nbytes < ($max_mailbox_size - $nLen)) {
		            		   		$all_other .= $arr_pieces[$i]."\n";
		    	            		$nbytes	+= strlen($arr_pieces[$i]);
            					} else break;
		       		   }
		    		}

                    ftruncate($fp_log,0);
                    fwrite($fp_log,$new_mes);
					fwrite($fp_log, $all_other);

                    if (!flock($fp_log, LOCK_UN));
    			    fclose($fp_log);
                }

            }

		}
		if ($t_id) $to_write .= "$t_id\t$status\t$from_nick\t$from_id\t$at_date\t$subject\t$body\t\n";
	}
}
if($group_id == -1) {
	ftruncate($fp,0);
	fwrite($fp,$new_mes."\t\n");
	fwrite($fp, $to_write);
	fflush($fp);
}
flock($fp, LOCK_UN);
fclose($fp);
?>