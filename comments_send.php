<?php
require_once("inc_common.php");
#for determining design:
include($engine_path."users_get_list.php");
set_variable("name");
set_variable("email");
set_variable("comment");

$name = urlencode($name);
// \@ is in admin_mail for perl
$success = mail(str_replace("\\@","@",$admin_mail), "comments from chat", 
	"From: $name\nEMail: $email\n--------\n$comment\n", 
	"From: <$email>\nReturn-Path: $admin_mail\n
	Content-Type: text/plain;\n".
	"Content-type: text/plain; ".(($charset!="") ? "charset=".$charset:"" )."\n".
	"Content-Transfer-Encoding: 8bit");
if ($success) $info_message = $w_feed_sent_ok;
else  $info_message = $w_feed_error;
include($file_path."designes/".$design."/comments_send.php");
?>