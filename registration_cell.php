<?php
require_once("inc_common.php");
#for determining design and user_name
include($engine_path."users_get_list.php");

set_variable("user_name");
$user_name = htmlspecialchars(trim($user_name));

    if (ereg("[^".$nick_available_chars."]", $user_name)) {
        $error_text = "$w_incorrect_nick (".$user_name.")<br><a href=\"index.php\">$w_try_again</a>";
        include($file_path."designes/".$design."/error_page.php");
         exit;
    }



set_variable("new_user_sex");
$new_user_sex = intval($new_user_sex);
set_variable("user_color");
$user_color = intval($user_color);
set_variable("room");
$room = intval($room);

$impro_id = 0;
$impro_code = -1;

if ($impro_registration) {
        include($ld_engine_path."impro.php");
        list($usec, $sec) = explode(' ', microtime());
        srand( (float) $sec + ((float) $usec * 100000));

        $impro_id = md5(uniqid(rand()));
        $impro_code = rand(1000,9999);
        impro_save($impro_id, $impro_code);
}

if(!intval($open_chat)) {
    $error_text=$w_roz_chat_closed;
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

if(disk_free_space($data_path) < 10*1024*1024 and is_file($data_path."engine/files/guardian.php") and intval($vocplus_useguardian)) {
   if($vocplus_guardian_dellogs) {
           include_once($data_path."engine/files/guardian.php");
              cleanUpOldLogs();
   }
}

if(disk_free_space($data_path) < 10*1024*1024) {
        $error_text=$w_roz_chat_closed."<br><b>".$w_roz_out_of_space." ($admin_mail)</b>";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

include($file_path."designes/".$design."/registration_cell.php");
?>