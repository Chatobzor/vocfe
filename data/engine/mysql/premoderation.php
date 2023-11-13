<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");


function premoder_approve($mesg_id) {
	global $mysql_table_prefix, $data_path, $engine_path, $mess_stat, $messages_file, $flood_protection, $logging_messages, $flood_in_last, $flood_time;
	$messages_to_show = array();
	$m_result = mysql_query("select * from ".$mysql_table_prefix."premoder where id=".intval($mesg_id)) or die("Cannot get message. ".mysql_error());
	if ($row = mysql_fetch_array($m_result, MYSQL_NUM)){
		$messages_to_show[0] = $row;
		$messages_to_show[0][MESG_ID] = 0;
	}
	mysql_free_result($m_result);
	mysql_query("delete from ".$mysql_table_prefix."premoder  where id=".intval($mesg_id)) or die("Cannot delete message. ".mysql_error());

	if (count($messages_to_show) >0) {
		$error = "";
		$whisper = $mess_array[MESG_TO];
		include($engine_path."messages_put.php");
		if ($mess_stat == 1 && !$error) {
			$fp = fopen($data_path."mess_stat.dat", "a+");
			flock($fp, LOCK_EX);
			fseek($fp,0);
			$normal_messages = intval(str_replace("\n","",@fgets($fp,1024)));
			$private_messages = intval(str_replace("\n","",@fgets($fp,1024)));
			if ($whisper)$private_messages++;
				else $normal_messages++;
			ftruncate($fp,0);
			fwrite($fp,$normal_messages."\n".$private_messages);
			fflush($fp);
			flock($fp, LOCK_UN);
			fclose($fp);
		}
	}
	
	return premoder_get();
}

function premoder_decline($mesg_id) {
	global $mysql_table_prefix;
	mysql_query("delete from ".$mysql_table_prefix."premoder  where id=".intval($mesg_id)) or die("Cannot delete message. ".mysql_error());
	return premoder_get();
}

function premoder_get() {
	global $mysql_table_prefix;
	$mesg_list = array();
	mysql_query("delete from ".$mysql_table_prefix."premoder where time<".(my_time()-7200)) or die(mysql_error());
	$m_result = mysql_query("select * from ".$mysql_table_prefix."premoder order by id") or die("cannot get messages. ".mysql_error());
	while ($row = mysql_fetch_array($m_result, MYSQL_NUM)){
		$mesg_list[] = $row;
	}
	mysql_free_result($m_result);
	return $mesg_list;
}

function premoder_add($messages_to_show) {
	global $mysql_table_prefix;
	$m_result = mysql_query("select count(*) from ".$mysql_table_prefix."premoder where 
							fromnick='".addslashes($messages_to_show[0][MESG_FROM])."' and
							body='".addslashes($messages_to_show[0][MESG_BODY])."'") or die(mysql_error());
	$num = mysql_result($m_result, 0, 0);
	mysql_free_result($m_result);
	if ($num) return;
	
	for ($i=0;$i<count($messages_to_show);$i++) {
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
		$sql_query = "insert into ".$mysql_table_prefix."premoder (room,time,fromnick, fromwotags, fromsession,fromid, fromavatar, tonick, tosession, toid,body) values ".$message;
		mysql_query($sql_query) or die("cannot insert message".mysql_error());
	}
}

?>