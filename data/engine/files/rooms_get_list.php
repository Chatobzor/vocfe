<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!defined("_ROOMS_")):
define("_ROOMS_",1);
$rooms_file = file($rooms_list_file);
unset($rooms);
unset($room_ids);
$room_ids = array();
$rooms = array();

$jail_id = 0;

for ($rg_i=0;$rg_i<count($rooms_file);$rg_i++) {
        if (strlen($rooms_file[$rg_i])<7) continue;
                //list($r_id, $r_title, $r_topic, $r_design,$r_bot) = explode("\t",str_replace("\r","",str_replace("\n","", $rooms_file[$rg_i])));
                $cr_ar = explode("\t", trim($rooms_file[$rg_i]), ROOM_TOTALFIELDS);
                if (intval($cr_ar[ROOM_ID]) == $cr_ar[ROOM_ID]) {
                        $room_ids[] = $cr_ar[ROOM_ID];
                        $ar_rooms[$cr_ar[ROOM_ID]] = $cr_ar;
                        //backward compability:

                        if(intval($cr_ar[ROOM_JAIL]) == 1) $jail_id = intval($cr_ar[ROOM_ID]);

                        $rooms[$cr_ar[ROOM_ID]] = array("title"=>$cr_ar[ROOM_TITLE],
                                                        "topic"=>$cr_ar[ROOM_TOPIC],
                                                        "design"=>$cr_ar[ROOM_DESIGN],
                                                        "bot"=>$cr_ar[ROOM_BOT],
                                                        "jail"=>$cr_ar[ROOM_JAIL],
                                                        "points"=>$cr_ar[ROOM_POINTS]);
                }
}
endif;
?>