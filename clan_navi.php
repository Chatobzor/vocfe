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

if ($current_user->clan_class<1) {
        $error_text = "$w_no_admin_rights";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

$clan_navi = array();
if ($current_user->clan_class & CLN_ADDUSER) $clan_navi[count($clan_navi)] = array("title"=>$w_roz_clan_edt_add_usr, "link"=>$chat_url."clan_work.php?op=add&session=".$session);
if ($current_user->clan_class & CLN_DELETEUSER) $clan_navi[count($clan_navi)] = array("title"=>$w_roz_clan_edt_del_usr, "link"=>$chat_url."clan_work.php?op=del&session=".$session);
if ($current_user->clan_class & CLN_EDITUSER) $clan_navi[count($clan_navi)] = array("title"=>$w_roz_clan_edt_edt_usr, "link"=>$chat_url."clan_work.php?op=edit_user&session=".$session);
if ($current_user->clan_class & CLN_EDIT) $clan_navi[count($clan_navi)] = array("title"=>$w_roz_clan_edt_edt_cln, "link"=>$chat_url."clan_work.php?op=edit_clan&session=".$session);
if ($current_user->clan_class > 0) $clan_navi[count($clan_navi)] = array("title"=>$w_clan_treasury  , "link"=>$chat_url."clan_work.php?op=clan_money&session=".$session);
include($file_path."designes/".$design."/clan_navi.php");
?>