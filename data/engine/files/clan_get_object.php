<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if ($is_regist_clan) {
        $is_regist_clan = intval($is_regist_clan);

	if (!file_exists($data_path."clans/".floor($is_regist_clan/2000)."/".$is_regist_clan.".clan")) {
		$error_text = $w_roz_clan_notfound;
		include($file_path."designes/".$design."/error_page.php");
		exit;
	}
    if(unserialize(implode("",file($data_path."clans/".floor($is_regist_clan/2000)."/".$is_regist_clan.".clan"))) === FALSE) {
   		$error_text = $w_roz_clan_notfound." (unserialize)";
		include($file_path."designes/".$design."/error_page.php");
		exit;
    }

	$current_clan = unserialize(implode("",file($data_path."clans/".floor($is_regist_clan/2000)."/".$is_regist_clan.".clan")));
}
else  {
		$error_text = $w_roz_clan_notfound." ($is_regist_clan)" ;
		include($file_path."designes/".$design."/error_page.php");
		exit;
}
?>