<?
include($engine_path."messages_put.php");

set_variable("new_status");
$new_status = htmlspecialchars(trim($new_status));
if(strlen($new_status) > 25) $new_status  = substr($new_status, 0, 25);
if($new_status != "") {
   $current_user->chat_status = $new_status;
   $action_items[$action_id]['Quantity']--;
   if($action_items[$action_id]['Quantity']<=0) unset($action_items[$action_id]);
}
?>
