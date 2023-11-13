<?php
require_once("inc_common.php");
include($engine_path."users_get_list.php");

if (!$exists) {
    $error_text = "$w_no_user";
    include($file_path."designes/".$design."/error_page.php");
    exit;
}

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");
include("user_validate.php");

include($file_path."designes/".$design."/common_body_start.php");
?>
    <div align=center><h2>История</h2></div>
    <div align=center><h3>Ваше общение за текущий сеанс</h3></div>
    <font face="Verdana" size=2>
        <script language="JavaScript">
            if (opener) {
                if (!opener.top.closed) {
                    for (i = 0; i < opener.top.arrHistorySize; i++) {
                        wrStr = opener.top.arrHistory[i];

                        while (wrStr.indexOf('parent') != -1) wrStr = wrStr.replace('parent', 'opener.top');

                        document.write(wrStr + '<br>');
                    }
                }
            }
        </script>
    </font>
<?php
include($file_path."designes/".$design."/common_body_end.php");