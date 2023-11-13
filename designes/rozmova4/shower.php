<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function rel()
{
	document.location.href='<?php echo $chat_url."shower.php?design=$design&room=$room";?>';
}
window.setTimeout('rel()',20000);
//-->
</script>
<NOSCRIPT>
<meta http-equiv="refresh" content="20;URL=shower.php?design=<?php echo $design;?>&room=<?php echo $room;?>">
</noscript>
<?php include($file_path."designes/".$design."/common_body_start.php");?>
<center>
<table border="0" cellpadding="0" cellspacing="4">
<tr><td colspan="5" background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>

<tr>
<td rowspan="3" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<td><?php echo $w_history;?>:</td>
<td rowspan="3" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<td><?php echo $out_users_header;?></td>
<td rowspan="3" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
</tr>

<tr><td background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td><td background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>
<tr><td><?php echo $out_messages;?></td><td><?php echo $out_users;?></td></tr>
<tr><td colspan="5" background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>
</table>
<small>
<?php echo $w_copyright;?></small>
</center>
<?php include($file_path."designes/".$design."/common_body_end.php");?>
