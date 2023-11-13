<?php
if (!defined("_COMMON_")) {
    echo "stop";
    exit;
}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");
?>
    <script type="text/javascript">
        <!--
        var checkobj;

        function agreesubmit(el) {
            checkobj = el;
            if (document.all || document.getElementById) {
                for (i = 0; i < checkobj.form.length; i++) {  //hunt down submit button
                    var tempobj = checkobj.form.elements[i];
                    if (tempobj.type.toLowerCase() == "submit")
                        tempobj.disabled = !checkobj.checked;
                }
            }
        }

        function defaultagree(el) {
            if (!document.all && !document.getElementById) {
                if (window.checkobj && checkobj.checked)
                    return true;
                else {
                    alert("Please read/accept Rules to continue installation");
                    return false;
                }
            }
        }

        //-->
    </script>
<?php
include($file_path."designes/".$design."/common_body_start.php"); ?>
    <form method="post" action="registration_add.php">
        <input type="hidden" name="session" value="<?php
        echo $session; ?>">
        <?php
        echo $w_registration; ?>
        <table border="0">
            <tr>
                <td><?php
                    echo $w_enter_login_nick; ?>:
                </td>
                <td><input type="text" size=15 maxlength=15 name="new_user_name" value="<?php
                    echo $user_name; ?>" class="input"></td>
            </tr>
            <tr>
                <td colspan="2"><small><?php
                        echo $w_login; ?></small></td>
            </tr>
            <?php
            if ($registration_mailconfirm) { ?>
                <tr>
                    <td><?php
                        echo $w_email; ?></td>
                    <td>
                        <input type="text" size=15 maxlength=150 name="new_user_mail" class="input"></td>
                </tr>
                <tr>
                    <td colspan="2"><small><?php
                            echo $w_regmail_enter_mail; ?></small></td>
                </tr>
            <?php
            } ?>

            <tr>
                <td><?php
                    echo $w_password; ?>:
                </td>
                <td><input type="password" maxlength=25 size=15 name="passwd1" class="input"></td>
            </tr>
            <tr>
                <td><?php
                    echo $w_confirm_password; ?>:
                </td>
                <td><input type="password" maxlength=25 size=15 name="passwd2" class="input"></td>
            </tr>
            <?php
            if ($impro_registration) { ?>
            <input type="hidden" name="session" name="impro_id" value="<?php
            echo $session; ?>">
            <input type="hidden" name="impro_id" value="<?php
            echo $impro_id; ?>">
            <tr>
                <td><?php
                    echo $w_impro_enter_code; ?></td>
                <td><input type="text" maxlength=4 size=4 name="impro_user_code" class="input"></td>
            </tr>
            <tr>
                <td></td>
                <td><img src="<?php
                    echo $chat_url."impro.php?impro_id=".$impro_id; ?>" border="0" with="80" height="33">
                    <?
                    }
                    echo "<tr><td>$w_gender: </td><td><select name=\"new_user_sex\" class=\"input\">";
                    echo "<option value=0>$w_unknown</option>\n<option value=1>$w_male</option>\n<option value=2>$w_female</option>\n</select></td></tr>\n";
                    ?>
            <tr>
                <td></td>
                <td>
                    <iframe width="750" height="200" src="<?= $chat_url ?>rules.php" name="S1"></iframe>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><p>
                    <div id="dl"><h3><input type="checkbox" name="agreed" value="1" onClick="agreesubmit(this)"
                                    class=input/> <?php
                            echo $w_roz_agreed; ?>.</h3></div>
                    </p>
                    <script>var c = 25;
                        fc();

                        function fc() {
                            if (c > 0) {
                                document.getElementById("dl").innerHTML = "<b><input value=" + c + ' size=1> <?=$w_reg_seconds_left?>.';
                                c = c - 3;
                                setTimeout("fc()", 1000)
                            } else {
                                document.getElementById("dl").innerHTML = unescape('<input type="checkbox" name="agreed" value="1" onClick="agreesubmit(this)" class=input /> <?=$w_roz_agreed?>.')
                            }
                        }</script>
                </td>
            </tr>
        </table>
        <input type="hidden" name="ref_id" value="<?php
        echo $ref_id; ?>">
        <input type="submit" value="<?php
        echo $w_registration; ?>" class="input_button" disabled="disabled">
    </form>
<?php
include($file_path."designes/".$design."/common_body_end.php"); ?>