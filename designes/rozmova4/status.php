<?php include("../../inc_common.php");
include($file_path."designes/".$design."/common_title.php");
include($engine_path."users_get_list.php");
?>
<script language="javascript">
var time_counter = 0;
var voc_alert = 0;
var initialized = 0;
function st_ini()
{
	with (window.parent.voc_status_view.window.document)
	{
		open('text/html','replace');
		writeln('<html><head><title><?php echo addslashes($w_title);?></title><style>'+parent.css_style+'</style>');
<?php 
echo "write('";
	eval('?>'.str_replace("'","\\'",str_replace("\r","",str_replace("\n","\\n",implode('',file($file_path."designes/".$design."/common_body_start.php"))))));
echo	"\\n');\n";
?>
		writeln('<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">');
		writeln('<tr><td valign="center" height="99%"><span class="jsnavi">Connect: </span><img src="<?php echo $current_design;?>images/conn_online.gif" width="10" height="15" border="0" vspace="0" hspace="0" align="middle" name="voc_status_image" title="online" alt="online">&nbsp;</td></tr>');
		writeln('<tr><td height="1"background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>');
		writeln('</table>');
<?php 
echo "write('";
	eval('?>'.str_replace("'","\\'",str_replace("\r","",str_replace("\n","\\n",implode('',file($file_path."designes/".$design."/common_body_end.php"))))));
echo	"\\n');\n";
?>
		close();
	}
	time_counter = 0;
	voc_alert = 0;
	if (initialized == 0)
	{
		initialized = 1;
		window.setTimeout('check()',1000);
	}
}

function check()
{
	time_counter++;
	if (time_counter > 15 && voc_alert == 0)
	{
		voc_alert = 1;
		window.parent.voc_status_view.window.document.voc_status_image.src = '<?php echo $current_design;?>images/conn_offline.gif';
	}
	
	
	if (time_counter > 35)
	{
		window.parent.parent.voc_mess_frameset.voc_shower.location.reload();
//		document.forms[0].submit();
	}
	else
	{
		window.setTimeout('check()',1000);
	}
}

function st_update()
{
	time_counter = 0;
	if (voc_alert == 1) { window.parent.voc_status_view.window.document.voc_status_image.src = '<?php echo $current_design;?>images/conn_online.gif'; }
	voc_alert = 0;
}

</script>
<body bgcolor="white" marginleft="0" margintop="0" marginwith="0" marginheight="0"> 
<!-- what instead of _top???-->
<form method="post" action="<?php echo $chat_url;?>voc.php" target="_top">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="room" value="<?php echo $room_id;?>">

</form>
<?php include($file_path."designes/".$design."/common_body_end.php");?>
