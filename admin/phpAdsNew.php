<?php
include("check_session.php");
include("../inc_common.php");
include("header.php");

$main_lang = eregi_replace("admin-", "", $lang);
include($file_path."languages/".$main_lang.".php");

//actions
set_variable("action");
?>
<center><font size=5 color = "#265D92" font="Verdana"><b>phpAdsNew</b></font></center>
<form method=POST action="<?php echo $chat_url;?>admin/phpAdsNew.php">
<input type=hidden value="<?php echo $lang;?>" name="lang">
<table align="center" width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
    <input type="hidden" name="session" value="<?php echo $session; ?>">
    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
    <input type="hidden" name="action" value="mod_cfg">
    <?php
            if($phpad_err != "") {
        ?>
           <tr><td colspan=2 class=head align=center bgcolor=#FFB9A1><FONT color=Red><?php echo $phpad_err; ?></FONT></td></tr>
        <?php
        }
    ?>
    <tr><td><?php echo $w_roz_clan_name; ?></td><td><input type=text name="clan_name"></td></tr>
    <tr><td><?php echo $w_roz_clan_email; ?></td><td><input type=text name="clan_email"></td></tr>
    <tr><td><?php echo $w_roz_clan_url; ?></td><td><input type=text name="clan_url"></td></tr>
    <tr><td><?php echo $w_roz_clan_avatar; ?></td><td><input type=file name="clan_avatar"></td></tr>
    <tr><td><?php echo $w_roz_clan_logo; ?></td><td><input type=file name="clan_logo"></td></tr>
    <tr><td><?php echo $w_roz_clan_border; ?></td><td><input type=checkbox name="clan_border" value=on></td></tr>
    <tr><td colspan=2 align=center><input type=submit class=button_small value="<?php echo $w_roz_add_clan; ?>"></td></tr>
</table>
</form>
</td></tr></table></center>
</body>
</html>