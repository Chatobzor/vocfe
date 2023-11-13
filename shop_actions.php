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
   header("Location: ".$chat_url."registration_form.php?session=$session&user_name=".urlencode($user_name));
   exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

include($file_path."user_validate.php");
include($engine_path."users_get_list.php");

include($engine_path."class_items.php");
include ($engine_path."transactions.php");

function selectUser($itemID,$what,$look_for){

global $file_path,$current_design, $design,$session,$ld_engine_path,$user_data_file,$nick_available_chars;
global $w_roz_user,$w_search_button;

include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");

//include($file_path."designes/".$design."/common_browser_detect.php");
?>
<script>
function set_user(){
        document.main_form.select_user.value=1;
        document.main_form.submit();
}
</script>
<center>
<form method="post" name="main_form">
        <input type="Hidden" name="what" value="<?=$what;?>">
        <input type="Hidden" name="itemID" value="<?=$itemID;?>">
        <input type="Hidden" name="session" value="<?=$session?>">
        <input type="Hidden" name="select_user" value="0">
        <?=$w_roz_user?>: <input type="Text" name="look_for" value="<?=$look_for;?>">
        <input type="Submit" class="input_button" value="<?=$w_search_button?>">
<?
        if($look_for!=""){
                $look_for = preg_replace("/[^$nick_available_chars]/", "", $look_for);
                $user_to_search = $look_for;
                include($ld_engine_path."users_search.php");
        }
if(count($u_ids)){?>
<hr>
                        <select name="userID">
                                <?
                                for ($i=0;$i<count($u_ids);$i++){
                                ?>
                                <option value="<?=$u_ids[$i];?>" <?php if(strcasecmp($u_names[$i], $look_for) == 0) {?>SELECTED<?php } ?>><?=$u_names[$i]?></option>
                                <?
                                }
                                ?>
                        </select>
                        <input type="Button" onclick="set_user()" class="input_button" value="OK">
<?}?>
</form>
</center>
<?
include($file_path."designes/".$design."/common_body_end.php");
exit();
}
$trans_engine=new transaction();
set_variable('what');
set_variable('itemID');
set_variable('look_for');
set_variable('userID');
set_variable('select_user');

$itemID = intval($itemID);
$userID = intval($userID);

$look_for = preg_replace("/[^$nick_available_chars]/", "", $look_for);

switch($what){
case 'refund':$t_res=$trans_engine->refund($is_regist,$itemID);
                          if($t_res==0)
                                 header("Location: user_info.php?session=$session");
                          else  {
                                 $error_text = $trans_engine->str_error($t_res);
                                 include($file_path."designes/".$design."/error_page.php");
                                }
                          die();
                                break;
case 'give':if(empty($userID) || $select_user=='0'){
                                selectUser($itemID,$what,$look_for);
                                exit();
                        }else{
                                $t_res=$trans_engine->present($is_regist,$userID,$itemID);
                                if($t_res==0)
                                        header("Location: user_info.php?session=$session");
                                else   {
                                        $error_text = $trans_engine->str_error($t_res);
                                        include($file_path."designes/".$design."/error_page.php");
                                       }
                                die();
                        }
                        break;
//added by DD
case 'transfer':if(empty($userID) || $select_user=='0'){
                                selectUser($itemID,$what,$look_for);
                                exit();
                        }else{
                                $t_res=$trans_engine->give($is_regist,$userID,$itemID);
                                if($t_res==0)
                                        header("Location: user_info.php?session=$session");
                                else   {
                                        $error_text = $trans_engine->str_error($t_res);
                                        include($file_path."designes/".$design."/error_page.php");
                                       }
                                die();
                        }
                        break;
case 'send_money':if(empty($userID) || $select_user=='0'){
                                        selectUser($itemID,$what,$look_for);
                                        exit();
                                  }break;
case 'remove':$trans_engine->remove($is_regist,$itemID); header("Location: user_info.php?session=$session");break;
default: break;//Security warning!!!
}

?>
