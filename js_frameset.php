<?php require_once("inc_common.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title><?php echo $w_title;?></title>
</head>
<frameset cols="*,0" frameborder="no" framespacing="0" border="0" borderwidth="0">
	<frame src="js_main.php?session=<?php echo $session;?>" noresize scrolling="auto" marginwidth="0" marginheight="0" name="voc_js_main">
	<frame src="js_writer.php?session=<?php echo $session;?>" noresize scrolling="no" marginwidth="0" marginheight="0">
</frameset>
</html>
