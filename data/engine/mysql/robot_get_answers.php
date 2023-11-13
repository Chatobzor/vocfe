<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select * from ".$mysql_table_prefix."robotspeak") or die("database error<br>".mysql_error());
while ($row = mysql_fetch_row($m_result)) {
	$user_phrase = htmlspecialchars($row[0]);
	$robot_answer = htmlspecialchars($row[1]);

	if (stristr($to_robot, $user_phrase) != False) {
		if (rand(0, 10)>= (10-$row[2])) {
			$robot_answer = str_replace("~", $user_name, $robot_answer);
			$messages_to_show[] = array(MESG_TIME=>my_time(),
									MESG_ROOM=>$room_id,
									MESG_FROM=>$rooms[$room_id]["bot"],
									MESG_FROMWOTAGS=>$rooms[$room_to_send]["bot"],
									MESG_FROMSESSION=>"",
									MESG_FROMID=>0,
									MESG_FROMAVATAR=>"",
									MESG_TO=>"",
									MESG_TOSESSION=>"",
									MESG_TOID=>"",
									MESG_BODY=>"<font color=\"$def_color\">".$robot_answer."</font>");
			//$messages_to_show[] = array("time"=>my_time(), "room_id"=>$room_id, "from"=>"<b>$w_rob_name</b>", "to"=>"","body"=>"<font color=\"$def_color\">$robot_answer</font>");
			//		$newMes = my_time() . "\tVoice\t\t$answer\t$DefaultColorNum";
			break;
		}
	}
}
mysql_free_result($m_result);
?>