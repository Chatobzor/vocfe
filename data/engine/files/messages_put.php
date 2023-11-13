<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if (!isset($error_text)) $error_text = "";

if (!defined("_CMP_MTIME_")):
define("_CMP_MTIME_", 1);
function cmp_mTime($a, $b)
{
   if (intval($a[MESG_TIME]) == intval($b[MESG_TIME])) {
       return 0;
   }
   return ($a[MESG_TIME] < $b[MESG_TIME]) ? -1 : 1;
}
endif;

if(count($messages_to_show)) {
        unset($messages);
        unset($newMessages);
        $is_flood = 0;
        $messages = array();
        $fp_mes = fopen($messages_file, "r+b");
        if (!$fp_mes) trigger_error("Could not messages.dat for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp_mes, LOCK_EX))
                trigger_error("Could not LOCK messages.dat. Do you use Win 95/98/Me?", E_USER_WARNING);

        $last_id = 0;
        while($ttt=fgets($fp_mes, 16384)) {
                if (strlen($ttt)<7) continue;
                $ttt = trim($ttt);
                list($test_last_id, $mess_stuff) = explode("\t",$ttt);
                if (is_numeric($test_last_id)) {
                        $messages[] = $ttt;
                        $last_id = $test_last_id+1;
                }
        }
        $total_messages = count($messages);

        if ($flood_protection) {
                $flood_start = (($total_messages - $flood_in_last)>0) ? ($total_messages-$flood_in_last):0;
                for ($i=$flood_start;$i<$total_messages;$i++) {
                        $mess_array = explode("\t",$messages[$i],MESG_TOTALFIELDS);
                        if (strcmp($mess_array[MESG_FROMWOTAGS],$messages_to_show[0][MESG_FROMWOTAGS])==0 ||
                                        strcmp($mess_array[MESG_FROMSESSION],$messages_to_show[0][MESG_FROMSESSION]) == 0) {
                                if ($mess_array[MESG_TIME]+$flood_time > my_time()) {
                                        $is_flood = 1;
                                        $error = 1;
                                        $error_text .= $w_flood."<br>\n";
                                        break;
                                }
                                for ($j=0;$j<count($messages_to_show);$j++) {
                                        if (strcmp(        strip_tags(str_replace("<img","",str_replace(" ","", $mess_array[MESG_BODY]))),
                                                                strip_tags(str_replace("<img","",str_replace(" ","",$messages_to_show[$j][MESG_BODY]))) ) == 0) {
                                                $is_flood = 1;
                                                $error = 1;
                                                $error_text .= $w_flood."<br>\n";
                                                break;
                                        }
                                }
                        }
                        if ($is_flood) break;
                }
        }
        if (!$is_flood) {

                usort($messages_to_show, "cmp_mTime");

                for ($i=0;$i<count($messages_to_show);$i++) {
                        $new_mess = $last_id;
                        for($j=1;$j<MESG_TOTALFIELDS;$j++) {
                            if($j == MESG_CLANID) $messages_to_show[$i][$j] = intval($messages_to_show[$i][$j]);
                                        $new_mess .= "\t".$messages_to_show[$i][$j];
                }
                        $messages[] = $new_mess;
                        $last_id++;
                }
                if ($logging_messages) {
                        include_once($data_path."engine/files/log_message.php");
                        log_message();
                }
        }
        $total_messages = count($messages);
        $start_at = ($total_messages > 40)? ($total_messages-40) : 0;

        fseek($fp_mes, 0);
        for ($i=$start_at; $i<$total_messages;$i++)
                fwrite($fp_mes, $messages[$i]."\n");
        fflush($fp_mes);
        ftruncate($fp_mes,ftell($fp_mes));
        flock($fp_mes, LOCK_UN);
        fclose($fp_mes);
}

?>