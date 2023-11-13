<html>
<head>
    <title>PASSWORD SECURITY</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo DEFAULT_CHARSET; ?>">
    <style>
        td, small, input {
            font-family: Tahoma, Verdana;
            color: white;
            font-size: 8pt;
            font-weight: bold;
        }

        input {
            font-family: Tahoma, Verdana;
            color: #abd256;
            font-size: 8pt;
            font-weight: bold;
        }

        .input_button {
            font-family: Verdana, Arial, Helvetica, sans-serif, Tahoma;
            font-size: 10px;
            background-color: #7E9B16;
            color: #FFFFFF;
            height: auto;
            width: auto;
            border: #98C00B;
            border-style: solid;
            border-top-width: 1px;
            border-right-width: 1px;
            border-bottom-width: 1px;
            border-left-width: 1px
        }
    </style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="middle" align="center">

            <table width="502" height="502" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="middle" align="center">

                        <table width="500" height="500" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <img src="<?= $current_design ?>images/pass_01.gif" width="199" height="59" alt="">
                                </td>
                                <td colspan="2">
                                    <img src="<?= $current_design ?>images/pass_02.gif" width="301" height="59" alt="">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="<?= $current_design ?>images/pass_03.gif" width="199" height="90" alt="">
                                </td>
                                <td bgcolor="#abd256" width="226" height="90" alt="">
                                    <font color="red" face="Tahoma" size="2"><b><?= $w_pass_secutity_time ?></b></font>
                                </td>
                                <td>
                                    <img src="<?= $current_design ?>images/pass_05.gif" width="75" height="90" alt="">
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3" height="351" bgcolor="#abd256" valign="up">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= $w_pass_secutity_note ?>
                                    <blockquote><?= $w_current_password ?></blockquote>
                                    <?php
                                    if ($info_message != "") {
                                        ?>
                                        <div align='center'><font color=red><?= $info_message ?></font></div>
                                    <?php
                                    } ?>
                                    <form action="user_info.php" method="POST">
                                        <input type="hidden" name="session" value="<?= $session ?>">
                                        <input type="hidden" name="act" value="change_pass">
                                        <table border="0" align="center">
                                            <TR>
                                                <TD><?= $w_password ?>:</TD>
                                                <TD><INPUT type="password" name="old_password" class="input"></td>
                                            </tr>
                                            <TR>
                                                <TD><I><?= $w_new_password ?></I>:</TD>
                                                <TD><INPUT type="password" name="passwd1" class="input"></TD>
                                            </TR>
                                            <TR>
                                                <TD><?= $w_confirm_password ?>:</TD>
                                                <TD><INPUT type="password" name="passwd2" class="input"></TD>
                                            </TR>
                                            <tr>
                                                <td colspan="2" align="right">
                                                    <hr noshade>
                                                    <INPUT type="submit" value="<?= $w_update ?>" class="input_button">
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>