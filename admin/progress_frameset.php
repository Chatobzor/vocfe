<?php include("check_session.php");
          include("../inc_common.php");
          set_variable("import_mysql_server");
          set_variable("import_mysql_user");
          set_variable("import_mysql_password");
          set_variable("import_mysql_db");
          set_variable("import_mysql_table_prefix");

      if($import_mysql_server != "") {
      //maybe some mysl or permision errors
?>
<frameset rows="50%,*" cols="*" framespacing="0" frameborder=0>
<?php } else { ?>
<frameset rows="100%,*" cols="*" framespacing="0" frameborder=0>
<?php } ?>
  <frame src="progress.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>&operation=<?php echo $operation;?>&import_mysql_server=<? echo $import_mysql_server; ?>&import_mysql_user=<? echo $import_mysql_user; ?>&import_mysql_password=<? echo $import_mysql_password; ?>&import_mysql_db=<? echo $import_mysql_db; ?>&import_mysql_table_prefix=<? echo $import_mysql_table_prefix; ?>" name="wr">
  <frame src="blank.html" name="hp">
</frameset>

</html>