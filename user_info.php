<?php
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
include_once($data_path."engine/files/user_log.php");
include($ld_engine_path."users_get_object.php");

include($file_path."user_validate.php");

include($engine_path."class_items.php");
include ($engine_path."get_item_list.php");

// password lifetime check.
// first introduced in VOC++ BSE
if($current_user->user_class > 0 or
   $current_user->custom_class > 0 or
   $current_user->allow_pass_check) {

   $CanBeDone = true;
   if($current_user->last_pass_check < my_time() - PASS_CHANGE_TIME) $CanBeDone = false;

   set_variable("act");
   if($act == "change_pass") {

    set_variable("old_password");
    set_variable("passwd1");
    set_variable("passwd2");

      $info_message = "";
      $passwd1 = str_replace("\t","",$passwd1);
      if ((!$passwd1) or ($passwd1 != $passwd2) )
        $info_message =  $w_password_mismatch;
      else  {
        if($md5_salt == "") {
         if($current_user->password == md5($old_password)) {

              if($current_user->password != md5($passwd1)) {
                $current_user->password = md5($passwd1);
                $current_user->last_pass_check = my_time();

                $User_UpdatePassword = true;
                include($ld_engine_path."user_info_update.php");
                $CanBeDone = true;
              }
              else {
                $info_message = $w_incorrect_password."!";
              }
         }
         else {
              $info_message = $w_incorrect_password."!";
         }
      } else {
           $passSalt = md5($old_password);
           $passSalt = $md5_salt.$passSalt;
           $passSalt = md5($passSalt);

           if($current_user->password == $passSalt or $current_user->password == md5($old_password)) {
              $passSalt = md5($passwd1);
              $passSalt = $md5_salt.$passSalt;
              $passSalt = md5($passSalt);

              if($current_user->password != $passSalt) {
                $current_user->password = $passSalt;

                $User_UpdatePassword = true;
                $current_user->last_pass_check = my_time();
                include($ld_engine_path."user_info_update.php");
                $CanBeDone = true;
              } else {
                     $info_message = $w_incorrect_password."!";
              }
           }
           else {
              $info_message = $w_incorrect_password."!";
           }
        }

     }

   }

   if(!$CanBeDone) {
       include($file_path."designes/".$design."/pass.php");
       exit;
   }
}


set_variable("op");

if($op == "do_exchange" and $is_regist_complete) {


   if(intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"]) > my_time()) {
         $MsgToPass = $w_user_chaos;
         $MsgToPass = str_replace("~", date("d.m.Y H:i:s",intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"])), $MsgToPass);
         $error_text = $MsgToPass;
         include($file_path."designes/".$design."/error_page.php");
         exit;
   }


   set_variable("crd");
   $crd = intval($crd);
   if($crd < 0) $crd = 0;

   if($crd > $current_user->points) {
      $error_text = $w_no_credits;
      $op = "exchange";
   }
   else if($crd <  $tarrifs["tax"]) {
      $error_text = $w_no_credits;
      $op = "exchange";
   }
   else {
     $oldCrd = $current_user->credits;
     $current_user->credits += intval($crd / $tarrifs["tax"]);
     $current_user->points  -= intval($crd / $tarrifs["tax"])*$tarrifs["tax"];
     include($ld_engine_path."user_info_update.php");

     include_once($data_path."engine/files/user_log.php");

      $fp = fopen($data_path."users/exchanges.log", "a+b");
      if($fp) {
           fwrite($fp,date("H:i:s d-m-Y", my_time())."\t".$user_name."\t".$current_user->nickname."\t".$crd."\t".$current_user->points."\n");
           fclose($fp);
      }

     $MsgToPass = $sw_adm_user_exchange;
     $MsgToPass = str_replace("~", intval($crd / $tarrifs["tax"])*$tarrifs["tax"], $MsgToPass);
     $MsgToPass = str_replace("#", intval($crd / $tarrifs["tax"]), $MsgToPass);
     $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
     $MsgToPass = str_replace("%", $current_user->credits, $MsgToPass);

     WriteToUserLog($MsgToPass, $is_regist, "");

     if($current_user->reffered_by > 0 and
        $current_user->ref_payment_done) {

        $old_reg   = $is_regist;
        $old_user  = $current_user;

        $is_regist = $current_user->reffered_by;
        include($ld_engine_path."users_get_object.php");

        $toAdd = intval(intval($crd / $tarrifs["tax"])*0.1);
        if($toAdd <= 0) $toAdd = 1;

        $oldCrd = $current_user->credits;
        $current_user->credits += $toAdd;
        include($ld_engine_path."user_info_update.php");

        $MsgToPass = $w_adm_reffered_payment;
        $MsgToPass = str_replace("#", $toAdd, $MsgToPass);
        $MsgToPass = str_replace("~", $old_user->nickname." (EX)", $MsgToPass);
        $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
        $MsgToPass = str_replace("%", $current_user->credits, $MsgToPass);

        WriteToUserLog($MsgToPass, $is_regist, "");

        $is_regist    = $old_reg;
        $current_user = $old_user;
      }

     $op = "";
   }
}

if($op == "do_transfer" and $is_regist_complete) {

   if(intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"]) > my_time()) {
         $MsgToPass = $w_user_chaos;
         $MsgToPass = str_replace("~", date("d.m.Y H:i:s",intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"])), $MsgToPass);
         $error_text = $MsgToPass;
         include($file_path."designes/".$design."/error_page.php");
         exit;
   }

   set_variable("crd_transfer");
   $crd_transfer = intval($crd_transfer);
   if($crd_transfer < 0) $crd_transfer = 0;

   if($crd_transfer == 0) {
     $error_text = $w_no_money;
     $op = "transfer";
   }

   set_variable("pass_transfer");

     if($md5_salt == "") {
         if($current_user->password != md5($pass_transfer)) {
            $error_text = $w_incorrect_password;
            $op = "transfer";
         }
      } else {
           $passSalt = md5($pass_transfer);
           $passSalt = $md5_salt.$passSalt;
           $passSalt = md5($passSalt);

           if($current_user->password != $passSalt and $current_user->password != md5($pass_transfer)) {
                 $error_text = $w_incorrect_password;
                 $op = "transfer";
           }
      }

   if(intval($crd_transfer + $tarrifs["transfer"]) < 0) {      $error_text = $w_no_money;
      $op = "transfer";
   }
   if(intval($crd_transfer + $tarrifs["transfer"]) > $current_user->credits) {
      $error_text = $w_no_money;
      $op = "transfer";
   }

   if($error_text == "") {
     set_variable("user_transfer");
     $user_transfer = preg_replace("/[^$nick_available_chars]/", "", $user_transfer);

     set_variable("clan_transfer");
     $clan_transfer = intval($clan_transfer);

     if($user_transfer != "") {
       //user-to-user transfer
       $user_to_search = $user_transfer;

       $u_ids = array();
       include($ld_engine_path."users_search.php");

       $IsFound = 0;

       if(count($u_ids)) {
            for($j = 0; $j < count($u_ids); $j++) {
               if(strcasecmp(trim($u_names[$j]), $user_to_search) == 0){
                      $send_to_id = $u_ids[$j];
                      $IsFound = 1;
                   break;
               }
            }
       }
       if(!$IsFound) {
          $error_text = $w_no_user;
          $op = "transfer";
       }
       else if($send_to_id != $is_regist) {
            $oldOne  = $is_regist;
            $oldNick = $current_user->nickname;
            $oldCrd  = $current_user->credits;

            $total_money = intval($crd_transfer + $tarrifs["transfer"]);
            if($total_money <= 0 or $total_money > $oldCrd) $total_money = 1;
            $current_user->credits -= intval($total_money);
            include($ld_engine_path."user_info_update.php");

            $fp = fopen($data_path."users/money_transfer.log", "a+b");
            if($fp) {
                         fwrite($fp,date("H:i:s d-m-Y", my_time())."\t".$user_name."\t".$user_to_search."\t".$total_money."\t".$current_user->credits."\n");
                         fclose($fp);
            }

            $oldNCrd = $current_user->credits;

            //target user
            $is_regist = $send_to_id;
            include($ld_engine_path."users_get_object.php");
            $nCrd = $current_user->credits;

            $fp = fopen($data_path."users/money_transfer.log", "a+b");
            if($fp) {
                         fwrite($fp,date("H:i:s d-m-Y", my_time())."\t".$user_name."\t".$user_to_search."\t".$crd_transfer."\t".$current_user->credits."\n");
                         fclose($fp);
            }

            $current_user->credits += intval($crd_transfer);
            include($ld_engine_path."user_info_update.php");

            //пишем первому в лог
            $MsgToPass = $sw_adm_money_transfer_from;
            $MsgToPass = str_replace("#", $crd_transfer, $MsgToPass);
            $MsgToPass = str_replace("~", $current_user->nickname." (id: $is_regist)", $MsgToPass);
            $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
            $MsgToPass = str_replace("%", $oldNCrd, $MsgToPass);

            WriteToUserLog($MsgToPass, $oldOne, "");

            //пишем второму в лог
            $MsgToPass = $sw_adm_money_transfer;
            $MsgToPass = str_replace("#", $crd_transfer, $MsgToPass);
            $MsgToPass = str_replace("~", $oldNick." (id: $oldOne)", $MsgToPass);
            $MsgToPass = str_replace("$", $nCrd, $MsgToPass);
            $MsgToPass = str_replace("%", $current_user->credits, $MsgToPass);

            WriteToUserLog($MsgToPass, $is_regist, "");

            $is_regist = $oldOne;
            include($ld_engine_path."users_get_object.php");
            $op = "";
       }

     } else if($clan_transfer == 1 and $current_user->clan_id > 0) {
           //user-to-clan transfer
           $oldCrd  = $current_user->credits;

           $total_money = $crd_transfer + $tarrifs["transfer"];
           $current_user->credits -= intval($total_money);
           include($ld_engine_path."user_info_update.php");

           $current_clan = new Clan;

           $is_regist_clan = intval($current_user->clan_id);
           include($ld_engine_path."clan_get_object.php");

           $oldClanCrd = $current_clan->credits;
           $current_clan->credits += $crd_transfer;

           //пишем первому в лог
           $MsgToPass = $sw_adm_money_transfer_from;
           $MsgToPass = str_replace("#", $crd_transfer, $MsgToPass);
           $MsgToPass = str_replace("~", $w_clan_treasury." \"".$current_clan->name."\"", $MsgToPass);
           $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
           $MsgToPass = str_replace("%", $current_user->credits, $MsgToPass);

            WriteToUserLog($MsgToPass, $is_regist, "");

            //пишем клану в лог
            $MsgToPass = $sw_adm_money_transfer;
            $MsgToPass = str_replace("#", $crd_transfer, $MsgToPass);
            $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
            $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
            $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

            $current_clan->money_log[] = array("time" => my_time(),
                                               "body" => $MsgToPass);

            include($ld_engine_path."clan_update_object.php");

           $op = "";
      }
   }
   else $op = "transfer";
}



//Added by MisterX
$actions=array();
$items=$current_user->items;
@reset($items);
while( list($id,$item)=@each($items) ){
        $items_to_render[$id]=$item_list[$item['ItemID']];
        if($item['Present']==1 or $item['Present']== '1')
                $items_presents[$id]=1;
        else
                $items_presents[$id]=0;

        if($item_list[$item['ItemID']]->action!="0" && !empty($item_list[$item['ItemID']]->action)){
                if(file_exists($engine_path."item_actions/".$item_list[$item['ItemID']]->action."/frontend.php"))
                        $actions[]=$item_list[$item['ItemID']]->action;
        }
}

$pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.gif";
if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
if ($pic_name == "") {
        $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpg";
        if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
}
if ($pic_name == "") {
        $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpeg";
        if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
}
$big_picture = $pic_name;

$pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".gif";
if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
if ($pic_name == "") {
        $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".jpg";
        if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
}
$small_picture = $pic_name;
$w_big_photo = str_replace("~",$max_photo_size,str_replace("*", $max_photo_width,str_replace("#",$max_photo_height,$w_big_photo)));

include($file_path."designes/".$design."/user_info.php");
?>