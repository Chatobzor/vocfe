<?php
require_once("inc_common.php");
include("events.php");
include("inc_to_canon_nick.php");
#for determining design and user_name if he is online
include($engine_path."users_get_list.php");
set_variable("new_user_name");
set_variable("passwd1");
set_variable("passwd2");
set_variable("new_user_sex");
set_variable("agreed");

set_variable("user_color");
$user_color = intval($user_color);
set_variable("room");
$room = intval($room);

set_variable("ref_id");
$ref_id = intval($ref_id);

if($ref_id < 0) $ref_id = 0;

if ($impro_registration) {
        set_variable("impro_user_code");
        set_variable("impro_id");
        include($ld_engine_path."impro.php");

        if (!impro_check($impro_id, $impro_user_code)){
                $error_text = $w_impro_incorrect_code."<br><a href=\"registration_form.php?session=".$session."\">".$w_try_again."</a>";
                include($file_path."designes/".$design."/error_page.php");
                exit;
        }
}
/*
if (get_magic_quotes_gpc())
{
        $new_user_name = stripslashes($new_user_name);
}*/
$passwd1 = str_replace("\t","",$passwd1);
$passwd2 =  str_replace("\t","",$passwd2);

if ((strlen($new_user_name)<$nick_min_length) or (strlen($new_user_name)>$nick_max_length)) {
        $error_text ="$w_incorrect_nick<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

if (ereg("[^".$nick_available_chars."]", $new_user_name)) {
        $error_text ="$w_incorrect_nick<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

if(strcasecmp($sw_usr_all_link, $new_user_name) == 0 or
   strcasecmp($sw_usr_adm_link, $new_user_name) == 0 or
   strcasecmp($sw_usr_boys_link, $new_user_name) == 0 or
   strcasecmp($sw_usr_girls_link, $new_user_name) == 0 or
   strcasecmp($sw_usr_they_link, $new_user_name) == 0 or
   strcasecmp($sw_usr_clan_link, $new_user_name) == 0 or
   strcasecmp($sw_usr_shaman_link, $new_user_name) == 0 or
   strcasecmp("&CMD", $new_user_name) == 0) {
        $error_text ="$w_incorrect_nick<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
                      }

riseEvent(EVENT_PREREG_USER, $new_user_name, $dummy);

if (strlen($passwd1)<3) {
        $error_text = $w_enter_password."<a href=\"registration_form.php?session=$session\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if ($passwd1 != $passwd2)  {
        $error_text = $w_password_mismatch."<a href=\"registration_form.php?session=$session\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
if ($agreed != 1)  {
        $error_text ="<a href=\"registration_form.php?session=$session\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
/*
if ($new_user_name != $user_name ) {
        //probably user tries to register nickname which is used in the moment by someone else
        // user_name == "" in case it's mailreg or registration is
        $uic = count($users);
        $cw = to_canon_nick($new_user_name);
        for ($i = 0; $i<$uic; $i++) {
                $temp_user = explode("\t", $users[$i]);
             if (!(strpos($cw, $temp_user[12]) === FALSE)) {
                        $error_text = $w_already_used."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
                }
        }
}*/
$pass_to_voc = $passwd1;

if($md5_salt != "") {
   $passSalt = md5($passwd1);
   $passSalt = $md5_salt.$passSalt;
   $passSalt = md5($passSalt);
   $passwd1  = $passSalt;
}
else $passwd1 = md5($passwd1);

$passwd2 = $passwd2;
if ($registration_mailconfirm) {
        set_variable("new_user_mail");
        include($ld_engine_path."registration_mail.php");
        list($usec, $sec) = explode(' ', microtime());
        srand( (float) $sec + ((float) $usec * 100000));

        $regkey = md5(uniqid(rand()));

        regmail_add($new_user_name, $passwd1, $new_user_mail, $regkey);
        if (!mail($new_user_mail, "registration at ".$w_title,
                str_replace("~", $new_user_name, str_replace("*", $w_title, str_replace("#",$chat_url."registration_activate.php?regkey=".$regkey, $w_regmail_body))),
                "From: ".str_replace("\\@", "@", $admin_mail)."\n".
                "Content-type: text/plain; ".(($charset!="") ? "charset=".$charset:"" )."\n".
                "Content-Transfer-Encoding: 8bit"
                ))
                trigger_error("Cannot send mail with activation code", E_USER_ERROR);
        $html_to_out = $w_regmail_sent;
        require($file_path."designes/".$design."/output_page.php");
        exit;
}
include($ld_engine_path."registration_add.php");
if ($club_mode) {
    riseEvent(EVENT_ADD_USER, $user_nick, $user_class);
}
include($file_path."designes/".$design."/registration_add.php");
?>