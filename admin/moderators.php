<?php
include("check_session.php");
include("header.php");
include("../inc_common.php");
include_once($file_path."languages/".$adm_lang_common);

function array_trim ( $array, $index ) {
   if (is_array ( $array ) ) {
     unset ( $array[$index] );
     array_unshift ( $array, array_shift ( $array ) );
     return $array;
     }
   else {
     return false;
     }
}
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<?php
$user_id = intval($user_id);
require("../inc_user_class.php");

set_variable("operation");
$class_to_set = 0;
for ($adm_level = 0; $adm_level<$total_admin_levels; $adm_level++) {
        $adm = pow(2,$adm_level);
        $current_level = "level".$adm_level;
        set_variable($current_level);
        if ($$current_level == "on") {
                $class_to_set += $adm;
                if ($adm == ADM_IP_BAN or $adm == ADM_BAN_MODERATORS) $class_to_set = $class_to_set|ADM_BAN;
        }
}
if ($long_life_data_engine == "mysql")
{
        include_once($ld_engine_path."inc_connect.php");
}
$is_regist = $user_id;
include($ld_engine_path."users_get_object.php");

#backward compatibility
if ("--".$current_user->user_class == "--admin") $current_user->user_class = ADM_BAN;
if ($operation=="update"||$operation == "update_htmlnick"||$operation == "update_vip"||
    $operation == "update_custom_greeting" || $operation == "update_custom_goodbye" ||
    $operation == "update_chat_status" || $operation == "update_priest"
    || $operation == "update_style_start" || $operation == "update_style_end"
    || $operation == "update_clan" || $operation == "update_clan_powers") {
        if ($operation=="update") $current_user->user_class = $class_to_set;
        else if($operation == "update_htmlnick") {
                set_variable("htmlnick");
                $current_user->htmlnick = $htmlnick;
        }
          else if($operation == "update_custom_greeting") {
                set_variable("custom_greeting");
                $current_user->login_phrase = $custom_greeting;
        }
           else if($operation == "update_custom_goodbye") {
                set_variable("custom_goodbye");
                $current_user->logout_phrase = $custom_goodbye;
        }
           else if($operation == "update_chat_status") {
                set_variable("chat_status");
                $current_user->chat_status = $chat_status;
        }

    else if($operation == "update_vip") {
                set_variable("vip");
                $current_user->user_class = ($vip!="")? _VIP_:0;
        }

    else if($operation == "update_priest") {
                set_variable("priest");
                if($priest != "") $current_user->custom_class = $current_user->custom_class|CST_PRIEST;
        else $current_user->custom_class = 0;
        }

    else if($operation == "update_style_start") {
                set_variable("style_start");
                if($style_start != "") $current_user->style_start = $style_start;
        else $current_user->style_start = "";
        }

        else if($operation == "update_style_end") {
                set_variable("style_end");
                if($style_end != "") $current_user->style_end = $style_end;
                else $current_user->style_end = "";
        }

        else if($operation == "update_clan_powers") {

            if($current_user->clan_id > 0) {
                 set_variable("clan_add_user");
                 set_variable("clan_delete_user");
                 set_variable("clan_edit");
                 set_variable("clan_edit_user");
                 set_variable("clan_status");

                 $clan_level = 0;

                 if(strcasecmp(trim($clan_add_user), "on") == 0)    $clan_level += pow(2, 0);
                 if(strcasecmp(trim($clan_delete_user), "on") == 0) $clan_level += pow(2,1);
                 if(strcasecmp(trim($clan_edit), "on") == 0)        $clan_level += pow(2,2);
                 if(strcasecmp(trim($clan_edit_user), "on") == 0)   $clan_level += pow(2,3);

                 $current_user->clan_class         = $clan_level;
            }

        }

        else if($operation == "update_clan") {
                set_variable("clan");

                include($ld_engine_path."clans_get_list.php");

                $clan = intval(trim($clan));
                $IsClanFound = false;

                for($i = 0; $i < count($clans_list); $i++) {
                    if($clans_list[$i]["id"] == $clan) { $IsClanFound = true; break;}
                }
                if(!$IsClanFound) $clan = 0;

                $current_clan = new Clan;

                //clan
                if($clan != $current_user->clan_id) {
                       if($current_user->clan_id > 0) {
                           $is_regist_clan = intval($current_user->clan_id);
                           include($ld_engine_path."clan_get_object.php");

                           $IsUserFound = false;

                           for($i=0; $i < count($current_clan->members); $i++) {
                                 if($current_clan->members[$i]["id"] == $is_regist or strcasecmp($current_clan->members[$i]["nick"], $current_user->nickname) == 0) {
                                   $IsUserFound = true;
                                   break;
                                 }
                           }

                           if($IsUserFound) {
                                   $current_clan->members = array_trim($current_clan->members, $i);
                                   include($ld_engine_path."clan_update_object.php");
                           }
                       }

                      $current_user->clan_id = $clan;
                      $is_regist_clan        = $current_user->clan_id;

                      if($is_regist_clan > 0) {
                           include($ld_engine_path."clan_get_object.php");

                           $IsUserFound = false;

                           for($i=0; $i < count($current_clan->members); $i++) {
                               if($current_clan->members[$i]["id"] == $is_regist) {
                                  $IsUserFound = true;
                                  break;
                               }
                           }
                           if(!$IsUserFound) {
                               $idx = count($current_clan->members);
                               $current_clan->members[$idx]["id"]   = $is_regist;
                               $current_clan->members[$idx]["nick"] = $current_user->nickname;
                               include($ld_engine_path."clan_update_object.php");
                           }
                      }

                }
        }

        $info_message = "";
        $User_UpdatePassword = true;
        include($ld_engine_path."user_info_update.php");
}
?>
<form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<Font color=Black><?php echo $adm_user." <font size=+1>[".$current_user->nickname."]</font> ".$adm_has_rights; ?>:</font><br>
<table border="0">
<?php for ($adm_level = 0; $adm_level<$total_admin_levels; $adm_level++) {
        $adm = pow(2,$adm_level);
        echo "<tr><td class=tip><font size ='2' color=Black style=\"font-weight: normal;\">".$w_adm_level[$adm]."</font></td>";
        echo "<td><input type=\"checkbox\" name=\"level".$adm_level."\"";
        if ($current_user->user_class & $adm && $current_user->user_class>0) echo "checked";
        echo "></td></tr>";

}
?>
</table>
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_htmlnick">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
Htmlnick:<br><input type="text" maxlength=512 size= 50 name="htmlnick" value="<?php echo htmlspecialchars($current_user->htmlnick);?>">
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td>
<form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_clan">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">

<?=$w_roz_clan?>: <select name="clan" class="input"><option value="">--</option>
<?php
     include($ld_engine_path."clans_get_list.php");
     for ($i=0; $i<count($clans_list); $i++) {
         echo "<option value=\"".$clans_list[$i]["id"]."\"";
         if ($clans_list[$i]["id"] == $current_user->clan_id) echo " selected";
         echo ">".$clans_list[$i]["name"]."</option>\n";
    }
  echo "</select>";
?>
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td>
<form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_clan_powers">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">

<?=$w_roz_clan_add_user?>: <input type=checkbox name="clan_add_user" <?php
                        if($current_user->clan_class & CLN_ADDUSER) echo  " checked";
                        echo "><br>";

echo $w_roz_clan_delete_user.": <input type=checkbox name=\"clan_delete_user\" ";
                        if($current_user->clan_class & CLN_DELETEUSER) echo " checked";
                        echo "><br>";

echo $w_roz_clan_edit.": <input type=checkbox name=\"clan_edit\" ";
                        if($current_user->clan_class & CLN_EDIT) echo " checked";
                       echo "><br>";

echo $w_roz_clan_edit_user.": <input type=checkbox name=\"clan_edit_user\" ";
                        if($current_user->clan_class & CLN_EDITUSER) echo " checked";
                        echo  ">";
?>
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>



<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_custom_greeting">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<?php  echo $w_roz_custom_login; ?>:<br><input type="text" size= 50 name="custom_greeting" value="<?php echo htmlspecialchars($current_user->login_phrase);?>">
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_custom_goodbye">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<?php  echo $w_roz_custom_logout; ?>:<br><input type="text" size= 50 name="custom_goodbye" value="<?php echo htmlspecialchars($current_user->logout_phrase);?>">
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_chat_status">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<?php  echo $w_roz_chat_status; ?>:<br><input type="text" size= 50 name="chat_status" value="<?php echo htmlspecialchars($current_user->chat_status);?>">
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_style_start">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<?php  echo $w_roz_style_start; ?>:<br><input type="text" size= 50 name="style_start" value="<?php echo htmlspecialchars($current_user->style_start);?>">
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_style_end">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<?php  echo $w_roz_style_end; ?>:<br><input type="text" size= 50 name="style_end" value="<?php echo htmlspecialchars($current_user->style_end);?>">
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_vip">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<input type="checkbox" name="vip" <?php if($current_user->user_class == _VIP_) echo "checked";?>> <?php echo $adm_vip_message; ?>
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

<tr><td><form method="post" action="moderators.php">
<input type="hidden" name="operation" value="update_priest">
<input type="hidden" name="session" value="<?php echo $session;?>">
<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
<input type="hidden" name="lang" value="<?php echo $lang;?>">
<input type="checkbox" name="priest" <?php if($current_user->custom_class & CST_PRIEST) echo "checked";?>> <?php echo $adm_shaman_message; ?>
<br>
<input type="submit" value="<?php echo $adm_update;?>" class=button>
</form>
</td></tr>

</table></center>
</body>
</html>