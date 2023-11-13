<?php
require_once("../../inc_common.php");
set_variable("session");
set_variable("user_to_search");

$user_to_search = trim($user_to_search);
if (ereg("[^".$nick_available_chars."]", $user_to_search)) {
    $error_text ="$w_incorrect_nick<br><a href=\"index.php\">$w_try_again</a>";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}

    $u_ids = array();
    $u_names = array();
        include($ld_engine_path."users_search.php");
        $html_to_out = "";
        if (count($u_ids)) {
            for($i = 0; $i < count($u_ids); $i++) {
                    if(strcasecmp(trim($u_names[$i]), $user_to_search) == 0){
                            header("location: ".$chat_url."fullinfo.php?session=$session&user_id=".$u_ids[$i]);
                            exit;
                }
            }
            $error_text = $w_search_no_found;
            $error_text = str_replace("~", "<b>".$user_to_search."</b>", $error_text);

            for($i = 0; $i < count($u_ids); $i++) $error_text .= "[".$u_names[$i]."]";

                        include($file_path."designes/".$design."/error_page.php");
                        exit;
        }
    else {
                    $error_text = $w_search_no_found;
            $error_text = str_replace("~", "<b>".$user_to_search."</b>", $error_text);
                        include($file_path."designes/".$design."/error_page.php");
                        exit;
    }
?>