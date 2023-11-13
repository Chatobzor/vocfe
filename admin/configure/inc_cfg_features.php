<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}
                include_once("../inc_common.php");
                echo "<input type=\"hidden\" name=\"session\" value=\"".$session."\">";
                echo "<table border=\"0\" width=\"500\">";

                echo "<tr><td>$adm_store_statistic: </td><td><select class=\"input\" name=\"mess_stat\">";
                echo "<option value=\"0\"";if (!$mess_stat)echo " selected";echo ">$adm_off</option>\n";
                echo "<option value=\"1\"";if ($mess_stat)echo " selected";echo ">$adm_on</option>\n";
                echo "</select></td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_store_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>$adm_en_web_indicat: </td><td><select class=\"input\" name=\"web_indicator\">";
                echo "<option value=\"0\"";if (!$web_indicator)echo " selected";echo ">$adm_on</option>\n";
                echo "<option value=\"1\"";if ($web_indicator)echo " selected";echo ">$adm_off</option>\n";
                echo "</select></td></tr>\n";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_en_web_ind_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td colspan=\"2\">$adm_en_logging:</td></tr>";
                echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"logging_messages\"";
                if ($logging_messages == 1) echo " checked";
                echo "> $adm_log_messsages;</td></tr>";
                echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"logging_ban\"";
                if ($logging_ban == 1) echo " checked";
                echo "> $adm_log_bans.</td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*".str_replace("[data]", $data_path, $adm_log_note)."</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
        /*
        //commented by DD

                echo "<tr><td>Enable images posting : </td><td><select class=\"input\" name=\"allow_pics\">";
                echo "<option value=\"0\"";if (!$allow_pics)echo " selected";echo ">No</option>\n";
                echo "<option value=\"1\"";if ($allow_pics)echo " selected";echo ">Yes</option>\n";
                echo "</select></td></tr>\n";
                echo "<tr><td colspan=\"2\">*Users can upload .jpg/.gif/.png file or send a link to an image on some site in the internet.<br>".
                        "The image will be stored on the chat-server and img-tag will be posted to the messages-frame.<br>".
                        "It means this feature requires _A LOT OF TRAFFIC_, so think twice before turning it on.<br>".
                        "You also have to install some cronjob to remove old, unused images from the server (see README).<br>".
                        "The feature is ROOM-BASED, you also need to enable it for particular room (in the chat/admin)</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td>Maximum size of image (in Bytes): </td><td>".
                        "<input type=\"text\" name=\"pics_maxsize\" size=\"6\" value=\"".$pics_maxsize."\" class=\"input\"></td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
                echo "<tr><td>Maximum width of image (in pixels): </td><td>".
                        "<input type=\"text\" name=\"pics_maxw\" size=\"6\" value=\"".$pics_maxw."\" class=\"input\"></td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
                echo "<tr><td>Maximum height of image (in pixels): </td><td>".
                        "<input type=\"text\" name=\"pics_maxh\" size=\"6\" value=\"".$pics_maxh."\" class=\"input\"></td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
        */
        //VOC++ Guardian
        if(is_file($data_path."engine/files/guardian.php")) {
          echo "<tr><td colspan=\"2\"><b>VOC++ Guardian</b>:</td></tr>";
                  echo "<tr><td colspan=\"2\" class=tip>*$adm_guardian_note</td></tr>";
          echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"vocplus_guardian_dellogs\"";
                   if (intval($vocplus_guardian_dellogs) == 1) echo " checked";
                  echo "> $adm_allow_del_logs.</td></tr>";
                  echo "<tr><td colspan=\"2\" class=tip>*$adm_allow_del_lg_n.</td></tr>";
          echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
        }

                echo "</table>";
?>