<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$ttt = str_replace("*","%",addslashes($user_to_search));
$m_result = mysql_query("select id, nick from ".$mysql_table_prefix."users where nick like '%$ttt%'") or die("database error: cannot search user<br>".mysql_error());
while ($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
	$u_ids[] = $row[0];
	$u_names[] = $row[1];
}
mysql_free_result($m_result);
?>