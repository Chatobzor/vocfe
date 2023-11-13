<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
function check_ban($to_check) {
	if (!is_array($to_check)) return 0;

	global $mysql_table_prefix;
	mysql_query("delete from ".$mysql_table_prefix."banlist where until<".my_time());
	$sql_in = "";
	for ($i=0;$i<count($to_check);$i++)
		$sql_in .= (($i>0) ? ",":"") ."'".addslashes($to_check[$i])."'";
	$m_result = mysql_query("select who from ".$mysql_table_prefix."banlist where who in (".$sql_in.")") or die("cannot check ban-info.".mysql_error());
	$banned = (mysql_num_rows($m_result)) ? 1:0;
	mysql_free_result($m_result);
	return $banned;
}



function get_all_bans() {
	global $mysql_table_prefix;
	$banned_users = array();
	$m_result = mysql_query("select who, until from ".$mysql_table_prefix."banlist");
	while ($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
		$banned_users[count($banned_users)] = array("who"=>$row[0],"until"=>$row[1]);
	}
	mysql_free_result($m_result);
	return $banned_users;
}

function unban($to_unban) {
	global $mysql_table_prefix;
	mysql_query("delete from ".$mysql_table_prefix."banlist where who='".addslashes($to_unban)."'");
}

?>