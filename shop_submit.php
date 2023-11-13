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

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

include($file_path."user_validate.php");
//include($engine_path."users_get_list.php");

set_variable("present");
set_variable("item");
set_variable("present_for");
set_variable("look_for");
set_variable("todo");

set_variable("reason");
$reason = htmlspecialchars(trim($reason));

$u_ids = array();
$u_names = array();
if($item!=0 && !empty($item)){
	switch ($present){
		case 0:	//start sale for me
				include($engine_path."transactions.php");
				$transaction=new transaction();
				$transaction_result=$transaction->buy($is_regist,$is_regist,$item,1,'');
				if($transaction_result==0)
					header("Location: shop.php?session=$session");
				break;
		case 1: //start sale for anything else
				if($todo!="buy"){
					include "shop_present.php";
				}else{
					include($engine_path."transactions.php");
					$transaction=new transaction();
					$transaction_result=$transaction->buy($is_regist,$present_for,$item,1,$reason);
					if($transaction_result==0)
						header("Location: shop.php?session=$session");
					else
						echo $transaction->str_error($transaction_result);
				}
				break;
		default: exit;
	}
}
?>