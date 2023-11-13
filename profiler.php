<?php

require_once("inc_common.php");
set_variable("session");
set_variable("user_to_search");

$u_ids = [];
include($ld_engine_path."users_search.php");
$html_to_out = "";
if (count($u_ids)) {
    header("location: fullinfo.php?session=$session&user_id=".$u_ids[0]);
} else {
    $error_text = $w_search_no_found;
    $error_text = eregi_replace("~", "<b>".user_to_search."</b>", $error_text);
    include($file_path."designes/".$design."/error_page.php");
    exit;
}