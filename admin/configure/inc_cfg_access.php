<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}
                echo "<input type=\"hidden\" name=\"session\" value=\"".$session."\">";
                echo "<table border=\"0\" width=\"500\">";
                echo "<tr><td colspan=\"2\">$adm_nick_note: $adm_nick_min_len: <input type=\"text\" name=\"nick_min_length\" value=\"$nick_min_length\" class=\"input\" size=\"3\"> ";
                echo "$adm_nick_max_len: <input type=\"text\" name=\"nick_max_length\" value=\"$nick_max_length\" class=\"input\" size=\"3\"></td></tr>";
                echo "<tr><td>$adm_nick_avail_char: </td><td><input type=\"text\" name=\"nick_available_chars\" value=\"$nick_available_chars\" class=\"input\" size=\"15\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_nick_av_chr_not</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_similar_select: </td><td><select name=\"current_to_canon\" class=\"input\">";
                for ($i=0;$i<count($to_canon_funcs);$i++)
                        echo ($to_canon_funcs[$i] == $current_to_canon) ? "<option selected>".$to_canon_funcs[$i]."</option>":"<option>".$to_canon_funcs[$i]."</option>";
                echo "</select></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_similar_note";
                echo "</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";


                echo "<tr><td>$adm_club_mode: </td><td><select name=\"club_mode\" class=\"input\">";

                echo "<option value=\"1\"";
                if ($club_mode) echo " selected";
                echo ">$adm_on</option>\n";
                echo "<option value=\"0\"";
                if (!$club_mode) echo " selected";
                echo ">$adm_off</option>\n";
                echo "</select>";

                echo "</td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_club_note</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_image_protected: </td><td><select name=\"impro_registration\" class=\"input\">";
                echo "<option value=\"1\"";
                if ($impro_registration) echo " selected";
                echo ">$adm_on</option>\n";
                echo "<option value=\"0\"";
                if (!$impro_registration) echo " selected";
                echo ">$adm_off</option>\n";
                echo "</td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_image_pr_note";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";


                echo "<tr><td>$adm_email_conf: </td><td><select name=\"registration_mailconfirm\" class=\"input\">";
                echo "<option value=\"1\"";
                if ($registration_mailconfirm) echo " selected";
                echo ">$adm_on</option>\n";
                echo "<option value=\"0\"";
                if (!$registration_mailconfirm) echo " selected";
                echo ">$adm_off</option></select>\n";
                echo "</td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_email_conf_note";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_max_nicks_email: </td>".
                        "<td><input type=\"text\" name=\"max_per_mail\" value=\"".$max_per_mail."\" class=\"input\" size=\"3\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_max_nicks_note.</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "</table>";
?>