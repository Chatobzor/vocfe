<?php
include("check_session.php");
include("header.php");
include("../inc_common.php");

set_variable("mode");
set_variable("import_mysql_server");
set_variable("import_mysql_user");
set_variable("import_mysql_password");
set_variable("import_mysql_db");
set_variable("import_mysql_table_prefix");

if( $mode == "import"
	and mysql_connect($import_mysql_server, $import_mysql_user, $import_mysql_password)
    and mysql_select_db($import_mysql_db)) {
       ?>
       <script language="JavaScript" type="text/javascript">
       <!--
	       location.href='<?php echo $chat_url; ?>admin/progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=import&import_mysql_server=<? echo $import_mysql_server; ?>&import_mysql_user=<?php echo $import_mysql_user; ?>&import_mysql_password=<?php echo $import_mysql_password; ?>&import_mysql_db=<?php echo $import_mysql_db; ?>&import_mysql_table_prefix=<?php echo $import_mysql_table_prefix; ?>';
       //->
       </script>
       <?php
        exit;
    }

		echo "<center><h2 style=\"color:#265D92;font-family:Verdana\">$adm_mysql_import</h2></center>\n";
        echo "<form method=\"POST\" action=\"".$chat_url."admin/import.php\">\n";
        echo "<input type=\"hidden\" name=\"session\" value=\"$session\">\n";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">\n";
        echo "<input type=\"hidden\" name=\"mode\" value=\"import\">\n";
		echo "<table border=\"0\" width=\"500\" align=center>";
		echo "<tr><td colspan=\"2\" align=center>$adm_mysql_import_no</td></tr>";
        echo "<tr><td>$adm_mysql_server:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"import_mysql_server\" value=\"$import_mysql_server\"></td></tr>";
		echo "<tr><td>$adm_mysql_username:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"import_mysql_user\" value=\"$import_mysql_user\"></td></tr>";
		echo "<tr><td>$adm_mysql_password:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"import_mysql_password\" value=\"$import_mysql_password\"></td></tr>";
		echo "<tr><td>$adm_mysql_db_name:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"import_mysql_db\" value=\"$import_mysql_db\"></td></tr>";
		echo "<tr><td>$adm_mysql_prefix:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"import_mysql_table_prefix\" value=\"$import_mysql_table_prefix\"></td></tr>";

			if ($mode == "import" and !mysql_connect($import_mysql_server, $import_mysql_user, $import_mysql_password))
			{
				echo "<tr><td colspan=\"2\"><font color=Red><b>$adm_mysql_error</b></font></td></tr>";
			}
			else
			{
				if ($mode == "import" and !mysql_select_db($import_mysql_db)) echo "<tr><td colspan=\"2\"><b>!!!! $adm_mysql_error_db $import_mysql_db !!!!</b></td></tr>";
			}

		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "<tr><td colspan=\"2\" align=center>";
        echo "<input type=\"submit\" class=\"button_small\" value=\"$adm_mysql_import\"></form>\n";
        echo "</td></tr>";
		echo "</table>";

?>