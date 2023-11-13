<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<body>
<?php
flush();
for($i = 0; $i < 15; $i++) {
	echo "<script>parent.frames['wr'].document.write('$i');</script>";
    flush();
    sleep(1);
}
?>
</body>
</html>