<?php
require_once("inc_common.php");
include($ld_engine_path."rooms_get_list.php");
include($engine_path."users_get_list.php");
$messages_to_show = array();
if (!$exists) {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

if ($current_user->user_class<1) {
        $error_text = "$w_no_admin_rights";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

$admin_navi = array();
if ($current_user->user_class & ADM_BAN) $admin_navi[count($admin_navi)] = array("title"=>$w_adm_level[ADM_BAN], "link"=>$chat_url."admin_work.php?op=ban&session=".$session);
if ($current_user->user_class & ADM_UN_BAN) $admin_navi[count($admin_navi)] = array("title"=>$w_adm_level[ADM_UN_BAN], "link"=>$chat_url."admin_work.php?op=unban&session=".$session);
//if ($current_user->user_class & ADM_CHANGE_TOPIC) $admin_navi[count($admin_navi)] = array("title"=>$w_adm_level[ADM_CHANGE_TOPIC], "link"=>$chat_url."admin_work.php?op=topic&session=".$session);
//if ($current_user->user_class & ADM_CREATE_ROOMS) $admin_navi[count($admin_navi)] = array("title"=>$w_adm_level[ADM_CREATE_ROOMS], "link"=>$chat_url."admin_work.php?op=rooms&session=".$session);
if ($current_user->user_class & ADM_EDIT_USERS) $admin_navi[count($admin_navi)] = array("title"=>$w_adm_level[ADM_EDIT_USERS], "link"=>$chat_url."admin_work.php?op=user&session=".$session);
if ($current_user->user_class & ADM_BAN) $admin_navi[count($admin_navi)] = array("title"=>$w_roz_common, "link"=>$chat_url."admin_work.php?op=common&session=".$session);
if ($current_user->user_class & ADM_VIEW_PRIVATE) $admin_navi[count($admin_navi)] = array("title"=>$w_roz_pvt_log, "link"=>$chat_url."admin_work.php?op=private&session=".$session);
if ($current_user->user_class & ADM_EDIT_USERS) $admin_navi[count($admin_navi)] = array("title"=>$w_roz_marr, "link"=>$chat_url."admin_work.php?op=marry&session=".$session);
if ($current_user->user_class & ADM_BAN) $admin_navi[count($admin_navi)] = array("title"=>$w_roz_similar, "link"=>$chat_url."admin_work.php?op=similar&session=".$session);
include($file_path."designes/".$design."/admin_navi.php");
?>