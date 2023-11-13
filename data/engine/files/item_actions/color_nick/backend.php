<?

$html_nick = "";

for($i = 0; $i < strlen($cu_array[USER_NICKNAME]); $i++) {
  $l = substr($cu_array[USER_NICKNAME], $i, 1);
  $var_name = "letter_color_".$i;
  set_variable($var_name);
  $l_col = intval($$var_name);

  if (($l_col < 0) or ($l_col >= count($registered_colors))) {$l_col = $default_color;}
  $r_col  = $registered_colors[$l_col][1];
  $r_test = str_replace('#', '', $r_col);

  if($r_test != $r_col) $html_nick .= '<font color="'.$r_col.'">'.$l.'</font>';
  else $html_nick .= '<font color="#'.$r_col.'">'.$l.'</font>';
}

if($current_user->custom_class == 0) $current_user->htmlnick = $html_nick;
$action_items[$action_id]['Quantity']--;
if($action_items[$action_id]['Quantity']<=0)
        unset($action_items[$action_id]);
?>
<script>alert('<?=str_replace("~", "", $w_succesfull_reg);?>');</script>