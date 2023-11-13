<?php include("check_session.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>VOC++ <?php echo $adm_administration; ?></title>
</head>

<frameset cols="280,*" bordercolor="#265D92" framespacing="3">
	<frame src="navi.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" noresize frameborder="yes">
	<frame src="welcome.php?session=<?php echo $session;?>&lang=<?php echo $lang; ?>" name="admin_main" scrolling="AUTO" frameborder="0">
</frameset>
</html>