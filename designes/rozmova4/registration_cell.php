<?php
if (!defined("_COMMON_")) {
    echo "stop";
    exit;
}
?>
<html>
<head>
    <title><?= $w_title ?></title>
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

            <table width="502" height="402" border="1" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="middle" align="center">
                        <table width="500" height="400" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td colspan="2" width="500" height="90">
                                    <img src="<?= $current_design ?>images/welcome_01.gif" width="298" height="90"
                                            alt=""></td>
                                <td>
                                    <img src="<?= $current_design ?>images/welcome_02.gif" width="202" height="90"
                                            alt=""></td>
                            </tr>
                            <tr>
                                <td width="171" height="82"><img src="<?= $current_design ?>images/welcome_03.gif"
                                            width="171" height="82" alt=""></td>
                                <td colspan="2" width="329" height="82" bgcolor="#abd256" style="font-size:10pt;">
                                    <?= $w_chat_welcome_main ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" bgcolor="#abd256" width="500" height="228">
                                    <blockquote>
                                        <?= $w_chat_welcome_text ?>
                                        <table width="400" height="228" cellspacing=5 border=0 align=center>
                                            <form method="post" action="<?php
                                            echo $chat_url; ?>voc.php">
                                                <input type="hidden" name="user_name" value="<?php
                                                echo $user_name; ?>">
                                                <input type="hidden" name="user_color" value="<?php
                                                echo $user_color; ?>">
                                                <input type="hidden" name="room" value="<?php
                                                echo $room; ?>">
                                                <input type="hidden" name="reg_word" value="true">
                                                <input type="hidden" name="impro_id" value="<?php
                                                echo $impro_id; ?>">
                                                <tr>
                                                    <td colspan=4></td>
                                                </tr>
                                                <tr>
                                                    <td><b><?= $w_impro_enter_code ?>:</b></td>
                                                    <td><img border=1 src="<?php
                                                        echo $chat_url."impro.php?impro_id=".$impro_id; ?>" border="0"
                                                                with="80" height="33">
                                                    </td>
                                                    <td><input type="text" maxlength=4 size=4 name="impro_user_code"
                                                                class="input"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan=4 align=center>(<?= $w_chat_welcome_note ?>)</td>
                                                </tr>
                                                <tr>
                                                    <td align="center" colspan=4><input type="submit"
                                                                value="<?= $w_chat_go ?>!" class="input_button"></td>
                                                </tr>
                                        </table>
                                        </form>

                                    </blockquote>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="<?= $current_design ?>images/spacer.gif" width="171" height="1" alt="">
                                </td>
                                <td>
                                    <img src="<?= $current_design ?>images/spacer.gif" width="127" height="1" alt="">
                                </td>
                                <td>
                                    <img src="<?= $current_design ?>images/spacer.gif" width="202" height="1" alt="">
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