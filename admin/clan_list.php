<?php
include("check_session.php");
include("../inc_common.php");
include("header.php");

$main_lang = eregi_replace("admin-", "", $lang);
include($file_path."languages/".$main_lang.".php");

function is_email($address) {
      $rc1 = (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
             '@'.
             '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
             '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
             $address));
      $rc2 = (preg_match('/.+\.\w\w+$/',$address));
      return ($rc1 && $rc2);
}
function is_url($address) {
	$url = fsockopen($address, 80, $errno, $errstr, 30);
	if(!$url) return false;
	return true;
}



//actions
set_variable("action");

if($action == "erase_clan") {
        set_variable("clan_id");
    $IsClanFound = false;

    $clan_id = intval(trim($clan_id));

    include($data_path."engine/files/clans_get_list.php");
    for($i=0; $i < count($clans_list); $i++) {
        if($clan_id == $clans_list[$i]["id"]) {
            $IsClanFound = true;
            $clan_name         = $clans_list[$i]["name"];
            $clans_list[$i]["id"] = 0;
            break;
        }
        }
    if($IsClanFound) {
        $fp = fopen($user_data_file, "r");
                ////flock($fp, LOCK_EX);
                fseek($fp,0);

        include("../inc_user_class.php");

        while ($data = fgets($fp, 4096)) {
                        $user = str_replace("\r","",str_replace("\n","",$data));
                        list($t_id, $t_nickname, $t_password, $t_class,$t_canon) = explode("\t",$user);

            $t_id = intval(trim($t_id));

                    if (file_exists($data_path."users/".floor($t_id/2000)."/".$t_id.".user")) {
                                $current_user = unserialize(implode("",file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")));
                        }

            if($current_user->clan_id ==  $clan_id) {
                                    $current_user->clan_id = 0;
                        $fp1 = fopen ($data_path."users/".floor($t_id/2000)."/".$t_id.".user", "wb");
                                                if (!$fp1) trigger_error("Could not open users/".floor($t_id/2000)."/".$t_id.".user for writing. Please, check permissions", E_USER_ERROR);
                                           //        if (!flock($fp1, LOCK_EX))
                                                //        trigger_error("Could not LOCK file. Do you use Win 95/98/Me?", E_USER_WARNING);
                                                fwrite($fp1,serialize($current_user));
                                                fflush($fp1);
                                                //flock($fp1, LOCK_UN);
                                                fclose($fp1);
            }
        }
        //flock($fp, LOCK_UN);
                fclose($fp);

        include($data_path."engine/files/clans_update_list.php");
        @unlink($data_path."clans/".floor($clan_id/2000)."/".$clan_id.".clan");
        @unlink($data_path."clans/".floor($clan_id/2000)."/clan_avatar_".$clan_id.".gif");
        @unlink($chat_path."clans-avatar/".floor($clan_id/2000)."/".$clan_id.".gif");
        @unlink($chat_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".gif");
        @unlink($chat_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpg");
        @unlink($chat_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpeg");
    }
}

if($action == "delete_clan") {
        $msg_test = $w_roz_clan_del_quest;
    set_variable("clan_id");
    $IsClanFound = false;

    $clan_id = intval(trim($clan_id));

    include($data_path."engine/files/clans_get_list.php");
    for($i=0; $i < count($clans_list); $i++) {
        if($clan_id == $clans_list[$i]["id"]) {
            $IsClanFound = true;
            $clan_name         = $clans_list[$i]["name"];
            break;
        }
        }
    if($IsClanFound) {
            $msg_test = str_replace("#", $clan_name, $msg_test);
?>
<center>
<center><font size=5 color = "#265D92" font="Verdana"><b><?php echo $w_roz_clans;?></b></font></center>
<table width="90%" cellpadding=4 cellspacing=0><tr><td width="70%" class=head align=center>
<tr><td align=CENTER>
<form action="<?php echo $chat_url; ?>admin/clan_list.php" encType="multipart/form-data">
<table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
    <input type="hidden" name="session" value="<?php echo $session; ?>">
    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
    <input type="hidden" name="action" value="erase_clan">
    <input type="hidden" name="clan_id" value="<?php echo $clan_id; ?>">
      <tr><td colspan=2 class=head align=center bgcolor=#FFB9A1><FONT color=Red><?php echo $msg_test; ?></FONT></td></tr>
        <tr><td align=right width=50%><input type=submit class=button value="<?php echo $w_roz_yes; ?>"></td><td align=LEFT>[ <a href="/admin/clan_list.php?&session=<?php echo $session; ?>"><b><?php echo $w_roz_no; ?></b></a> ]</td></tr>
</table>
</td></tr></table>
</center>
</form></body></html>
<?php
        }
        exit;
}

if($action == "add_clan") {
        include("../inc_user_class.php");
    $current_clan = new Clan;

    set_variable("clan_name");
    set_variable("clan_email");
    set_variable("clan_url");
    //set_variable("clan_avatar");
    //set_variable("clan_logo");

    $clan_err  = "";

    $clan_name = trim(htmlspecialchars($clan_name));
    if(strlen($clan_name) == 0) $clan_err = $w_roz_clan_err_name;

    $clan_name = eregi_replace(" +", " ",$clan_name);

    if (ereg("[^ ".$nick_available_chars."]", $clan_name)) {
        $clan_err = $w_roz_clan_err_name;
        }


    include("../inc_to_canon_nick.php");
    $canon_new_name = to_canon_nick($clan_name);

    include($data_path."engine/files/clans_get_list.php");
    for($i=0; $i < count($clans_list); $i++) {
                if(strcasecmp(to_canon_nick(trim($clans_list[$i]["name"])), $canon_new_name) == 0) {
          $clan_err = $w_roz_clan_err_name;
          break;
        }
        }

    $clan_email = trim(htmlspecialchars($clan_email));
    if(strlen($clan_email) > 0) {
             if        (!is_email($clan_email)) $clan_err = $w_roz_clan_err_email;
    }

    $clan_url = trim(htmlspecialchars($clan_url));

    if(strlen($clan_url) > 0) {
             if        (!is_url(htmlentities($clan_url))) $clan_err = $w_roz_clan_err_url;
    }

    if(count($clans_list)) {
            $is_regist_clan = 0;
            for($i = 0; $i < count($clans_list); $i++) {
                   if($clans_list[$i]["id"] > $is_regist_clan) $is_regist_clan = $clans_list[$i]["id"];
            }
            $is_regist_clan++;
    }
    else $is_regist_clan = 1;

    if (isset($HTTP_POST_FILES['clan_avatar']['name'])) $clan_avatar = $HTTP_POST_FILES['clan_avatar']['tmp_name'];
        else $clan_avatar = "";

    if(strlen($clan_avatar) > 0) {
            list($roz_width, $roz_height, $type, $attr) = getimagesize($clan_avatar);
        echo $roz_width.",".$roz_height.",".$type."<br>";
            if($type != 1 or $roz_height > 14 or $roz_width > 18) $clan_err = $w_roz_clan_err_avatar." ($roz_width x $roz_height)";
    }

        if (isset($HTTP_POST_FILES['clan_logo']['name'])) $clan_logo = $HTTP_POST_FILES['clan_logo']['tmp_name'];
        else $clan_logo = "";

    if(strlen($clan_logo) > 0) {
            list($roz_width, $roz_height, $type, $attr) = getimagesize($clan_logo);
            if($type != 1 or $type != 2  or $roz_height > 200 or $roz_width > 200) $clan_err = $w_roz_clan_err_logo." ($roz_width x $roz_height, $type)";
    }

    if(strlen(trim($clan_err)) == 0) {
            $current_clan->name                                 = $clan_name;
        $current_clan->registration_time         = my_time();
        $current_clan->email                                 = $clan_email;
        $current_clan->url                                         = $clan_url;

               include($ld_engine_path."clan_update_object.php");

        $idx = count($clans_list);

        $clans_list[$idx]["id"]         = $is_regist_clan;
        $clans_list[$idx]["name"]   = $clan_name;

        include($data_path."engine/files/clans_update_list.php");

                if(strlen(trim($clan_avatar)) > 0){

        if(!@is_dir($data_path."clans-avatar/".floor($is_regist_clan/2000)))
                if (ini_get('safe_mode'))
                        trigger_error("Your PHP works in SAFE MODE, please create directory clans-avatar/".floor($is_regist_clan/2000),E_USER_ERROR);
                else
                        mkdir($data_path."clans-avatar/".floor($is_regist_clan/2000),0777);

                $newLoc = $file_path."clans-avatar/".floor($is_regist_clan/2000)."/".$is_regist_clan.".gif";
            echo $newLoc."<br>";
            move_uploaded_file ($clan_avatar, $newLoc);
            chmod($newLoc, 0644);
        }
        if(strlen(trim($clan_logo)) > 0){

        if(!@is_dir($data_path."clans-logo/".floor($is_regist_clan/2000)))
                if (ini_get('safe_mode'))
                        trigger_error("Your PHP works in SAFE MODE, please create directory clans-logo/".floor($is_regist_clan/2000),E_USER_ERROR);
                else
                        mkdir($data_path."clans-logo/".floor($is_regist_clan/2000),0777);

                if($type == 1) $newLoc = $file_path."clans-avatar/".floor($is_regist_clan/2000)."/".$is_regist_clan.".gif";
            else $newLoc = $file_path."clans-avatar/".floor($is_regist_clan/2000)."/".$is_regist_clan.".jpg";
            move_uploaded_file ($clan_avatar, $newLoc);
            chmod($newLoc, 0644);
        }
    }

}

?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<?php
echo "<center><font size=5 color = \"#265D92\" font=\"Verdana\">$w_roz_clans</font></center>\n";

include($data_path."engine/files/clans_get_list.php");

function cmp ($a, $b)
 {
  return strcasecmp($a["name"], $b["name"]);
 }

uasort($clans_list, "cmp");

for($i=0; $i < count($clans_list); $i++) {
        echo "<a href=\"".$chat_url."admin/clans.php?lang=$lang&clan_id=".$clans_list[$i]["id"]."&session=$session\">".$clans_list[$i]["name"]."</a> <a style='font-decoration: none;' href=\"".$chat_url."admin/clan_list.php?clan_id=".$clans_list[$i]["id"]."&session=$session&action=delete_clan&lang=$lang\">[$w_roz_remove_clan]</a><br>\n";
}
?>
<form method=POST action="<?php echo $chat_url;?>admin/clan_list.php" encType="multipart/form-data">
<input type=hidden value="<?php echo $lang;?>" name="lang">
<table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
    <input type="hidden" name="session" value="<?php echo $session; ?>">
    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
    <input type="hidden" name="action" value="add_clan">
    <?php
            if($clan_err != "") {
        ?>
           <tr><td colspan=2 class=head align=center bgcolor=#FFB9A1><FONT color=Red><?php echo $clan_err; ?></FONT></td></tr>
        <?php
        }
    ?>
        <tr><td colspan=2 class=head align=center bgcolor=#265D92><?php echo $w_roz_add_clan; ?></td></tr>
    <tr><td><?php echo $w_roz_clan_name; ?></td><td><input type=text name="clan_name"></td></tr>
    <tr><td><?php echo $w_roz_clan_email; ?></td><td><input type=text name="clan_email"></td></tr>
    <tr><td><?php echo $w_roz_clan_url; ?></td><td><input type=text name="clan_url"></td></tr>
    <tr><td><?php echo $w_roz_clan_avatar; ?></td><td><input type=file name="clan_avatar"></td></tr>
    <tr><td><?php echo $w_roz_clan_logo; ?></td><td><input type=file name="clan_logo"></td></tr>
    <tr><td><?php echo $w_roz_clan_border; ?></td><td><input type=checkbox name="clan_border" value=on></td></tr>
    <tr><td colspan=2 align=center><input type=submit class=button_small value="<?php echo $w_roz_add_clan; ?>"></td></tr>
</table>
</form>
</td></tr></table></center>
</body>
</html>