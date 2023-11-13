<?php  if (!defined("_COMMON_")) {echo "stop";exit;}
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

echo $out_message;
if ($browser == "msie" && $user_chat_type!="reload")
	echo "<script>parent.parent.voc_navibar.reg_add();</script>";
else
	echo "<script>opener.reg_add();</script>";

include($file_path."designes/".$design."/common_body_end.php");
?>