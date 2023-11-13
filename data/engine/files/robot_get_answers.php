<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$phrases = file($robotspeak_file);
for ($i=0;$i<count($phrases); $i++) {
        $phrase = str_replace("\n","",$phrases[$i]);
        list($user_phrase, $robot_answer, $prob) = split("\t", $phrase);
        $user_phrase = htmlspecialchars($user_phrase);
        $robot_answer = htmlspecialchars($robot_answer);

        $robot_answer = addURLS($robot_answer);

        if (stristr($to_robot, $user_phrase) != false) {
                if (rand(0, 10)>= (10-$prob)) {
                        $robot_answer = str_replace("~", $user_name, $robot_answer);
                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                MESG_ROOM=>$room_id,
                                                                                MESG_FROM=>$w_rob_name,
                                                                                MESG_FROMWOTAGS=>$w_rob_name,
                                                                                MESG_FROMSESSION=>"",
                                                                                MESG_FROMID=>0,
                                                                                MESG_TO=>"",
                                                                                MESG_TOSESSION=>"",
                                                                                MESG_TOID=>"",
                                                                                MESG_BODY=>"<font color=\"$def_color\">$robot_answer</font>");
                        break;
                }
        }
}
?>
