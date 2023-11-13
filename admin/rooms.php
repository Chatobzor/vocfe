<?php
include("check_session.php");
include("header.php");
include("../inc_common.php");
include($ld_engine_path."rooms_operations.php");
if ($long_life_data_engine == "mysql") {
        include_once($ld_engine_path."inc_connect.php");
}

function prepare_param ($param_name) {
        global $$param_name;
        $var = $$param_name;
        if (get_magic_quotes_gpc())
                $var = stripslashes($var);
        $var = htmlspecialchars(str_replace("\r"," ",str_replace("\n"," ",str_replace("\t"," ",$var))));
        return $var;
}

if (isset($max_num)) {

        set_variable("jail");
        $jail = intval($jail);

        for ($i=0;$i<=$max_num;$i++) {
                $param = "id_".$i; $r_id = intval($$param);
                $param = "title_".$i; $r_title = prepare_param($param);
                $param = "topic_".$i; $r_topic = prepare_param($param);
                $param = "design_".$i; $r_design = prepare_param($param);
                $param = "bot_".$i; $r_bot = prepare_param($param);
                $param = "pics_".$i; $r_pics = (isset($$param)) ? 1 : 0;
                $param = "premoder_".$i; $r_premoder = (isset($$param)) ? 1 : 0;
                $param = "club_".$i; $r_club = (isset($$param)) ? 1 : 0;
                $param = "pass_".$i; $r_pass = prepare_param($param);
                $param = "points_".$i; $r_points = (isset($$param)) ? 1 : 0;

                if($jail == $r_id) $r_jail = 1;
                else $r_jail = 0;

                if ($r_title != "") {
                        if ($r_id>=0)
                                room_update($r_id,$r_title,$r_topic,$r_design,$r_bot,"",$r_pics, $r_premoder, $r_club, $r_pass, $r_jail, $r_points);
                        else
                                room_add($r_title,$r_topic,$r_design,$r_bot,"","",$r_pics, $r_premoder, $r_club, $r_pass, $r_jail, $r_points);
                }
        }
        echo "<center><p>&nbsp;</p><span class=dat>$adm_rooms_updated!</span></center>";
}

echo "<p><center><h2 style=\"color:#265D92;font-family:Verdana\">$adm_rooms_admin</h2><form method=\"post\" action=\"rooms.php\">";
echo "<input type=\"hidden\" name=\"session\" value=\"$session\">";
echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
echo "<table border=\"3\" cellpadding=3 cellspacing=3 bordercolor=#CCCCCC>\n".
        "<tr class=mes align=center><td bgcolor=#666666><font color=White>$adm_rooms_name</font></td><td bgcolor=#666666><font color=White>$adm_topic</font></td>".
        "<td bgcolor=#666666><font color=White>$adm_predefined<font size=-1>*</font></font></td><td bgcolor=#666666><font color=White>$adm_bot_name</font></td>".
        "<td bgcolor=#666666><font color=White>$adm_premoderate**</font></td>\n".
        "<td bgcolor=#666666><font color=White>$adm_rooms_points</font></td>\n".
        "<td bgcolor=#666666><font color=White>$adm_rooms_club</font></td>\n".
        "<td bgcolor=#666666><font color=White>$adm_rooms_pass</font></td>\n".
        "<td bgcolor=#666666><font color=White>$adm_rooms_jail</font></td></tr>\n";
include($ld_engine_path."rooms_get_list.php");
$all_designes = array();
$handle = opendir("../designes/");
while (false !== ($tmp_file = readdir($handle)))
        if ($tmp_file!="." and $tmp_file != "..")
                $all_designes[] = $tmp_file;
closedir($handle);
for ($rg_i=0;$rg_i<count($room_ids);$rg_i++) {
        echo "<input type=\"hidden\" name=\"id_$rg_i\" value=\"".$room_ids[$rg_i]."\">";
        echo "<tr><td><input type=\"text\" class=\"input\" value=\"".str_replace("\"","&quot;",$ar_rooms[$room_ids[$rg_i]][ROOM_TITLE])."\" name=\"title_$rg_i\"></td>\n";
        echo "<td><input type=\"text\" class=\"input\" value=\"".str_replace("\"","&quot;",$ar_rooms[$room_ids[$rg_i]][ROOM_TOPIC])."\" name=\"topic_$rg_i\"></td>\n";
        echo "<td><select name=\"design_$rg_i\" class=\"input\">\n";
        echo "<option value=\"\">---</option>\n";
        for ($des_i = 0; $des_i<count($all_designes);$des_i++) {
                echo "<option value=\"".$all_designes[$des_i]."\"";
                if ($all_designes[$des_i] == $ar_rooms[$room_ids[$rg_i]][ROOM_DESIGN]) echo " selected";
                echo ">".$all_designes[$des_i]."</option>\n";
        }
        echo "</select></td>";
        echo "<td><input type=\"text\" class=\"input\" value=\"".str_replace("\"","&quot;",$ar_rooms[$room_ids[$rg_i]][ROOM_BOT])."\" name=\"bot_$rg_i\"></td>";
        echo "<td><input type=\"checkbox\" name=\"premoder_$rg_i\"";
        if ($ar_rooms[$room_ids[$rg_i]][ROOM_PREMODER] == 1) echo " checked";
        echo "></td>\n";

        echo "<td><input type=\"checkbox\" name=\"points_$rg_i\" ";
        if(intval($ar_rooms[$room_ids[$rg_i]][ROOM_POINTS]) == 1) echo "checked";
        echo "></td>";

        echo "<td><input type=\"checkbox\" name=\"club_$rg_i\" ";
        if(intval($ar_rooms[$room_ids[$rg_i]][ROOM_CLUBONLY]) == 1) echo "checked";
        echo "></td>";

        echo "<td><input type=\"text\" class=\"input\" value=\"".str_replace("\"","&quot;",$ar_rooms[$room_ids[$rg_i]][ROOM_PASSWORD])."\" name=\"pass_$rg_i\"></td>";

        echo "<td><input type=\"radio\" name=\"jail\" value=\"".$room_ids[$rg_i]."\" ";
        if(intval($ar_rooms[$room_ids[$rg_i]][ROOM_JAIL]) == 1) echo "checked";
        echo "></td>";

        echo "</tr>\n";
}

        echo "<input type=\"hidden\" name=\"id_$rg_i\" value=\"-1\">";
        echo "<tr><td><b>$adm_new</b>:<input type=\"text\" class=\"input\" value=\"\" name=\"title_$rg_i\"></td>\n";
        echo "<td><input type=\"text\" class=\"input\" value=\"\" name=\"topic_$rg_i\"></td>\n";
        echo "<td><select name=\"design_$rg_i\" class=\"input\">\n";
        echo "<option value=\"\">---</option>\n";
        for ($des_i = 0; $des_i<count($designes);$des_i++)
                echo "<option value=\"".$designes[$des_i]."\">".$designes[$des_i]."</option>\n";
        echo "</select></td>";

        echo "<td><input type=\"text\" class=\"input\" value=\"".str_replace("\"","&quot;",$w_rob_name)."\" name=\"bot_$rg_i\"></td>";
        echo "<td><input type=\"checkbox\" name=\"premoder_$rg_i\"></td>";
        echo "<td><input type=\"checkbox\" name=\"points_$rg_i\"></td>";
        echo "<td><input type=\"checkbox\" name=\"club_$rg_i\"</td>";

        echo "<td><input type=\"text\" class=\"input\" value=\"\" name=\"pass_$rg_i\"></td>";

        echo "<td><input type=\"checkbox\" name=\"jail_new\" ";
        echo "></td>";
        echo "</tr>\n";

echo "</table><input type=\"hidden\" name=\"max_num\" value=\"$rg_i\">";
echo "<br><input type=\"submit\" value=\"$adm_save & $adm_update\" class=\"button_small\"></form></center>\n";
?>
<blockquote>
<span class=txt>*<?php echo $adm_predefined_note; ?></span>
<br><br>
<span class=txt>**<?php echo $adm_premoderated_note; ?></span>
</blockquote>
</body></html>