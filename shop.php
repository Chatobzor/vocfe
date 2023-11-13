<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");

include($file_path."tarrifs.php");

if (!$exists) {
    $error_text = "$w_no_user";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}

if (!$is_regist_complete) {
    header("Location: ".$chat_url."registration_form.php?user_name=".urlencode($user_name));
    exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

include($file_path."user_validate.php");

if (intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"]) > my_time()) {
    $MsgToPass = $w_user_chaos;
    $MsgToPass = str_replace(
        "~",
        date(
            "d.m.Y H:i:s",
            intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"])
        ),
        $MsgToPass
    );
    $error_text = $MsgToPass;
    include($file_path."designes/".$design."/error_page.php");
    exit;
}

include($engine_path."users_get_list.php");

include($engine_path."class_items.php");
include($engine_path."get_item_list.php");
include($engine_path."shop_get_cat_list.php");
set_variable('type');
include($file_path."designes/".$design."/shop.php");