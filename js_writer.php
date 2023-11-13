<html>
<head>
<META HTTP-EQUIV=Content-Type CONTENT="text/html; charset=windows-1251">
</head>
<body>
<?php
require_once("inc_common.php");
error_reporting(0);
set_variable("last_message");
include($engine_path."users_get_list.php");
include($file_path."inc_form_message.php");
if (!$exists)  {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}
include($engine_path."messages_get_list.php");

$out_messages = array();

if ($last_message=="") {
        $header_string = file($file_path."designes/".$design."/daemon_html_header.html");

        for($i=0;$i<count($header_string);$i++)

        $already_showed = 0;
        $total_out = "";
        $total_messages = count($messages);
        //to get $last_id
        list($last_id, $to_out) = form_message(0,$messages[$total_messages-1], $ignored_users);
        for ($i=$total_messages-1;$i>=0;$i--) {
                if ($already_showed>=$history_size) break;
                list($unused, $to_out) = form_message(0,$messages[$i], $ignored_users);
                if ($to_out!="") {
                        $already_showed++;
                        $total_out = $to_out." \n".$total_out;
                }
                if ($already_showed>9) break;
        }
        echo "<script>$total_out \n parent.ping();</script>\n";
} else {
        $last_message = intval($last_message);
        list($last_id,$to_out) = show_messages($last_message, $messages,$ignored_users);
        if ($to_out == "") echo "<script>parent.ping();</script>\n";
        else echo "<script>$to_out \n parent.ping</script>\n";
}
echo "<script>window.setTimeout('document.location.href=\"".$chat_url."js_writer.php?session=$session&last_message=$last_id\"',2500);</script>\n";

function show_messages($last_id, $messages, $ignored_users) {
        $total_messages = count($messages);
        $total_out = "";
        for ($i=0;$i<$total_messages;$i++) {
                list($message_id, $to_out) = form_message($last_id, $messages[$i],$ignored_users);
                $total_out .= $to_out;
        }
        return array($message_id,$total_out);
}
?></body></html>