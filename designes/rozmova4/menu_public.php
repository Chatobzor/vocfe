<?php if (!defined("_COMMON_")) {echo "stop";exit;}
//include($file_path."designes/".$design."/common_title.php");
//include($file_path."designes/".$design."/common_browser_detect.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<style>
<!--
a, td
{
        font-family: Verdana, Arial;
        font-size:10px;
        color:#3D4976;
        font-weight: bold;
        text-decoration: none;
}
a:hover { color:white }
-->
</style>
<script language="JavaScript">
<!--
function click_pause()
{
   var fvVal = parent.voc_shower.pause;

   if (fvVal != 1) {
       document.all('pause_img').src = pause_on.src;
       document.all('pause_img').alt = '<?php echo $w_roz_pause_tip_on; ?>';

       parent.voc_shower.pause = 1;
   } else {

       document.all('pause_img').src = pause_off.src;
       document.all('pause_img').alt = '<?php echo $w_roz_pause_tip; ?>';
       parent.voc_shower.pause = 0;
   }
}

if (document.images)
   {
     pause_on     = new Image(24,24);
     pause_on.src ="<?=$current_design?>grunge/pause_on.gif";

     pause_off     = new Image(24,24);
     pause_off.src ="<?=$current_design?>grunge/grunge_11.gif";

     chat_filter_on     = new Image(24,24);
     chat_filter_on.src ="<?=$current_design?>grunge/filter_on.gif";

     chat_filter_off     = new Image(24,24);
     chat_filter_off.src ="<?=$current_design?>grunge/grunge_12.gif";;
}

function click_filter()
{

 var fvVal = null;
 var po = parent.voc_sender.document.getElementById('ChatFilter');
 if(!po) po= parent.voc_sender.document.all('ChatFilter');

 if(po) fvVal = po.value;
 else {
     alert('Error !');
     return;
 }

   if (fvVal != '1') {
              alert('<?php echo $w_filter_tip_on; ?>');

              document.all('filter_img').src = chat_filter_on.src;
              document.all('filter_img').alt = '<?php echo $w_filter_tip_on; ?>';

              parent.voc_sender.document.forms[0].ChatFilter.value = '1';
              parent.voc_sender.document.forms[0].act.value = 'filter_on';
   } else {
              alert('<?php echo $w_filter_tip; ?>');

              document.all('filter_img').src = chat_filter_off.src;
              document.all('filter_img').alt = '<?php echo $w_filter_tip; ?>';

              parent.voc_sender.document.forms[0].ChatFilter.value = '0';
              parent.voc_sender.document.forms[0].act.value = 'filter_off';
   }
   parent.voc_sender.document.forms[0].submit();
}


//-->
</script>
</head>
<body bgcolor="#abd256"leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr align="center">
    <td width="143" height="24" background="<?=$current_design?>grunge/grunge_09.gif" align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<nobr><b><font color=white><?=$rooms[$cu_array[USER_ROOM]]["title"]?></font></a></b></nobr>&nbsp;&nbsp;</td>
    <td width="23" height="24"><a href="#" onClick="parent.clear_pub()"><img src="<?=$current_design?>grunge/grunge_10.gif" width="23" height="24" border="0" alt="<?=$w_roz_clear_pub_all?>"></a></td>
    <td width="20" height="24"><a href="#" onClick="click_pause()"><img border="0" name="pause_img" id="pause_img" alt="<?=$w_roz_pause_tip?>" src="<?=$current_design?>grunge/grunge_11.gif" width="20" height="24"></a></td>
    <td width="20" height="24"><a href="#" onClick="click_filter()"><img border="0" name="filter_img" id="filter_img" alt="<?=$w_filter_tip?>" src="<?=$current_design?>grunge/grunge_12.gif" width="20" height="24"></a></td>
    <td height="24">&nbsp;</td>
    <td width="224" height="24"><img src="<?=$current_design?>grunge/grunge_14.gif" width="224" height="24"></td>
  </tr>
</table>
</body>
</html>