<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");
?>
<script>
document.write('<a href="<?php echo $chat_url."who.php?session=$session";?>&photoss='+parent.pho_word+'" target="voc_who"><?php echo $w_who_in_current_room;?></a>');
</script>

<table border="0" cellpadding="0" cellspacing="0" width="200" height="100%">
<tr><td rowspan="1" width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>
<td width="199" align="center" valign="top"><br><br>
<table width="180" border="0" cellpadding="0" cellspacing="0" >

<?php
for($kk=0;$kk<count($room_ids);$kk++) {
        $total_users = count($rooms[$room_ids[$kk]]["in_room"]);
        echo "<tr><td align=\"center\">".$w_in_room."<br> <a href=\"javascript:document.forms[0].room.value='".$room_ids[$kk]."';document.forms[0].submit();\"><span class=\"cap\">&quot;".addslashes($rooms[$room_ids[$kk]]["title"])."&quot;</span></a><br><b>$total_users</b> ".w_people($total_users)."<br>";
        echo "<br>";
        for($i=0; $i < count($rooms[$room_ids[$kk]]["in_room"]); $i++) {
              echo  "<a href=\"".$current_design."profiler.php?session=$session&user_to_search=".urlencode($rooms[$room_ids[$kk]]["in_room"][$i])."\" target=\"new\">".$rooms[$room_ids[$kk]]["in_room"][$i]."</a><br>";
         }
        echo "<br>&nbsp;</td></tr>";
        echo "<tr><td height=\"1\" background=\"".$current_design."images/hor_dot_line.gif\" align=\"left\"><img src=\"".$current_design."images/hor_dot_line.gif\" width=\"10\" height=\"1\"></td></tr>";
        //echo "<tr><td></td></tr>";
}
?></table></td></tr>
</table>
<form method="post" action="<?php echo $chat_url;?>voc.php" target="_parent">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="room" value=""></form>
<?php include($file_path."designes/".$design."/common_body_end.php"); ?>
