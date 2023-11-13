<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

        if($group != 1) {
            $send_to_id = intval($send_to_id);
            if(!@is_dir($data_path."user-privates/".floor($send_to_id/2000)))
                if (ini_get('safe_mode'))
                        trigger_error("Your PHP works in SAFE MODE, please create directory ".$data_path."user-privates/".floor($send_to_id/2000),E_USER_ERROR);
                else
                        mkdir($data_path."user-privates/".floor($send_to_id/2000),0777);

                $board_file = $data_path."user-privates/".floor($send_to_id/2000)."/".$send_to_id.".msg";
      } else {
                    $send_to_id = intval($send_to_id);

                    if ($send_to_id == 1) $board_file = $data_path."user-privates/groups/adminz.msg";
                    if ($send_to_id == 2) $board_file = $data_path."user-privates/groups/shamanz.msg";
                    if ($send_to_id == 0) $board_file = $data_path."user-privates/groups/all.msg";
                    if ($send_to_id == 3) $board_file = $data_path."user-privates/groups/boyz.msg";
                    if ($send_to_id == 4) $board_file = $data_path."user-privates/groups/girlz.msg";
                    if ($send_to_id == 5) $board_file = $data_path."user-privates/groups/they.msg";
                    if($clan_id > 0 and $send_to_id == 6) $board_file = $data_path."user-privates/groups/clan_".$clan_id.".msg";
        }

        $fp = fopen($board_file,"a+b");
        if (!$fp) trigger_error("Could not open mail-file $board_file for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                trigger_error("Could not LOCK mail-file $boad_file. Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);

        $fs = filesize($board_file);
        $all_other =  fread ($fp,filesize($board_file));

    if($fs > $max_mailbox_size) {
       $arr_pieces           = explode("\n", $all_other);
       $nLen                 = strlen(my_time()."\t$user_name\t$html_nick\t$whisper\t$mesg\t\n");
       $nbytes               = 0;
       $all_other        = "";

       for($i = 0; $i < count($arr_pieces); $i++) {
           if($nbytes < ($max_mailbox_size - $nLen)) {
                if(strlen($arr_pieces[$i]) > 20) $all_other .= $arr_pieces[$i]."\n";
                $nbytes    += strlen($arr_pieces[$i]);
            } else break;
       }
       //$all_other        = "";
    }
        $to_write = my_time()."\t$user_name\t$html_nick\t$whisper\t$mesg\t\n";
        ftruncate($fp,0);
        fwrite($fp, $to_write.$all_other);
        fflush($fp);

        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($board_file, 0777);
?>