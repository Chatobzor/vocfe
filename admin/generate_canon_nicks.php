<?php include("check_session.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<?php
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<blockquote><span class=head><font color=Black>
<?php
$dummy = " ";
for($i = 0; $i < 128; $i++) $dummy .= "&nbsp;\n";
$curr  = 0;

include("../inc_common.php");
if ($long_life_data_engine == "mysql") {
	include_once($ld_engine_path."inc_connect.php");
}
include("../inc_to_canon_nick.php");
if ($long_life_data_engine == "files") {
	$users = array();
	$users = file($user_data_file);
    $pass = count($users) / 298;
    if($pass < 1) $pass = 1;

    echo "<script>parent.frames['wr'].reInitBar($pass, ".count($users).");</script>\n";

    flush();
    ob_end_flush();
	for ($i=0; $i<count($users);$i++)
	{
        echo " ";
       	if(intval($i - $curr) >= intval($pass)) {
        	$curr = $i;
    		//echo $dummy;
	        echo "<script>parent.frames['wr'].incrCount();</script>\n";
            ob_flush();
        }

		$user = str_replace("\n","",$users[$i]);
		list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user);
		$t_canon = to_canon_nick($t_nickname);
		$users[$i] = "".$t_id."\t".$t_nickname."\t".$t_password."\t".$t_class."\t".$t_canon."\t".$t_mail."\n";
	}
	$fp = fopen($user_data_file, "w");
	flock($fp, LOCK_EX);
	fwrite($fp,implode("",$users));
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
    echo "<script>parent.frames['wr'].finish();alert('$adm_gen_canon_ok')</script>";
} else if ($long_life_data_engine == "mysql") {
	$m_result = mysql_query("select id, nick from ".$mysql_table_prefix."users") or die("database error<br>".mysql_error());
	while($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
		$canon_nick = addslashes(to_canon_nick($row[1]));
		mysql_query("update ".$mysql_table_prefix."users set canon_nick='".$canon_nick."' where id=".$row[0]) or die("database error<br>".mysql_error());
	}
	mysql_free_result($m_result);
}
?>
</font></blockquote>
</td></tr></table></center>
</body>
</html>