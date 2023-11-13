<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
function users_delete($user_ids) {
        global $user_data_file;
        global $data_path;

        $fp = fopen($user_data_file, "r+b");
        flock($fp, LOCK_EX);
        fseek($fp,0);
        $read_pos = ftell($fp);
        $write_pos = ftell($fp);
        while (!feof($fp)) {
                $line_count = 0;
                $new_users = array();
                fseek($fp, $read_pos);
                $has_del = 0;
                while (($data = fgets($fp, 4096))){
                        $user = str_replace("\r","",str_replace("\n","",$data));
                        list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user);
                        if (!in_array($t_id, $user_ids))
                                $new_users[] = $user;
                        else riseEvent(EVENT_REM_USER, $t_nickname, $t_id);        
                        $line_count++;
                }
                $fend = feof($fp);
                $read_pos = ftell($fp);
                fseek($fp, $write_pos);
                $total_users = count($new_users);
                for ($i=0;$i<$total_users;$i++) {
                        fwrite($fp,$new_users[$i]);
                        if ($i<($total_users)-1 || !$fend ) fwrite($fp, "\n");
                }
                $write_pos = ftell($fp);
                if ($fend) {
                        fflush($fp);
                        ftruncate($fp, ftell($fp));
                        break;
                }
                unset($new_users);
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        //the same for similar nicks table
        $fp = fopen($data_path."similar_nicks.tmp", "r+b");
        flock($fp, LOCK_EX);
        fseek($fp,0);
        $read_pos = ftell($fp);
        $write_pos = ftell($fp);
        while (!feof($fp)) {
                $line_count = 0;
                $new_users = array();
                fseek($fp, $read_pos);
                $has_del = 0;
                while (($data = fgets($fp, 4096))){
                        $user = str_replace("\r","",str_replace("\n","",$data));
                        list($t_id, $t_nickname, $t_password, $t_dummy, $t_dummy00, $t_dumm0, $t_dummy1, $t_dummy2) = explode("\t",$user);
                        if (!in_array($t_id, $user_ids))
                                $new_users[] = $user;
                        $line_count++;
               }
                $fend = feof($fp);
                $read_pos = ftell($fp);
                fseek($fp, $write_pos);
                $total_users = count($new_users);
                for ($i=0;$i<$total_users;$i++) {
                        fwrite($fp,$new_users[$i]);
                        if ($i<($total_users)-1 || !$fend ) fwrite($fp, "\n");
                }
                $write_pos = ftell($fp);
                if ($fend) {
                        fflush($fp);
                        ftruncate($fp, ftell($fp));
                        break;
                }
                unset($new_users);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
}
?>