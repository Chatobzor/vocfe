<?php
include("check_session.php");
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head align=center>
<form method="post" action="user_delete.php">
<input type="hidden" name="session" value="<?php echo $session;?>">
<?php
include("../inc_common.php");
include("../inc_user_class.php");
if ($tstInfoUser !="") {
	if ($long_life_data_engine == "mysql") {
		include_once($ld_engine_path."inc_connect.php");
	}
	echo "<center><h2 style=\"color:#265D92;font-family:Verdana\">".$adm_search_results."</h2><table border=\"0\" width=250></center>\n";
	echo "<tr><td><b>$adm_user_view</b></td><td><b>$adm_check_delete</b></td></tr>";
	$users = array();
	$users_to_show = array();
	if ($long_life_data_engine == "files") {
		$t_id = 0;
		$ttt = str_replace("\\*","([".$nick_available_chars."]+)",quotemeta($tstInfoUser));

		$fp = fopen($user_data_file, "r");
		flock($fp, LOCK_EX);
		fseek($fp,0);
		$ii=0;
		while ($data = fgets($fp, 4096))
		{
			$user = str_replace("\r","",str_replace("\n","",$data));
			list($t_id, $t_nickname, $t_password, $t_class,$t_canon,$t_mail) = explode("\t",$user);
			if (eregi($ttt,$t_nickname)) $users_to_show[] = $user;
		}
		flock($fp, LOCK_UN);
		fclose($fp);

		$total_checkboxes = 0;
		if (!isset($inactiv)) $inactiv = 0;
		if ($inactiv) {
			for ($i=0;$i<count($users_to_show);$i++) {
				list($t_id, $t_nickname, $t_password, $t_class) = explode("\t",$users_to_show[$i]);
				$t_user = unserialize(implode("",file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")));
				if ($t_user->last_visit < (my_time()-$inactiv*2592000) and $t_user->registered_at < (my_time()-$inactiv*2592000)){
					$total_checkboxes++;
					echo "<tr><td><a href=\"moderators.php?lang=$lang&user_id=$t_id&session=$session\">$t_nickname</a> &nbsp; &nbsp;</td><td><input type=\"checkbox\" name=\"user_ids[]\" value=\"$t_id\"></td></tr>\n";
				}
			}
		}
		else {
			$total_checkboxes = count($users_to_show);
			for ($i=0;$i<count($users_to_show);$i++) {
				list($t_id, $t_nickname, $t_password, $t_class) = explode("\t",$users_to_show[$i]);
				echo "<tr><td><a href=\"moderators.php?lang=$lang&user_id=$t_id&session=$session\">$t_nickname</a> &nbsp; &nbsp;</td><td><input type=\"checkbox\" name=\"user_ids[]\" value=\"$t_id\"></td></tr>\n";
			}
		}
	}//end of files
	if ($long_life_data_engine == "mysql") {
		if (!isset($inactiv)) $inactiv = 0;
		else $inactiv = my_time()-$inactiv*2592000;
		$m_result = mysql_query("select * from ".$mysql_table_prefix."users where last_visit<$inactiv and nick like '%".str_replace("*","%",addslashes($tstInfoUser))."%'") or die("database error<br>".mysql_error());
		$total_checkboxes = mysql_num_rows($m_result);
		while($row = mysql_fetch_array($m_result, MYSQL_NUM))
			echo "<tr><td><a href=\"moderators.php?user_id=".$row[0]."&session=$session\">".$row[1]."</a> &nbsp; &nbsp;</td><td><input type=\"checkbox\" name=\"user_ids[]\" value=\"".$row[0]."\"></td></tr>\n";
		mysql_free_result($m_result);
	}//end of mysql

}
?>
</table><?php if ($total_checkboxes){?>
<font color=Black><?php echo $adm_check_uncheck; ?>:</font><input type="checkbox" onClick="javascript:for(i=1;i<=<?php echo $total_checkboxes;?>;i++){if ((document.forms[0].elements[i].checked && !document.forms[0].elements[<?php echo $total_checkboxes+1;?>].checked) || (!document.forms[0].elements[i].checked && document.forms[0].elements[<?php echo $total_checkboxes+1;?>].checked))document.forms[0].elements[i].click();}">
<?php }?><br><br>
<input type="submit" value="<?php echo $adm_delete;?>" class=button></form>
</td></tr></table></center>
</body>
</html>