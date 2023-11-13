<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
        $board_file = "";

        if($group != 1) {
            if(!@is_dir($data_path."private-board/".floor($send_to_id/2000)))
                if (ini_get('safe_mode'))
                        trigger_error("Your PHP works in SAFE MODE, please create directory ".$data_path."private-board/".floor($send_to_id/2000),E_USER_ERROR);
                else
                        mkdir($data_path."private-board/".floor($send_to_id/2000),0777);

               $board_file = $data_path."private-board/".floor($send_to_id/2000)."/".$send_to_id.".msg";
       } else {
            $send_to_id = intval($send_to_id);

            if ($send_to_id == 1) $board_file = $data_path."private-board/groups/adminz.msg";
            if ($send_to_id == 2) $board_file = $data_path."private-board/groups/shamanz.msg";

            if($cu_array[USER_CLASS] & ADM_BAN_MODERATORS) {
               if ($send_to_id == 0) $board_file = $data_path."private-board/groups/all.msg";
               if ($send_to_id == 3) $board_file = $data_path."private-board/groups/boyz.msg";
               if ($send_to_id == 4) $board_file = $data_path."private-board/groups/girlz.msg";
               if ($send_to_id == 5) $board_file = $data_path."private-board/groups/they.msg";
            }

            if($cu_array[USER_CLANID] > 0 and $send_to_id == 6) $board_file = $data_path."private-board/groups/clan_".$cu_array[USER_CLANID].".msg";
        }

        if($board_file == "") exit;

        $fp = fopen($board_file,"a+b");
        if (!$fp) trigger_error("Could not open mail-file $board_file for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                trigger_error("Could not LOCK mail-file $boad_file. Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);

        $fs = filesize($board_file);
        $new_mes = intval(str_replace("\t\n","",fgets($fp, 100)))+1;

        $all_other =  fread ($fp,filesize($board_file));
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