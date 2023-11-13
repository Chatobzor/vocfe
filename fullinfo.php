<?php
require_once("inc_common.php");
#only to determine design:
include($engine_path."users_get_list.php");

if(!$is_regist_complete) $session = "";

if(!isset($user_id)) set_variable("user_id");
$user_id = intval($user_id);

#fake for including files, without functions
$is_regist = $user_id;
if ($is_regist) {
        include("inc_user_class.php");
        include($ld_engine_path."users_get_object.php");
} else {
        $error_text = str_replace("~", $user_id,$w_search_no_found);
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

$pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.gif";
if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

if ($pic_name == ""){

 $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpg";
 if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

 if ($pic_name == ""){
     $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpeg";
     if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
 }

}

$sex = $current_user->sex;
$sexStr = $w_unknown;
switch ($sex) {
        case 1: $sexStr = $w_male; break;
        case 2: $sexStr = $w_female; break;
}

include($file_path."designes/".$design."/fullinfo.php");
?>