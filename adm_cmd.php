<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

$admin_script              = "";
$admin_script_type         = "java";

$whisper = htmlspecialchars(trim($whisper));
$mesg    = htmlspecialchars($mesg);


include($ld_engine_path."rooms_get_list.php");
include($engine_path."users_get_list.php");

include($file_path."tarrifs.php");

$messages_to_show = array();
if (!$exists) {
  ?>
    <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_user;?>!');</script>
    <?php
    $mesg = "bla";
    include($file_path."designes/".$design."/sender.php");
    exit;
}
$sender_rights = $cu_array[USER_CLASS];
$custom_rights = $current_user->custom_class;
$admin_id           = $is_regist;

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");
include_once($data_path."engine/files/user_log.php");

$check_type = "admin_command";
include("user_validate.php");

if(intval($current_user->show_for_moders) == 0 and $current_user->user_class & ADM_VIEW_PRIVATE) $IsSilentMode = true;
else $IsSilentMode = false;

if (($current_user->user_class & ADM_BAN) && $banType == "") $banType = "ban";


if ($current_user->user_class<1 and $current_user->custom_class == 0) {
    ?>
    <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
    <?php
    $mesg = "bla";
    include($file_path."designes/".$design."/sender.php");
    exit;
}

if( $banType == "do_alert" or
    $banType == "do_multiplywindows" or
    $banType == "do_redirect" or
    $banType == "do_shutdown" or
    $banType == "do_mouseoff" or
    $banType == "do_keyboardoff" or
    $banType == "do_silence" or
    $banType == "do_ban" or
    $banType == "do_ip_ban" or
    $banType == "do_hash_ban" or
    $banType == "do_damn" or
    $banType == "do_undamn" or
    $banType == "do_ring" or
    $banType == "do_reward" or
    $banType == "do_jail" or
    $banType == "do_subnet_ban" or
    $banType == "do_clear" or
    $banType == "do_chaos") {
            if($whisper == $sw_usr_all_link or
               $whisper == $sw_usr_adm_link or
               $whisper == $sw_usr_boys_link or
               $whisper == $sw_usr_girls_link or
               $whisper == $sw_usr_they_link or
               $whisper == $sw_usr_clan_link or
               $whisper == $sw_usr_shaman_link) {
                        $error_text = "$w_search_no_found";
                        $error_text = str_replace("~", $whisper, $error_text);
                        ?>
                        <script language="JavaScript" type="text/javascript"> alert('<?php echo $error_text;?>!');</script>
                         <?php
                        $mesg = "bla";
                        include($file_path."designes/".$design."/sender.php");
                        exit;
            }

                $old_reg = $is_regist;
                $tmp_admin_rights = $sender_rights;
                $user_to_search = $whisper;
                // try to locate the second user
                $u_ids = array();
                include($ld_engine_path."users_search.php");

                    $IsFound = 0;

                            if (count($u_ids)) {
                            for($j = 0; $j < count($u_ids); $j++) {
                                    if(strcasecmp(trim($u_names[$j]), $user_to_search) == 0){
                                           $is_regist = $u_ids[$j];
                                            include("inc_user_class.php");
                                            include($ld_engine_path."users_get_object.php");
                                            $IsFound = 1;
                    break;
                                }
                            }
                            }

            if(!$IsFound and strcasecmp($whisper, $rooms[$room_id]["bot"]) != 0) {
                        $error_text = "$w_search_no_found";
                        $error_text = str_replace("~", $whisper, $error_text);
                                ?>
                          <script language="JavaScript" type="text/javascript"> alert('<?php echo $error_text;?>!');</script>
                         <?php
                        $mesg = "bla";
                        include($file_path."designes/".$design."/sender.php");
                        exit;
            }

            if($IsFound and $banType != "do_clear") {
                if(intval($current_user->plugin_info["punishment_time"]) + 15 > my_time()) {
                     $error_text = "$w_already_punished";
                        ?>
                        <script language="JavaScript" type="text/javascript"> alert('<?php echo $error_text;?>!');</script>
                         <?php
                        $mesg = "bla";
                        include($file_path."designes/".$design."/sender.php");
                        exit;
                } else {
                     $current_user->plugin_info["punishment_time"] = my_time();
                     include($ld_engine_path."user_info_update.php");
                }
            }

            $banuser_array = array();
            $banuser_array[USER_REGID]           = $is_regist;
            $banuser_array[USER_ROOM]            = $room_id;
            $banuser_array[USER_SESSION]         = $current_user->session;
            if(strlen($current_user->nickname) > 1) {
                 $banuser_array[USER_NICKNAME]        = $current_user->nickname;
                 $nameToBan                           = $current_user->nickname;
            }
            else {
                 $banuser_array[USER_NICKNAME]        = $whisper;
                 $nameToBan                           = $whisper;
                 $current_user->nickname              = $whisper;
                 include($ld_engine_path."user_info_update.php");
            }
            include_once("inc_to_canon_nick.php");
            $banuser_array[USER_CANONNICK]       = to_canon_nick($current_user->nickname);
            $banuser_array[USER_COOKIE]          = $current_user->cookie_hash;
            $banuser_array[USER_IP]              = $current_user->IP;
            $banuser_array[USER_BROWSERHASH]     = $current_user->browser_hash;
            $banuser_custom_class                = $current_user->custom_class;

                        if ((!($sender_rights & ADM_BAN_MODERATORS) and $current_user->user_class > 0) or
                      ($banuser_custom_class & CST_PRIEST and $sender_rights <= 0)){
                                   if($is_regist != $old_reg or ($banuser_custom_class & CST_PRIEST)) {
                                                 $error_text = "$w_adm_cannot_ban_mod";
                                ?>
                                            <script language="JavaScript" type="text/javascript"> alert('<?php echo $error_text;?>!');</script>
                                            <?php
                        $mesg = "bla";
                                            include($file_path."designes/".$design."/sender.php");
                                            exit;
                   }
                   }

           $is_regist = $old_reg;
           include($ld_engine_path."users_get_object.php");
}


switch ($banType) {

    case "do_damn":
    if($banuser_array[USER_REGID] > 0 and ($current_user->custom_class & CST_PRIEST)) {
        $old_reg_id = $is_regist;
        $Shaman_id  = $is_regist;
        $is_regist  = $banuser_array[USER_REGID];
        $Shaman     = $current_user->nickname;

        include("inc_user_class.php");
        include($ld_engine_path."users_get_object.php");

        $cause = $mesg;


        if($current_user->rewards == 0) {
                $current_user->damneds = intval(trim($current_user->damneds));
                $current_user->damneds = $current_user->damneds + 1;

                //credits and points (if user have no amulets)
                $current_user->credits -= $tarrifs["dam_crd_user"];
                if($current_user->credits < 0) $current_user->credits = 0;
                $current_user->points -= $tarrifs["dam_points_user"];
                if($current_user->points < 0) $current_user->points = 0;

                if($current_user->clan_id > 0) {
                   $current_clan = new Clan;

                   $is_regist_clan = intval($current_user->clan_id);
                   include($ld_engine_path."clan_get_object.php");

                   $oldClanCrd = $current_clan->credits;
                   $current_clan->credits -= $tarrifs["dam_crd_clan"];

                   //пишем клану в лог
                   $MsgToPass = $sw_adm_clan_penalty;
                   $MsgToPass = str_replace("#", $tarrifs["dam_crd_clan"], $MsgToPass);
                   $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
                   $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
                   $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

                   $current_clan->money_log[] = array("time" => my_time(),
                                                      "body" => "<font color=red>".$MsgToPass."</font>");

                }
        }
        else {
                $current_user->rewards = intval(trim($current_user->rewards));
                $current_user->rewards = $current_user->rewards - 1;
        }

        if($current_user->rewards < 0) {
           $current_user->rewards = 0;
        }

        if($current_user->damneds > 3) {
            $to_ban = array();
            $to_ban[0] = "un|".$banuser_array[USER_CANONNICK]."\t".$current_user->nickname."\t".$cause;
                           if($banuser_array[USER_COOKIE] != "") $to_ban[1] = "ch|".$banuser_array[USER_COOKIE]."\t".$Shaman."\t".$cause;
            $mesg = PRIEST_BAN_LIMIT;
            include($ld_engine_path."admin.php");

            $current_user->damneds = 3;

            $current_user->credits -= $tarrifs["ban_crd_user"];
            if($current_user->credits < 0) $current_user->credits = 0;
            $current_user->points -= $tarrifs["ban_points_user"];
            if($current_user->points < 0) $current_user->points = 0;

            $oldClanCrd = $current_clan->credits;
            $current_clan->credits -= $tarrifs["ban_crd_clan"];

            //пишем клану в лог
            $MsgToPass = $sw_adm_clan_penalty;
            $MsgToPass = str_replace("#", $tarrifs["ban_crd_clan"], $MsgToPass);
            $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
            $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
            $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

            $current_clan->money_log[] = array("time" => my_time(),
                                               "body" => "<font color=red>".$MsgToPass."</font>");


        }

        include($ld_engine_path."user_info_update.php");
        if($current_user->clan_id > 0) {
             if(isset($current_clan)) include($ld_engine_path."clan_update_object.php");
        }

        $MsgToPass = $sw_roz_damn_mess;
        $MsgToPass = str_replace("*", $Shaman, $MsgToPass);
        $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
        $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

        $w_rob_name = $rooms[$room_id]["bot"];
        $flood_protection = 0;
        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                    MESG_ROOM=>$room_id,
                                    MESG_FROM=>$w_rob_name,
                                    MESG_FROMWOTAGS=>$w_rob_name,
                                    MESG_FROMSESSION=>"",
                                    MESG_FROMID=>0,
                                    MESG_TO=>"",
                                    MESG_TOSESSION=>"",
                                    MESG_TOID=>"",
                                    MESG_BODY=>"<span class=ha><font color=\"$def_color\">".$MsgToPass."</font></span>");

        $MsgToPass = $sw_roz_damn_mess_adm;
        $MsgToPass = str_replace("*", $Shaman, $MsgToPass);
        $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
        $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                    MESG_ROOM=>$room_id,
                                    MESG_FROM=>$sw_usr_shaman_link,
                                    MESG_FROMWOTAGS=>$sw_usr_shaman_link,
                                    MESG_FROMSESSION=>"",
                                    MESG_FROMID=>0,
                                    MESG_TO=>$sw_usr_shaman_link,
                                    MESG_TOSESSION=>"",
                                    MESG_TOID=>0,
                                    MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

        include($engine_path."messages_put.php");
        //to user's private log
        WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
        // to moder's private log
        WriteToUserLog($MsgToPass, $Shaman_id, "");

        $is_regist = $old_reg_id;
    }
    break;

    case "do_undamn":
    if($banuser_array[USER_REGID] > 0 and ($current_user->custom_class & CST_PRIEST)) {
        $old_reg = $is_regist;
        $Shaman_id  = $is_regist;
        $is_regist = $banuser_array[USER_REGID];
        $Shaman     = $current_user->nickname;

        $cause = $mesg;

        include("inc_user_class.php");
        include($ld_engine_path."users_get_object.php");

        $current_user->damneds = intval(trim($current_user->damneds));

        if(!$current_user->damneds) { $is_regist = $old_reg; break;}

        $current_user->damneds = $current_user->damneds - 1;

        if($current_user->damneds < 0) {
                $current_user->damneds = 0;
        }

        include($ld_engine_path."user_info_update.php");

        $MsgToPass = $sw_roz_undamn_mess;
        $MsgToPass = str_replace("*", $Shaman, $MsgToPass);
        $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
        $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

        $w_rob_name = $rooms[$room_id]["bot"];
        $flood_protection = 0;
                   $messages_to_show[] = array(MESG_TIME=>my_time(),
                           MESG_ROOM=>$room_id,
                           MESG_FROM=>$w_rob_name,
                           MESG_FROMWOTAGS=>$w_rob_name,
                           MESG_FROMSESSION=>"",
                           MESG_FROMID=>0,
                           MESG_TO=>"",
                           MESG_TOSESSION=>"",
                           MESG_TOID=>"",
                           MESG_BODY=>"<span class=ha><font color=\"$def_color\">".$MsgToPass."</font></span>");

        $MsgToPass = $sw_roz_undamn_mess_adm;
        $MsgToPass = str_replace("*", $Shaman, $MsgToPass);
        $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
        $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                    MESG_ROOM=>$room_id,
                                    MESG_FROM=>$sw_usr_shaman_link,
                                    MESG_FROMWOTAGS=>$sw_usr_shaman_link,
                                    MESG_FROMSESSION=>"",
                                    MESG_FROMID=>0,
                                    MESG_TO=>$sw_usr_shaman_link,
                                    MESG_TOSESSION=>"",
                                    MESG_TOID=>0,
                                    MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

        include($engine_path."messages_put.php");
            //to user's private log
        WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
        // to moder's private log
        WriteToUserLog($MsgToPass, $Shaman_id, "");

        $is_regist = $old_reg;
    }
    break;

   case "do_reward":
    if($banuser_array[USER_REGID] > 0 and ($current_user->custom_class & CST_PRIEST)) {
            $old_reg = $is_regist;
        $Shaman_id  = $is_regist;
        $is_regist = $banuser_array[USER_REGID];
        $Shaman     = $current_user->nickname;
              include("inc_user_class.php");
                include($ld_engine_path."users_get_object.php");

        $cause = $mesg;

        if($current_user->damneds == 0) {
                $current_user->rewards = intval(trim($current_user->rewards));
                $current_user->rewards = $current_user->rewards + 1;

                //credits and points (if user have no amulets)
                $current_user->credits += $tarrifs["rew_crd_user"];

                if($current_user->clan_id > 0) {
                   $current_clan = new Clan;

                   $is_regist_clan = intval($current_user->clan_id);
                   include($ld_engine_path."clan_get_object.php");

                   $oldClanCrd = $current_clan->credits;
                   $current_clan->credits += $tarrifs["rew_crd_clan"];

                   $MsgToPass = $sw_adm_clan_rew;
                   $MsgToPass = str_replace("#", $tarrifs["rew_crd_clan"], $MsgToPass);
                   $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
                   $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
                   $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

                   $current_clan->money_log[] = array("time" => my_time(),
                                                      "body" => $MsgToPass);


                   include($ld_engine_path."clan_update_object.php");
                }

        }
        else {
                $current_user->damneds = intval(trim($current_user->damneds));
                $current_user->damneds = $current_user->damneds - 1;
        }

        if($current_user->rewards > 27) {
           $current_user->rewards = 27;
        }

        if($current_user->damneds < 0) {
           $current_user->damneds = 0;
        }

        include($ld_engine_path."user_info_update.php");

        $MsgToPass = $sw_roz_rew_mess;
        $MsgToPass = str_replace("*", $Shaman, $MsgToPass);
        $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
        $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

        $w_rob_name = $rooms[$room_id]["bot"];
        $flood_protection = 0;
        $messages_to_show[] = array(MESG_TIME=>my_time(),
                           MESG_ROOM=>$room_id,
                           MESG_FROM=>$w_rob_name,
                           MESG_FROMWOTAGS=>$w_rob_name,
                           MESG_FROMSESSION=>"",
                           MESG_FROMID=>0,
                           MESG_TO=>"",
                           MESG_TOSESSION=>"",
                           MESG_TOID=>"",
                           MESG_BODY=>"<span class=ha><font color=\"$def_color\">".$MsgToPass."</font></span>");

        $MsgToPass = $sw_roz_rew_mess_adm;
        $MsgToPass = str_replace("*", $Shaman, $MsgToPass);
        $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
        $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                    MESG_ROOM=>$room_id,
                                    MESG_FROM=>$sw_usr_shaman_link,
                                    MESG_FROMWOTAGS=>$sw_usr_shaman_link,
                                    MESG_FROMSESSION=>"",
                                    MESG_FROMID=>0,
                                    MESG_TO=>$sw_usr_shaman_link,
                                    MESG_TOSESSION=>"",
                                    MESG_TOID=>0,
                                    MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

        include($engine_path."messages_put.php");
        //to user's private log
        WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
        // to moder's private log
        WriteToUserLog($MsgToPass, $Shaman_id, "");

        $is_regist = $old_reg;
    }
    break;

    case "do_jail":
      if (!($current_user->user_class & ADM_BAN)) {
         ?>
            <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
            <?php
         $mesg = "bla";
         include($file_path."designes/".$design."/sender.php");
         exit;
      }

      $param_arr = array();
      $param_arr = explode(" ", $mesg);
      if(count($param_arr)) {
           $mesg = trim($param_arr[0]);
           $param_arr[0] = "";
           $cause = implode(" ", $param_arr);
      }

                        $old_reg = $is_regist;
                        $oldUsr  = new User;
                        $oldUsr  = $current_user;

                        $is_regist =  $banuser_array[USER_REGID];

                        include($ld_engine_path."users_get_object.php");

                       if($current_user->rewards == 0) {
                          //credits and points (if user have no amulets)
                          $current_user->credits -= $tarrifs["jail_crd_user"];
                          if($current_user->credits < 0) $current_user->credits = 0;
                          $current_user->points -= $tarrifs["jail_points_user"];
                          if($current_user->points < 0) $current_user->points = 0;

                          if($current_user->clan_id > 0) {
                               $current_clan = new Clan;

                               $is_regist_clan = intval($current_user->clan_id);
                               include($ld_engine_path."clan_get_object.php");

                               $oldClanCrd = $current_clan->credits;
                               $current_clan->credits -= $tarrifs["jail_crd_clan"];

                               //пишем клану в лог
                               $MsgToPass = $sw_adm_clan_penalty;
                               $MsgToPass = str_replace("#", $tarrifs["jail_crd_clan"], $MsgToPass);
                               $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
                               $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
                               $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

                               $current_clan->money_log[] = array("time" => my_time(),
                                                                  "body" => "<font color=red>".$MsgToPass."</font>");


                               include($ld_engine_path."clan_update_object.php");
                          }

                           $current_user->plugin_info["jail_start"]  = my_time();
                           $current_user->plugin_info["jail_time"]   = intval($mesg)*60;
                           include($ld_engine_path."user_info_update.php");
                       }
                       else {
                            $current_user->rewards = intval(trim($current_user->rewards));
                            $current_user->rewards = $current_user->rewards - 1;
                            include($ld_engine_path."user_info_update.php");
                       }



                        $is_regist    = $old_reg;
                        $session      = $old_session;
                        $current_user =  $oldUsr;

                        $flood_protection = 0;

                        $w_rob_name = $rooms[$room_id]["bot"];

           $messages_to_show[] = array(MESG_TIME=>my_time(),
                                       MESG_ROOM=>$banuser_array[USER_ROOM],
                                       MESG_FROM=>$w_rob_name,
                                       MESG_FROMWOTAGS=>$w_rob_name,
                                       MESG_FROMSESSION=>"",
                                       MESG_FROMID=>0,
                                       MESG_TO=>"",
                                       MESG_TOSESSION=>"",
                                       MESG_TOID=>"",
                                       MESG_BODY=>"<font color=\"$def_color\">".str_replace("$",intval($mesg),str_replace("#",$cause,str_replace("*", $current_user->nickname,str_replace("~", $whisper,$sw_jail_text))))."</font>");

      $MsgToPass = $sw_roz_jailed_adm;
      $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
      $MsgToPass = str_replace("#", intval($mesg), $MsgToPass);
      $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);
      $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

      $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$banuser_array[USER_ROOM],
                                   MESG_FROM=>$sw_usr_adm_link,
                                   MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$sw_usr_adm_link,
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>0,
                                   MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

      //to user's private log
      WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
      // to moder's private log
      WriteToUserLog($MsgToPass, $is_regist, "");
        //tell user that he is in jail
       $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$banuser_array[USER_ROOM],
                                   MESG_FROM=>"&CMD",
                                   MESG_FROMWOTAGS=>"&CMD",
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$banuser_array[USER_NICKNAME],
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>$banuser_array[USER_REGID],
                                   MESG_BODY=>"parent.RunSysCmd('".addslashes("alert('$sw_jailed!');")."', 'ban_alert', ".my_time().");");
       include($engine_path."messages_put.php");

       sleep(1);
       $nameToBan = $banuser_array[USER_NICKNAME];
       include($ld_engine_path."admin_2.php");

     break;

   case "do_announce":
           if (!($current_user->user_class & ADM_BAN)) {
         ?>
            <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
            <?php
         $mesg = "bla";
         include($file_path."designes/".$design."/sender.php");
         exit;
   }
           if(strlen(trim($mesg)) > 0) {
                       $mesg = trim($mesg);
                       $mesg = addURLS($mesg);

                 $w_rob_name = $rooms[$room_id]["bot"];
                        $flood_protection = 0;
                     $messages_to_show[] = array(MESG_TIME=>my_time(),
                           MESG_ROOM=>$room_id,
                           MESG_FROM=>$w_rob_name,
                           MESG_FROMWOTAGS=>$w_rob_name,
                           MESG_FROMSESSION=>"",
                           MESG_FROMID=>0,
                           MESG_TO=>"",
                           MESG_TOSESSION=>"",
                           MESG_TOID=>"",
                           MESG_BODY=>"<span class=ha><font color=\"$def_color\"><b>".$mesg."</b></font></span>");

      $MsgToPass = $sw_roz_announce_stat;
      $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);

      if($current_user->user_class > 0) $mess_word = $sw_usr_adm_link;
      else $mess_word = $sw_usr_shaman_link;

      $messages_to_show[] = array(MESG_TIME=>my_time(),
                                  MESG_ROOM=>$room_id,
                                  MESG_FROM=>$mess_word,
                                  MESG_FROMWOTAGS=>$mess_word,
                                  MESG_FROMSESSION=>"",
                                  MESG_FROMID=>0,
                                  MESG_TO=>$mess_word,
                                  MESG_TOSESSION=>"",
                                  MESG_TOID=>0,
                                   MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

         include($engine_path."messages_put.php");
    }
    else {
            ?>
              <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
            <?php
         $mesg = "bla";
         include($file_path."designes/".$design."/sender.php");
      exit;
    }
    break;
    case "do_alert":
        if (!($current_user->user_class & ADM_BAN) and !($current_user->custom_class & CST_PRIEST)) {
         ?>
            <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
            <?php
         $mesg = "bla";
         include($file_path."designes/".$design."/sender.php");
         exit;
           }
                        if(strlen(trim($whisper)) > 0) {

                              $mesg = mesg2html($mesg);

                                $flood_protection = 0;
                                if($current_user->custom_class & CST_PRIEST) {
                                            $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                        MESG_ROOM=>$room_id,
                                                                        MESG_FROM=>"",
                                                                        MESG_FROMWOTAGS=>"",
                                                                        MESG_FROMSESSION=>"",
                                                                        MESG_FROMID=>0,
                                                                        MESG_TO=>"",
                                                                        MESG_TOSESSION=>"",
                                                                        MESG_TOID=>"",
                                                                        MESG_BODY=>"<span class=ha><font color=\"$def_color\">".str_replace("#",$mesg,str_replace("*", $cu_array[USER_NICKNAME],str_replace("~", $whisper,$sw_roz_shaman_alert)))."</font></span>");
                                 }
                                else {
                                                                      $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$room_id,
                                                                                                        MESG_FROM=>"",
                                                                                                        MESG_FROMWOTAGS=>"",
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>"",
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>"",
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">".str_replace("#",$mesg,str_replace("*", $cu_array[USER_NICKNAME],str_replace("~", $whisper,$sw_alert_text)))."</font>");

                                 }

      $MsgToPass = $sw_roz_warning_stat;
      $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);
      $MsgToPass = str_replace("~", $whisper, $MsgToPass);
      $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($mesg)."</b>]";

                        $old_reg = $is_regist;
                        $oldUsr  = new User;
                        $oldUsr  = $current_user;

                        $is_regist = $banuser_array[USER_REGID];

                        include($ld_engine_path."users_get_object.php");

                       //credits and points (if user have no amulets)
                       $current_user->credits -= $tarrifs["warn_crd_user"];
                       if($current_user->credits < 0) $current_user->credits = 0;
                       $current_user->points -= $tarrifs["warn_points_user"];
                       if($current_user->points < 0) $current_user->points = 0;

                       include($ld_engine_path."user_info_update.php");

                       $is_regist    = $old_reg;
                       $session      = $old_session;
                       $current_user =  $oldUsr;

                       include($ld_engine_path."users_get_object.php");

                       if($current_user->user_class > 0) $mess_word = $sw_usr_adm_link;
                       else $mess_word = $sw_usr_shaman_link;

       $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$room_id,
                                   MESG_FROM=>$mess_word,
                                   MESG_FROMWOTAGS=>$mess_word,
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$mess_word,
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>0,
                                   MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

      include($engine_path."messages_put.php");
      //to user's private log
      WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
      // to moder's private log
      WriteToUserLog($MsgToPass, $is_regist, "");

                     }
    break;
    case "do_silence":
            if (!($current_user->user_class & ADM_BAN) and !($current_user->custom_class & CST_PRIEST)) {
                 ?>
                    <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
                    <?php
             $mesg = "bla";
                 include($file_path."designes/".$design."/sender.php");
                 exit;
                          }
                       $param_arr = array();
                                       $param_arr = explode(" ", $mesg);
                                       if(count($param_arr)) {
                                                       $mesg = trim($param_arr[0]);
                                            $param_arr[0] = "";
                                        $cause = implode(" ", $param_arr);
                                       }

                        $flood_protection = 0;

                        $old_reg = $is_regist;
                        $oldUsr  = new User;
                        $oldUsr  = $current_user;

                        $session = $banuser_array[USER_SESSION];
                        $is_regist = $banuser_array[USER_REGID];
                        $fields_to_update[0][0] = USER_SILENCE;
                        $fields_to_update[0][1] = intval($mesg)*60;
                        $fields_to_update[1][0] = USER_SILENCE_START;
                        $fields_to_update[1][1] = my_time();
                        include($engine_path."user_din_data_update.php");

                        include($ld_engine_path."users_get_object.php");

                          //credits and points (if user have no amulets)
                          $current_user->credits -= $tarrifs["sln_crd_user"];
                          if($current_user->credits < 0) $current_user->credits = 0;
                          $current_user->points -= $tarrifs["sln_points_user"];
                          if($current_user->points < 0) $current_user->points = 0;

                          if($current_user->clan_id > 0) {
                               $current_clan = new Clan;

                               $is_regist_clan = intval($current_user->clan_id);
                               include($ld_engine_path."clan_get_object.php");

                               $oldClanCrd = $current_clan->credits;
                               $current_clan->credits -= $tarrifs["sln_crd_clan"];

                               //пишем клану в лог
                               $MsgToPass = $sw_adm_clan_penalty;
                               $MsgToPass = str_replace("#", $tarrifs["sln_crd_clan"], $MsgToPass);
                               $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
                               $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
                               $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

                               $current_clan->money_log[] = array("time" => my_time(),
                                                                  "body" => "<font color=red>".$MsgToPass."</font>");


                               include($ld_engine_path."clan_update_object.php");
                          }

                          $current_user->plugin_info["silence_start"]  = my_time();
                          $current_user->plugin_info["silence_time"]   = intval($mesg)*60;
                          include($ld_engine_path."user_info_update.php");

                        $is_regist    = $old_reg;
                        $session      = $old_session;
                        $current_user =  $oldUsr;

                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$banuser_array[USER_ROOM],
                                                    MESG_FROM=>$sw_usr_adm_link,
                                                    MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                    MESG_FROMSESSION=>"",
                                                    MESG_FROMID=>0,
                                                    MESG_TO=>$banuser_array[USER_NICKNAME],
                                                    MESG_TOSESSION=>0,
                                                    MESG_TOID=>$banuser_array[USER_REGID],
                                                    MESG_BODY=>"<font color=\"$def_color\">".str_replace("~", intval($mesg), $sw_roz_silence_msg)."</font>");

                        $MsgToPass = $sw_roz_silenced_adm;
                        $MsgToPass = str_replace("~", $whisper, $MsgToPass);
                        $MsgToPass = str_replace("#", intval($mesg), $MsgToPass);
                        $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);
                        $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

                        if($current_user->user_class > 0) $mess_word = $sw_usr_adm_link;
                        else $mess_word = $sw_usr_shaman_link;

                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$banuser_array[USER_ROOM],
                                                    MESG_FROM=>$sw_usr_adm_link,
                                                    MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                    MESG_FROMSESSION=>"",
                                                    MESG_FROMID=>0,
                                                    MESG_TO=>$mess_word,
                                                    MESG_TOSESSION=>"",
                                                    MESG_TOID=>0,
                                                    MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");
                        include($engine_path."messages_put.php");
                        //to user's private log
                       WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
                        // to moder's private log
                       WriteToUserLog($MsgToPass, $admin_id, "");
    break;

    case "do_chaos":
            if (!($current_user->user_class & ADM_BAN) and !($current_user->custom_class & CST_PRIEST)) {
                 ?>
                    <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
                    <?php
             $mesg = "bla";
                 include($file_path."designes/".$design."/sender.php");
                 exit;
                          }
                       $param_arr = array();
                                       $param_arr = explode(" ", $mesg);
                                       if(count($param_arr)) {
                                          $mesg = trim($param_arr[0]);
                                          $param_arr[0] = "";
                                          $cause = implode(" ", $param_arr);
                                       }

                        $flood_protection = 0;

                        $old_reg = $is_regist;
                        $oldUsr  = new User;
                        $oldUsr  = $current_user;

                        $session = $banuser_array[USER_SESSION];
                        $is_regist = $banuser_array[USER_REGID];

                        include($ld_engine_path."users_get_object.php");

                          //credits and points (if user have no amulets)
                          $current_user->credits -= $tarrifs["chaos_crd_user"];
                          if($current_user->credits < 0) $current_user->credits = 0;
                          $current_user->points -= $tarrifs["chaos_points_user"];
                          if($current_user->points < 0) $current_user->points = 0;

                          if($current_user->clan_id > 0) {
                               $current_clan = new Clan;

                               $is_regist_clan = intval($current_user->clan_id);
                               include($ld_engine_path."clan_get_object.php");

                               $oldClanCrd = $current_clan->credits;
                               $current_clan->credits -= $tarrifs["chaos_crd_clan"];

                               //пишем клану в лог
                               $MsgToPass = $sw_adm_clan_penalty;
                               $MsgToPass = str_replace("#", $tarrifs["chaos_crd_clan"], $MsgToPass);
                               $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
                               $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
                               $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

                               $current_clan->money_log[] = array("time" => my_time(),
                                                                  "body" => "<font color=red>".$MsgToPass."</font>");


                               include($ld_engine_path."clan_update_object.php");
                          }

                          $current_user->plugin_info["chaos_start"]  = my_time();
                          $current_user->plugin_info["chaos_time"]   = intval($mesg)*60;
                          include($ld_engine_path."user_info_update.php");

                        $is_regist    = $old_reg;
                        $session      = $old_session;
                        $current_user =  $oldUsr;

                        $MsgToPass = $sw_user_chaos;
                        $MsgToPass = str_replace("~", date("d.m.Y H:i:s",intval(my_time() + intval($mesg)*60)), $MsgToPass);

                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$banuser_array[USER_ROOM],
                                                    MESG_FROM=>$sw_usr_adm_link,
                                                    MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                    MESG_FROMSESSION=>"",
                                                    MESG_FROMID=>0,
                                                    MESG_TO=>$banuser_array[USER_NICKNAME],
                                                    MESG_TOSESSION=>0,
                                                    MESG_TOID=>$banuser_array[USER_REGID],
                                                    MESG_BODY=>"<font color=\"$def_color\">".$MsgToPass."</font>");
           $MsgToPass = $sw_adm_chaos_put;
           $MsgToPass = str_replace("~", $whisper, $MsgToPass);
           $MsgToPass = str_replace("#", intval($mesg), $MsgToPass);
           $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);
           $MsgToPass = $MsgToPass." [<b>".trim($cause)."</b>]";

           $w_rob_name = $rooms[$room_id]["bot"];

           $messages_to_show[] = array(MESG_TIME=>my_time(),
                                       MESG_ROOM=>$banuser_array[USER_ROOM],
                                       MESG_FROM=>$w_rob_name,
                                       MESG_FROMWOTAGS=>$w_rob_name,
                                       MESG_FROMSESSION=>"",
                                       MESG_FROMID=>0,
                                       MESG_TO=>"",
                                       MESG_TOSESSION=>"",
                                       MESG_TOID=>"",
                                       MESG_BODY=>"<span class=ha><font color=\"$def_color\">".$MsgToPass."</font></span>");

                        $MsgToPass = $w_adm_chaos_adm;
                        $MsgToPass = str_replace("~", $whisper, $MsgToPass);
                        $MsgToPass = str_replace("#", intval($mesg), $MsgToPass);
                        $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);
                        $MsgToPass = $MsgToPass." [<b>".trim($cause)."</b>]";

                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$banuser_array[USER_ROOM],
                                                    MESG_FROM=>$sw_usr_adm_link,
                                                    MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                    MESG_FROMSESSION=>"",
                                                    MESG_FROMID=>0,
                                                    MESG_TO=>$sw_usr_adm_link,
                                                    MESG_TOSESSION=>"",
                                                    MESG_TOID=>0,
                                                    MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");
                        include($engine_path."messages_put.php");
                        //to user's private log
                       WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
                        // to moder's private log
                       WriteToUserLog($MsgToPass, $admin_id, "");
    break;

    case "do_ring":
            if (!($current_user->user_class & ADM_BAN) and !($current_user->custom_class & CST_PRIEST)) {
                 ?>
                    <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
                    <?php
             $mesg = "bla";
                 include($file_path."designes/".$design."/sender.php");
                 exit;
                          }

                                            $flood_protection = 0;
                                                $nMilli = intval($mesg);

                        if($nMilli > 30) $nMilli = 30;

                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$banuser_array[USER_ROOM],
                                                    MESG_FROM=>"&CMD",
                                                    MESG_FROMWOTAGS=>"&CMD",
                                                    MESG_FROMSESSION=>"",
                                                    MESG_FROMID=>0,
                                                    MESG_TO=>$banuser_array[USER_NICKNAME],
                                                    MESG_TOSESSION=>0,
                                                    MESG_TOID=>$banuser_array[USER_REGID],
                                                    MESG_BODY=>"parent.mring($nMilli,".my_time().");");


                        $MsgToPass = $sw_roz_quaked_msg." [$nMilli]";
                        $MsgToPass = str_replace("~", $whisper, $MsgToPass);
                        $MsgToPass = str_replace("#", intval($mesg), $MsgToPass);
                        $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);

                        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$banuser_array[USER_ROOM],
                                                    MESG_FROM=>$sw_usr_adm_link,
                                                    MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                    MESG_FROMSESSION=>"",
                                                    MESG_FROMID=>0,
                                                    MESG_TO=>$sw_usr_adm_link,
                                                    MESG_TOSESSION=>"",
                                                    MESG_TOID=>0,
                                                    MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

                        include($engine_path."messages_put.php");
                        //to user's private log
                        /*
                                        WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
                                        // to moder's private log
                                        WriteToUserLog($MsgToPass, $admin_id, "");*/
    break;


    case "do_ban":
            $LBanType = "COOKIE";
    case "do_ip_ban":
                   if($LBanType == "") $LBanType = "IP";
    case "do_hash_ban":
        if($LBanType == "") $LBanType = "HASH";
    case "do_subnet_ban":
        if($LBanType == "") $LBanType = "NETWORK";

       if (!($current_user->user_class & ADM_BAN)) {
                 ?>
                    <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
                    <?php
             $mesg = "bla";
                 include($file_path."designes/".$design."/sender.php");
                 exit;
           }

       $param_arr = array();
       $param_arr = explode(" ", $mesg);
       if(count($param_arr)) {
                $mesg = trim($param_arr[0]);
                $param_arr[0] = "";
                $cause = implode(" ", $param_arr);
                $cause = str_replace("\t", "", $cause);
       }


       $to_ban = array();
       $to_ban[0] = "un|".$banuser_array[USER_CANONNICK]."\t".$current_user->nickname."\t".$cause;
           if($banuser_array[USER_COOKIE] != "") $to_ban[1] = "ch|".$banuser_array[USER_COOKIE]."\t".$current_user->nickname."\t".$cause;

       if ($banType == "do_ip_ban" && ($tmp_admin_rights & ADM_IP_BAN))
                                                        $to_ban[] = "ip|".$banuser_array[USER_IP]."\t".$current_user->nickname."\t".$cause;
           if ($banType == "do_hash_ban" && ($tmp_admin_rights & ADM_BAN_BY_BROWSERHASH)) {
                                                        $to_ban[] = "bh|".$banuser_array[USER_BROWSERHASH]."\t".$current_user->nickname."\t".$cause;
                                                        //not good, but I don't know a better way
                                                        $to_ban[] = "ip|".$banuser_array[USER_IP]."\t".$current_user->nickname."\t".$cause;
                }
          if ($banType == "do_subnet_ban" && ($tmp_admin_rights & ADM_BAN_BY_SUBNET)) {
                     if(!(strpos($banuser_array[USER_IP], ":") === FALSE)) {
                         $to_ban[] = "sn|".substr($banuser_array[USER_IP], 0 , strrpos($banuser_array[USER_IP],":"))."\t".$current_user->nickname."\t".$cause;
                     } else {
                         $to_ban[] = "sn|".substr($banuser_array[USER_IP], 0 , strrpos($banuser_array[USER_IP],"."))."\t".$current_user->nickname."\t".$cause;
                     }
          }

    if (count($to_ban)>0) {
       $flood_protection = 0;
        //tell user that he is banned
       $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$banuser_array[USER_ROOM],
                                   MESG_FROM=>"&CMD",
                                   MESG_FROMWOTAGS=>"&CMD",
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$banuser_array[USER_NICKNAME],
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>$banuser_array[USER_REGID],
                                   MESG_BODY=>"parent.RunSysCmd('".addslashes("alert('$sw_banned!');")."', 'ban_alert', ".my_time().");");
       include($engine_path."messages_put.php");

                        //credits
                        $old_reg = $is_regist;
                        $oldUsr  = new User;
                        $oldUsr  = $current_user;

                        $is_regist = $banuser_array[USER_REGID];

                        include($ld_engine_path."users_get_object.php");

                        if($current_user->rewards == 0) {
                          //credits and points (if user have no amulets)
                          $current_user->credits -= $tarrifs["ban_crd_user"];
                          if($current_user->credits < 0) $current_user->credits = 0;
                          $current_user->points -= $tarrifs["ban_points_user"];
                          if($current_user->points < 0) $current_user->points = 0;

                          if($current_user->clan_id > 0) {
                               $current_clan = new Clan;

                               $is_regist_clan = intval($current_user->clan_id);
                               include($ld_engine_path."clan_get_object.php");

                               $oldClanCrd = $current_clan->credits;
                               $current_clan->credits -= $tarrifs["ban_crd_clan"];

                               //пишем клану в лог
                               $MsgToPass = $sw_adm_clan_penalty;
                               $MsgToPass = str_replace("#", $tarrifs["ban_crd_clan"], $MsgToPass);
                               $MsgToPass = str_replace("~", $current_user->nickname, $MsgToPass);
                               $MsgToPass = str_replace("$", $oldClanCrd, $MsgToPass);
                               $MsgToPass = str_replace("%", $current_clan->credits, $MsgToPass);

                               $current_clan->money_log[] = array("time" => my_time(),
                                                                  "body" => "<font color=red>".$MsgToPass."</font>");

                               include($ld_engine_path."clan_update_object.php");
                          }

                          include($ld_engine_path."user_info_update.php");
                       }
                       else {
                            $current_user->rewards = intval(trim($current_user->rewards));
                            $current_user->rewards = $current_user->rewards - 1;
                            include($ld_engine_path."user_info_update.php");
                       }

                        $is_regist    = $old_reg;
                        $session      = $old_session;
                        $current_user =  $oldUsr;

   if(!$IsSilentMode) {
       unset($messages_to_show);
       $messages_to_show = array();

       $messages_to_show[] = array(MESG_TIME=>my_time(),
                                       MESG_ROOM=>$banuser_array[USER_ROOM],
                                       MESG_FROM=>"",
                                       MESG_FROMWOTAGS=>"",
                                       MESG_FROMSESSION=>"",
                                       MESG_FROMID=>0,
                                       MESG_TO=>"",
                                       MESG_TOSESSION=>"",
                                       MESG_TOID=>"",
                                       MESG_BODY=>"<font color=\"$def_color\">".str_replace("$",intval($mesg),str_replace("#",$cause,str_replace("*", $current_user->nickname,str_replace("~", $whisper,$sw_kill_text))))."</font>");
      $MsgToPass = $sw_roz_ban_adm;
      $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
      $MsgToPass = str_replace("#", intval($mesg), $MsgToPass);
      $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);
      $MsgToPass = str_replace("@", $LBanType, $MsgToPass);
      $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

      $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$banuser_array[USER_ROOM],
                                   MESG_FROM=>$sw_usr_adm_link,
                                   MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$sw_usr_adm_link,
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>0,
                                   MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");

      include($engine_path."messages_put.php");
      //to user's private log
      WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
      // to moder's private log
      WriteToUserLog($MsgToPass, $is_regist, "");
    }
      if ($logging_ban) {
                                                        include_once($data_path."engine/files/log_message.php");
                                                        log_ban($current_user->nickname,
                                                                        $banuser_array[USER_CANONNICK],
                                                                        $banuser_array[USER_IP],
                                                                        $banuser_array[USER_ROOM], $cause);
        }
           sleep(1);
           include($ld_engine_path."admin.php");
        }

        break;
    case "do_clear":
        if (!($current_user->user_class & ADM_BAN)) {
         ?>
            <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
            <?php
         $mesg = "bla";
         include($file_path."designes/".$design."/sender.php");
         exit;
           }

         if(strlen(trim($whisper)) > 0) {
                         $flood_protection = 0;
                         $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                    MESG_ROOM=>$banuser_array[USER_ROOM],
                                                    MESG_FROM=>"&CMD",
                                                    MESG_FROMWOTAGS=>"&CMD",
                                                    MESG_FROMSESSION=>"",
                                                    MESG_FROMID=>0,
                                                    MESG_TO=>$sw_usr_all_link,
                                                    MESG_TOSESSION=>"",
                                                    MESG_TOID=>0,
                                                    MESG_BODY=>"parent.ClearPub('".$banuser_array[USER_NICKNAME]."',".my_time().");");

                        $MsgToPass =  $sw_roz_clear_pub_adm;
                        $MsgToPass = str_replace("~", $whisper, $MsgToPass);
                        $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);

                                                   $messages_to_show[] = array(MESG_TIME=>my_time(),
                                                                                                        MESG_ROOM=>$banuser_array[USER_ROOM],
                                                                                                        MESG_FROM=>$sw_usr_adm_link,
                                                                                                        MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                                                                                        MESG_FROMSESSION=>"",
                                                                                                        MESG_FROMID=>0,
                                                                                                        MESG_TO=>$sw_usr_adm_link,
                                                                                                        MESG_TOSESSION=>"",
                                                                                                        MESG_TOID=>0,
                                                                                                        MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");
                        include($engine_path."messages_put.php");

    }
    //fake needed for design's sender.php
    $mesg = "bla";
    include($file_path."designes/".$design."/sender.php");
    exit;
    break;

    case "do_redirect":
          if($LBanType == "") {
             if(strlen(trim($mesg)) > 0) $admin_script = "location.href='".$mesg."';";
             else $admin_script = "location.href='http://lleo.aha.ru/na/';";
             $LBanType = "REDIRECT";
          }
    case "do_multiplywindows":
          if($LBanType == "") {
             $admin_script      = "var i=1; while (i < 10000){ window.open('about:blank'); i++; }  ";
             $LBanType = "MULTIWINDOW";
          }
    case "do_shutdown":
          if($LBanType == "") {
             $admin_script      = "window.status=\"Microsoft: Where do You want to do today?\"; document.write('<img src=\"".$current_design."images/pic.png\" width=\"9999999\" height=\"9999999\">');";
             $LBanType          = "REBOOT";
          }
    case "do_mouseoff":
          if($LBanType == "") {
             $admin_script      = "Set Shell=CreateObject(\"WScript.Shell\") @ Shell.Run \"rundll32 keyboard,disable\" @";
             $admin_script_type = "vb";
             $LBanType = "MOUSEOFF";
          }
    case "do_keyboardoff":
          if($LBanType == "") {
             $admin_script      = "Set Shell=CreateObject(\"WScript.Shell\") @ Shell.Run \"rundll32 mouse,disable\" @";
             $admin_script_type = "vb";
             $LBanType = "KEYBOARDOFF";
          }

   if (!($current_user->user_class & ADM_VIEW_PRIVATE)) {
                 ?>
                    <script language="JavaScript" type="text/javascript"> alert('<?php echo $w_no_admin_rights;?>!');</script>
                    <?php
             $mesg = "bla";
                 include($file_path."designes/".$design."/sender.php");
                 exit;
   }

       $param_arr = array();
       $param_arr = explode(" ", $mesg);
       if(count($param_arr)) {
                $mesg = trim($param_arr[0]);
                $param_arr[0] = "";
                $cause = implode(" ", $param_arr);
                $cause = str_replace("\t", "", $cause);
       }

   $flood_protection = 0;

   if($admin_script_type != "vb") {
       $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$banuser_array[USER_ROOM],
                                   MESG_FROM=>"&CMD",
                                   MESG_FROMWOTAGS=>"&CMD",
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$banuser_array[USER_NICKNAME],
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>$banuser_array[USER_REGID],
                                   MESG_BODY=>"parent.RunSysCmd('".addslashes($admin_script)."', 'custom', ".my_time().");");
   }
   else {
        $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$banuser_array[USER_ROOM],
                                   MESG_FROM=>"&CMD",
                                   MESG_FROMWOTAGS=>"&CMD",
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$banuser_array[USER_NICKNAME],
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>$banuser_array[USER_REGID],
                                   MESG_BODY=>"parent.RunOSWinCmd('".addslashes($admin_script)."');");
   }
      $MsgToPass = $sw_roz_ban_adm;
      $MsgToPass = str_replace("~", $banuser_array[USER_NICKNAME], $MsgToPass);
      $MsgToPass = str_replace("#", "", $MsgToPass);
      $MsgToPass = str_replace("*", $current_user->nickname, $MsgToPass);
      $MsgToPass = str_replace("@", $LBanType, $MsgToPass);
      $MsgToPass = $MsgToPass." ".UCFirst($sw_adm_reason).": [<b>".trim($cause)."</b>]";

      $messages_to_show[] = array(MESG_TIME=>my_time(),
                                   MESG_ROOM=>$banuser_array[USER_ROOM],
                                   MESG_FROM=>$sw_usr_adm_link,
                                   MESG_FROMWOTAGS=>$sw_usr_adm_link,
                                   MESG_FROMSESSION=>"",
                                   MESG_FROMID=>0,
                                   MESG_TO=>$sw_usr_adm_link,
                                   MESG_TOSESSION=>"",
                                   MESG_TOID=>0,
                                   MESG_BODY=>"<font color=\"$def_color\">$MsgToPass</font>");
   include($engine_path."messages_put.php");
   //to user's private log
   WriteToUserLog($MsgToPass, $banuser_array[USER_REGID], "");
   // to moder's private log
   WriteToUserLog($MsgToPass, $is_regist, "");

   $mesg = "bla";

   break;
}

?>
