<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";
include_once($ld_engine_path."inc_connect.php");
if (!isset($rooms))
{
	include($ld_engine_path."rooms_get_list.php");
}
$def_color = $registered_colors[$default_color][1];
$orig_f_p = $flood_protection;
$flood_protection = 1;
$users = array();
$exists = 0;
$is_regist = 0;
$user_name = "";
$ignored_users = array();
$ignored_list = array();
$room_id = 0;


$messages_to_show = array();
mysql_query("update ".$mysql_table_prefix."who set time=".time()." where session='".addslashes($session)."'");

$m_result = mysql_query("select * from ".$mysql_table_prefix."who order by user_name") or die(mysql_error());
while ($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
	if ($row[2]<time()-$disconnect_time) {
		$nick_to_remove = $row[0];
		$room_to_send = $row[10];
		mysql_query("delete from ".$mysql_table_prefix."who where user_name='".addslashes($nick_to_remove)."'") or die(mysql_error());
		if (mysql_affected_rows())
			$messages_to_show[] = array(MESG_TIME=>my_time(), 
							MESG_ROOM=>$room_to_send,  
							MESG_FROM=>$rooms[$room_to_send]["bot"], 
							MESG_FROMWOTAGS=>$rooms[$room_to_send]["bot"],
							MESG_FROMSESSION=>"",
							MESG_FROMID=>0,
							MESG_FROMAVATAR=>"",
							MESG_TO=>"", 
							MESG_TOSESSION=>"",
							MESG_TOID=>"",
							MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", $nick_to_remove, $sw_rob_idle)."</font>");
	} else {
		$users[]=implode("\t",$row);
		if ($row[USER_SESSION] == $session) {
	
			$user_name = $row[USER_NICKNAME];
			$row[USER_TIME] = time();
			$exists = 1;
			$is_regist = $row[USER_REGID];
			$tail_num = $row[USER_TAILID];
			$user_ip =$row[USER_IP];
			$room_id = $row[USER_ROOM];
			$user_status = $row[USER_STATUS];
			$user_chat_type = $row[USER_CHATTYPE];
			if (!in_array($row[USER_SKIN], $designes)) $row[USER_SKIN] = $default_design;
			if ($rooms[$room_id]["design"] != "")
				$row[USER_SKIN] = $rooms[$room_id]["design"];
			$design = $row[USER_SKIN];
			$current_design = $chat_url."designes/".$design."/";
			$ignored_list = explode(",",$row[USER_IGNORLIST]);
			for ($i=0;$i<count($ignored_list);$i++)
				$ignored_users[strtolower($ignored_list[$i])] = 1;
			$cu_array = $row;
			if ($row[USER_LANG] != $language) {
				if (!in_array($row[USER_LANG], $allowed_langs)) $row[USER_LANG] = $language;
				else { 
					include_once($file_path."languages/".$row[USER_LANG].".php"); 
					$registered_colors = $s_registered_colors;
					$default_color = $s_default_color;
					$highlighted_color = $s_highlighted_color;
				}
				$user_lang = $row[USER_LANG];
			}
		}
	}
}
mysql_free_result($m_result);
include($engine_path."messages_put.php");
unset($messages_to_show);
$flood_protection = $orig_f_p;
?>