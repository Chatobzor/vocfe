<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
mysql_query("delete from ".$mysql_table_prefix."who where user_name like '".addslashes($nameToBan)."'") or die(mysql_error());
?>