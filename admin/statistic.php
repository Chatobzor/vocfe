<?php include("check_session.php");
include("header.php");
if (!isset($p))$p = "";?>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.setTimeout('location.href="statistic.php?lang=<?php echo $lang; ?>&session=<?php echo $session;?>&p=<?php echo $p;?>"',500000);
//-->
</script>
<?php if ($p != "y" and $p != "") $p = ""; ?>
<div align=center><font size=5 face="Verdana, Tahoma" color="#265D92" align=CENTER><b><?php echo $adm_statistics; ?></b></font><br>
<span class=tip>(<b><a class=tip href="statistic.php?lang=<?php echo $lang; ?>&session=<?php echo $session;?>&p=<?php echo $p; ?>"><span class=tip><?php echo $adm_refresh; ?></span></a></b>, <?php echo $adm_check_stat_for; ?> <b><a href="statistic.php?lang=<?php echo $lang; ?>&session=<?php echo $session;?>&p=<?php   if($p == "y") echo "";
																																																							   			 else echo "y"; ?>"><span class=tip>
                                                                                                                                                                                                                                         <?php   if($p == "") { echo $adm_yesterday; $day_word = $adm_today; }
                                                                                                                                                                                                                                                 else { echo $adm_today; $day_word = $adm_yesterday; }
                                                                                                                                                                                                                                         ?></span></a></b>)</span></div>
<blockquote><font size=2 style="font-family: Verdana, Tahoma;"><b><?php echo $adm_users." ".$day_word; ?>:</b></font></blockquote>
<div align=CENTER><img border=1 src="users_statistic.php?session=<?php echo $session;?>&a=<?php echo time();?>&p=<?php echo $p; ?>" width="700" height="320"></div>
<blockquote><font size=2 style="font-family: Verdana, Tahoma;"><b><?php echo $adm_messages_per_m." ".$day_word; ?>:<br>
<span class=tip>- <?php echo $adm_blue_expl; ?>;<br>
- <?php echo $adm_green_expl; ?>;<br>
- <?php echo $adm_red_expl; ?>.<br></b></span></font></blockquote>
<div align=CENTER><img  border = 1 src="mess_statistic.php?session=<?php echo $session;?>&a=<?php echo time();?>&p=<?php echo $p; ?>" width="700" height="320"></div>
</body>
</html>