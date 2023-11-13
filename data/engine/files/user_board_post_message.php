<?php
//if (!defined("_COMMON_")) {echo "stop";exit;}
if(strlen(trim($user_contrib)) > 0) {
    if(!@is_dir($data_path."user-board/".floor($is_regist/2000)))
	if (ini_get('safe_mode'))
		trigger_error("Your PHP works in SAFE MODE, please create directory ".$data_path."user-board/".floor($is_regist/2000),E_USER_ERROR);
	else
		mkdir($data_path."user-board/".floor($is_regist/2000),0777);

	$fp = fopen($data_path."user-board/".floor($is_regist/2000)."/".$is_regist.".contrib","a+b");
	if (!$fp) trigger_error("Could not open ".$data_path."user-board /".floor($is_regist/2000)."/".$is_regist.".contrib for writing. Please, check permissions", E_USER_ERROR);
	if (!flock($fp, LOCK_EX))
		trigger_error("Could not LOCK ".$data_path."user-board/".floor($is_regist/2000)."/".$is_regist.".contrib. Do you use Win 95/98/Me?", E_USER_WARNING);
	fseek($fp,0);

	$fs = filesize($data_path."user-board/".floor($is_regist/2000)."/".$is_regist.".contrib");
	$new_mes = intval(str_replace("\t\n","",fgets($fp, 100)))+1;

	$all_other =  fread ($fp,filesize($data_path."user-board/".floor($is_regist/2000)."/".$is_regist.".contrib"));
	$last_id = intval(substr($all_other,0,strpos($all_other,"\t")));
	$last_id++;

    if($fs > $max_mailbox_size) {
       $arr_pieces 	= explode("\n", $all_other);
       $nLen 		= strlen($user_contrib);
       $nbytes		= 0;
       $all_other	= "";

       for($i = 0; $i < count($arr_pieces); $i++) {
            if($nbytes < ($max_mailbox_size - $nLen)) {
                $all_other .= $arr_pieces[$i]."\n";
                $nbytes	+= strlen($arr_pieces[$i]);
            } else break;
       }
    }

	$to_write = "$last_id\t".$cu_array[USER_NICKNAME]."\t".my_time()."\t$user_contrib\t\n";
	ftruncate($fp,0);
	fwrite($fp,$new_mes."\t\n");
	fwrite($fp, $to_write.$all_other);

	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
	$info_message = $w_message_sended;
    }
?>