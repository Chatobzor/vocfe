<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");

function impro_get_code($impro_id) {
	global $mysql_table_prefix;
	$code = "error";
	$m_result = mysql_query("select code from ".$mysql_table_prefix."impro where id='".addslashes($impro_id)."'") or die("database error, cannot get image code<br>".mysql_error());
	if (mysql_num_rows($m_result)) 
		$code = mysql_result($m_result, 0, 0);
	mysql_free_result($m_result);
	return $code;
}

function impro_save($impro_id, $impro_code) {
	global $mysql_table_prefix;
	mysql_query("delete from ".$mysql_table_prefix."impro where time<".(time()-1200)) or die("database error, cannot delete old image codes<br>".mysql_error());
	mysql_query("insert into ".$mysql_table_prefix."impro values (".time().", '".addslashes($impro_id)."', ".intval($impro_code).")") or die("database error, cannot save image code<br>".mysql_error());
}

function impro_check($impro_id, $impro_code) {
	global $mysql_table_prefix;
	mysql_query("delete from ".$mysql_table_prefix."impro where time<".(time()-1200)) or die("database error, cannot delete old image codes<br>".mysql_error());
	mysql_query("delete from ".$mysql_table_prefix."impro where id='".addslashes($impro_id)."' and code=".intval($impro_code)."") or die("database error, cannot check image code<br>".mysql_error());
	return mysql_affected_rows();
}
?>