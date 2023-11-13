<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(count($messages_to_show)) {
	if (!isset($error_text)) $error_text = "";
	include_once($ld_engine_path."inc_connect.php");
	$is_flood = 0;
	if($flood_protection) {
		include($ld_engine_path."messages_get_list.php");
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
					if (strcmp(	strip_tags(str_replace("<img","",str_replace(" ","", $mess_array[MESG_BODY]))), 
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
	
	if (!$is_flood)
	{
		// i need several inserts to know the last id.
		$message = "";
		$last_id = 0;
		for ($i=0;$i<count($messages_to_show);$i++) {
		//print_r($messages_to_show[$i]);
			$message = "(".intval($messages_to_show[$i][MESG_ROOM]).",".
					intval($messages_to_show[$i][MESG_TIME]).", ".
					"'".addslashes($messages_to_show[$i][MESG_FROM])."', ".
					"'".addslashes($messages_to_show[$i][MESG_FROMWOTAGS])."', ".
					"'".addslashes($messages_to_show[$i][MESG_FROMSESSION])."', ".
					intval($messages_to_show[$i][MESG_FROMID]).",".
					"'".addslashes($messages_to_show[$i][MESG_FROMAVATAR])."',".
					"'".addslashes($messages_to_show[$i][MESG_TO])."', ".
					"'".addslashes($messages_to_show[$i][MESG_TOSESSION])."', ".
					"'".addslashes($messages_to_show[$i][MESG_TOID])."', ".
					"'".addslashes($messages_to_show[$i][MESG_BODY])."')";
			$sql_query = "insert into ".$mysql_table_prefix."messages (room,time,fromnick, fromwotags, fromsession,fromid, fromavatar, tonick, tosession, toid,body) values ".$message;
			mysql_query($sql_query) or die("cannot insert message".mysql_error());
			if (mysql_insert_id()>$last_id) $last_id = mysql_insert_id();
		}
		//$sql_query = "insert into ".$mysql_table_prefix."messages (room,time,from_nick,to_nick,body) values ".implode(",",$messages);
		if ($last_id) 
			mysql_query("delete from ".$mysql_table_prefix."messages where id<".($last_id-39)) or die(mysql_error());
		if ($logging_messages) {include_once($data_path."engine/files/log_message.php"); log_message();}
	}
}
?>