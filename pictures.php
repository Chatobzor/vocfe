<?php
require_once("inc_common.php");
#for determining design:
include($engine_path."users_get_list.php");

if (!$exists) {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");
include("user_validate.php");

$pic_phrases = array();
$pic_urls = array();
include($ld_engine_path."pictures.php");


include($file_path."designes/".$design."/pictures.php");
?>
