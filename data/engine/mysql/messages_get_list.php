<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select * from ".$mysql_table_prefix."messages  order by id desc limit 40") or die(mysql_error());
$total_messages = mysql_num_rows($m_result);
$messages = array();
for ($i=$total_messages-1; $row = mysql_fetch_row($m_result); $i--)
	$messages[$i] = implode("\t", $row);
mysql_free_result($m_result);
?>