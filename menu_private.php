<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");
include($engine_path."rooms_get_list.php");
if (!$exists)  {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

include($file_path."designes/".$design."/menu_private.php");
?>