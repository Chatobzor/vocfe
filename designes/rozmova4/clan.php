<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");?>
<frameset rows="40,*" frameborder="no" framespacing="0" border="0" borderwidth="0">
        <frame src="<?php echo $chat_url."clan_navi.php?session=".$session;?>"  noresize scrolling="no" marginwidth="0" marginheight="0" name="voc_clan_navibar">
        <frame src="<?php echo $chat_url."clan_work.php?session=".$session;?>"  noresize scrolling="auto" marginwidth="0" marginheight="0" name="voc_clan_work">
</frameset>