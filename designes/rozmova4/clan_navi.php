<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");
echo " | ";
for ($i=0;$i<count($clan_navi);$i++) {
        echo "<a href=\"".$clan_navi[$i]["link"]."\" target=\"voc_clan_work\" class=\"jsnavi\">".$clan_navi[$i]["title"]."</a> | ";
}
include($file_path."designes/".$design."/common_body_end.php");?>