<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");

if(isset($_REQUEST[session_name()]) or isset($HTTP_REQUEST[session_name()])){
	session_start();
}

if ($browser == "msie") {
?>
<script>
function e(){
	parent.moveFromBoard(event,1);
}
document.onmousemove=e;</script>
<?php } include($file_path."designes/".$design."/common_body_start.php");?>
<?php if ($is_regist){?>
<a href="board_list.php?session=<?php echo $session;?>"><?php echo $w_back_to_userboard;?></a><br>
<blockquote>
<?php
	}
echo $info_message;?>
<?php if (count($u_ids)) {?>
<form method="post" action="board_post.php">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="group" value="<?php echo $group;?>">
<?php echo $w_select_nick;?>: <select name="send_to_id" class="input">
<?php
for($i=0;$i<count($u_ids);$i++)
{
	echo "<option value=\"".$u_ids[$i]."\">".$u_names[$i]."</option>\n";
}?>
</select><br>
<?=$w_impro_enter_code?>: &nbsp;<input type="text" name="keystring" class="input"><br>
<img border="1" src="<?=$current_design?>profile_captcha.php?<?php echo session_name()?>=<?php echo session_id()?>"></p>
<?php echo $w_subject;?>: <input type="text" name="subject" size="20" maxlength="50" value="<?php echo $tmp_subject;?>" class="input"><br>
<?php  echo $w_message_text;?>:<br>
<textarea cols="40" rows="10" name="message" class="input">
<?php echo $tmp_body;?>
</textarea>
<br>
<input type="submit" value="<?php echo $w_send;?>" class="input">
</form>
</blockquote>
<?php }?>
<hr>
<form method="post" action="board_send.php">
<input type="hidden" name="session" value="<?php echo $session;?>">
<blockquote>
<?php

echo "<a href=\"board_send.php?session=$session&send_to=1&group=1\">$w_usr_adm_link</a><br>";
echo "<a href=\"board_send.php?session=$session&send_to=2&group=1\">$w_usr_shaman_link</a><br>";
if($cu_array[USER_CLASS] & ADM_BAN_MODERATORS) {
	 echo "<a href=\"board_send.php?session=$session&send_to=0&group=1\">$w_usr_all_link</a><br>";
	 echo "<a href=\"board_send.php?session=$session&send_to=3&group=1\">$w_usr_boys_link</a><br>";
	 echo "<a href=\"board_send.php?session=$session&send_to=4&group=1\">$w_usr_girls_link</a><br>";
     echo "<a href=\"board_send.php?session=$session&send_to=5&group=1\">$w_usr_they_link</a><br>";
}
if($cu_array[USER_CLANID] > 0) echo "<a href=\"board_send.php?session=$session&send_to=6&group=1\">$w_usr_clan_link</a><br>";
?>
</blockquote>
<?php
echo $w_not_shure_in_nick."<br>".$w_enter_nick;?>: <input type="text" name="send_to" maxlength="15" class="input">
<input type="submit" value="<?php echo $w_search_button;?>" class="input">
</form>
<?php include($file_path."designes/".$design."/common_body_end.php");?>