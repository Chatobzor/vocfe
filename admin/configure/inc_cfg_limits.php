<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}
                echo "<input type=\"hidden\" name=\"session\" value=\"".$session."\">";
                echo "<table border=\"0\" width=\"500\">";

        echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"open_chat\"";
                if ($open_chat == 1) echo " checked";
                echo "> $adm_open_chat.</td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*".$adm_open_chat_tip.".</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

        echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"enable_gzip\"";
                if ($enable_gzip == 1) echo " checked";
                echo "> $adm_enable_gzip.</td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*".$adm_enable_gzip_not.".</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

        echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"enable_reiting\"";
                if ($enable_reiting == 1) echo " checked";
                echo "> $adm_photo_reiting.</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_md5_salt: </td><td><input type=\"text\" size=\"15\" maxlength=\"15\" class=\"input\" name=\"md5_salt\" value=\"$md5_salt\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_md5_salt_value.</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_email: </td><td><input type=\"text\" size=\"25\" class=\"input\" name=\"admin_mail\" value=\"".str_replace("\\@","@",$admin_mail)."\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_email_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_time_offset: </td><td><input type=\"text\" name=\"time_offset\" value=\"".($time_offset/3600)."\" class=\"input\" size=\"3\"> $adm_time_hours</td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_time_note. ";
                echo "$adm_time_guess <script>document.write( Math.round((Math.round((new Date()).getTime()/1000)-".time().")/3600));</script>.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_mailbox_size: </td><td><input type=\"text\" size=\"5\" class=\"input\" name=\"max_mailbox_size\" value=\"$max_mailbox_size\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_mailbox_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_disconnect: </td><td><input type=\"text\" size=\"5\" class=\"input\" name=\"disconnect_time\" value=\"$disconnect_time\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_disconnect_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_history_size: </td><td><input type=\"text\" size=\"5\" class=\"input\" name=\"history_size\" value=\"$history_size\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_history_note.</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td colspan=\"2\">$adm_photo_limits:</td></tr>\n";
                echo "<tr><td>$adm_maximum_size </td><td><input type=\"text\" name=\"max_photo_size\" value=\"".$max_photo_size."\" class=\"input\"> $adm_size_bytes;</td></tr>\n";
                echo "<tr><td>$adm_maximum_width </td><td><input type=\"text\" name=\"max_photo_width\" value=\"".$max_photo_width."\" class=\"input\"> $adm_size_pixels;</td></tr>\n";
                echo "<tr><td>$adm_maximum_height </td><td><input type=\"text\" name=\"max_photo_height\" value=\"".$max_photo_height."\" class=\"input\"> $adm_size_pixels.</td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_size_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_maximum_users: </td><td><input type=\"text\" size=\"3\" class=\"input\" name=\"max_connect\" value=\"$max_connect\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_maximum_usr_not.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_max_conn_ip: </td><td><input type=\"text\" size=\"3\" class=\"input\" name=\"max_from_ip\" value=\"$max_from_ip\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_max_conn_ip_not.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_max_capital: </td><td><input type=\"text\" class=\"input\" name=\"max_cap_letters\" size=\"5\" value=\"$max_cap_letters\"></td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_max_cap_note.</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_max_smileys: </td><td><input type=\"text\" size=\"3\" class=\"input\" name=\"max_images\" value=\"$max_images\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_max_smileys_not</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_flood_protect: </td><td><select name=\"flood_protection\" class=\"input\">\n";
                echo "<option value=\"1\"";
                echo ($flood_protection==1)? " selected":"";
                echo ">$adm_on</option>\n<option value=\"0\"";
                echo ($flood_protection==0)? " selected":"";
                echo ">$adm_off</option>\n</select><br>\n";
                echo "<tr><td colspan=\"2\">$adm_time_messages: </td></tr>";
                echo "<tr><td></td><td><input type=\"text\" name=\"flood_time\" value=\"$flood_time\" size=\"2\" class=\"input\"></td></tr>\n";
                echo "<tr><td colspan=\"2\">$adm_check_last</td></tr>";
                echo "<tr><td></td><td><input type=\"text\" name=\"flood_in_last\" value=\"$flood_in_last\" size=\"3\" class=\"input\"></td></tr>";
                echo "<tr><td></td><td>$adm_max_messages.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "</table>";
?>