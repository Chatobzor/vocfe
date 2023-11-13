<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if (count($fields_to_update))
{
	$field_names = array("user_name", "session", "time", "sex", "photo", "user_id", "tail_id", "remote_addr", "user_status", "last_action", "room", "fake_ignor", "canon_nick", "chat_type", "user_lang", "
						htmlnick", "priv_tailid", "cookie", "browserhash", "user_class", "design");
	//1--chars, 0 -- numbers:
	$field_types = array(1,1,0,0,1,0,0,1,1,0,0,0,1,0,1,1,0,1,1,0,1);
	
	$sql_query = "update ".$mysql_table_prefix."who set ";
	
	$setters = array();
	for($j=0;$j<count($fields_to_update);$j++)
		$setters[] = ($field_types[$fields_to_update[$j][0]]) ? 
						$field_names[$fields_to_update[$j][0]] . "='" .addslashes($fields_to_update[$j][1])."'"
						:$field_names[$fields_to_update[$j][0]] . "=" .intval($fields_to_update[$j][1]);
	$sql_query .= implode(", ", $setters)." where session='".addslashes($session)."'";
	mysql_query($sql_query) or die(mysql_error());
	
	$m_result = mysql_query("select * from ".$mysql_table_prefix."who where session='".addslashes($session)."'") or die(mysql_error());
	$row = mysql_fetch_array($m_result, MYSQL_NUM);
	$cu_array = $row;
	$exists = 1;
	$user_name = $row[USER_NAME];
	$is_regist = $row[USER_REGID];
	$tail_num = $row[USER_TAILID];
	$user_ip = $row[USER_IP];
	$room_id = $row[USER_ROOM];
	$user_status = $row[USER_STATUS];
	$user_chat_type = $row[USER_CHATTYPE];
	$design = $row[USER_SKIN];
	if (!in_array($design, $designes)) $design = $default_design;
	if ($rooms[$room_id]["design"] != "")
		$design = $rooms[$room_id]["design"];
	$current_design = $chat_url."designes/".$design."/";
	$ignored_list = explode(",",$row[11]);
	for ($i=0;$i<count($ignored_list);$i++)
		$ignored_users[strtolower($ignored_list[$i])] = 1;
}
?>