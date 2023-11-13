<?php if (!defined("_COMMON_")) {echo "stop";exit;}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<body>
<?php

if($act != "") {
        ?>
  <script>parent.ret_sub();</script>
        <?php
}

if ((!$error)&&($mesg != "") or $banType != "")
{
?>
<script>
if(parent.nNav == 2) parent.ret_sub();
</script>
<?php
}
if ($error != "") echo "<script>alert('".str_replace("<br>","\\n",str_replace("\n","\\n",str_replace("\r","",str_replace("'","\'",$error_text))))."');</script>";
?>
</body>
</html>