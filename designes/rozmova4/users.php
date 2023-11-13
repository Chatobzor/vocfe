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
<?php } include($file_path."designes/".$design."/common_body_start.php");
echo $info_message; 
if (count($u_ids))
{
	echo "$w_search_results<br>";
	for ($i=0;$i<count($u_ids);$i++)
		echo "<a href=\"fullinfo.php?user_id=".$u_ids[$i]."&session=$session\">".$u_names[$i]."</a><br>\n";
}?>
<hr>

<?php echo $w_search;?>
<form method="post" action="users.php">
<input type="hidden" name="session" value="<?php echo $session;?>">
<table border="0"><tr><td valign="middle">
<?php echo $w_enter_nick;?>: <input type="text" name="look_for" class="input"> </td>
<td valign="middle">
<input type="submit" value="<?php echo $w_search_button;?>" class="input">
</td></tr></table>
<?php echo $w_not_shure_in_nick?>
</form>
<?php include($file_path."designes/".$design."/common_body_end.php");?>
