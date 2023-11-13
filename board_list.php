<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");

if (!$exists) {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

if(!$is_regist_complete) {
    $error_text = "<div align=center>$w_roz_only_for_club.</div>";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}
include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");
include("user_validate.php");

include($ld_engine_path."hidden_board_get_messages.php");

include($file_path."designes/".$design."/board_list.php");
?>