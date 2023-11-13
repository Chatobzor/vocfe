<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");
if ($browser == "msie") {
?>
<script>
function e(){
	parent.moveFromBoard(event,1);
}
document.onmousemove=e;</script>
<?php } include($file_path."designes/".$design."/common_body_start.php");?>
<form method="post" action="board_delete.php">
<input type="hidden" name="session" value="<?php echo $session;?>">
<table border="0" cellpadding="0" cellspacing="3">
<tr><td><?php echo $w_status;?></td>
<td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<td><b><?php echo $w_from;?></b></td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td><td><b><?php echo $w_subject;?></b></td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td><td><b><?php echo $w_at_date;?></b></td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td><td></td></tr>

<tr><td colspan="9" background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>

<?php
for ($j=0;$j<6;$j++)
{
	for($i = 0; $i < count($board_messages[$j]); $i++) {
?>
<tr><td><?php echo $board_messages[$j][$i]["status"];?></td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<?php if($j == 0) { ?>
<td><?php if($board_messages[$j][$i]["status"] == $w_stat[1]) echo "<b>"; echo $board_messages[$j][$i]["from"]; if($board_messages[$j][$i]["status"] == $w_stat[1]) echo "</b>";?></td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<?php } else { ?>
<td><b><?php echo $board_messages[$j][$i]["from"];?> -&gt;&nbsp;
<?php
if($j == 1) echo $w_usr_adm_link;
else if ($j == 2) echo $w_usr_shaman_link;
else if ($j == 3) echo $w_usr_clan_link;
else if ($j == 4) {
		if($cu_array[USER_GENDER] == GENDER_BOY) echo $w_usr_boys_link;
        else if($cu_array[USER_GENDER] == GENDER_GIRL) echo $w_usr_girls_link;
        else if($cu_array[USER_GENDER] == GENDER_THEY) echo $w_usr_they_link;
     }
else if ($j == 5) echo $w_usr_all_link;
?></b>
</td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<?php }  ?>
<td>
<?php if($board_messages[$j][$i]["status"] == $w_stat[1]) echo "<b>"; ?>
<a href="board_view.php?session=<?php echo $session;?>&id=<?php echo $board_messages[$j][$i]["id"];?>&group=<?php echo $j; ?>"><?php echo htmlspecialchars($board_messages[$j][$i]["subject"]);?> </a></td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10">
<?php if($board_messages[$j][$i]["status"] == $w_stat[1]) echo "</b>"; ?>
</td>
<td><?php echo $board_messages[$j][$i]["date"];?></td><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<td>
<?php if($j == 0) { ?>
<input type="hidden" name="types_to_del[]" value="<?php echo $j;?>"><input type="checkbox" name="mess_to_del[]" value="<?php echo $board_messages[$j][$i]["id"];?>">
<?php } ?>
</td></tr>
<tr><td colspan="9" background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>
<?php } }?>
</table><br>
<input type="submit" value="<?php echo $w_del_checked;?>" class="input"> <a href="<?php echo $chat_url."board_send.php?session=$session"; ?>"><?php echo $w_send_mes; ?></a>
</form>
<?php include($file_path."designes/".$design."/common_body_end.php");?>