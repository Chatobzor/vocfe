<?php
include("inc_common.php");

include($engine_path."users_get_list.php");
include($file_path."events.php");

set_variable("message");
set_variable("subject");
set_variable("send_to_id");
set_variable("group");
$send_to_id = intval($send_to_id);
if (!$exists)  {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");
include("user_validate.php");

if(isset($_REQUEST[session_name()]) or isset($HTTP_REQUEST_VARS[session_name()])){
	session_start();
}

  if(count($_POST)>0 or count($HTTP_POST_VARS)>0){
		if(isset($_SESSION['captcha_keystring'])){
			if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['keystring']){
			}else{
        		$error_text = $w_impro_incorrect_code;
	        	include($file_path."designes/".$design."/error_page.php");
        		exit;
			}
		}
		else if(isset($HTTP_SESSION_VARS['captcha_keystring'])){
			if(isset($HTTP_SESSION_VARS['captcha_keystring']) && $HTTP_SESSION_VARS['captcha_keystring'] ==  $HTTP_POST_VARS['keystring']){
			}else{
        		$error_text = $w_impro_incorrect_code;
	        	include($file_path."designes/".$design."/error_page.php");
        		exit;
			}
		} else {
        		$error_text = $w_impro_incorrect_code;
	        	include($file_path."designes/".$design."/error_page.php");
        		exit;
		}
   }

   unset($_SESSION['captcha_keystring']);
   unset($HTTP_SESSION_VARS['captcha_keystring']);

$message = fixup_contributions($message);

$message = htmlspecialchars($message);
$message = str_replace("\n","<br>",$message);
$message = str_replace("\r","",$message);
$message = str_replace("\t"," ",$message);
$message = str_replace("  "," &nbsp;",$message);

if(strlen($message) > 2048) $message = substr($message, 0, 2048);

$subject = fixup_contributions($subject);
$subject = str_replace("\t"," ",$subject);
$subject = htmlspecialchars($subject);

if(strlen($subject) > 100) $subject = substr($subject, 0, 100);

riseEvent(EVENT_POST_OFFLINEPM, $user_name, $message);
riseEvent(EVENT_POST_OFFLINEPM, $user_name, $subject);

if (function_exists('preg_replace')){
        $message = preg_replace("/[0-9a-f]{32}/", "1234", $message);
        $subject = preg_replace("/[0-9a-f]{32}/", "1234", $subject);
}

$info_message = "";

include($ld_engine_path."hidden_board_post_message.php");

include($file_path."designes/".$design."/board_post.php");
?>