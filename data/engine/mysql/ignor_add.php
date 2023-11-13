<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$ignored_list = array();
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";
$m_result = mysql_query("select ignor from ".$mysql_table_prefix."who where session='".addslashes($session)."'") or die(mysql_error());
if (mysql_num_rows($m_result)) {
	list($ign_string) = mysql_fetch_array($m_result,MYSQL_NUM);
	$ignored_list = explode(",",$ign_string);
	if (!in_array($add_to_ignor,$ignored_list)) {
		$ignored_list[count($ignored_list)] = $add_to_ignor;
		$ign_string = implode(",",$ignored_list);
		mysql_query("update ".$mysql_table_prefix."who set ignor='".addslashes($ign_string)."' where session='".addslashes($session)."'");
	}
}
mysql_free_result($m_result);
?>