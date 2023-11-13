<?
        if (!defined("_COMMON_")) {echo "stop";exit;}
        include($file_path."designes/".$design."/common_title.php");
        include($file_path."designes/".$design."/common_browser_detect.php");
        include($file_path."designes/".$design."/common_body_start.php");

        if($look_for!="") {
                $user_to_search = $look_for;
                include($ld_engine_path."users_search.php");
                //if (!count($u_ids)) $info_message = "<b>".str_replace("~","&quot;<b>".htmlspecialchars($look_for)."</b>&quot;",$w_search_no_found)."</b><br>";
        }

?>
        <script>
                function buy(){
                        document.myForm.todo.value="buy";
                        document.myForm.submit();
                }
        </script>
        <form method="post" name="myForm" action="shop_submit.php">
        <input type="Hidden" name="session" value="<?=$session?>">
        <input type="Hidden" name="item" value="<?=$item?>">
        <input type="Hidden" name="present_for" value="">
        <input type="Hidden" name="present" value="1">
        <input type="Hidden" name="todo" value="search">
        <table align="center">
                <tr>
                        <td><?=$w_roz_user?> :</td><td><input class = "input" type="Text" name="look_for" value="">&nbsp; <input class="input_button" type="Submit" value="OK"></td>
                </tr>
        <?if(count($u_ids)){?>
                <tr>
                        <td colspan="2"><strong> <?=$w_shop_present?>:</strong></td>
                </tr>
                <tr>
                        <td><?=$w_roz_user?>:</td>
                        <td>
                        <select name="present_for">
                                <?
                                for ($i=0;$i<count($u_ids);$i++){
                                ?>
                                <option value="<?=$u_ids[$i];?>" <?php if(strcasecmp($u_names[$i], $look_for) == 0) {?>SELECTED<?php } ?>><?=$u_names[$i]?></option>
                                <?
                                }
                                ?>
                        </select>
                        </td>
                </tr>
                <tr>
                        <td><?=$w_admin_reason?>:</td>
                        <td><input type="Text" name="reason"></td>
                </tr>
                <tr>
                        <td colspan="2"><input class="input_button" type="Button" value="<?=$w_shop_present?>" onclick="buy();"></td>
                </tr>
        <?}?>
        </table>
        </form>
<?
        include($file_path."designes/".$design."/common_body_end.php");
?>
