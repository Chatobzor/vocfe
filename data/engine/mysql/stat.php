<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");

function engine_total_users() {
	global $mysql_table_prefix;
	$m_result = mysql_query("select count(id) from ".$mysql_table_prefix."users") or die("database error: cannot find users<br>".mysql_error());
	$total_users = mysql_result($m_result, 0, 0);
	mysql_free_result($m_result);
	return $total_users;
}


function engine_last_registered($num) {
	global $mysql_table_prefix,$file_path;
	$last_user = array();
	$m_result = mysql_query("select id, nick from ".$mysql_table_prefix."users order by id desc limit $num")or die("database error: cannot find users<br>".mysql_error());
	while ($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
		$t_id = $row[0];
		$t_nickname = $row[1];
		
		$pic_name = "".floor($t_id/2000)."/".$t_id . ".big.gif";
		if (!@file_exists($file_path."photos/$pic_name")) $pic_name="";
		if ($pic_name == "") {
			$pic_name = "".floor($t_id/2000)."/".$t_id . ".big.jpg";
			if (!@file_exists($file_path."photos/$pic_name")) $pic_name="";
		}
		$photo = ($pic_name!="") ? true:false;
		$last_users[] = array("nickname"=>$t_nickname,"photo"=>$photo,"id"=>$t_id);
	}
	mysql_free_result($m_result);
	return $last_users;
}
?>