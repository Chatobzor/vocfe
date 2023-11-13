<?
/*
User-items=array(
Date
ItemID
FromNick( Whne i buy some item for me, this field = my user ID)
FromID
Quantity
Reason ( Can be empty)
Present
)
//When transaction is started file with items and profile must be locked
*/

include_once($data_path."engine/files/user_log.php");

class transaction {
        var $items;
        var $items_last_id;

        function str_error($num){
           global $w_shop_no_money, $w_shop_no_items, $w_shop_no_such_item, $shop_vip, $w_shop_no_present;

                switch($num){
                        case 0: return "No errors"; break;
                        case -1: return $w_shop_no_money; break;
                        case -2: return "Sender not found"; break;
//                        case -3: return "Sender not found"; break;
                        case -4: return "Receiver not found"; break;
                        case -5: return $w_shop_no_items; break;
                        case -6: return $w_shop_no_such_item; break;
                        case -7: return $shop_vip; break;
                        case -8: return $w_shop_no_present; break;
                }
        }
        function transaction(){
                global $engine_path, $data_path;
                include_once($engine_path."class_items.php");
                include_once($engine_path."get_item_list.php");
                $this->items=$item_list;
                $this->items_last_id=$last_id;
        }
        function remove_action(){

        }
        function sendMoney($from_user_ID,$to_user_ID,$how){
                global $ld_engine_path, $engine_path, $data_path, $tarrifs;
                //Load users begin
                $is_regist = $from_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,'sendMoney',-1,$how,'sendMoney',-2);
                        return -2;
                }
                include_once("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");
                $from_user=$current_user;

                $is_regist = $to_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,'sendMoney',-1,$how,'sendMoney',-3);
                        return -3;
                }
                include($ld_engine_path."users_get_object.php");
                $to_user=$current_user;
                //Load users end

                if($from_user->credits<($how+$tarrifs['transaction'])){
                        $this->writeLog($from_user_ID,$to_user_ID,'sendMoney',-1,$how,'sendMoney',-1);
                        return -1;
                }
                $from_user->credits=$from_user->credits-$how-$tarrifs['transaction'];
                $to_user->credits=$to_user->credits+$how;

                //Save data begin
                $is_regist=$from_user_ID;
                $current_user=$from_user;
                include($ld_engine_path."user_info_update.php");

                $is_regist=$to_user_ID;
                $current_user=$to_user;
                include($ld_engine_path."user_info_update.php");
                //Save data end
                $this->writeLog($from_user_ID,$to_user_ID,'sendMoney',-1,$how,'sendMoney',0);
                return 0;
        }
        function remove($user_ID,$itemID){
                global $ld_engine_path, $engine_path, $data_path, $tarrifs;
                global $max_mailbox_size, $sw_adm_user_item_removed;

                $is_regist = $user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        return -2;
                }
                include_once("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");
                $user=$current_user;
                @reset($user->items);
                $action_to_remove=$this->items[$user->items[$itemID]['ItemID']]->action;

                $itmTitle = $this->items[$user->items[$itemID]['ItemID']]->title;

                while(list($id,$item)=@each($user->items)){
                        if($itemID!=$id)
                                $rebuilded_array[]=$item;
                }

                if(file_exists($engine_path."item_actions/".$action_to_remove."/remove.php")){
                        $action_user=$user;
                        include $engine_path."item_actions/".$action_to_remove."/remove.php";
                        $user=$action_user;
                }
                $user->items=$rebuilded_array;
                $is_regist=$user_ID;
                $current_user=$user;
                include($ld_engine_path."user_info_update.php");

                //пишем в лог что вещь удалена
                $MsgToPass = $sw_adm_user_item_removed;
                $MsgToPass = str_replace("~", $itmTitle, $MsgToPass);

                WriteToUserLog($MsgToPass, $is_regist, "");
        }

        function refund($from_user_ID,$item_id){
                global $ld_engine_path, $engine_path, $data_path, $tarrifs;
                global $sw_adm_user_item_returned;

                $is_regist = $from_user_ID;

                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        return -2;
                }

                include_once("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");

                $user=$current_user;
                if($user->items[$item_id]['Present']!='1' && $user->items[$item_id]['FromID']==$from_user_ID){
                                $item_to_refund=$user->items[$item_id]['ItemID'];
                                @reset($user->items);
                                while(list($id,$item)=@each($user->items)){
                                        if($item_id!=$id)
                                                $rebuilded_array[]=$item;
                                }
                }else{
                        return -8;
                }
                $user->items=$rebuilded_array;
//                echo $this->items[$item_to_refund]->price;
//                die();
                $total=$this->items[$item_to_refund]->price;

                //Added by DD
                if($this->items[$item_to_refund]->quantity != -1) {
                       $this->items[$item_to_refund]->quantity++;
                       $this->write_items_to_file($this->items,$this->items_last_id);
                }
                //end of added by DD

                if($this->items[$item_to_refund]->vip!=1){
                        if($user->is_member){
                                //Calculate discount
                                $total=intval($total-(($total/100)*$tarrifs["vip_discount"]));
                        }
                }

                $action_to_remove=$this->items[$item_to_refund]->action;
                if(file_exists($engine_path."item_actions/".$action_to_remove."/remove.php")){
                        $action_user=$user;
                        include $engine_path."item_actions/".$action_to_remove."/remove.php";
                        $user=$action_user;
                }

                $oldCrd   = $user->credits;
                $itmTitle = $this->items[$item_to_refund]->title;

                $user->credits = $user->credits + $total - $tarrifs['transaction'];

                $is_regist=$from_user_ID;
                $current_user=$user;

                include($ld_engine_path."user_info_update.php");

                //пишем в лог что вещь отдана в магазин
                $MsgToPass = $sw_adm_user_item_returned;
                $MsgToPass = str_replace("~", $itmTitle, $MsgToPass);
                $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
                $MsgToPass = str_replace("%", $user->credits, $MsgToPass);

                WriteToUserLog($MsgToPass, $is_regist, "");

                return 0;
        }
function present($from_user_ID,$to_user_ID,$item_array_id){
                global $ld_engine_path, $engine_path, $data_path, $tarrifs;
                global $max_mailbox_size, $sw_adm_user_buy, $sw_adm_user_present_from, $sw_adm_user_present;
                global $sw_adm_user_transfer, $sw_adm_user_present_from;

                $is_regist = $from_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'giveItem',-2);
                        return -2;
                }
                include_once("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");
                $from_user=$current_user;

                $is_regist = $to_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'giveItem',-3);
                        return -3;
                }
                include($ld_engine_path."users_get_object.php");
                $to_user=$current_user;

                $itm=$from_user->items[$item_array_id];
                if($itm['Present']!='1'){
                        if($from_user_ID==$to_user_ID){
                                $itm['Present']='1';
                                $from_user->items[]=$itm;
                        }else{
                                $itm['Present']='1';
                                $to_user->items[]=$itm;
                        }
                }else{
                        return -8;
                }

                $item_to_give=$from_user->items[$item_array_id]['ItemID'];
                @reset($from_user->items);
                while(list($id,$item)=@each($from_user->items)){
                        if($item_array_id!=$id)
                                $rebuilded_array[]=$item;
                }

                $action_to_remove=$this->items[$item_to_give]->action;

                if($from_user_ID!=$to_user_ID){
                        if(file_exists($engine_path."item_actions/".$action_to_remove."/remove.php")){
                                $action_user=$from_user;
                                include $engine_path."item_actions/".$action_to_remove."/remove.php";
                                $from_user=$action_user;
                        }
                        if(file_exists($engine_path."item_actions/".$action_to_remove."/buy.php")){
                                $action_user=$to_user;
                                include $engine_path."item_actions/".$action_to_remove."/buy.php";
                                $to_user=$action_user;
                        }
                }
                $from_user->items=$rebuilded_array;
                $is_regist=$from_user_ID;
                $current_user=$from_user;
                include($ld_engine_path."user_info_update.php");

                if($from_user_ID!=$to_user_ID){

                   //пишем в лог что от себя передал
                   $MsgToPass = $sw_adm_user_present_from;
                   $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                   $MsgToPass = str_replace("#", $to_user->nickname." (id: $to_user_ID)", $MsgToPass);

                   WriteToUserLog($MsgToPass, $is_regist, "");

                   $is_regist=$to_user_ID;
                   $current_user=$to_user;
                   include($ld_engine_path."user_info_update.php");

                   //пишем в лог что вещь передана
                   $MsgToPass = $sw_adm_user_present;
                   $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                   $MsgToPass = str_replace("#", $from_user->nickname." (id: $from_user_ID)", $MsgToPass);

                   WriteToUserLog($MsgToPass, $is_regist, "");
                }
                else {
                   //передал, фактически подарил сам себе (зачем, спрашивается? :))
                   //пишем в лог что от себя подарил
                   $MsgToPass = $sw_adm_user_present_from;
                   $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                   $MsgToPass = str_replace("#", $current_user->nickname." (id: $is_regist)", $MsgToPass);

                    WriteToUserLog($MsgToPass, $is_regist, "");

                    //пишем в лог что вещь получена в подарок
                    $MsgToPass = $sw_adm_user_present;
                    $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                    $MsgToPass = str_replace("#", $current_user->nickname." (id: $is_regist)", $MsgToPass);
                    WriteToUserLog($MsgToPass, $is_regist, "");
                }
                return 0;
        }

        function give($from_user_ID,$to_user_ID,$item_array_id){
                global $ld_engine_path, $engine_path, $data_path, $tarrifs;
                global $max_mailbox_size, $sw_adm_user_buy, $sw_adm_user_present_from, $sw_adm_user_present;
                global $sw_adm_user_transfer, $sw_adm_user_transfer_from;

                $is_regist = $from_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'giveItem',-2);
                        return -2;
                }
                include_once("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");
                $from_user=$current_user;

                $is_regist = $to_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'giveItem',-3);
                        return -3;
                }
                include($ld_engine_path."users_get_object.php");
                $to_user=$current_user;
                if($from_user->credits<$tarrifs["transaction"])
                        return -1;

                $itm=$from_user->items[$item_array_id];
                if($itm['Present']!='1'){
                        if($from_user_ID==$to_user_ID){
                                $itm['Present']='0';
                                $from_user->items[]=$itm;
                        }else{
                                $itm['Present']='0';
                                $to_user->items[]=$itm;
                        }
                }else{
                        return -8;
                }

                $oldCrd = $from_user->credits;

                $from_user->credits = $from_user->credits-$tarrifs["transaction"];
                $item_to_give=$from_user->items[$item_array_id]['ItemID'];
                @reset($from_user->items);
                while(list($id,$item)=@each($from_user->items)){
                        if($item_array_id!=$id)
                                $rebuilded_array[]=$item;
                }


                $action_to_remove=$this->items[$item_to_give]->action;

                if($from_user_ID!=$to_user_ID){
                        if(file_exists($engine_path."item_actions/".$action_to_remove."/remove.php")){
                                $action_user=$from_user;
                                include $engine_path."item_actions/".$action_to_remove."/remove.php";
                                $from_user=$action_user;
                        }
                        if(file_exists($engine_path."item_actions/".$action_to_remove."/buy.php")){
                                $action_user=$to_user;
                                include $engine_path."item_actions/".$action_to_remove."/buy.php";
                                $to_user=$action_user;
                        }
                }
                $from_user->items=$rebuilded_array;
                $is_regist=$from_user_ID;
                $current_user=$from_user;
                include($ld_engine_path."user_info_update.php");

                if($from_user_ID!=$to_user_ID){

                   //пишем в лог что от себя передал
                   $MsgToPass = $sw_adm_user_transfer_from;
                   $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                   $MsgToPass = str_replace("#", $to_user->nickname." (id: $to_user_ID)", $MsgToPass);
                   $MsgToPass = str_replace("*", $tarrifs["transaction"], $MsgToPass);
                   $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
                   $MsgToPass = str_replace("%", $from_user->credits, $MsgToPass);

                   WriteToUserLog($MsgToPass, $is_regist, "");

                   $is_regist=$to_user_ID;
                   $current_user=$to_user;
                   include($ld_engine_path."user_info_update.php");

                   //пишем в лог что вещь передана
                   $MsgToPass = $sw_adm_user_transfer;
                   $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                   $MsgToPass = str_replace("#", $from_user->nickname." (id: $to_user_ID)", $MsgToPass);

                   WriteToUserLog($MsgToPass, $is_regist, "");
                }
                else {
                   //передал, фактически подарил сам себе (зачем, спрашивается? :))
                   //пишем в лог что от себя подарил
                   $MsgToPass = $sw_adm_user_present_from;
                   $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                   $MsgToPass = str_replace("#", $current_user->nickname." (id: $is_regist)", $MsgToPass);

                    WriteToUserLog($MsgToPass, $is_regist, "");

                    //пишем в лог что вещь получена в подарок
                    $MsgToPass = $sw_adm_user_present;
                    $MsgToPass = str_replace("~", $this->items[$item_to_give]->title, $MsgToPass);
                    $MsgToPass = str_replace("#", $current_user->nickname." (id: $is_regist)", $MsgToPass);
                    WriteToUserLog($MsgToPass, $is_regist, "");
                }
                return 0;
        }
        function writeLog($senderID,$receiverID,$itemID,$quantity,$price,$operation,$result){
                global $data_path;
                $fp = fopen($data_path."transactions.dat","a");
                   if (!$fp)
                        trigger_error("Could not open $data_path transactions.dat for writing. Please, check permissions", E_USER_ERROR);
                if (!flock($fp, LOCK_EX))
                        trigger_error("Could not write to $data_path transactions.dat! Do you use Win 95/98/Me?", E_USER_WARNING);
                fputs($fp,time()."\t".$senderID."\t".$receiverID."\t".$itemID."\t".$quantity."\t".$price."\t".$operation."\t".$result."\n");
                flock($fp, LOCK_UN);
                fclose($fp);
        }
        function spec_trim($s){
                $s=str_replace("\t","",$s);
                $s=str_replace("\r","",$s);
                $s=str_replace("\n","",$s);
                return $s;
        }
        function write_items_to_file($array,$id=0){
                global $data_path;

                $fp = fopen($data_path."items.dat","w+");
                if (!$fp)
                        trigger_error("Could not open $data_path/items.dat for writing. Please, check permissions", E_USER_ERROR);
                if (!flock($fp, LOCK_EX))
                        trigger_error("Could not write to $data_path/items.dat! Do you use Win 95/98/Me?", E_USER_WARNING);
                fputs($fp,$this->spec_trim($id));
                @reset($array);
                $begin="\n";
                while(list($id,$clas)=@each($array)){
                        $clas->saled=$this->spec_trim($clas->saled);
                        $clas->vip=$this->spec_trim($clas->vip);
                        if(strlen($clas->title) > 50)  $clas->title=substr($clas->title, 0, 50);
                        $str=$begin.$id."\t".$clas->title."\t".$clas->image."\t".$clas->price."\t".$clas->quantity."\t".$clas->saled."\t".$clas->vip."\t".$clas->category."\t".$clas->action;
                        fputs($fp,$str);
                }
                flock($fp, LOCK_UN);
                fclose($fp);
        }

        function buy($from_user_ID,$to_user_ID,$item,$quantity,$reason){
                global $ld_engine_path, $engine_path, $data_path, $tarrifs;
                global $max_mailbox_size, $sw_adm_user_buy, $sw_adm_user_present_from, $sw_adm_user_present;

                $is_regist = $from_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'buy',-2);
                        return -2;
                }
                include_once("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");
                $from_user=$current_user;

                $is_regist = $to_user_ID;
                if (!file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'buy',-3);
                        return -4;
                }
                include($ld_engine_path."users_get_object.php");
                $to_user=$current_user;

                if(empty($item) || $item==0){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'buy',-6);
                        return -6;
                }
                if($item!=$this->items[$item]->id){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'buy',-6);
                        return -6;
                }
                if($this->items[$item]->quantity!=-1){
                        if($this->items[$item]->quantity<$quantity){
                                $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'buy',-5);
                                return -5;
                        }
                }

                if($this->items[$item]->vip==1 and !$from_user->is_member){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'buy',-7);
                        return -7;
                }

                $total=$this->items[$item]->price*$quantity;
                if($this->items[$item]->vip!=1){
                        if($from_user->is_member){
                                //Calculate discount
                                $total=intval($total-(($total/100)*$tarrifs["vip_discount"]));
                        }
                }
                if($from_user->credits<$total){
                        $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,0,'buy',-1);
                        return -1;
                }
                //process_transaction
                $reason=$this->spec_trim(htmlspecialchars($reason));
                $item_array=array(
                        'Date'=>time(),
                        'ItemID'=>$item,
                        'FromNick'=>$from_user->nickname,
                        'FromID'=>$from_user_ID,
                        'Quantity'=>$quantity,
                        'Reason'=>$reason,
                        'Present'=> 0);
                if($to_user_ID==$from_user_ID)
                        $from_user->items[]=$item_array;
                else
                        $to_user->items[]=$item_array;
                //Calculate total
                $oldCrd = $from_user->credits;

                $from_user->credits=$from_user->credits-$total;

                $this->items[$item]->saled++;
                if($this->items[$item]->quantity!=-1)
                        $this->items[$item]->quantity--;

                $this->write_items_to_file($this->items,$this->items_last_id);
                //working with_actions
                if(($this->items[$item]->action!='0') and !empty($this->items[$item]->action)){
                        if(file_exists($engine_path."item_actions/".$this->items[$item]->action."/buy.php")){
                                if($from_user_ID!=$to_user_ID){
                                        $action_user=$to_user;
                                        include($engine_path."item_actions/".$this->items[$item]->action."/buy.php");
                                        $to_user=$action_user;
                                }else{
                                        $action_user=$from_user;
                                        include($engine_path."item_actions/".$this->items[$item]->action."/buy.php");
                                        $from_user=$action_user;
                                }
                                unset($action_user);
                        }
                }


                $is_regist=$from_user_ID;
                $current_user=$from_user;

                include($ld_engine_path."user_info_update.php");

                if($to_user_ID!=$from_user_ID){
                       //пишем в лог что купил себе
                      $MsgToPass = $sw_adm_user_buy;
                      $MsgToPass = str_replace("~", $this->items[$item]->title, $MsgToPass);
                      $MsgToPass = str_replace("#", $total, $MsgToPass);
                      $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
                      $MsgToPass = str_replace("%", $current_user->credits, $MsgToPass);

                      WriteToUserLog($MsgToPass, $is_regist, "");

                      //пишем в лог что от себя подарил
                      $MsgToPass = $sw_adm_user_present_from;
                      $MsgToPass = str_replace("~", $this->items[$item]->title, $MsgToPass);
                      $MsgToPass = str_replace("#", $to_user->nickname." (id: $to_user_ID)", $MsgToPass);

                      WriteToUserLog($MsgToPass, $is_regist, "");

                      //пишем в лог что вещь получена в подарок
                      $MsgToPass = $sw_adm_user_present;
                      $MsgToPass = str_replace("~", $this->items[$item]->title, $MsgToPass);
                      $MsgToPass = str_replace("#", $current_user->nickname." (id: $is_regist)", $MsgToPass);

                      $is_regist=$to_user_ID;
                      $current_user=$to_user;
                      include($ld_engine_path."user_info_update.php");

                      WriteToUserLog($MsgToPass, $is_regist, "");
                }
                else {
                      //пишем в лог что купил себе
                      $MsgToPass = $sw_adm_user_buy;
                      $MsgToPass = str_replace("~", $this->items[$item]->title, $MsgToPass);
                      $MsgToPass = str_replace("#", $total, $MsgToPass);
                      $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
                      $MsgToPass = str_replace("%", $current_user->credits, $MsgToPass);

                      WriteToUserLog($MsgToPass, $is_regist, "");
                }

                $this->writeLog($from_user_ID,$to_user_ID,$item,$quantity,$total,'buy',0);
                return 0;
        }

}
?>
