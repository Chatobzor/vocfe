<?php
include("check_session.php");
include("../inc_common.php");
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<?php

echo "<center><h2 style=\"color:#265D92;font-family:Verdana\">$adm_moder_list</h2></center>\n";
if ($long_life_data_engine == "mysql") {
	include_once($ld_engine_path."inc_connect.php");
}

if ($long_life_data_engine == "files") {
	$fp = fopen($user_data_file, "r");
	flock($fp, LOCK_EX);
	while ($data = fgets($fp, 4096)) {
		$user = str_replace("\r","",str_replace("\n","",$data));
		list($t_id, $t_nickname, $t_password, $t_class,$t_canon, $t_mail) = explode("\t",$user);
		if ($t_class>0 or $t_class == "admin") {
			echo "<a href=\"moderators.php?lang=$lang&user_id=$t_id&session=$session\">$t_nickname</a><br>\n";
		}
	}
	flock($fp, LOCK_UN);
	fclose($fp);
} else if ($long_life_data_engine == "mysql") {
	$m_result = mysql_query("select id, nick from ".$mysql_table_prefix."users where user_class>0") or die("database error<br>".mysql_error());
	while(list($t_id,$t_nickname) = mysql_fetch_array($m_result, MYSQL_NUM)) {
		echo "<a href=\"moderators.php?lang=$lang&user_id=$t_id&session=$session\">$t_nickname</a><br>\n";
	}
	mysql_free_result($m_result);
}

?>

</td></tr></table>
<p>
<span class=tip><font size="2" color=black><?php echo $adm_new_moder; ?>.</font></span></center>
</body>
</html>