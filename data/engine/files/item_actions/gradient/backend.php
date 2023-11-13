<?
set_variable("start_color");
set_variable("end_color");
$current_user->plugin_info["gradient_color_start"] = intval($start_color);
$current_user->plugin_info["gradient_color_end"] = intval($end_color);
$current_user->style_start  = "<b>";
$current_user->style_end  = "</b>";

$action_items[$action_id]['Quantity']--;
if($action_items[$action_id]['Quantity']<=0)
        unset($action_items[$action_id]);
?>
