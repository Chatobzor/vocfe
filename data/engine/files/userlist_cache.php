<?php
/**********************************************************************
COPYRIGHT (c) 2006 Creatiff Design.  All Rights Reserved
This document and the information it contains is confidential and
proprietary to Creatiff. It may not be used, copied, reproduced,
transmitted, or stored in any form or by any means, electronic,
recording, photocopying, mechanical or otherwise, without the
prior written permission of Creatiff.

Revision History:

Date                        Initials                Description of Changes
-----------------------------------------------------------------------
Jun 12 2006                DareDEVIL                 Added new engine code

cache structure:

-- timestamp
-- array of uids

************************************************************************/
if (!defined("_COMMON_")) {echo "stop";exit;}

set_variable("cache_time");
$cache_time = intval($cache_time);
$curr_time = my_time();
if($curr_time - 24*3600 > $cache_time or $cache_time > $curr_time) $cache_time = 0;

$usrCache = new userlist_Cache;

if(file_exists($data_path."userlist.tmp")) {
   $usrCache = unserialize(implode("",file($data_path."userlist.tmp")));
   if(!is_object($usrCache)) $usrCache = new userlist_Cache;
}

 if(intval($usrCache->timestamp) < my_time() - 60) {
      //rendering cache
     $cacheTime   = my_time();
     $onlineUsers = array();

     for($i=0;$i<count($users);$i++) {
        $user_array = explode("\t",$users[$i]);

        $user_array[USER_INVISIBLE]    = intval(trim($user_array[USER_INVISIBLE]));
        $user_array[USER_HTMLNICK]     = (strlen($user_array[USER_HTMLNICK])==0)?$user_array[USER_NICKNAME]:$user_array[USER_HTMLNICK];
        if($user_array[USER_GENDER] != 1 and $user_array[USER_GENDER] != 2) $user_array[USER_GENDER] = 3;

        $uid = intval($user_array[USER_REGID]);
        $is_regist = intval($user_array[USER_REGID]);

        if(file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
            include($ld_engine_path."users_get_object.php");
        }
        else continue;

        $onlineUsers[] = $uid;

        if($current_user->clan_id > 0) {
          if(is_file($file_path."clans-avatar/".floor($current_user->clan_id/2000)."/".$current_user->clan_id.".gif")) {
               $clan_avatar = $images_url."clans-avatar/".floor($current_user->clan_id/2000)."/".$current_user->clan_id.".gif";
           }
        }
        else $clan_avatar = "";

       $pic_name = "" . floor($user_array[USER_REGID]/2000) . "/" . $user_array[USER_REGID] . ".big.gif";
       if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

       if ($pic_name == ""){
          $pic_name = "" . floor($user_array[USER_REGID]/2000) . "/" . $user_array[USER_REGID] . ".big.jpg";
          if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

          if($pic_name == ""){
             $pic_name = "" . floor($user_array[USER_REGID]/2000) . "/" . $user_array[USER_REGID] . ".big.jpeg";
             if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
          }
       }
       if($pic_name != "") $is_photo = 1;
       else $is_photo = 0;

        if(intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"]) > my_time()) {
            $is_chaos = 1;
        }
        else $is_chaos = 0;

        if(intval($current_user->plugin_info["silence_start"]) + intval($current_user->plugin_info["silence_time"]) > my_time()) {
            $is_silence = 1;
        }
        else $is_silence = 0;

      if(array_key_exists(intval($user_array[USER_REGID]), $usrCache->u_cache)) {
           if($usrCache->u_cache[$uid]["user_id"]         != $user_array[USER_REGID] or
              $usrCache->u_cache[$uid]["clan_id"]         != $user_array[USER_CLANID] or
              $usrCache->u_cache[$uid]["room"]            != $user_array[USER_ROOM] or
              $usrCache->u_cache[$uid]["session"]         != $user_array[USER_SESSION] or
              $usrCache->u_cache[$uid]["user_class"]      != intval($user_array[USER_CLASS]) or
              $usrCache->u_cache[$uid]["custom_class"]    != intval($user_array[USER_CUSTOMCLASS]) or
              $usrCache->u_cache[$uid]["nickname"]        != $user_array[USER_NICKNAME] or
              $usrCache->u_cache[$uid]["enc"]             != urlencode(trim($user_array[USER_NICKNAME])) or
              $usrCache->u_cache[$uid]["htmlnick"]        != $user_array[USER_HTMLNICK] or
              $usrCache->u_cache[$uid]["sex"]             != intval($user_array[USER_GENDER]) or
              $usrCache->u_cache[$uid]["small_photo"]     != $user_array[USER_AVATAR] or
              $usrCache->u_cache[$uid]["status"]          != $user_array[USER_STATUS] or
              $usrCache->u_cache[$uid]["show_for_moders"] != $current_user->show_for_moders or
              $usrCache->u_cache[$uid]["show_admin"]      != $current_user->show_admin or
              $usrCache->u_cache[$uid]["clan_avatar"]     != $clan_avatar or
              $usrCache->u_cache[$uid]["marr"]            != $current_user->married_with or
              $usrCache->u_cache[$uid]["photo"]           != $is_photo or
              $usrCache->u_cache[$uid]["damneds"]         != intval(trim($current_user->damneds)) or
              $usrCache->u_cache[$uid]["rewards"]         != intval(trim($current_user->rewards)) or
              $usrCache->u_cache[$uid]["is_member"]       != intval($current_user->is_member) or
              $usrCache->u_cache[$uid]["is_dealer"]       != intval($current_user->is_dealer) or
              $usrCache->u_cache[$uid]["inv"]             !=  $user_array[USER_INVISIBLE] or
              $usrCache->u_cache[$uid]["chaos"]           != $is_chaos or
              $usrCache->u_cache[$uid]["silence"]         != $is_silence or
              $usrCache->u_cache[$uid]["webcam"]          != intval($current_user->allow_webcam)) {
                    $usrCache->u_cache[$uid]["user_id"]         = $user_array[USER_REGID];
                    $usrCache->u_cache[$uid]["clan_id"]         = $user_array[USER_CLANID];
                    $usrCache->u_cache[$uid]["room"]            = $user_array[USER_ROOM];
                    $usrCache->u_cache[$uid]["session"]         = $user_array[USER_SESSION];
                    $usrCache->u_cache[$uid]["user_class"]      = intval($user_array[USER_CLASS]);
                    $usrCache->u_cache[$uid]["custom_class"]    = intval($user_array[USER_CUSTOMCLASS]);
                    $usrCache->u_cache[$uid]["nickname"]        = $user_array[USER_NICKNAME];
                    $usrCache->u_cache[$uid]["enc"]             = urlencode(trim($user_array[USER_NICKNAME]));
                    $usrCache->u_cache[$uid]["htmlnick"]        = $user_array[USER_HTMLNICK];
                    $usrCache->u_cache[$uid]["sex"]             = intval($user_array[USER_GENDER]);
                    $usrCache->u_cache[$uid]["small_photo"]     = $user_array[USER_AVATAR];
                    $usrCache->u_cache[$uid]["status"]          = $user_array[USER_STATUS];
                    $usrCache->u_cache[$uid]["show_for_moders"] = $current_user->show_for_moders;
                    $usrCache->u_cache[$uid]["show_admin"]      = $current_user->show_admin;
                    $usrCache->u_cache[$uid]["clan_avatar"]     = $clan_avatar;
                    $usrCache->u_cache[$uid]["marr"]            = $current_user->married_with;
                    $usrCache->u_cache[$uid]["photo"]           = $is_photo;
                    $usrCache->u_cache[$uid]["damneds"]         = intval(trim($current_user->damneds));
                    $usrCache->u_cache[$uid]["rewards"]         = intval(trim($current_user->rewards));
                    $usrCache->u_cache[$uid]["is_member"]       = intval($current_user->is_member);
                    $usrCache->u_cache[$uid]["is_dealer"]       = intval($current_user->is_dealer);
                    $usrCache->u_cache[$uid]["inv"]             =  $user_array[USER_INVISIBLE];
                    $usrCache->u_cache[$uid]["chaos"]           = $is_chaos;
                    $usrCache->u_cache[$uid]["silence"]         = $is_silence;
                    $usrCache->u_cache[$uid]["timestamp"]       = $cacheTime;
                    $usrCache->u_cache[$uid]["webcam"]          = intval($current_user->allow_webcam);
              }
         } else {
                    $usrCache->u_cache[$uid]["user_id"]         = $user_array[USER_REGID];
                    $usrCache->u_cache[$uid]["clan_id"]         = $user_array[USER_CLANID];
                    $usrCache->u_cache[$uid]["room"]            = $user_array[USER_ROOM];
                    $usrCache->u_cache[$uid]["session"]         = $user_array[USER_SESSION];
                    $usrCache->u_cache[$uid]["custom_class"]    = intval($user_array[USER_CUSTOMCLASS]);
                    $usrCache->u_cache[$uid]["user_class"]      = intval($user_array[USER_CLASS]);
                    $usrCache->u_cache[$uid]["nickname"]        = $user_array[USER_NICKNAME];
                    $usrCache->u_cache[$uid]["enc"]             = urlencode(trim($user_array[USER_NICKNAME]));
                    $usrCache->u_cache[$uid]["htmlnick"]        = $user_array[USER_HTMLNICK];
                    $usrCache->u_cache[$uid]["sex"]             = intval($user_array[USER_GENDER]);
                    $usrCache->u_cache[$uid]["small_photo"]     = $user_array[USER_AVATAR];
                    $usrCache->u_cache[$uid]["status"]          = $user_array[USER_STATUS];
                    $usrCache->u_cache[$uid]["show_for_moders"] = $current_user->show_for_moders;
                    $usrCache->u_cache[$uid]["show_admin"]      = $current_user->show_admin;
                    $usrCache->u_cache[$uid]["clan_avatar"]     = $clan_avatar;
                    $usrCache->u_cache[$uid]["marr"]            = $current_user->married_with;
                    $usrCache->u_cache[$uid]["photo"]           = $is_photo;
                    $usrCache->u_cache[$uid]["damneds"]         = intval(trim($current_user->damneds));
                    $usrCache->u_cache[$uid]["rewards"]         = intval(trim($current_user->rewards));
                    $usrCache->u_cache[$uid]["is_member"]       = intval($current_user->is_member);
                    $usrCache->u_cache[$uid]["is_dealer"]       = intval($current_user->is_dealer);
                    $usrCache->u_cache[$uid]["inv"]             = $user_array[USER_INVISIBLE];
                    $usrCache->u_cache[$uid]["chaos"]           = $is_chaos;
                    $usrCache->u_cache[$uid]["silence"]         = $is_silence;
                    $usrCache->u_cache[$uid]["webcam"]          = intval($current_user->allow_webcam);
                    $usrCache->u_cache[$uid]["timestamp"]       = $cacheTime;
         }
        //end rendering
        }
        riseEvent(EVENT_RENDER_USERLIST, $user_name, $room_id);
        //removing non-online users from the cache
        $cache_keys = array_keys($usrCache->u_cache);
        $aa         = count($cache_keys);

        for($i = 0; $i < $aa; $i++) {
           if(!in_array($cache_keys[$i], $onlineUsers)) unset($usrCache->u_cache[$cache_keys[$i]]);
        }
        $usrCache->timestamp = my_time();

        //update the cache on disk
        $fp = fopen ($data_path."userlist.tmp", "wb");
        if (!$fp) trigger_error("Could not open ".$data_path."userlist.tmp for writing. Please, check permissions", E_USER_ERROR);
        if (!flock($fp, LOCK_EX))
             trigger_error("Could not LOCK file. Do you use Win 95/98/Me?", E_USER_WARNING);
        fwrite($fp,serialize($usrCache));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    unset($out_users);
    $out_users = array();

    $changed_users = array();

    $tot_users  = count($usrCache->u_cache);
    $cache_keys = array_keys($usrCache->u_cache);

    $who_j = 0;
    $IsInList = false;

    if(!isset($onlineUsers)) {
        $onlineUsers = array();

        for($i=0;$i<count($users);$i++) {
                $user_array = explode("\t",$users[$i]);
                $uid = intval($user_array[USER_REGID]);
                $onlineUsers[] = $uid;
        }
    }

    for($i = 0; $i < $tot_users; $i++) {

         $i_key = $cache_keys[$i];

         if($usrCache->u_cache[$i_key]["inv"] == 1 and !$IsModer) continue;

         if($usrCache->u_cache[$i_key]["inv"] == 1 and $IsModer) {
                if(!$usrCache->u_cache[$i_key]["show_for_moders"] and (intval($usrCache->u_cache[$i_key]["user_class"]) & ADM_BAN_MODERATORS)) {
                   if(!$IsAdmin) continue;
                }
         }

         $rooms[$usrCache->u_cache[$i_key]["room"]]["users"]++;

         if($usrCache->u_cache[$i_key]["session"] == $session) {
                   ?>
                    <script language="JavaScript" type="text/javascript">
                    parent.voc_invis = '<?php echo intval($cache_users[$i]["inv"]); ?>';
                    </script>
                    <?php
        }

        if(intval($usrCache->u_cache[$i_key]["room"]) == intval($room_id) and
           intval($usrCache->u_cache[$i_key]["timestamp"]) >= $cache_time) {
             $changed_users[] = intval($usrCache->u_cache[$i_key]["user_id"]);

             $out_users[$who_j] = $usrCache->u_cache[$i_key];

             if ($usrCache->u_cache[$i_key]["user_class"] &  ADM_BAN_MODERATORS) { $out_users[$who_j]["powers"] = "m"; }
             else $out_users[$who_j]["powers"] = "u";

             if($out_users[$who_j]["powers"] == "m" and $usrCache->u_cache[$i_key]["show_admin"] == 0) {
                 $out_users[$who_j]["htmlnick"] = $out_users[$who_j]["nickname"];
                 $out_users[$who_j]["powers"] = "u";
             }

            if(($usrCache->u_cache[$i_key]["custom_class"] & CST_PRIEST) and (strlen(trim($out_users[$who_j]["htmlnick"])) == 0 or $out_users[$who_j]["htmlnick"] == $out_users[$who_j]["nickname"])) {
                $out_users[$who_j]["htmlnick"] = "<FONT color=Black><b>".$out_users[$who_j]["nickname"]."</b></FONT>";
            }

            if(($usrCache->u_cache[$i_key]["user_class"] > 0) and  strlen(trim($out_users[$who_j]["htmlnick"])) == 0) {
                $out_users[$who_j]["htmlnick"] = "<FONT color=Black><i>".$out_users[$who_j]["nickname"]."</i></FONT>";
             }

             if(intval($out_users[$who_j]["clan_id"]) == intval($cu_array[USER_CLANID]) and intval($out_users[$who_j]["clan_id"]) > 0) {
                 if($out_users[$who_j]["powers"] == "m"){
                    if($out_users[$who_j]["show_admin"] == 0) $out_users[$who_j]["powers"] = "c";
                 }
                 else $out_users[$who_j]["powers"] = "c";
             }
             if(intval($out_users[$who_j]["user_id"]) == intval($cu_array[USER_REGID])) $IsInList = true;
             $who_j++;
        }
    }

    if(!$IsInList and $cache_time == 0) {
       //user logged but not exists in cache
       $user_array = $cu_array;

       $user_array[USER_INVISIBLE] = intval(trim($user_array[USER_INVISIBLE]));
       if($user_array[USER_INVISIBLE] == 1 and !$IsModer) continue;

       $rooms[$user_array[USER_ROOM]]["users"]++;

       $out_users[$who_j]["nickname"] = trim($user_array[USER_NICKNAME]);
       $out_users[$who_j]["enc"] = urlencode(trim($user_array[USER_NICKNAME]));
       $out_users[$who_j]["htmlnick"] = (strlen($user_array[USER_HTMLNICK])==0)?$user_array[USER_NICKNAME]:$user_array[USER_HTMLNICK];
       $out_users[$who_j]["sex"] = intval($user_array[USER_GENDER]);

       if($out_users[$who_j]["sex"] != 1 and $out_users[$who_j]["sex"] != 2) $out_users[$who_j]["sex"] = 3;

       $out_users[$who_j]["small_photo"] = $user_array[USER_AVATAR];
       $out_users[$who_j]["user_id"] = $user_array[USER_REGID];
       $out_users[$who_j]["status"] = $user_array[USER_STATUS];

       $is_regist = $user_array[USER_REGID];

       if(file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")){
          include($ld_engine_path."users_get_object.php");
       }
       else continue;

        if($user_array[USER_INVISIBLE] == 1 and $IsModer) {
            if(!$current_user->show_for_moders and ($user_array[USER_CLASS] & ADM_BAN_MODERATORS)) {
                if(!$IsAdmin) continue;
           }
        }

         if ($current_user->user_class &  ADM_BAN_MODERATORS) { $out_users[$who_j]["powers"] = "m"; }
         else $out_users[$who_j]["powers"] = "u";

         if($out_users[$who_j]["powers"] == "m" and $current_user->show_admin == 0) {
            $out_users[$who_j]["htmlnick"] = $user_array[USER_NICKNAME];
            $out_users[$who_j]["powers"] = "u";
         }

         if(intval($user_array[USER_CLANID]) == intval($cu_array[USER_CLANID]) and $user_array[USER_CLANID] > 0) {
            if($out_users[$who_j]["powers"] == "m"){
               if($current_user->show_admin == 0) $out_users[$who_j]["powers"] = "c";
            }
            else $out_users[$who_j]["powers"] = "c";
          }

          if($current_user->clan_id > 0) {
             if(is_file($file_path."clans-avatar/".floor($current_user->clan_id/2000)."/".$current_user->clan_id.".gif")) {
                        $out_users[$who_j]["clan_avatar"] = $images_url."clans-avatar/".floor($current_user->clan_id/2000)."/".$current_user->clan_id.".gif";
             }
          }

          if ($current_user->married_with != "") { $out_users[$who_j]["marr"] = 1; }
          else $out_users[$who_j]["marr"] = 0;

          $pic_name = "" . floor($user_array[USER_REGID]/2000) . "/" . $user_array[USER_REGID] . ".big.gif";
          if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

          if ($pic_name == ""){
                    $pic_name = "" . floor($user_array[USER_REGID]/2000) . "/" . $user_array[USER_REGID] . ".big.jpg";
                    if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

                    if($pic_name == ""){
                       $pic_name = "" . floor($user_array[USER_REGID]/2000) . "/" . $user_array[USER_REGID] . ".big.jpeg";
                       if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
                    }
          }

          if($pic_name != "") $out_users[$who_j]["photo"] = 1;
          else $out_users[$who_j]["photo"] = 0;

          if(($current_user->custom_class & CST_PRIEST) and  strlen(trim($current_user->htmlnick)) == 0) {
             $out_users[$who_j]["htmlnick"] = "<FONT color=Black><b>".$user_array[USER_NICKNAME]."</b></FONT>";
          }

          if(($current_user->user_class > 0) and  strlen(trim($current_user->htmlnick)) == 0) {
                $out_users[$who_j]["htmlnick"] = "<FONT color=Black><i>".$user_array[USER_NICKNAME]."</i></FONT>";
          }


          $out_users[$who_j]["damneds"] = intval(trim($current_user->damneds));
          $out_users[$who_j]["rewards"] = intval(trim($current_user->rewards));

          $out_users[$who_j]["is_member"] = intval($current_user->is_member);
          $out_users[$who_j]["is_dealer"] = intval($current_user->is_dealer);

          $out_users[$who_j]["inv"] = $user_array[USER_INVISIBLE];

          $out_users[$who_j]["marr"] = $current_user->married_with;

          $out_users[$who_j]["webcam"] = intval($current_user->allow_webcam);

          $who_j++;
    }
?>