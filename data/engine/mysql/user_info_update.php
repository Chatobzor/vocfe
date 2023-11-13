<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
include_once($ld_engine_path."inc_connect.php");
//update col user_class!
mysql_query("update ".$mysql_table_prefix."users set user_class=".intval($current_user->user_class).", passwd='".addslashes($current_user->password)."', user_info='".addslashes(serialize($current_user))."' where id=$is_regist") or die("database error: cannot update user information<br>".mysql_error());
$info_message .= $w_succ_updated."<br>";
?>