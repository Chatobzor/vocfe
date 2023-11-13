<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!defined("_ROOMS_")):
define("_ROOMS_",1);
include_once($ld_engine_path."inc_connect.php");
unset($rooms);
unset($room_ids);
$room_ids = array();
$rooms = array();
$m_result = mysql_query("select * from ".$mysql_table_prefix."rooms") or die("database error: cannot access rooms infomration");
while ($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
	$room_ids[] = $row[ROOM_ID];
	$ar_rooms[$row[ROOM_ID]] = $row;

	$rooms[$row[0]] = array("title"=>$row[ROOM_TITLE], "topic"=>$row[ROOM_TOPIC], "design"=>$row[ROOM_DESIGN], "bot"=>$row[ROOM_BOT]);
}
mysql_free_result($m_result);
endif;
?>