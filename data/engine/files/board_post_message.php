<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

        $fp = fopen($data_path."board/".floor($send_to_id/2000)."/".$send_to_id.".msg","a+b");
        if (!$fp) trigger_error("Could not open mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                trigger_error("Could not LOCK mail-file board/".floor($is_regist/2000)."/".$is_regist.".msg. Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);

        $fs = filesize($data_path."board/".floor($send_to_id/2000)."/".$send_to_id.".msg");


        $new_mes = intval(str_replace("\t\n","",fgets($fp, 100)))+1;

        $all_other =  fread ($fp,filesize($data_path."board/".floor($send_to_id/2000)."/".$send_to_id.".msg"));
        $last_id = intval(substr($all_other,0,strpos($all_other,"\t")));
        $last_id++;

        if($fs > $max_mailbox_size) {
           $arr_pieces         = explode("\n", $all_other);
           $nLen                 = strlen("$last_id\t1\t$user_name\t$is_regist\t".my_time()."\t$subject\t$message\t\n");
           $nbytes                = 0;
           $all_other        = "";

        for($i = 0; $i < count($arr_pieces); $i++) {
            if($nbytes < ($max_mailbox_size - $nLen)) {
                $all_other .= $arr_pieces[$i]."\n";
                $nbytes        += strlen($arr_pieces[$i]);
                } else break;
            }
       }

        $to_write = "$last_id\t1\t$user_name\t$is_regist\t".my_time()."\t$subject\t$message\t\n";
        ftruncate($fp,0);
        fwrite($fp,$new_mes."\t\n");
        fwrite($fp, $to_write.$all_other);

        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        $info_message = $w_message_sended;
?>