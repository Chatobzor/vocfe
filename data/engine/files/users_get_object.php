<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
if ($is_regist) {
        if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")) {
                $error_text = str_replace("~","",$w_search_no_found);
                include($file_path."designes/".$design."/error_page.php");
                exit;
        }
        $current_user = unserialize(implode("",file($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")));
        #backward compatibility
        if ("--".$current_user->user_class == "--admin") $current_user->user_class = ADM_BAN;
          #DD bugfix :-)
        if (trim($current_user->chat_status) == "0") $current_user->chat_status = "";
           if (trim($current_user->clan_status) == "0") $current_user->clan_status = "";
}
?>