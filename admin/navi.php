<?php include("check_session.php");?>
<?php include("header.php"); ?>
<?php include("../inc_common.php"); ?>
<table width="100%" height="100%" align="top" border="0" cellpadding=0 cellspacing=0 bgcolor="#E3E3ED">
<tr><td align="center" valign="top">

<form method="post" action="search.php" target="admin_main">
<table bgcolor=#265D92 width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=head align=center>
<?php echo $adm_user_search;?> :</td></tr></table>
<table width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=menu align=left>
<input type="hidden" name="session" value="<?php echo $session;?>"><input type="text" size="20" name="tstInfoUser" class=input> <input type="submit" value="  >  " class=button_small><br>
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<span class=desc><?php echo $adm_inactive;?> </span><select name="inactiv" class=dd>
<option value="">--</option>
<option value="1">1</option>
<option value="3">3</option>
<option value="6">6</option>
<option value="12">12</option>
</select> <span class=desc><?php echo $adm_months;?></span><br>
</form>
</td></tr></table>
<table bgcolor=#265D92 width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=head align=center>
<?php echo $adm_admin_tools;?> :</td></tr></table>
<table width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=menu align=left>
<a href="quiz.php?session=<?php echo $session;?>" target="admin_main" class=menu>[Викторина]</a><br>
<a href="mod_list.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_moder_list;?>]</a><br>
<a href="progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=shaman" target="admin_main" class=menu>[<?php echo $adm_shamans_list;?>]</a><br>
<a href="clan_list.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_clans_list;?>]</a><br>
<a href="rooms.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class="menu">[<?php echo $adm_rooms_admin;?>]</a><br>
<a href="cr.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_robik_class;?>]</a><br>
<a href="progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=canon" target="admin_main" class=menu>[<?php echo $adm_canon_nicks;?>]</a><br><br></td></tr></table>
<table bgcolor=#265D92 width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=head align=center>
<?php echo $adm_shop_manager; ?> :</td></tr></table>
<table width="100%" cellpadding=4 cellspacing=0 border="0">
        <tr><td width="100%" class=menu align=left>
<a href="shop.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_shop_manager_itmes; ?>]</a><br>
<a href="shop_cats.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_shop_manager_cats; ?>]</a><br>
<a href="transaction_log.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_shop_manager_log; ?>]</a>
</td></tr></table>
<br>

<table bgcolor=#265D92 width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=head align=center>
<?php echo $adm_other_tools; ?> :</td></tr></table>
<table width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=menu align=left>
<a href="conv.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_sm_convert; ?>]</a><br>
<!--
<a href="daemon.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[Daemon-tools]</a><br>
-->
<a href="statistic.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_statistics; ?>]</a><br>
<a href="import.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_mysql_import; ?>]</a><br>
<?php if(is_file($data_path."engine/files/guardian.php")) { ?>
<a href="progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=guardian" target="admin_main" class=menu>[VOC++ Guardian]</a><br>
<?php } ?>
<a href="progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=index" target="admin_main" class=menu>[<?php echo $adm_reconstruct_idx; ?>]</a><br>
<a href="progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=index_similar" target="admin_main" class=menu>[<?php echo $adm_gen_similar_table; ?>]</a><br>
<a href="progress_frameset.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&operation=index_register" target="admin_main" class=menu>[<?php echo $adm_register_all; ?>]</a><br>
<br>
</td></tr>
</td>
</tr>
<tr>
</td>
<table bgcolor=#265D92 width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=head align=center>
<font color=White><?php echo $adm_configuration;?> :</font></td></tr></table>
<table width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=menu align=left>
<a href="admin_conf.php?step=0&session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_main_pathes; ?>]</a><br>

<a href="admin_conf.php?step=1&session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_daemon_settings; ?>]</a><br>

<a href="admin_conf.php?step=2&session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_engines; ?>]</a><br>

<a href="admin_conf.php?step=3&session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_options_and_lim; ?>]</a><br>

<a href="admin_conf.php?step=4&session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_user_access_lim; ?>]</a><br>

<a href="admin_conf.php?step=5&session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_add_features; ?>]</a><br>

<a href="admin_conf.php?step=6&session=<?php echo $session;?>&lang=<?php echo $lang; ?>" target="admin_main" class=menu>[<?php echo $adm_look_and_feel; ?>]</a><br>
</td></tr></table>
<table bgcolor=#265D92 width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=head align=center>
<font color=White>Plugins :</font></td></tr></table>
<table width="100%" cellpadding=4 cellspacing=0><tr><td width="100%" class=menu align=left>
<?php
if (is_dir($file_path."plugins")) {
   if ($dh = opendir($file_path."plugins")) {
       while (($file = readdir($dh)) !== false) {
           if($file != "." && $file != "..") {
                   if(is_dir($file_path."plugins/".$file)) {
                   //Plugin dir found
                   //trying to load config
                     if(is_file($file_path."plugins/".$file."/config.php")) {
                          include($file_path."plugins/".$file."/config.php");
                             ?>
                             <a href="plugin_info.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>&plugin=<?php echo $file; ?>" target="admin_main" class=menu>[<?php echo $VOCPlugin_Name." / ".$VOCPlugin_Language." (".$VOCPlugin_Version.")"; ?>]</a><br>
                             <?php
                     }
                   }
           }
       }
       closedir($dh);
   }
}
?>
</td></tr></table>

</td>
</tr>
</table>
</body>
</html>
