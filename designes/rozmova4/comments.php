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

<?php echo $w_feed_headline; ?><br>


<form method="post" action="comments_send.php">
<input type="hidden" name="session" value="<?php echo $session;?>">
<table><tr><td>
<?php echo $w_feed_name;?>: </td><td><input type="text" name="name" class="input"></td></tr>
<tr><td>eMail: </td><td><input type="text" name="email" class="input"></td></tr></table>
<?php echo $w_feed_message;?>:<br>
<textarea name="comment" rows="10" cols="36" class="input"></textarea><br>
<input type="submit" value="<?php echo $w_send;?>" class="input">
</form>
<?php include($file_path."designes/".$design."/common_body_end.php");?>
