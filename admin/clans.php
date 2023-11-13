<?php
include("check_session.php");
include("../inc_common.php");
include("header.php");

$main_lang = eregi_replace("admin-", "", $lang);
include($file_path."languages/".$main_lang.".php");

//actions
set_variable("action");

if($action == "edit_clan") {
        include("../inc_user_class.php");
    $current_clan                 = new Clan();
    $mode_add_clan            = false;
    include("../edit_clan.php");
}

set_variable("clan_id");
$clan_id = intval(trim($clan_id));

if($clan_id < 1) exit;

$is_regist_clan = $clan_id;

include("../inc_user_class.php");
$current_clan = new Clan();

include($ld_engine_path."clan_get_object.php");

if($current_clan->border == 1) $current_clan->border = "checked";
else $current_clan->border = "";

?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<?php
echo "<center><h2 style=\"color:#265D92;font-family:Verdana\">$w_roz_clans</h2></center>\n";
?>
<form method=POST action="<?php echo $chat_url ?>admin/clans.php" encType="multipart/form-data">
<table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
    <input type="hidden" name="session" value="<?php echo $session; ?>">
    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
    <input type="hidden" name="action" value="edit_clan">
    <input type="hidden" name="clan_id" value="<?php echo $clan_id; ?>">
    <?php
            if($clan_err != "") {
        ?>
           <tr><td colspan=2 class=head align=center bgcolor=#FFB9A1><FONT color=Red><?php echo $clan_err; ?></FONT></td></tr>
        <?php
        }
    ?>
        <tr><td colspan=2 class=head align=center bgcolor=#265D92><?php echo $current_clan->name; ?></td></tr>
    <tr><td><?php echo $w_roz_clan_name; ?></td><td><input type=text name="clan_name" value="<?php echo $current_clan->name; ?>"></td></tr>
    <tr><td><?php echo $w_roz_clan_email; ?></td><td><input type=text name="clan_email" value="<?php echo $current_clan->email; ?>"></td></tr>
    <tr><td><?php echo $w_roz_clan_url; ?></td><td><input type=text name="clan_url" value="<?php echo htmlentities($current_clan->url); ?>"></td></tr>
    <tr><td><?php echo $w_roz_clan_cst_greet; ?></td><td><input type=text name="clan_greeting" value="<?php echo htmlentities($current_clan->greeting); ?>"></td></tr>
    <tr><td><?php echo $w_roz_clan_cst_goodbye; ?></td><td><input type=text name="clan_goodbye" value="<?php echo htmlentities($current_clan->goodbye); ?>"></td></tr>
    <?php

    if(is_file($file_path."clans-avatar/".floor($clan_id/2000)."/".$clan_id.".gif")) {
    ?>
    <tr><td><?php echo $w_roz_clan_avatar; ?></td><td><img src="../<?php echo "clans-avatar/".floor($clan_id/2000)."/".$clan_id.".gif";?>"></td></tr>
    <?php }
    ?>
          <tr><td align=right><?php echo $w_roz_clan_del_avatar; ?></td><td><input type=checkbox name="delete_avatar"></td></tr>
    <tr><td><?php echo $w_roz_clan_avatar; ?></td><td><input type=file name="clan_avatar"></td></tr>
    <?php if(is_file($file_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".gif")) { ?>
    <tr><td> <?php echo $w_roz_clan_logo; ?></td><td><img src="../<?php echo "clans-logos/".floor($clan_id/2000)."/".$clan_id.".gif";?>"></td></tr>
    <?php } ?>
    <?php if(is_file($file_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpg")) { ?>
    <tr><td> <?php echo $w_roz_clan_logo; ?></td><td><img src="../<?php echo "clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpg";?>"></td></tr>
    <?php } ?>
           <tr><td align=right><?php echo $w_roz_clan_del_logo; ?></td><td><input type=checkbox name="delete_logo"></td></tr>
    <tr><td><?php echo $w_roz_clan_logo; ?></td><td><input type=file name="clan_logo"></td></tr>
    <tr><td><?php echo $w_roz_clan_border; ?></td><td><input type=checkbox name="clan_border" <?php echo $current_clan->border; ?>></td></tr>
    <tr><td colspan=2 align=center><input type=submit class=button_small value="<?php echo $w_roz_clan_edit_btn; ?>"></td></tr>
</table>
</form>
</td></tr></table></center>
</body>
</html>