<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
$m_result = mysql_query("select user_name from ".$mysql_table_prefix."who where user_id=".intval($is_regist)) or die(mysql_error());
$exists = mysql_num_rows($m_result);
?>