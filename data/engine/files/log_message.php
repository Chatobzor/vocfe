<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

function log_clearing($to_ban, $room, $author) {
        global $data_path;

        $messages_file = $data_path."messages.dat";
        $fp_log = fopen($messages_file, "r+");
        flock($fp_log, LOCK_EX);
        if (!$fp_log) {
                echo "<script>alert('Could not lock [$messages_file] for writing. Please, check permissions!');</script>";
            return;
        }

        unset($tmp_messages);
                $tmp_messages         = array();
        $chk_msg                 = array();

        fseek($fp_log,0);

                while($ttt=fgets($fp_log, 16384)) {
                if (strlen($ttt)<7) continue;
            $chk_msg = explode("\t",trim($ttt));

            if(strcasecmp($chk_msg[MESG_FROMWOTAGS], $to_ban) != 0)  {
               $tmp_messages[] = implode("\t", $chk_msg);
            }
            else {
                    if(trim($chk_msg[MESG_TO]) != ""  or intval(trim($chk_msg[MESG_ROOM])) != $room) $tmp_messages[] = implode("\t", $chk_msg);
            }
                }

        fseek($fp_log,0);
        ftruncate($fp_log, 0);
                   foreach($tmp_messages as $tmp_msg) fwrite($fp_log, $tmp_msg."\n");

        flock($fp_log, LOCK_UN);
        fclose($fp_log);
}


function log_ban($admin, $to_ban, $ip_to_ban, $room, $reason) {
        global $data_path, $HTTP_SERVER_VARS;
        $REMOTE_ADDR = "";
        if (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) $REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        $fp_log = fopen($data_path."logs/".date("Y-m-d", my_time()).".log", "a+");
        flock($fp_log, LOCK_EX);
        fwrite($fp_log, date("H:i:s",my_time())."\t"."[BAN]\t".
                        $REMOTE_ADDR ."\t".
                        $admin."\troom ".$room."\t".
                        $to_ban."(".$ip_to_ban.") ". $reason ."\n");
        fflush($fp_log);
        flock($fp_log, LOCK_UN);
        fclose($fp_log);
}

function log_unban($admin, $to_unban) {
        global $data_path, $HTTP_SERVER_VARS;
        $REMOTE_ADDR = "";
        if (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) $REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        $fp_log = fopen($data_path."logs/".date("Y-m-d", my_time()).".log", "a+");
        flock($fp_log, LOCK_EX);
        fwrite($fp_log, date("H:i:s",my_time())."\t"."[UNBAN]\t".
                        $REMOTE_ADDR ."\t".
                        $admin."\troom N/A\t".
                        $to_unban."\n");
        fflush($fp_log);
        flock($fp_log, LOCK_UN);
        fclose($fp_log);
}

function log_message() {
        global $data_path, $messages_to_show, $HTTP_SERVER_VARS;
        global $max_mailbox_size;
        global $sw_usr_all_link,
               $sw_usr_adm_link,
               $sw_usr_boys_link,
               $sw_usr_girls_link,
               $sw_usr_they_link,
               $sw_usr_clan_link,
               $sw_usr_shaman_link;


        $REMOTE_ADDR = "";
        if (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) $REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];

        $fp_log         = fopen($data_path."logs/".date("Y-m-d", my_time()).".log", "a+");
        $fp_prv_log = fopen($data_path."logs/".date("Y-m-d", my_time())."-private.log", "a+");

        flock($fp_log, LOCK_EX);
        flock($fp_prv_log, LOCK_EX);

        for ($i=0;$i<count($messages_to_show);$i++)
                if ($messages_to_show[$i][MESG_TO]=="") {
                        fwrite($fp_log, date("H:i:s",$messages_to_show[$i][MESG_TIME])."\t"."[MSG]\t".
                                $REMOTE_ADDR ."\t".
                                $messages_to_show[$i][MESG_FROMWOTAGS]."\troom ".$messages_to_show[$i][MESG_ROOM]."\t".
                                $messages_to_show[$i][MESG_BODY]."\n");
        }
        else {
                        fwrite($fp_prv_log, date("H:i:s",$messages_to_show[$i][MESG_TIME])."\t".
                                $REMOTE_ADDR ."\t".
                                $messages_to_show[$i][MESG_FROMWOTAGS]."\t".$messages_to_show[$i][MESG_TO]."\t".
                                $messages_to_show[$i][MESG_BODY]."\n");

                       if($messages_to_show[$i][MESG_FROMWOTAGS] != "&CMD") {
                       /// loggin messages to common user privates
                       $send_to_id = -1;

                       $user_name = $messages_to_show[$i][MESG_FROMWOTAGS];
                       $whisper   = $messages_to_show[$i][MESG_TO];
                       $mesg      = $messages_to_show[$i][MESG_BODY];
                       $html_nick = $messages_to_show[$i][MESG_FROM];


                       if($messages_to_show[$i][MESG_TO] ==  $sw_usr_all_link   or
                          $messages_to_show[$i][MESG_TO] ==  $sw_usr_adm_link   or
                          $messages_to_show[$i][MESG_TO] ==  $sw_usr_boys_link  or
                          $messages_to_show[$i][MESG_TO] ==  $sw_usr_girls_link or
                          $messages_to_show[$i][MESG_TO] ==  $sw_usr_they_link  or
                          $messages_to_show[$i][MESG_TO] ==  $sw_usr_clan_link  or
                          $messages_to_show[$i][MESG_TO] ==  $sw_usr_shaman_link) {
                                  //sending to group
                                  $group = 1;
                                  if($messages_to_show[$i][MESG_TO] ==  $sw_usr_all_link) $send_to_id    = 0;
                                  if($messages_to_show[$i][MESG_TO] ==  $sw_usr_adm_link) $send_to_id    = 1;
                                  if($messages_to_show[$i][MESG_TO] ==  $sw_usr_shaman_link) $send_to_id = 2;
                                  if($messages_to_show[$i][MESG_TO] ==  $sw_usr_boys_link) $send_to_id   = 3;
                                  if($messages_to_show[$i][MESG_TO] ==  $sw_usr_girls_link) $send_to_id  = 4;
                                  if($messages_to_show[$i][MESG_TO] ==  $sw_usr_they_link) $send_to_id   = 5;
                                  if($messages_to_show[$i][MESG_TO] ==  $sw_usr_clan_link) {
                                          $send_to_id   = 6;
                                          if(intval($messages_to_show[$i][MESG_CLANID]) > 0) $clan_id = $messages_to_show[$i][MESG_CLANID];
                                  }

                                  if($send_to_id > -1 and ($messages_to_show[$i][MESG_TO] != $messages_to_show[$i][MESG_FROMWOTAGS])) include($data_path."engine/files/user_private_post_message.php");

                                  //sending to user (in case in group-to group messages we simply pass it);

                                  if(intval($messages_to_show[$i][MESG_FROMID]) > 0) {
                                     $group = 0;
                                     $send_to_id =  $messages_to_show[$i][MESG_FROMID];
                                     if($send_to_id > 0 and ($messages_to_show[$i][MESG_TO] != $messages_to_show[$i][MESG_FROMWOTAGS])) include($data_path."engine/files/user_private_post_message.php");
                                  }
                          }
                       else {
                                  $old_i = $i;
                                  if(intval($messages_to_show[$i][MESG_FROMID]) > 0) {
                                      $group = 0;
                                      $send_to_id =  $messages_to_show[$i][MESG_FROMID];
                                      if($send_to_id > 0)  include($data_path."engine/files/user_private_post_message.php");
                                  }

                                  $i = $old_i;

                                  if(intval($messages_to_show[$i][MESG_TOID]) > 0) {
                                       $group = 0;
                                       $send_to_id =  $messages_to_show[$i][MESG_TOID];
                                       if($send_to_id > 0 and $messages_to_show[$i][MESG_TO] != $messages_to_show[$i][MESG_FROMWOTAGS]) include($data_path."engine/files/user_private_post_message.php");
                                  }

                       }

                     //end if &CMD;
                     }
        }
        fflush($fp_log);
        flock($fp_log, LOCK_UN);
        fclose($fp_log);

        fflush($fp_prv_log);
        flock($fp_prv_log, LOCK_UN);
        fclose($fp_prv_log);



}
?>