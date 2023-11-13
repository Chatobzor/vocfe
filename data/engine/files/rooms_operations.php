<?php
if (!defined("_COMMON_")) {echo "stop";exit;}


function change_topic($room_id, $topic) {
        global $rooms_list_file;
        $fp = fopen($rooms_list_file, "a+");
        if (!flock($fp, LOCK_EX)) die ("can't lock file");
        fseek($fp,0);
        $to_write = "";
        while ($line = fgets($fp, 16984)) {
                if (strlen($line)<7) continue;
                $room_array = explode("\t", trim($line), ROOM_TOTALFIELDS);
                if ($room_array[ROOM_ID] == $room_id) {
                        $room_array[ROOM_TOPIC] = $topic;
                        $to_write .= implode("\t",$room_array) . "\n";
                } else $to_write .= trim($line)."\n";
        }
        ftruncate($fp,0);
        fwrite($fp, $to_write);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
}

function room_delete($room_id) {
        global $rooms_list_file;
        $fp = fopen($rooms_list_file, "a+");
        if (!flock($fp, LOCK_EX)) die ("can't lock file");
        fseek($fp,0);
        $to_write = "";
        while ($line = fgets($fp, 16984)) {
                if (strlen($line)<7) continue;
                $room_array = explode("\t", trim($line), ROOM_TOTALFIELDS);
                if ($room_array[ROOM_ID] != $room_id) $to_write .= trim($line)."\n";
        }
        ftruncate($fp,0);
        fwrite($fp, $to_write);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
}

function room_update($room_id,$name,$topic,$design,$bot,$allowed_users=-1,$allow_pics = -1, $premoder = -1, $club = -1, $pass, $jail = -1, $points = -1) {
        global $rooms_list_file;
        //$new_data = array ($name,$topic,$design,$bot);
        $fp = fopen($rooms_list_file, "a+");
        if (!flock($fp, LOCK_EX)) die ("can't lock file");
        fseek($fp,0);
        $to_write = "";
        while ($line = fgets($fp, 16984)) {
                if (strlen($line)<7) continue;
                $room_array = explode("\t", trim($line), ROOM_TOTALFIELDS);
                if ($room_array[ROOM_ID] == $room_id) {
                        $room_array[ROOM_TITLE] = $name;
                        $room_array[ROOM_TOPIC] = $topic;
                        $room_array[ROOM_DESIGN] = $design;
                        $room_array[ROOM_BOT] = $bot;
                        if ($allowed_users != -1) $room_array[ROOM_ALLOWEDUSERS] = $allowed_users;
                        if ($allow_pics != -1) $room_array[ROOM_ALLOWPICS] = $allow_pics;
                        if ($premoder != -1) $room_array[ROOM_PREMODER] = $premoder;

                        if ($club != -1) $room_array[ROOM_CLUBONLY] = $club;
                        $room_array[ROOM_PASSWORD] = $pass;
                        if ($jail != -1) $room_array[ROOM_JAIL]     = $jail;

                        if ($club != -1) $room_array[ROOM_POINTS] = $points;

                        $to_write .= implode("\t",$room_array) . "\n";
                } else $to_write .= trim($line)."\n";
        }
        ftruncate($fp,0);
        fwrite($fp, $to_write);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
}
function room_add($name,$topic,$design,$bot,$creator = "",$allowed_users="",$allow_pics = 0, $premoder = 0, $club = 0, $pass = "", $jail = 0, $points = 0) {
        global $rooms_list_file;
        $new_data = array ($name,$topic,$design,$bot);
        $fp = fopen($rooms_list_file, "a+");
        if (!flock($fp, LOCK_EX)) die ("can't lock file");
        fseek($fp,0);
        $to_write = "";
        $last_id = 0;
        while ($line = fgets($fp, 16984)) {
                if (strlen($line)<7) continue;
                $room_array = explode("\t", trim($line), ROOM_TOTALFIELDS);
                if ($room_array[ROOM_ID] > $last_id) $last_id = $room_array[ROOM_ID];
                $to_write .= trim($line)."\n";
        }
        $last_id++;
        $room_array[ROOM_ID] = $last_id;
        $room_array[ROOM_TITLE] = $name;
        $room_array[ROOM_TOPIC] = $topic;
        $room_array[ROOM_DESIGN] = $design;
        $room_array[ROOM_BOT] = $bot;
        $room_array[ROOM_CREATOR] = $creator;
        $room_array[ROOM_ALLOWEDUSERS] = $allowed_users;
        $room_array[ROOM_ALLOWPICS] = $allow_pics;
        $room_array[ROOM_PREMODER] = $premoder;

        if ($club != -1) $room_array[ROOM_CLUBONLY] = $club;
        $room_array[ROOM_PASSWORD] = $pass;
        if ($jail != -1) $room_array[ROOM_JAIL]     = $jail;

        if ($points != -1) $room_array[ROOM_POINTS] = $points;

        $to_write .= implode("\t",$room_array) . "\n";
        ftruncate($fp,0);
        fwrite($fp, $to_write);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
}

?>