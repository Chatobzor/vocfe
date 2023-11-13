<?php include("../../inc_common.php");
include($engine_path."users_get_list.php");
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");
	$chat_type = $user_chat_type;

	if (!in_array($chat_type, $chat_types)) $chat_type = $chat_types[0];
	if ($chat_type=="tail") $shower = "$daemon_url?$session";
	elseif ($chat_type=="reload") $shower = $chat_url."messages.php?session=$session";
	elseif ($chat_type=="php_tail") $shower = $chat_url."tail.php?session=$session";
	elseif ($chat_type=="js_tail") $shower = $chat_url."js_frameset.php?session=$session";
?>
<script>function hide_box() {
	document.all['board_div'].style.visibility = "hidden";
	document.all['board_header'].style.visibility = "hidden";
}
function show_box() {
	document.all['board_div'].style.visibility = "visible";
	document.all['board_header'].style.visibility = "visible";
}
function st_update() {
	parent.st_update();
}
function st_ini() {
	parent.st_ini();
}
</script>
<script language="JavaScript1.3">
var drag = false;
var blx = 3;
var bly = 3;
var startx = 0;
var starty = 0;
//its not really good, but if i'm moving the pseudowindow outside of visible area, the document automatically increased
var docwidth = 0;
var docheight = 0;
function move(){
	if (blx>-100 && blx < docwidth){
		document.all['board_header'].style.pixelLeft = blx;
		document.all['board_div'].style.pixelLeft = blx+1;
	} else {
		if (blx<0) blx = -5;
		else blx = docwidth;
	}
	if (bly >-5 && bly < docheight) {
		document.all['board_header'].style.pixelTop = bly;
		document.all['board_div'].style.pixelTop = bly+22;
	} else { 
		if (bly<0) bly = -5;
		else bly = docheight;
	}
	
	
}

function moveFromMSG(ev) {
	if (ev.button==1 && drag) {
		blx = ev.clientX - startx;
		bly = ev.clientY - starty;
		move();
		ev.returnValue = false;
	}
}
function moveFromBoard(ev,offs) {
	if (ev.button==1 && drag) {
		blx = blx + ev.clientX - startx;
		bly = bly + ev.clientY - starty;
		if (offs == 1) {blx+=1; bly+=22;}
		move();
		ev.returnValue = false;
	}
}
function startDrag(ev) {
	if (docwidth == 0) docwidth = document.body.scrollWidth-50;
	if (docheight == 0) docheight = document.body.scrollHeight-15;
	startx = ev.offsetX;
	starty = ev.offsetY;
	drag = true;
}
</script>

<style>
#board_div{POSITION:absolute; visibility: hidden;  Z-INDEX: 200;LEFT: 4; TOP: 25; WIDTH:500; HEIGHT: 280; background-color:#f5f5f5;}
#board_header{POSITION:absolute; visibility: hidden;  Z-INDEX: 150;LEFT: 3; TOP: 3; WIDTH:503; HEIGHT: 303; background-color:#f5f5f5;}
#messages_div{POSITION:absolute; visibility: visible;  Z-INDEX: 100;LEFT: 0; TOP: 0; WIDTH:100%; HEIGHT: 100%;}
</style>
</head>
<body bgcolor="#FFFFFF"  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<iframe id="board_div" name="message_box" src="about:blank" frameborder="no" framespacing="0" border="0" borderwidth="0" scrolling="auto">
</iframe>
<iframe id="board_header" name="closebox" src="" frameborder="no" framespacing="0" border="0" borderwidth="0" scrolling="no">
</iframe>
<script>
with (document.closebox.document) {
	open('text/html','replace');
	write('<html><script>function a(){if (event.srcElement.name!=\'close\'){ parent.startDrag(event);}}function b(){parent.moveFromBoard(event,0);}');
	write('function c(){parent.drag=false;}document.onmouseup=c;document.onmousedown = a;document.onmousemove = b;<'+'/script>');
	write('<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0"><table border="0" cellpadding="0" cellspacing="0" width="503" height="303">');
	write('<table border="0" cellpadding="0" cellspacing="0" width="503" height="303">');
	write('<tr><td colspan="3" height="1" background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>');
	write('<tr><td width="1" height="20" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>');
	write('<td bgcolor="#6060ff" align="right"><img src="<?php echo $current_design;?>images/close_box.gif" title="close" alt="close" width="20" height="20" onClick="javascript:parent.hide_box();" name="close"></td>');
	write('<td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td></tr>');
	write('<tr><td height="1" colspan="3" background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>');
	write('<tr><td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td>');
	write('<td>');
	write('content here</td>');
	write('<td width="1" background="<?php echo $current_design;?>images/vert_dot_line.gif" valign="top"><img src="<?php echo $current_design;?>images/vert_dot_line.gif" width="1" height="10"></td></tr>');
	write('<tr><td height="1" colspan="3" background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>');
	write('</table></body></html>');
	//close();
}
</script>
<iframe id="messages_div" name="voc_shower" src="<?php echo $shower.(($priv_frame && $chat_type == "tail")?"&t=n":"");?>" frameborder="no" framespacing="0" border="1" borderwidth="0" scrolling="auto">
</iframe>
</body>
</html>
