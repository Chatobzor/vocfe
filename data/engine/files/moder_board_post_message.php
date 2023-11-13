<?php
//if (!defined("_COMMON_")) {echo "stop";exit;}
if(strlen(trim($moder_message)) > 0) {
    if(!@is_dir($data_path."moder-board/".floor($is_regist/2000)))
        if (ini_get('safe_mode'))
                trigger_error("Your PHP works in SAFE MODE, please create directory data/moder-board/".floor($is_regist/2000),E_USER_ERROR);
        else
                mkdir($data_path."moder-board/".floor($is_regist/2000),0777);

        $fp = fopen($data_path."moder-board/".floor($is_regist/2000)."/".$is_regist.".mod","a+b");
        if (!$fp) trigger_error("Could not open moder-board /".floor($is_regist/2000)."/".$is_regist.".mod for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
                trigger_error("Could not LOCK moder-board/".floor($is_regist/2000)."/".$is_regist.".mod. Do you use Win 95/98/Me?", E_USER_WARNING);
        fseek($fp,0);

        $fs = filesize($data_path."moder-board/".floor($is_regist/2000)."/".$is_regist.".mod");
        $new_mes = intval(str_replace("\t\n","",fgets($fp, 100)))+1;

        $all_other =  fread ($fp,filesize($data_path."moder-board/".floor($is_regist/2000)."/".$is_regist.".mod"));
        $last_id = intval(substr($all_other,0,strpos($all_other,"\t")));
        $last_id++;

        $to_write = "$last_id\t$moder_user_name\t".my_time()."\t$moder_message\t\n";

        ftruncate($fp,0);
        fwrite($fp,$new_mes."\t\n");
        fwrite($fp, $to_write.$all_other);

        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        $info_message = $w_message_sended;
    }
?>