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
<?php echo $info_message;?>
<a href="user_info.php?session=<?php echo $session;?>"><?php echo $w_about_me;?></a>
<?php include($file_path."designes/".$design."/common_body_end.php");?>
