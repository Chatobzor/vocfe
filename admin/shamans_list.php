<?php
include("check_session.php");
include("../inc_common.php");
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<?php

echo "<center><h2 style=\"color:#265D92;font-family:Verdana\">$adm_shamans_list</h2></center>\n";
	$fp = fopen($data_path."shamans_list.tmp", "rb");
    if($fp) {
		while ($data = fgets($fp, 4096)) {
			$user = str_replace("\r","",str_replace("\n","",$data));
			list($t_id, $t_nickname) = explode("\t",$user);
			echo "<a href=\"moderators.php?lang=$lang&user_id=$t_id&session=$session\">$t_nickname</a><br>\n";
		}
    fclose($fp);
    }
?>

</td></tr></table>
<p>
<span class=tip><font size="2" color=black><?php echo $adm_new_shaman; ?>.</font></span></center>
</body>
</html>