<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");
echo " | ";
for ($i=0;$i<count($admin_navi);$i++) {
	echo "<a href=\"".$admin_navi[$i]["link"]."\" target=\"voc_admin_work\" class=\"jsnavi\">".$admin_navi[$i]["title"]."</a> | ";
}
include($file_path."designes/".$design."/common_body_end.php");?>