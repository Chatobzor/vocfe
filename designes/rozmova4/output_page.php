<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");
echo "<table><tr><td>";
if (isset($html_to_out))
	echo $html_to_out;
echo "</td></tr></table>";
include($file_path."designes/".$design."/common_body_end.php");?>