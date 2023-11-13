<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}
		echo "<input type=\"hidden\" name=\"session\" value=\"".$session."\">";
		echo "<table border=\"0\" width=\"500\">";
        //echo "<tr><td colspan=\"2\">$adm_engines_sysV:<br>\n";
		$engines[0][0] = "Files";
		$engines[0][1] = "files";
        //VOC++ commented
	   //$engines[1][0] = "MySQL";
	   //$engines[1][1] = "mysql";
       /*
		if (function_exists('shmop_open')) {
			$engines[2][0] = "Shared memory";
			$engines[2][1] = "shm";
			echo "$adm_shared_exists</td></tr>\n";
		}
		else
			echo "$adm_shared_not_exst</td></tr>\n";
        */
		//echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "<tr><td>$adm_select_main_eng:</td><td>";
		echo "<select name=\"engine\" class=\"input\">";
		for ($i=0;$i<count($engines);$i++){
			echo "<option value=\"".$engines[$i][1]."\"";
			if ($engine == $engines[$i][1]) echo " selected";
			//echo ">".$engines[$i][0]."</option>\n";
            echo ">".$lng_engines[$i]."</option>\n";
		}
		echo "</select></td></tr>\n";
		echo "<tr><td>$adm_select_add_eng:</td><td>";
		$ld_engines[0][0] = "Files";
		$ld_engines[0][1] = "files";
	   //$ld_engines[1][0] = "MySQL";
	   //$ld_engines[1][1] = "mysql";
		echo "<select name=\"long_life_data_engine\" class=\"input\">";
		for ($i=0;$i<count($ld_engines);$i++) {
			echo "<option value=\"".$ld_engines[$i][1]."\"";
			if ($long_life_data_engine == $ld_engines[$i][1]) echo " selected";
			echo ">".$lng_engines[$i]."</option>\n";
		}
		echo "</select></td></tr>\n";

		if (function_exists('shmop_open')) {
			echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
			echo "<tr><td colspan=\"2\">Please, set Shared Memory blocks IDs (if you want to use shared memory engine). It should be just two numbers, i.e. 804,805 by default. But be carefull, you can lost your data if this IDs are used by other programs and/or users (check it with ipcs)</td></tr>";
			echo "<tr><td>Messages block id:</td><td><input type=\"text\" class=\"input\" name=\"shm_mess_id\" size=\"5\" value=\"$shm_mess_id\"></td></tr>\n";
			echo "<tr><td>User-list block id:</td><td><input type=\"text\" class=\"input\" name=\"shm_users_id\" size=\"5\" value=\"$shm_users_id\"></td></tr>\n";
		}

		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "<tr><td colspan=\"2\"><b>$adm_mysql_settings:</b></td></tr>";
		echo "<tr><td>$adm_mysql_server:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"mysql_server\" value=\"$mysql_server\"></td></tr>";
		echo "<tr><td>$adm_mysql_username:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"mysql_user\" value=\"$mysql_user\"></td></tr>";
		echo "<tr><td>$adm_mysql_password:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"mysql_password\" value=\"$mysql_password\"></td></tr>";
		echo "<tr><td>$adm_mysql_db_name:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"mysql_db\" value=\"$mysql_db\"></td></tr>";
		//echo "<tr><td>Prefix for MySQL Tables:</td><td><input type=\"text\" size=\"15\" class=\"input\" name=\"mysql_table_prefix\" value=\"$mysql_table_prefix\"></td></tr>";

		//if ($long_life_data_engine == "mysql" or $engine == "mysql") {

			if (!mysql_connect($mysql_server, $mysql_user, $mysql_password))
			{
				echo "<tr><td colspan=\"2\"><font color=Red><b>$adm_mysql_error</b></font></td></tr>";
			}
			else
			{
				if (!mysql_select_db($mysql_db)) echo "<tr><td colspan=\"2\"><b>!!!! $adm_mysql_error_db $mysql_db !!!!</b></td></tr>";
			}
        // }
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		$all_chat_types = array("tail","js_tail","reload","php_tail");
		echo "<tr><td>$adm_chat_types: </td><td>";
		echo "<select name=\"chat_types[]\" multiple class=\"input\">";
		for($i=0;$i<count($all_chat_types);$i++)
		{
			echo "<option value=\"".$all_chat_types[$i]."\"";
			if (in_array($all_chat_types[$i],$chat_types)) echo " selected";
			echo ">".$all_chat_types[$i]."</option>\n";
		}
		echo "</select></td></tr>\n";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_chat_types_tip</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "</table>";

?>