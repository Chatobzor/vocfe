<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$priv_messages = array();


proceed_file($data_path."user-privates/groups/all.msg");
if($cu_array[USER_CLASS] > 0)  { proceed_file($data_path."user-privates/groups/adminz.msg"); proceed_file($data_path."user-privates/groups/shamanz.msg"); }
if($cu_array[USER_CUSTOMCLASS] == CST_PRIEST) proceed_file($data_path."user-privates/groups/shamanz.msg");
if($cu_array[USER_CLANID] > 0) proceed_file($data_path."user-privates/groups/clan_".$cu_array[USER_CLANID].".msg");
//genderz
if($cu_array[USER_GENDER] == GENDER_BOY) { $board_file = "boyz.msg"; $group_id = 3; }
else if ($cu_array[USER_GENDER] == GENDER_GIRL){ $board_file = "girlz.msg"; $group_id = 4; }
else if ($cu_array[USER_GENDER] == GENDER_THEY or intval($cu_array[USER_GENDER]) == 0 or intval($cu_array[USER_GENDER]) == -1) { $board_file = "they.msg"; $group_id = 5; }
if($board_file == "") { $board_file = "they.msg"; $group_id = 5; }

proceed_file($data_path."user-privates/groups/$board_file");
//user messages

proceed_file($data_path."user-privates/".floor($cu_array[USER_REGID]/2000)."/".$cu_array[USER_REGID].".msg");


function proceed_file($file) {
global  $current_user, $data_path, $private_message, $private_message_fromme, $priv_messages;

        if(is_file($file)) @chmod($file, 0777);
        else return;

        $fp = fopen($file,"rb");
        if (!$fp) return;


        if (!flock($fp, LOCK_EX))
                        trigger_error("Could not LOCK mail-file $file. Do you use Win 95/98/Me?", E_USER_WARNING);

           fseek($fp,0);
           while(!feof($fp)) {
           $message = fgets($fp, 4096);
           if ($message!=""){
                        $message = str_replace("\n", "", $message);

                        list($mess_time, $user_nick, $html_nick, $to_nick, $body) = explode("\t",$message);

                        if($body == "") continue;

                         if(intval($mess_time) < intval($current_user->last_actiontime) or
                           intval($mess_time) < intval($current_user->registered_at)
                         ) continue;
                         if($user_nick == "&CMD") continue;

                        if($cu_array[USER_NICKNAME] == $user_nick) $to_out = $private_message_fromme;
                        else $to_out = $private_message;

                        if($html_nick == "") $html_nick = $user_nick;

                        $to_out = str_replace("[HOURS]",date("H",$mess_time),$to_out);
                        $to_out = str_replace("[MIN]",date("i",$mess_time),$to_out);
                        $to_out = str_replace("[SEC]",date("s",$mess_time),$to_out);
                        $to_out = str_replace("[NICK]",$html_nick,$to_out);
                        $to_out = str_replace("[NICK_WO_TAGS]",$user_nick,$to_out);
                        $to_out = str_replace("[TO]",$to_nick,$to_out);
                        $to_out = str_replace("[PRIVATE]",$to_nick,$to_out);
                        $to_out = str_replace("[MESSAGE]",$body,$to_out);
                        $to_out = addslashes($to_out);
                        $to_out = eregi_replace("</script>","</'+'script'+'>", $to_out);
                        $to_out = "AddMsgToPriv('".$to_out."', '".addslashes($user_nick)."', '".addslashes($to_nick)."');";
                        $priv_messages[intval($mess_time)] = $to_out;
                }
        }
        if (!flock($fp, LOCK_UN));
        fclose($fp);
}


?>