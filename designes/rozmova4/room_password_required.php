<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");?>
<?php include($file_path."designes/".$design."/common_body_start.php");?>
<form method="post" action="voc.php"><?php echo $w_enter_password_room;?>:
<input type="hidden" name="room" value="<?php echo $room;?>">
<?php if($exists) { ?>
<input type="hidden" name="session" value="<?php echo $session;?>">
<?php } else { ?>
<input type="hidden" name="user_lang" value="<?php echo $user_lang;?>">
<input type="hidden" name="user_name" value="<?php echo $user_name;?>">
<input type="hidden" name="chat_type" value="<?php echo $chat_type;?>">
<input type="hidden" name="design" value="<?php echo $design;?>">
<input type="hidden" name="password" value="<?php echo $password;?>">
<?php } ?>
<input type="password" name="room_password" class="input"> &nbsp;
<input type="submit" value="<?php echo $w_login_button;?>" class="input">
<script>document.forms[0].room_password.focus();</script>
<?php include($file_path."designes/".$design."/common_body_end.php");?>