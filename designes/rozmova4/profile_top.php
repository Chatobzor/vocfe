<?php
require_once("../../inc_common.php");
#only to determine design:
include($engine_path."users_get_list.php");

if(!$is_regist_complete) $session = "";

set_variable("user_id");
$user_id = intval($user_id);

#fake for including files, without functions
$is_regist = $user_id;
if ($is_regist) {
        include($file_path."inc_user_class.php");
        include($ld_engine_path."users_get_object.php");
} else {
        $error_text = str_replace("~","",$w_search_no_found);
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

include($file_path."designes/".$design."/common_title.php");
?>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" rightmargin="0" bottommargin="0">
<table border="0" cellpadding="0" cellspacing="0">
        <tr>
                <td width=50% bgcolor="#7ec63e"  height="77" valign="middle" align="center">
                  <font face="Garamond, Times New Roman, Helvetica" color=White size=6><b>
              <?php
                if($current_user->user_class > 0) {
                      if($current_user->user_class & ADM_BAN_MODERATORS) {
                        if($current_user->show_admin) {
                           echo $w_roz_administrator;
                        } else echo $w_roz_user;
                      }
                      else echo $w_roz_moderator;
                }
                else {
                    if($current_user->custom_class & CST_PRIEST) {
                        echo $w_roz_priest;
                   } else echo $w_roz_user;
                }
               ?>
                  </b></font>
                </td>
                <td width="177" height="77"><img src="main/profile_02.jpg" width="177" height="77" alt=""></td>
                <td align=center valign="middle" width="50%">
                <font face="Garamond, Times New Roman, Helvetica" color="#bf0d0d" size=6><b>
                <?php echo $current_user->nickname; ?>
                </b></font>
                </td>
        </tr>
        <tr>
          <td colspan="3" height="20">
            <table border="0" cellpadding="0" cellspacing="0">
             <tr>
               <td width="30%">&nbsp;&nbsp;
                <a style="text-decoration: none;" target="_blank" href="<?php echo $chat_url;?>board_send.php?session=<?php echo $session;?>&send_to_id=<?php echo $user_id;?>"><font face="Verdana, Tahoma, Arial" size=1 color="#bf0d0d"><b>[ <?=$w_roz_offline_pm?> ]</b></a>&nbsp;
               </td>
               <td width="147" height="20"><img src="<?php echo $current_design;?>main/profile_05.jpg" width="147" height="20" alt=""></td>
               <td width="70%" height="20" bgcolor="#7ec63e" align="right">
               <font face="Verdana, Tahoma, Arial" size=1 color="#FFFFFF"><?=ucfirst($w_roz_profile)?> : <b><?php echo $chat_url."users/".$current_user->nickname;?>&nbsp;&nbsp;</b></font>
               </td>
             </tr>
            </table>
          </td>
        </tr>
</table>

</body></html>