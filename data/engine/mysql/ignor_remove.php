<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$ignored_list = array();
if(!isset($session)) $session = "0";
if(!is_string($session)) $session = "0";
$m_result = mysql_query("select ignor from ".$mysql_table_prefix."who where session='".addslashes($session)."'") or die(mysql_error());
$removed = 0;
if (mysql_num_rows($m_result)) {
	list($ign_string) = mysql_fetch_array($m_result,MYSQL_NUM);
	$ignored_list = explode(",",$ign_string);
	$new_ignored_list = array();
	for ($i=0;$i<count($ignored_list);$i++) {
		if ($remove_from_ignor != $ignored_list[$i])
			$new_ignored_list[] = $ignored_list[$i];
		else $removed = 1;
	}
	if ($removed)  {
		$ign_string = implode(",",$new_ignored_list);
		mysql_query("update ".$mysql_table_prefix."who set ignor='".addslashes($ign_string)."' where session='".addslashes($session)."'");
	}
}
mysql_free_result($m_result);
?>