<?php
require_once("inc_common.php");
#trying to get preffered design from cookies
set_variable("c_design");
set_variable("c_user_name");
$design = "";
set_variable("design");
if ($c_design!="" and $design=="") $design = $c_design;
if (!in_array($design, $designes)) $design = $default_design;

set_variable("user_lang");
set_variable("c_ulang");
if ($c_ulang != "" && $user_lang == "") $user_lang = $c_ulang;
if (!in_array($user_lang, $allowed_langs)) $user_lang = $language;
//here it can fails, i don't care. will be used default system language
else include_once($file_path."languages/".$language.".php");

include($file_path."designes/".$design."/index.php");
?>