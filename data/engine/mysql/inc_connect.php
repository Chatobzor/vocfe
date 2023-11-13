<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if(!defined("_CONNECT_")):
define("_CONNECT_",1);
if (!mysql_connect($mysql_server, $mysql_user, $mysql_password))
	trigger_error("Cannot connect to the database. ".mysql_error(),E_USER_ERROR);
if (!mysql_select_db($mysql_db))
	trigger_error("Cannot select database. ".mysql_error(),E_USER_ERROR);
endif;
?>