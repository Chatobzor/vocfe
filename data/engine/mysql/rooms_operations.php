<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");

function change_topic($room_id, $topic) {
	global $mysql_table_prefix;
	mysql_query("update ".$mysql_table_prefix."rooms set topic='".addslashes($topic)."' where id=".intval($room_id)) or die("database error: cannot update rooms infomration");
}

function room_delete($room_id) {
	global $mysql_table_prefix;
	mysql_query("delete from ".$mysql_table_prefix."rooms where id=".intval($room_id)) or die("database error: cannot update rooms infomration");
}



function room_update($room_id,$name,$topic,$design,$bot,$allowed_users=-1,$allow_pics = -1, $premoder = -1) {
	global $mysql_table_prefix;
	if ($allowed_users != -1) $room_array[ROOM_ALLOWEDUSERS] = $allowed_users;
	if ($allow_pics != -1) $room_array[ROOM_ALLOWPICS] = $allow_pics;
	if ($premoder != -1) $room_array[ROOM_PREMODER] = $premoder;
	
	mysql_query("update ".$mysql_table_prefix."rooms set name='".addslashes($name)."',
			topic='".addslashes($topic)."', 
			design='".addslashes($design)."', 
			bot_name='".addslashes($bot)."'".
			(($allowed_users != -1) ? ", allowed_users='".addslashes($allowed_users)."'":"").
			(($allow_pics != -1) ? ", allow_pics=".intval($allow_pics):"").
			(($premoder != -1) ?", premoder=".intval($premoder):"").
			" where id=".intval($room_id)) or die("database error: cannot update rooms infomration".mysql_error());
}
function room_add($name,$topic,$design,$bot,$creator = "",$allowed_users="",$allow_pics = 0, $premoder = 0) {
	global $mysql_table_prefix;
	mysql_query("insert into ".$mysql_table_prefix."rooms (name,topic,design,bot_name, creator, allowed_users, allow_pics, premoder)
			 values('".addslashes($name)."','".addslashes($topic)."','".addslashes($design)."','".
			 	addslashes($bot)."', '".addslashes($creator)."', '".addslashes($allowed_users)."', ".intval($allow_pics).", ".intval($premoder).")") or die("database error: cannot update rooms infomration");
	return mysql_insert_id();
}
?>