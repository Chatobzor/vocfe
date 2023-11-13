<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$canon_view = to_canon_nick($user_name);
$exists = 0;
$is_regist = 0;
$hi = 0;


$nicks_to_remove = array();
$m_result = mysql_query("select user_name, room from ".$mysql_table_prefix."who where time<".(time()-$disconnect_time)) or die(mysql_error());
while ($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
	$nick_to_remove = $row[0];
	$room_to_send = $row[1];
	$nicks_to_remove[] = "'".addslashes($nick_to_remove)."'";
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
}
mysql_free_result($m_result);
if (count($nicks_to_remove))
	mysql_query("delete from ".$mysql_table_prefix."who where user_name in (".implode(",",$nicks_to_remove).")") or die(mysql_error());

$m_result = mysql_query("select count(session) from ".$mysql_table_prefix."who where session!='".addslashes($session)."'") or die(mysql_error());
$total_in_chat = mysql_result($m_result,0,0);
mysql_free_result($m_result);
if ($total_in_chat>=$max_connect) {
	$error_text = "$w_too_many<br><a href=\"index.php\">$w_try_again_later</a>";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}

$m_result = mysql_query("select count(session) from ".$mysql_table_prefix."who where remote_addr='".addslashes($REMOTE_ADDR)."' and session!='".addslashes($session)."'")or die(mysql_error());
$total_from_ip = mysql_result($m_result,0,0);
if ($total_from_ip>$max_from_ip) {
	$error_text = "$w_too_many_from_ip<br><a href=\"index.php\">$w_try_again_later</a>";
	include($file_path."designes/".$design."/error_page.php");
	exit;
}

$m_result = mysql_query("select * from ".$mysql_table_prefix."who where canon_nick='".addslashes($canon_view)."'");
$exists = mysql_num_rows($m_result);
if ($exists && !$registered_user) {
	$error_text = "$w_already_used<br><a href=\"index.php\">$w_try_again</a>";
	include($file_path."designes/".$design."/error_page.php");
	mysql_free_result($m_result);
	exit;
}
$tmp_name = "" . floor($registered_user/2000) . "/" . $registered_user . ".gif";
$tmp_name2 = "" .  floor($registered_user/2000) . "/" . $registered_user . ".jpg";
$photo = "";
if (file_exists($file_path."photos/$tmp_name")) 
	$photo = $tmp_name;
elseif(file_exists($file_path."photos/$tmp_name2")) 
	$photo = $tmp_name2;

if (!in_array($design, $designes)) $design = $default_design;
if ($exists) {
			$user_array[USER_NICKNAME] = $user_name;
			$user_array[USER_SESSION] = $session;
			$user_array[USER_TIME] = time();
			$user_array[USER_GENDER] = $sex;
			$user_array[USER_AVATAR] = "";
			#check for small photo
			$tmp_name = "" . floor($registered_user/2000) . "/" . $registered_user . ".gif";
			$tmp_name2 = "" .  floor($registered_user/2000) . "/" . $registered_user . ".jpg";
			if (file_exists($file_path."photos/$tmp_name")) 
				$user_array[USER_AVATAR] = $tmp_name;
			elseif(file_exists($file_path."photos/$tmp_name2")) 
				$user_array[USER_AVATAR] = $tmp_name2;
			$user_array[USER_REGID] = $registered_user;
			$user_array[USER_TAILID] = "0";
			$user_array[USER_IP] = $REMOTE_ADDR;
			$user_array[USER_STATUS] = 0;
			$user_array[USER_LASTSAYTIME] = my_time();
			$user_array[USER_ROOM] = $room_id;
			$user_array[USER_CANONNICK] = to_canon_nick($user_name);
			$user_array[USER_HTMLNICK] = $htmlnick;
			$user_array[USER_CHATTYPE] = $chat_type;
			$user_array[USER_LANG] = $user_lang;
			$user_array[USER_COOKIE] = $c_hash;
			$user_array[USER_CLASS] = $current_user->user_class;
			$user_array[USER_BROWSERHASH] = $browser_hash;
			if (!in_array($design, $designes)) $design = $default_design;
			$user_array[USER_SKIN] = $design;
			$exists = $registered_user;
			$hi = 0;
			$cu_array = $user_array;
	mysql_query("update ".$mysql_table_prefix."who set 
							user_name='".addslashes($user_name)."',
							session='".addslashes($session)."', 
							time=".time().", 
							sex=".intval($sex).", 
							photo='".addslashes($photo)."', 
							user_id=".intval($registered_user).", 
							tail_id=0, 
							remote_addr='".addslashes($REMOTE_ADDR)."', 
							user_status=0, 
							last_action=".my_time().", 
							room=".intval($room_id).", 
							chat_type='".addslashes($chat_type)."', 
							user_lang='".addslashes($user_lang)."', 
							design='".addslashes($design)."',
							htmlnick='".addslashes($htmlnick)."',
							cookie='".addslashes($c_hash)."' 
							where canon_nick='".addslashes($canon_view)."'") or die("cannot update.".mysql_error());
	$hi = 0;
}
else {
	mysql_query("insert into ".$mysql_table_prefix."who (user_name,session,time,sex,photo,user_id,tail_id,remote_addr,user_status,last_action,
									room,chat_type,user_lang,design,canon_nick,htmlnick, cookie, browserhash, user_class) 
							values (
							'".addslashes($user_name)."',
							'".addslashes($session)."', 
							".time().", 
							".intval($sex).", 
							'".addslashes($photo)."', 
							".intval($registered_user).", 
							0, 
							'".addslashes($REMOTE_ADDR)."', 
							0, 
							".my_time().", 
							".intval($room_id).", 
							'".addslashes($chat_type)."', 
							'".addslashes($user_lang)."', 
							'".addslashes($design)."',
							'".addslashes($canon_view)."',
							'".addslashes($htmlnick)."',
							'".addslashes($c_hash)."',
							'".addslashes($browser_hash)."',
							".intval(($registered_user) ? $current_user->user_class : 0).")") or die("cannot insert".mysql_error());
							
	//copy from files/voc.php, to fill cu_array
	//it's much better to move it into chat/voc.php
	$user_array = array_fill(0,USER_TOTALFIELDS-1,"");
	$user_array[USER_NICKNAME] = $user_name;
	$user_array[USER_SESSION] = $session;
	$user_array[USER_TIME] = time();
	$user_array[USER_GENDER] = $sex;
	$user_array[USER_AVATAR] = "";
	#check for small photo
	$tmp_name = "" . floor($registered_user/2000) . "/" . $registered_user . ".gif";
	$tmp_name2 = "" .  floor($registered_user/2000) . "/" . $registered_user . ".jpg";
	if (file_exists($file_path."photos/$tmp_name")) 
		$user_array[USER_AVATAR] = $tmp_name;
	elseif(file_exists($file_path."photos/$tmp_name2")) 
		$user_array[USER_AVATAR] = $tmp_name2;
	$user_array[USER_REGID] = $registered_user;
	$user_array[USER_TAILID] = "0";
	$user_array[USER_IP] = $REMOTE_ADDR;
	$user_array[USER_STATUS] = 0;
	$user_array[USER_LASTSAYTIME] = my_time();
	$user_array[USER_ROOM] = $room_id;
	$user_array[USER_CANONNICK] = to_canon_nick($user_name);
	$user_array[USER_HTMLNICK] = $htmlnick;
	$user_array[USER_CHATTYPE] = $chat_type;
	$user_array[USER_LANG] = $user_lang;
	$user_array[USER_IGNORLIST] = "";
	$user_array[USER_COOKIE] = $c_hash;
	$user_array[USER_BROWSERHASH] = $browser_hash;
	$user_array[USER_CLASS] = ($registered_user) ? $current_user->user_class : 0;
	if (!in_array($design, $designes)) $design = $default_design;
	$user_array[USER_SKIN] = $design;
	$cu_array = $user_array;
	$hi = 1;
}
?>