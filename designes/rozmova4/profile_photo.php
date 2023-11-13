<?php
require_once("../../inc_common.php");
if (!defined("_COMMON_")) {echo "stop";exit;}

include($engine_path."users_get_list.php");
include_once($file_path."inc_user_class.php");

$IsModer = false;
$CanVote = false;

if($cu_array[USER_CLASS] > 0) $IsModer = true;

if(!$is_regist_complete or !$exists) $session = "";

include_once($data_path."engine/files/user_log.php");

set_variable("act");
set_variable("photo_rei");

$photo_rei = intval($photo_rei);
if($photo_rei < 0 or $photo_rei > (MAX_PHOTO_REITING - 1)) $photo_rei = 0;

if($is_regist and $is_regist_complete and $exists and $enable_reiting) {
 include($ld_engine_path."users_get_object.php");
 $CanVote = true;

    if($act == "make_vote") {
                if($current_user->online_time < 25000) {
                   if($current_user->credits < 5) {
                       $error_text = "$w_no_credits (25 000)";
                       include($file_path."designes/".$design."/error_page.php");
                       exit;
                   }
               }

              if(intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"]) > my_time()) {
                  $MsgToPass = $w_user_chaos;
                  $MsgToPass = str_replace("~", date("d.m.Y H:i:s", intval($current_user->plugin_info["chaos_start"]) + intval($current_user->plugin_info["chaos_time"])), $MsgToPass);
                  $error_text = $MsgToPass;
                  include($file_path."designes/".$design."/error_page.php");
                  exit;
              }


              if(my_time() - $current_user->plugin_info["last_comment_time"] < 200) {
               $error_text = "$w_flood";
                include($file_path."designes/".$design."/error_page.php");
                exit;
              }
    }
}

set_variable("user_id");
$user_id = intval($user_id);

if($user_id == $is_regist) $CanVote = false;

#fake for including files, without functions
$is_regist = $user_id;

$pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.gif";
if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

if ($pic_name == ""){

 $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpg";
 if (!file_exists($file_path."photos/$pic_name")) $pic_name="";

 if ($pic_name == ""){
     $pic_name = "" . floor($is_regist/2000) . "/" . $is_regist . ".big.jpeg";
     if (!file_exists($file_path."photos/$pic_name")) $pic_name="";
 }
}
$IsPhoto = true;
if($pic_name == "") {
 $pic_name = "no_photo.gif";
 $IsPhoto = false;
 $CanVote = false;
}

if ($is_regist) {
        include($ld_engine_path."users_get_object.php");

        if(!$current_user->photo_take_part) $CanVote = false;

        if($CanVote and $enable_reiting) {
            for($i = 0; $i < count($current_user->photo_voted); $i++) {
                if(intval($current_user->photo_voted[$i]) == intval($cu_array[USER_REGID])) $CanVote = false;
                break;
            }

            if(in_array(intval($cu_array[USER_REGID]), $current_user->photo_voted)) $CanVote = false;

           if($CanVote and $act == "make_vote") {
               $current_user->photo_voted[]                                       =  intval($cu_array[USER_REGID]);
               $current_user->photo_voted_mark[]                                  =  $photo_rei;

               $tov = 0;
               for($i = 0; $i < count($current_user->photo_voted); $i++) {
                  $tov += $current_user->photo_voted_mark[$i];
               }
               $current_user->photo_reiting =  $tov;

               include($ld_engine_path."user_info_update.php");
               $CanVote = false;
           }
        }

} else {
        $error_text = str_replace("~","",$w_search_no_found);
        include($file_path."designes/".$design."/error_page.php");
        exit;
}


if($act == "del_photo" and
   $cu_array[USER_CLASS] > 0 and
   $current_user->user_class == 0
   and $IsPhoto
   and $user_id == $is_regist and
   $exists) {

   unlink($file_path."photos/$pic_name");
   $pic_name = "no_photo.gif";

   $current_user->photo_voted   =  array();
   $current_user->photo_voted_mark  =  array();
   $current_user->photo_reiting =  0;

   $CanVote = false;
   include($ld_engine_path."user_info_update.php");

   $MsgToPass = $sw_mod_remove_photo_adm;
   $MsgToPass = str_replace("#", $current_user->nickname, $MsgToPass);
   $MsgToPass = str_replace("$", $cu_array[USER_NICKNAME], $MsgToPass);

   WriteToUserLog($MsgToPass, $cu_array[USER_REGID], "");
   WriteToUserLog($MsgToPass, $is_regist, "");

   $group      = 0;
   $send_to_id = $is_regist;
   $subject    = $sw_mod_remove_photo_subj;
   $message    = $sw_mod_remove_photo_user;
   $user_name  = "";

   include($ld_engine_path."hidden_board_post_message.php");
}



include($file_path."designes/".$design."/common_title.php");

//if(!$current_user->show_group_1 and !$IsModer) $pic_name = "no_photo.gif";
list($roz_width, $roz_height, $type, $attr) = getimagesize($file_path."photos/".$pic_name);

$CanBeEnlarged = false;

if($roz_width > 300 or $roz_height > 300) {
 $CanBeEnlarged = true;

 if($roz_width > 300) {
    $ratio = 300 / $roz_width;
    $roz_width  = 300;
    $roz_height = $roz_height * $ratio;
 }
 if($roz_height > 300) {
    $ratio = 300 / $roz_height;
    $roz_height = 300;
    $roz_width = $roz_width * $ratio;
 }

}

$is_regist = $user_id;
if ($is_regist) {
        include("inc_user_class.php");
        include($ld_engine_path."users_get_object.php");
} else {
        $error_text = str_replace("~","",$w_search_no_found);
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

?>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" rightmargin="0" bottommargin="0">
<table width=100% height=100% border="0" cellpadding="0" cellspacing="0">
<tr><td width="100%" height="100%" align="center" valign="middle">
    <table border="0" cellpadding="0" cellspacing="0">
<?php
  if($IsPhoto and $cu_array[USER_CLASS] > 0 and $current_user->user_class == 0) {
  ?>
 <form action="profile_photo.php" method="post">
  <tr><td align="center" colspan="2">
      <input type="hidden" name="session" value="<?=$session?>">
      <input type="hidden" name="user_id" value="<?=$user_id?>">
      <input type="hidden" name="act" value="del_photo">
      <input type="submit" value="<?=$w_mod_remove_photo?>" class="input_button">
  </td></tr>
    </form>
  <?php
  }
?>
    <?php

    $tmp_status = "";

    for($j = 0; $j < count($sw_roz_user_status); $j++) {
        if(intval($current_user->online_time) > $sw_roz_user_status[$j]["points"]) {
                $tmp_status = $sw_roz_user_status[$j]["status"];
                break;
        }
    }

      if($current_user->rewards > 0
         or $current_user->damneds > 0
         or $current_user->chat_status != ""
         or $tmp_status != "") {
    ?>
     <tr>
       <td colspan="2" height="18" align=center>
        <table border="0" cellpadding="0" cellspacing="0">
          <tr><td valign="up" height="18">
            <font color="#bf0d0d" face="Verdana, Tahoma, Arial" size=2><?php
            if($current_user->chat_status) echo "<b>".$current_user->chat_status."</b>";
            else echo ucfirst($tmp_status);
            ?>&nbsp;</font>
          </td><td>
         <!--img src="<?php echo $current_design;?>main/profile_left.gif" width="21" height="21" alt=""--><script language="JavaScript">
    var nViewed = 0, j;
    var nRed, nSilver, nGold;

    var Rewards = <?php echo intval($current_user->rewards);?>;

    nGold       = Math.ceil(Math.floor(Rewards/9));
    nSilver     = Math.ceil(Math.floor((Rewards - nGold*9)/3));
    nRed        = Rewards - nGold*9 - nSilver*3;

    <?php
      $goldImg         = "<img width=18 height=18  src=\"".$current_design."img/amul_orange.gif\">";
      $silverImg         = "<img width=18 height=18 src=\"".$current_design."img/amul_gray.gif\">";
      $simpleImg         = "<img width=18 height=18 src=\"".$current_design."img/amul_red.gif\">";
    ?>

   with (document) {
            for(j = 0; j < nGold; j++) if(nViewed < 3) { write('<?php echo $goldImg; ?>'); nViewed++; }
            for(j = 0; j < nSilver; j++) if(nViewed < 3) { write('<?php echo $silverImg; ?>'); nViewed++;}
            for(j = 0; j < nRed; j++) if(nViewed < 3) { write('<?php echo $simpleImg; ?>'); nViewed++;}
   }
</script><?php

    for($i = 0; $i < intval($current_user->damneds); $i++) {
         echo "<img src=\"".$current_design."img/amul_curse.gif\" width=18 height=18>";
    }

?><!--img src="<?php echo $current_design;?>main/profile_right.gif" width="21" height="21" alt=""-->
             </td>
        </tr>
       </table>
     </td>
    </tr>
    <?php } ?>
     <tr>
       <td align=center>
          <table border="0" cellpadding="0" width="<?php echo $roz_width+7;?>" height="<?php echo $roz_height+7;?>" cellspacing="0">
            <tr>
               <td><a href="<?php echo $images_url."photos/".$pic_name.'?rand='.time(); ?>" style="text-decoration: none" target="_blank"><img style="border-color: #000000" src="<?php echo $images_url."photos/".$pic_name.'?rand='.time(); ?>" border=1 width="<?php echo $roz_width;?>" height="<?php echo $roz_height;?>"></a></td>
               <td height=100%>
                  <table border="0" cellpadding="0" height=100% cellspacing="0">
                     <tr>
                       <td width="6" height="7"><img src="<?php echo $current_design;?>main/profile_11.gif" width="6" height="7" alt=""></td>
                     </tr><tr>
                       <td background="<?php echo $current_design;?>main/profile_13.gif"></td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td colspan="2" width="100%">
                  <table border="0" cellpadding="0" width="100%" cellspacing="0">
                    <tr>
                      <td width="6" height="7"><img src="<?php echo $current_design;?>main/profile_19.gif" width="6" height="7" alt=""></td>
                      <td width="100%" background="<?php echo $current_design;?>main/profile_20.gif"></td>
                      <td width="6" height="7"><img src="<?php echo $current_design;?>main/profile_21.gif" width="6" height="7" alt=""></td>
                    </tr>
                  </table>
               </td>
            </tr>
          </table>
       </td>
     </tr>
     <?php
        if($CanBeEnlarged) {
          ?>
     <tr>
       <td align=center>
        <a href="<?php echo $images_url."photos/".$pic_name; ?>" style="text-decoration: none" target="_blank">
          <font face="Verdana, Tahoma, Arial" size=1>
           (кликните на фото для того,<br>что бы увеличить изображение)</a>
          </b></font>
       </td>
     </tr>
     <?php
        } if($enable_reiting) { ?>
     <tr>
       <td align=center>
           <b><?=$w_photo_reiting?>: <?php

           $tov = 0;
           for($i = 0; $i < count($current_user->photo_voted); $i++) {
               $tov += $current_user->photo_voted_mark[$i];
           }
           echo $tov;?></b> (<?=$w_photo_reiting_vote?>: <?=count($current_user->photo_voted)?>)
       </td>
     </tr>
     <?php if($CanVote) {
     ?>
     <form action="profile_photo.php" method="post">
     <input type="hidden" name="session" value="<?=$session?>">
     <input type="hidden" name="user_id" value="<?=$user_id?>">
     <input type="hidden" name="act" value="make_vote">
     <tr>
       <td align=center>
          <?=$w_photo_reiting_do?>: <select name="photo_rei" class="input_button"><?php
              for($i = 0; $i < MAX_PHOTO_REITING; $i++) {
                  echo "<option value=\"$i\">$i</option>";
              }
           ?></select> <input type="submit" value="OK" class="input_button">
       </td>
     </tr>
     </form>
     <?php }} ?>
     </table>
</td></tr>
</table>