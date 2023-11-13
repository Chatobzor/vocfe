<?php

if (!defined("_COMMON_")) {
    echo "stop";
    exit;
}
include($file_path."designes/".$design."/common_title.php"); ?>
<?php
include($file_path."designes/".$design."/common_body_start.php"); ?>
<center>
    <table border="0" cellspacing="8" width="720">
        <form action="<?php
        echo $chat_url; ?>voc.php" method="POST" target="_parent">
            <input type="hidden" name="user_lang" value="<?php
            echo $user_lang; ?>">
            <tr>
                <td rowspan="2" valign="top" width="250"><h3><?php
                        echo $w_title ?></h3>
                    <a href="http://mvoc.ru" target="_blank"><img src="http://mvoc.ru/public/img/ref/b01.jpg" border="0"
                                title="Все для VOC++ чатов" alt="Все для VOC++ чатов"></a>
                    <?php
                    if ($club_mode) {
                        echo "<br>".$w_registered_only."<br><a href=\"".$chat_url."registration_form.php?design=".$design."\" target=\"_parent\">".$w_registration."</a>";
                    } ?>
                    <?php
                    if (count($allowed_langs) > 1) {
                        echo "<br><br>".$w_sel_lang.": | ";
                        for ($i = 0; $i < count($allowed_langs); $i++) {
                            echo "<a href=\"".$chat_url."?design=".$design."&user_lang=".$allowed_langs[$i]."\" target=\"_parent\">".$allowed_langs[$i]."</a> | ";
                        }
                    } ?>

                </td>
                <td rowspan="2">&nbsp;&nbsp;&nbsp;</td>
                <td valign="top">
                    <table>
                        <tr>
                            <td><?php
                                echo $w_enter_login_nick; ?>:
                            </td>
                            <td><input type="text" name="user_name" size=15 value="<?php
                                echo $c_user_name; ?>" class="input" tabindex="100"></td>
                            <td> &nbsp; <input type="Submit" value="<?php
                                echo $w_login_button ?>" class="input" tabindex="105"></td>
                        </tr>
                        <tr>
                            <td colspan="3"><small><?php
                                    echo $w_login; ?></small></td>
                        </tr>
                        <tr>
                            <td><?php
                                echo $w_password; ?>:
                            </td>
                            <td><input type="password" name="password" size=15 value="" class="input" tabindex="101">
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><?php
                                if (!$club_mode) {
                                    echo "<small>".$w_for_registered."</small>";
                                } ?></td>
                        </tr>

                        <?php
                        if (count($room_ids) > 1){ ?>
                        <tr>
                            <td><?php
                                echo $w_select_room; ?>:
                            </td>
                            <td><select name="room" class="input"
                                        onchange="javascript:parent.voc_shower.location.href='shower.php?design=<?php
                                        echo $design; ?>&room='+this.options[this.selectedIndex].value;" tabindex="102">
                                    <?php
                                    for ($i = 0; $i < count($room_ids); $i++) {
                                        echo "<option value=\"".$room_ids[$i]."\"";
                                        if ($room_ids[$i] == $room_id) {
                                            echo " selected";
                                        }
                                        echo ">".$rooms[$room_ids[$i]]["title"]." (".$rooms[$room_ids[$i]]["users"].")</option>\n";
                                    }
                                    }//end (if (count(room_ids)>1)
                                    ?>
                                </select>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                        if (count($chat_types) > 1)
                        {
                        echo "<tr><td>".$w_select_type; ?>:&nbsp;
                </td>
                <td>
                    <select name="chat_type" class="input" tabindex="103">
                        <?php
                        for ($i = 0; $i < count($chat_types); $i++) {
                            echo "<option value=\"".$chat_types[$i]."\"";
                            if ($chat_type == $chat_types[$i]) {
                                echo " selected";
                            }
                            echo ">".$w_chat_type[$chat_types[$i]]."</option>\n";
                        }
                        ?>
                    </select></td>
                <td>&nbsp;</td>
            </tr>
            <?php
            }
            if (count($designes) > 1) {
                echo "<tr><td>$w_select_design: </td><td><select name=\"design\" onChange=\"javascript:parent.document.location='index.php?user_lang=".$user_lang."&design='+this.options[this.selectedIndex].value;\" class=\"input\" tabindex=\"104\">";
                for ($i = 0; $i < count($designes); $i++) {
                    echo "<option value=\"".$designes[$i]."\"";
                    if ($designes[$i] == $design) {
                        echo " selected";
                    }
                    echo ">".$designes[$i]."</option>\n";
                }
                echo "</select></td><td>&nbsp;</td></tr>\n";
            } else {
                echo "<input type=\"hidden\" name=\"design\" value=\"$design\">\n";
            }
            ?>
    </table>
    </td></tr>

    </form>
    </table>
</center>
<?php
include($file_path."designes/".$design."/common_body_end.php");
