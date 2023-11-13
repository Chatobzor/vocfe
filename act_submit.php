<?
require_once("inc_common.php");
include($engine_path."users_get_list.php");

include($file_path."tarrifs.php");

if (!$exists)  {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

if(!$is_regist_complete) {
   header("Location: ".$chat_url."registration_form.php?user_name=".urlencode($user_name));
   exit;
}

include_once($data_path."engine/files/user_log.php"); 

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

include($file_path."user_validate.php");
include($engine_path."users_get_list.php");

include($engine_path."class_items.php");
include ($engine_path."get_item_list.php");

set_variable('action_name');
set_variable('param');
set_variable('victim');

$victim = preg_replace("/[^$nick_available_chars]/", "", $victim);
$victim = trim($victim);

$actions=array();
$items=$current_user->items;
@reset($items);
$have_action='';
print_r($item_list[$items[0]['ItemID']]);
while( list($id,$item)=@each($items) ){
        if($item_list[$item['ItemID']]->action!="0" && !empty($item_list[$item['ItemID']]->action)){
                if(file_exists($engine_path."item_actions/".$item_list[$item['ItemID']]->action."/backend.php") && $item_list[$item['ItemID']]->action==$action_name && !$have_action){
                        $itmTitle = $item_list[$item['ItemID']]->title;
                        $have_action=$id;
                }
        }
}
if(strlen($have_action)>0){
        $action_id=$have_action;
        $action_items=$items;
        include $engine_path."item_actions/".$action_name."/backend.php";
        $current_user->items=$action_items;
        include($ld_engine_path."user_info_update.php");

        if($victim != "") $MsgToPass = $w_adm_user_item_used_on;
        else $MsgToPass = $w_adm_user_item_used;

        $MsgToPass = str_replace("~", $itmTitle, $MsgToPass);
        if($victim != "") $MsgToPass = str_replace("#", $victim, $MsgToPass);

        WriteToUserLog($MsgToPass, $is_regist, "");

        header("Location: user_info.php?session=$session");
}else{
        die('Hack atempt');
        header("Location: user_info.php?session=$session");
}
?>
