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
<table border="0" width="450" cellpadding="0" cellspacing="3">
<tr><td>
<?php
echo "<b>$w_from:</b> ".$board_message["from"]."<br><b>$w_at_date:</b> ".$board_message["date"]."<br><b>$w_subject:</b> ".htmlspecialchars($board_message["subject"])."</td></tr>\n";?>
<tr><td background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>
<tr><td><?php echo $board_message["body"];?></td></tr>
<tr><td background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>
<tr><td><?php if ($board_message["from_id"]){?><input type="button" value="<?php echo $w_answer;?>" onclick="javascript:document.location.href='board_send.php?session=<?php echo $session;?>&group=<?=intval($group)?>&message_id=<?php echo $board_message["id"];?>';" class="input"> &nbsp; <?php } ?>
<?php if($group == 0) { ?><input type="button" value="<?php echo $w_delete;?>" onclick="javascript:document.location.href='board_delete.php?session=<?php echo $session;?>&mess_to_del[]=<?php echo $board_message["id"];?>';" class="input"> &nbsp;<?php } ?>
<input type="button" value="<?php echo $w_back_to_userboard;?>" onclick="javascript:document.location.href='board_list.php?session=<?php echo $session;?>';" class="input">
</td></tr></table>
<?php include($file_path."designes/".$design."/common_body_end.php");?>