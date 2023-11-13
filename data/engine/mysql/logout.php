<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";
$m_result = mysql_query("select * from ".$mysql_table_prefix."who where session='".addslashes($session)."'") or die(mysql_error());
$exists = mysql_num_rows($m_result);
$user_array = mysql_fetch_array($m_result, MYSQL_NUM);

$user_name = $user_array[USER_NICKNAME];
$user_array[USER_TIME]=time();
$exists = 1;
$is_regist = $user_array[USER_REGID];
$user_ip = $user_array[USER_IP];
$room_id = $user_array[USER_ROOM];
if (!in_array($user_array[USER_SKIN], $designes)) $user_array[USER_SKIN] = $default_design;
$design = $user_array[USER_SKIN];
$current_design = $chat_url."designes/".$design."/";
if ($user_array[USER_LANG] != $language) {
	if (!in_array($user_array[USER_LANG], $allowed_langs)) $user_array[USER_LANG] = $language;
	else { include_once($file_path."languages/".$user_array[USER_LANG].".php"); }
	$user_lang = $user_array[USER_LANG];
}
$cu_array = $user_array;
mysql_free_result($m_result);
if ($exists) mysql_query("delete from ".$mysql_table_prefix."who where session='".addslashes($session)."'") or die(mysql_error());
?>