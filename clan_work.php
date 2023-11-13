<?php
require_once("inc_common.php");
include($ld_engine_path."rooms_get_list.php");
include($engine_path."users_get_list.php");

include_once($data_path."engine/files/user_log.php");
include($file_path."tarrifs.php");

$messages_to_show = array();
if (!$exists) {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if(!$is_regist_complete) {
    $error_text = "<div align=center>$w_roz_only_for_club.</div>";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

include("user_validate.php");

set_variable("op");
if(strlen(trim($op)) == 0) $op = "add";

if ($current_user->clan_class<1 or $current_user->clan_id < 1) {
        $error_text = "$w_no_admin_rights";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

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

switch ($op) {
    case "do_money_transfer":
      if (!($current_user->clan_class & CLN_ADDUSER)) {
              $error_text = $w_adm_no_permission;
              include($file_path."designes/".$design."/error_page.php");
              exit;
      }

     set_variable("crd_transfer");
     $crd_transfer = intval($crd_transfer);
     if($crd_transfer < 0) $crd_transfer = 0;

     $is_regist_clan = $current_user->clan_id;
     $current_clan = new Clan();
     include($ld_engine_path."clan_get_object.php");

     if($crd_transfer == 0) {
       $error_text = $w_no_money;
       $op = "clan_money_transfer";
     }
     set_variable("pass_transfer");

     if($md5_salt == "") {
         if($current_user->password != md5($pass_transfer)) {
            $error_text = $w_incorrect_password;
            $op = "clan_money_transfer";
         }
      } else {
           $passSalt = md5($pass_transfer);
           $passSalt = $md5_salt.$passSalt;
           $passSalt = md5($passSalt);

           if($current_user->password != $passSalt and $current_user->password != md5($pass_transfer)) {
                 $error_text = $w_incorrect_password;
                 $op = "clan_money_transfer";
           }
      }

     if(intval($crd_transfer) > $current_clan->credits) {
       $error_text = $w_no_money;
       $op = "clan_money_transfer";
     }

    if($error_text == "") {
       set_variable("user_transfer");
       $user_transfer = preg_replace("/[^$nick_available_chars]/", "", $user_transfer);

       if($user_transfer != "") $user_to_search = $user_transfer;

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
       else {
            $oldOne  = $is_regist;
            $oldNick = $current_user->nickname;
            $oldCrd  = $current_clan->credits;

            $total_money = intval($crd_transfer);

            $current_clan->credits -= intval($total_money);
                        //пишем клану в лог
            $MsgToPass = $w_clan_money_transfer_cln;
            $MsgToPass = str_replace("#", $crd_transfer, $MsgToPass);
            $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
            $MsgToPass = str_replace("!", $user_transfer, $MsgToPass);
            $MsgToPass = str_replace("$", $oldCrd, $MsgToPass);
            $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

            $current_clan->money_log[] = array("time" => my_time(),
                                               "body" => $MsgToPass);

            include($ld_engine_path."clan_update_object.php");

            $oldNCrd = $current_user->credits;

            //target user
            $is_regist = $send_to_id;
            include($ld_engine_path."users_get_object.php");
            $nCrd = $current_user->credits;
            $current_user->credits += intval($crd_transfer);
            include($ld_engine_path."user_info_update.php");

            //пишем первому в лог
            WriteToUserLog($MsgToPass, $oldOne, "");

            //пишем второму в лог
            WriteToUserLog($MsgToPass, $is_regist, "");

            $is_regist = $oldOne;
            include($ld_engine_path."users_get_object.php");
            $op = "";
       }

    }
    if($error_text != "") {
        include($file_path."designes/".$design."/error_page.php");
        exit;
    } else {
        $error_text = $w_money_transfer_ok;
        include($file_path."designes/".$design."/error_page.php");
        exit;
    }

    break;

    case "clan_money_transfer":
      if (!($current_user->clan_class & CLN_ADDUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
      }

    include($file_path."designes/".$design."/common_title.php");
    include($file_path."designes/".$design."/common_body_start.php");
    echo "<b>$w_clan_money_transfer</b><br>";

    include("inc_user_class.php");
    $is_regist_clan = $current_user->clan_id;
    $current_clan = new Clan();
    include($ld_engine_path."clan_get_object.php");

  ?>
   <form method=post action="clan_work.php">
   <input type="hidden" name="session" value="<?php echo $session;?>">
   <input type="hidden" name="op" value="do_money_transfer">
      <table border="0" cellpadding="2" cellspacing="2" width="500">
         <tr><td><b><font color="red"><?=$error_text?></font></b></td></tr>
         <tr><td><?=$w_money?>:</td><td><b><?=$current_clan->credits?></b></td></tr>
         <tr><td><?=$w_money_transfer_amount?>:</td><td><b><input type="text" name="crd_transfer" value="<?=intval($crd_transfer)?>" class="input"></b></td></tr>
         <tr><td><?=$w_roz_user?>:</td><td><input type="text" name="user_transfer" value="<?=$user_transfer?>" class="input"></td></tr>
         <tr><td colspan=2><small><?=$w_money_transfer_password?></small></td></tr>
         <tr><td><?=$w_password?>:</td><td><b><input type="password" name="pass_transfer" class="input"></b></td></tr>
      </table>
      <input type="submit" value="<?=$w_money_transfer_accept?>" class="input_button">
   </form>
  <?php
   include($file_path."designes/".$design."/common_body_end.php");
   exit;
       break;

       case "clan_money":
                if (!($current_user->clan_class & CLN_ADDUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
               include("inc_user_class.php");
               $is_regist_clan = $current_user->clan_id;
               $current_clan = new Clan();
               include($ld_engine_path."clan_get_object.php");

               $html_to_out .= "<center><h2 style=\"color:navy;font-family:Verdana\">".$w_clan_treasury."</h2>";
               $html_to_out .= "<table width=\"95%\" cellpadding=4 cellspacing=0 align=center>";
               $html_to_out .= "<tr><td align=CENTER colspan=2><b>$w_money: ".$current_clan->credits."</b></td></tr>";
               $html_to_out .= "<form action=\"clan_work.php\" method=\"post\">\n";
               $html_to_out .= "<input type=hidden name=session value=\"$session\">\n";
               $html_to_out .= "<input type=hidden name=op value=\"clan_money_transfer\">\n";

               if ($current_user->clan_class & CLN_EDITUSER) {
                     $html_to_out .= "<tr><td align=CENTER colspan=2><input class=input_button type=submit value=\"$w_clan_money_transfer\"></td></tr>";
               }

               $tCount = count($current_clan->money_log);
               for($i = 0; $i < $tCount; $i++) {
                $html_to_out .= "<tr><td width=150 align=CENTER><small>".date("H:i:s d-m-Y", $current_clan->money_log[$tCount-$i-1]["time"])."</small></td><td><small>".$current_clan->money_log[$tCount-$i-1]["body"]."</small></td></tr>";
               }

               $html_to_out .= "</table>";

       break;

       case "add":
                if (!($current_user->clan_class & CLN_ADDUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $html_to_out = "<form method=\"post\" action=\"".$chat_url."clan_work.php\">".
                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                "<input type=\"hidden\" name=\"op\" value=\"user_search\">".
                                                "<table border=\"0\"><tr><td valign=\"middle\">".
                                                $w_enter_nick.": <input type=\"text\" name=\"user_to_search\" class=\"input\"> </td>".
                                                "<td valign=\"middle\">".
                                                "<input type=\"submit\" value=\"".$w_search_button."\" class=\"input_button\">".
                                                "</td></tr></table>\n</form>";
        break;
        case "user_search":
                if (!($current_user->clan_class & CLN_ADDUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("user_to_search");
                $u_ids = array();
                include($ld_engine_path."users_search.php");
                $html_to_out = "";
                if (count($u_ids)) {
                        $html_to_out .= "$w_search_results<br>";
                        for ($i=0;$i<count($u_ids);$i++)
                                $html_to_out .= "".htmlspecialchars($u_names[$i])." -- [<a href=\"".$chat_url."clan_work.php?op=add_user&user_id=".$u_ids[$i]."&session=$session\">".$w_roz_clan_edt_add_usr."</a>]<br>";
                } else
                        $html_to_out .= str_replace("~","&quot;<b>".$user_to_search."</b>&quot;",$w_search_no_found);
        break;

    case "del":
                if (!($current_user->clan_class & CLN_DELETEUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
       include("inc_user_class.php");
       $is_regist_clan = $current_user->clan_id;
       include($ld_engine_path."clan_get_object.php");

       $html_to_out .= "<b>$w_roz_clan_edt_del_usr:<br></b>";
       $html_to_out .= "$w_search_results<br>";
        for ($i=0;$i<count($current_clan->members);$i++)
                        $html_to_out .= htmlspecialchars($current_clan->members[$i]["nick"])." -- [<a href=\"".$chat_url."clan_work.php?op=delete_user&user_id=".$current_clan->members[$i]["id"]."&session=$session\">".$w_roz_clan_edt_del_usr."</a>]<br>";

        break;


    case "add_user":
                if (!($current_user->clan_class & CLN_ADDUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $my_clan_id = $current_user->clan_id;

        set_variable("user_id");
        $user_id = intval(trim($user_id));

        $old_reg   = $is_regist;
        $is_regist = $user_id;

        include("inc_user_class.php");
        include($ld_engine_path."users_get_object.php");

        if(!$current_user->registered) {
            $error_text = "<div align=center>$w_roz_not_in_club</div>";
            include($file_path."designes/".$design."/error_page.php");
            exit;
        }

        if($current_user->credits < ($tarrifs["add_clan_user"] + $tarrifs["add_clan_clan"])) {
            $error_text = "<div align=center><b>$current_user->nickname:</b> $w_no_money</div>";
            include($file_path."designes/".$design."/error_page.php");
            exit;
        }

        if($current_user->clan_id == 0) {

           $is_regist_clan = $my_clan_id;
           include($ld_engine_path."clan_get_object.php");
               //include($ld_engine_path."clans_get_list.php");
           $idx = count($current_clan->members);

           if($idx >= MAX_CLANMEMBERS) {
                           $error_text = $w_roz_clan_exceeds_lim;
                $error_text = str_replace("#", MAX_CLANMEMBERS, $error_text);
                                include($file_path."designes/".$design."/error_page.php");
                                exit;
           }

           $current_user->clan_id  = $my_clan_id;
           $current_user->credits -= ($tarrifs["add_clan_user"] + $tarrifs["add_clan_clan"]);
           include($ld_engine_path."user_info_update.php");

           $current_clan->members[$idx]["id"]   = $is_regist;
           $current_clan->members[$idx]["nick"] = $current_user->nickname;

           $oldClanCrd += $current_clan->credits;
           $current_clan->credits += $tarrifs["add_clan_clan"];

           //пишем клану в лог
           $MsgToPass = $sw_adm_money_transfer;
           $MsgToPass = str_replace("#", $tarrifs["add_clan_clan"], $MsgToPass);
           $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
           $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
           $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

           $current_clan->money_log[] = array("time" => my_time(),
                                              "body" => $MsgToPass);

           //to user's private log
           $MsgToPass =  $sw_adm_user_add_clan;
           $MsgToPass =  str_replace("~", $user_name, $MsgToPass);
           $MsgToPass =  str_replace("#", $current_user->nickname, $MsgToPass);
           $MsgToPass .= "\"".$current_clan->name."\"";

           WriteToUserLog($MsgToPass, $is_regist, "");
           WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");

           include($ld_engine_path."clan_update_object.php");
          $html_to_out = $info_message;
        }
        else {
                $html_to_out = $w_roz_clan_user_exists;
        }

        $is_regist   = $old_reg;
        break;

    case "delete_user":
                if (!($current_user->clan_class & CLN_DELETEUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                $my_clan_id = $current_user->clan_id;

                set_variable("user_id");
        $user_id = intval(trim($user_id));

        $old_reg   = $is_regist;
        $is_regist = $user_id;

        include("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");

        $current_user->clan_id          = 0;
        $current_user->clan_class = 0;
        include($ld_engine_path."user_info_update.php");

        $is_regist_clan = $my_clan_id;
        include($ld_engine_path."clan_get_object.php");

        $IsUserFound = false;

         for($i=0; $i < count($current_clan->members); $i++) {
                                if($current_clan->members[$i]["id"] == $is_regist) {
                                   $IsUserFound = true;
                                   break;
                       }
         }

         if($IsUserFound) {
           $current_clan->members = array_trim($current_clan->members, $i);
           include($ld_engine_path."clan_update_object.php");

            //to user's private log
           $MsgToPass =  $sw_adm_user_del_clan;
           $MsgToPass =  str_replace("~", $user_name, $MsgToPass);
           $MsgToPass =  str_replace("#", $current_user->nickname, $MsgToPass);
           $MsgToPass .= "\"".$current_clan->name."\"";

           WriteToUserLog($MsgToPass, $is_regist, "");
           WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");
         }

         $html_to_out = $info_message;
         $is_regist   = $old_reg;
        break;


        case "edit_user":
                if (!($current_user->clan_class & CLN_EDITUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
       include("inc_user_class.php");
       $is_regist_clan = $current_user->clan_id;
       include($ld_engine_path."clan_get_object.php");

       $html_to_out .= "<b>$w_roz_clan_edt_edt_usr:<br></b>";
       $html_to_out .= "$w_search_results<br>";
                 for ($i=0;$i<count($current_clan->members);$i++)
                                $html_to_out .= "<a href=\"".$chat_url."clan_work.php?op=update_user&user_id=".$current_clan->members[$i]["id"]."&session=$session\">".htmlspecialchars($current_clan->members[$i]["nick"])."</a><br>";

        break;
        case "update_user":
                  if (!($current_user->clan_class & CLN_EDITUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
                set_variable("user_id");
                $user_id = intval($user_id);

                $is_regist = $user_id;
                include("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");

                if($current_user->clan_id != $cu_array[USER_CLANID]) exit;

                $html_to_out = "<form method=\"post\" action=\"".$chat_url."clan_work.php\">".
                                                "<input type=\"hidden\" name=\"session\" value=\"$session\">".
                                                "<input type=\"hidden\" name=\"op\" value=\"update_user_info\">".
                                                "<input type=\"hidden\" name=\"user_id\" value=\"$user_id\">".
                                                "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">".
                        "<tr><td colspan=2 align=CENTER><FONT size=3>".$current_user->nickname."</FONT></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_status.": </td><td><input type=\"text\" size=\"15\" name=\"clan_status\" value=\"".$current_user->clan_status."\" class=\"input\" maxlength=20></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_add_user.": </td><td><input type=checkbox name=\"clan_add_user\" class=\"input\"";
                        if($current_user->clan_class & CLN_ADDUSER) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_delete_user.": </td><td><input type=checkbox name=\"clan_delete_user\" class=\"input\"";
                        if($current_user->clan_class & CLN_DELETEUSER) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_edit.": </td><td><input type=checkbox name=\"clan_edit\" class=\"input\"";
                        if($current_user->clan_class & CLN_EDIT) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "<tr><td>".$w_roz_clan_edit_user.": </td><td><input type=checkbox name=\"clan_edit_user\" class=\"input\"";
                        if($current_user->clan_class & CLN_EDITUSER) $html_to_out .= " checked";
                        $html_to_out .= "></td></tr>";

                        $html_to_out .= "</table><br><input type=\"submit\" value=\"".$w_update."\" class=\"input_button\">.</form>";
        break;
          case "update_user_info":
            if (!($current_user->clan_class & CLN_EDITUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }

       set_variable("user_id");
       $user_id = intval($user_id);

        $is_regist = $user_id;
        include("inc_user_class.php");
        include($ld_engine_path."users_get_object.php");

        if($current_user->clan_id != $cu_array[USER_CLANID]) exit;

        set_variable("clan_add_user");
        set_variable("clan_delete_user");
        set_variable("clan_edit");
        set_variable("clan_edit_user");
        set_variable("clan_status");

        $clan_level = 0;
        $info_message = "";
                $html_to_out = "";

        if(strcasecmp(trim($clan_add_user), "on") == 0) $clan_level += pow(2, 0);
        if(strcasecmp(trim($clan_delete_user), "on") == 0) $clan_level += pow(2,1);
        if(strcasecmp(trim($clan_edit), "on") == 0) $clan_level += pow(2,2);
        if(strcasecmp(trim($clan_edit_user), "on") == 0) $clan_level += pow(2,3);

        $current_user->clan_class         = $clan_level;
        $current_user->clan_status         = htmlspecialchars(trim($clan_status));
        if(strlen($current_user->clan_status) > 20) $current_user->clan_status = substr($current_user->clan_status, 0, 20);

                   include($ld_engine_path."user_info_update.php");
                $html_to_out .= $info_message;
    break;

    case "edit_clan":
    set_variable("clan_err");
           if (!($current_user->clan_class & CLN_EDITUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }

    $is_regist_clan = $current_user->clan_id;

        include("inc_user_class.php");
        $current_clan = new Clan();
        include($ld_engine_path."clan_get_object.php");

        if($current_clan->border == 1) $current_clan->border = "checked";
        else $current_clan->border = "";

    $clan_id = $current_user->clan_id;

    $html_to_out .= "<center><table width=\"90%\" cellpadding=4 cellspacing=0><tr><td width=\"90%\" class=head>";
    $html_to_out .= "<center><h2 style=\"color:navy;font-family:Verdana\">".ucfirst($w_roz_clan_edt_edt_cln)."</h2></center>\n";
    $html_to_out .= "<form method=POST action=\"".$chat_url."clan_work.php\" encType=\"multipart/form-data\">";
        $html_to_out .= "<table width=\"90%\" cellpadding=4 cellspacing=0><tr><td width=\"90%\" class=head>";
    $html_to_out .= "<input type=\"hidden\" name=\"session\" value=\"$session\">";
    $html_to_out .= "<input type=\"hidden\" name=\"op\" value=\"edit_clan_info\">";
    $html_to_out .=  "<input type=\"hidden\" name=\"clan_id\" value=\"".$current_user->clan_id."\">";

           if($clan_err != "")  $html_to_out .= "<tr><td colspan=2 class=head align=center bgcolor=#FFB9A1><FONT color=Red>$clan_err;</FONT></td></tr>";

        $html_to_out .= "<tr><td colspan=2 class=head align=center bgcolor=#B7D6FF><b>".$current_clan->name."</b></td></tr>";
    $html_to_out .= "<tr><td>$w_roz_clan_name:</td><td><input class=input type=text name=\"clan_name\" maxlength=20 value=\"".$current_clan->name."\"></td></tr>";
    $html_to_out .= "<tr><td>$w_roz_clan_email:</td><td><input class=input type=text name=\"clan_email\" value=\"".$current_clan->email."\"></td></tr>";
    $html_to_out .= "<tr><td>$w_roz_clan_url:</td><td><input class=input type=text name=\"clan_url\" value=\"".htmlentities($current_clan->url)."\"></td></tr>";
    //$html_to_out .= "<tr><td>$w_roz_clan_cst_greet:</td><td><input type=text class=input name=\"clan_greeting\" value=\"".$current_clan->greeting."\"></td></tr>";
    //$html_to_out .= "<tr><td>$w_roz_clan_cst_goodbye:</td><td><input type=text class=input name=\"clan_goodbye\" value=\"".$current_clan->goodbye."\"></td></tr>";

    if(is_file($file_path."clans-avatar/".floor($clan_id/2000)."/".$clan_id.".gif")) {
        $html_to_out .= "<tr><td>$w_roz_clan_avatar</td><td><img src=\"$chat_url/clans-avatar/".floor($clan_id/2000)."/".$clan_id.".gif\"></td></tr>";
        }
    $html_to_out .= "<tr><td align=right>$w_roz_clan_del_avatar:</td><td><input type=checkbox class=input name=\"delete_avatar\"></td></tr>";
    $html_to_out .= "<tr><td>$w_roz_clan_avatar</td><td><input type=file class=input name=\"clan_avatar\"></td></tr>";

    if(is_file($file_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".gif")) {
            $html_to_out .= "<tr><td>$w_roz_clan_logo:</td><td><img src=\"$chat_url/clans-logos/".floor($clan_id/2000)."/".$clan_id.".gif\"></td></tr>";
    }

    if(is_file($file_path."clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpg")) {
            $html_to_out .= "<tr><td>$w_roz_clan_logo:</td><td><img src=\"$chat_url/clans-logos/".floor($clan_id/2000)."/".$clan_id.".jpg\"></td></tr>";
    }
    $html_to_out .= "<tr><td align=right>$w_roz_clan_del_logo:</td><td><input type=checkbox name=\"delete_logo\"></td></tr>";
    $html_to_out .= "<tr><td>$w_roz_clan_logo</td><td><input class=input type=file name=\"clan_logo\"></td></tr>";
    $html_to_out .= "<tr><td>$w_roz_clan_border</td><td><input class=input type=checkbox name=\"clan_border\"";
    $html_to_out .= $current_clan->border."></td></tr>";
    $html_to_out .= "<tr><td colspan=2 align=center><input type=submit value=\"$w_roz_clan_edit_btn\" class=input_button></td></tr>";
    $html_to_out .= "</table></form></td></tr></table></center>";
    break;

    case "edit_clan_info":
           if (!($current_user->clan_class & CLN_EDITUSER)) {
                        $error_text = $w_adm_no_permission;
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
        }

    include("inc_user_class.php");
    $current_clan                 = new Clan();
    $is_regist_clan = $current_user->clan_id;
          include($ld_engine_path."clan_get_object.php");

    $mode_add_clan            = false;
    $base_dir                        = true;
    include("edit_clan.php");
    echo "<script language=\"JavaScript\">location.href='".$chat_url."clan_work.php?session=$session&op=edit_clan&clan_err=$clan_err';</script>";
    break;
}
include($file_path."designes/".$design."/output_page.php");
?>