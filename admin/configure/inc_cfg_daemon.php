<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}
                echo "<input type=\"hidden\" name=\"session\" value=\"".$session."\">";
                echo "<table border=\"0\" width=\"500\">";
//well, it's already defined in the path to inc_common.php at the step 1
//                echo "<tr><td colspan=\"2\">System (file) path to the root of your web-chat interface: </td></tr>";
//                echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><input type=\"text\" size=\"75\" class=\"input\" name=\"file_path\" value=\"$file_path\"></td></tr>";
//                echo "<tr><td colspan=\"2\">*This is the system path to your <b>chat/</b> directory. The last symbol must be a slash!!!<br>";
//                echo "I guess, it should be something like <b>".realpath("../")."/</b></td></tr>\n";
//                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";


                echo "<tr><td><nobr><b>$adm_chat_url:</b> </nobr></td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"chat_url\" value=\"$chat_url\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_chat_url_tip ";
                $check_for_root = dirname(dirname($HTTP_SERVER_VARS["REQUEST_URI"]));
                if ($check_for_root == "/") $check_for_root = "";
                echo "<b>http://".$HTTP_SERVER_VARS["SERVER_NAME"].$check_for_root."/</b></font></td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td><nobr><b>$adm_image_url</b>: </nobr></td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"images_url\" value=\"$images_url\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_image_tip\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td><nobr><b>$adm_daemon_url</b>: </nobr></td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"daemon_host\" value=\"$daemon_host\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_daemon_url_tip <b>http://".$HTTP_SERVER_VARS["SERVER_NAME"]."</b>. \n";
                echo "$adm_daemon_url_tip2</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";


               echo "<tr><td valign=\"top\"><nobr><b>$adm_listen_type</b>: </nobr></td><td>".
                        "<input type=\"radio\" value=\"1\" name=\"daemon_type\"".(($daemon_type!=2)?" checked":"")."> $adm_listen_standart.<br>".
                        "<input type=\"radio\" value=\"2\" name=\"daemon_type\"".(($daemon_type==2)?" checked":"")."> $adm_listen_mod.<br>".
                        "</td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_listen_note</td></tr>";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td><nobr><b>$adm_daemon_port</b>: </nobr></td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"daemon_port\" value=\"$daemon_port\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_daemon_port_tip</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "<tr><td><nobr><b>$adm_daemon_listen</b>: </nobr></td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"daemon_listen\" value=\"$daemon_listen\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_daemon_listen_tip</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

        echo "<tr><td><nobr>$adm_mod_voc_socket: </nobr></td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"modvoc_socket\" value=\"$modvoc_socket\"></td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_modvoc_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
        //silly checking
        //determinig locale in UNIX/Linux with locale -a command (not BSD's!)
        $handle = popen('locale -a', 'r');
        $read   = "";
        $selVal = "";
        while (!feof ($handle)) {
             $read = fgets($handle, 4096);
             $read = trim($read);
             if(strlen($read) == 0) continue;
             $selVal .= "<option value=\"$read\" ";
                if($read == $locale) $selVal .= "selected";
             $selVal .= " >$read</option>";
        }
        pclose($handle);

        echo "<tr><td><nobr><b>$adm_locale</b>: </nobr></td><td>";
        if($selVal != "") { echo "<select class=\"input\" name=\"locale\">";
                            echo $selVal;
                            echo "</select>";
                          }
        else {
                echo "<input type=\"text\" class=\"input\" name=\"locale\" value=\"$locale\">";
        }
        echo "</td></tr>";
                echo "<tr><td colspan=\"2\" class=tip>*$adm_locale_note.</td></tr>\n";
                echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

                echo "</table>";
?>