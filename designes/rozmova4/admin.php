<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");?>
<frameset rows="60,*" frameborder="no" framespacing="0" border="0" borderwidth="0">
	<frame src="<?php echo $chat_url."admin_navi.php?session=".$session;?>"  noresize scrolling="no" marginwidth="0" marginheight="0" name="voc_admin_navibar">
	<frame src="<?php echo $chat_url."admin_work.php?session=".$session;?>"  noresize scrolling="auto" marginwidth="0" marginheight="0" name="voc_admin_work">
</frameset>