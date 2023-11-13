<?php 
include("../../inc_common.php");
//i need next line to get design if it's not in the list of available skins, but setted for particular room.
include($engine_path."users_get_list.php");
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr><td valign="center" height="99%"> &nbsp; </td></tr>
<tr><td height="1"background="<?php echo $current_design;?>images/hor_dot_line.gif" align="left"><img src="<?php echo $current_design;?>images/hor_dot_line.gif" width="10" height="1"></td></tr>
</table>
<?php include($file_path."designes/".$design."/common_body_end.php");?>
