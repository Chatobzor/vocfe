<?php  if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");?>
<script language="JavaScript">
<!--
function rel()
{
	document.location.href='<?php echo $chat_url."messages.php?session=$session";?>';
}
window.setTimeout('rel()',20000);

function update()
{
	window.setTimeout('rel',1000);
}
function up()
{
	scroll(1,10000000);
}
//-->
</script>
<noscript>
<?php
echo "<meta http-equiv=\"refresh\" content=\"20;URL=messages.php?session=$session\">\n";
?>
</noscript>
<?php  include($file_path."designes/".$design."/common_body_start.php"); echo $rooms[$room_id]["topic"];?><hr>

<?php 
for ($i=0;$i<count($out_messages);$i++)
{
	echo $out_messages[$i];
}
?>
<?php include($file_path."designes/".$design."/common_body_end.php");?>
