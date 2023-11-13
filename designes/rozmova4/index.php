<?php if (!defined("_COMMON_")) {echo "stop";exit;}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title><?php echo $w_welcome;?></title>
</head>
<frameset rows="200,*"  frameborder=no framespacing=0 border=0 borderwidth=0>
  <frame src="welcome.php?design=<?php echo $design; ?>&user_lang=<?php echo $user_lang;?>" noresize scrolling="no" marginwidth=2 marginheight=2>
  <frame src="shower.php?design=<?php echo $design; ?>&user_lang=<?php echo $user_lang;?>" noresize marginwidth=2 marginheight=2 name="voc_shower">
</frameset>
</html>
