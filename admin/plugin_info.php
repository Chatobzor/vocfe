<?php include("check_session.php");
          include("../inc_common.php");
          set_variable("plugin");
?>
<frameset rows="230,*" cols="*" framespacing="0" frameborder=0>
  <frame src="plugin_view.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>&plugin=<?php echo $plugin;?>" name="wr">
  <frame src="plugin_configure.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>&plugin=<?php echo $plugin;?>" name="hp">
</frameset>

</html>