<?php if (!defined("_COMMON_")) {echo "stop";exit;}
//include($file_path."designes/".$design."/common_title.php");
//include($file_path."designes/".$design."/common_browser_detect.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<script language="JavaScript">
<!--
<?php
if ($browser == "msie" && $user_chat_type!="reload")
{
?>
function open_win(win_file, win_title)
{
        with(window.parent.voc_mess_frameset.message_box.document)
        {
                open('text/html','replace');
                close();
        }
        window.parent.voc_mess_frameset.message_box.document.location.href = ''+win_file;
        window.parent.voc_mess_frameset.show_box();
}
<?php }else{ ?>
function open_win(win_file, win_title)
{
window.open(win_file, win_title, 'resizable=yes,width=850,height=550,toolbar=no,scrollbars=yes,location=no,menubar=no,status=no');
}
<?php }?>

//-->
</script>
<style>
<!--
a
{
        font-family: Verdana, Arial;
        font-size:10px;
        color:#3D4976;
        font-weight: bold;
        text-decoration: none;
}
a:hover { color:white }
-->
</style>
</head>
<body bgcolor="#ffb900"leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background="<?php echo $current_design;?>img/top_green_menu.jpg">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" background="<?php echo $current_design;?>grunge/grunge_02.gif">
  <tr align="center">
    <td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('http://192.168.4.92/rules.php', 'rules');"><font color=red>Правила</font></a></b></td>
    <td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('shop.php?session=<?php echo $session;?>', 'shop');"><font color=white><?php echo $shop;?></font></a></b></td>
    <td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('board_list.php?session=<?php echo $session;?>', 'help');"><font color=white><?php echo $w_roz_offline_pm; ?></font></a></b></td>
    <td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('users.php?session=<?php echo $session;?>', 'usersinfo');"><font color=white><?php echo $w_info_about;?></font></a></b></td>
    <td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('pictures.php?session=<?php echo $session;?>', 'pictures');"><font color=white><?php echo $w_pictures;?></font></a></b></td>
    <td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('clan_view.php?session=<?php echo $session;?>', 'clans');"><font color=white><?php echo $w_roz_clans;?></font></a></b></td>
<?php if (count($room_ids)){?><td width="10%"><b><a href="rooms.php?session=<?php echo $session;?>" target="voc_who_visible"><font color=white><?php echo $w_who_in_rooms; ?></font></a></b></td><?php }?>
<?php if (!$is_regist_complete) {?>
<td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('registration_form.php?session=<?php echo $session;?>', 'registration');"><font color=red><?php echo $w_registration; ?></font></a></b></td>
<?php }else{?>
<td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('user_info.php?session=<?php echo $session;?>', 'perosnalinfo');"><font color=white><?php echo $w_about_me;?></font></a></b></td>

<?php if  ($current_user->user_class=="admin") {?>
<td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('admin.php?session=<?php echo $session;?>', 'gun');"><font color=white><?php echo $w_gun;?></font></a></b></td>

<?php }
        if ($current_user->custom_class & CST_PRIEST) {?>
<td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('admin_work.php?op=marry&session=<?php echo $session;?>', 'gun');"><font color=white><?php echo $w_roz_marr; ?></font></a></b></td>
<?php }

if ($current_user->clan_class > 0 and $current_user->clan_id > 0) {?>
<td width="10%"><b><a href="javascript:;" onclick="javascript:open_win('clan.php?session=<?php echo $session;?>', 'clan');"><font color=white><?php echo $w_roz_my_clan; ?></font></a></b></td>
<?php }

}?>
<td width="10%"><b><a href="logout.php?session=<?php echo $session;?>" target="_parent"><font color=white><?php echo ucfirst(Выход); ?></font></a></b></td>
  </tr>
</table>
</body>
</html>