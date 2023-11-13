<?php
require_once("inc_common.php");
include("inc_to_canon_nick.php");
set_variable("regkey");

include($ld_engine_path."registration_mail.php");
if (regmail_activate($regkey))
	$html_to_out = $w_regmail_activated."<br><a href=\"".$chat_url."\">".$w_login_button."</a>";
else
	$html_to_out = $w_regmail_no_code;
require($file_path."designes/".$default_design."/output_page.php");

?>
