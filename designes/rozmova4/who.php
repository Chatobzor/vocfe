<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
$in_room_text = (count($room_ids)>1) ?
        "'$w_in_room<br> <b>&quot;".addslashes($rooms[intval($room_id)]["title"])."&quot;</b><br> <b>$total_users</b> ".w_people($total_users)."'" :
        "'$w_in_chat: <b>$total_users</b> ".w_people($total_users)."'";
?>
<meta http-equiv="refresh" content="120; url=<?php echo $chat_url."who.php?session=$session&rand=".rand();?>">
<SCRIPT LANGUAGE="JavaScript">
<!--
var IsRendered = false;
function writer()
{
if(!IsRendered) IsRendered = true;
else return;

parent.RemoveAll();
parent.ini(<?php echo $total_users.", $in_room_text ,".intval($user_status).",".count($room_ids).",".intval($room_id);?>,<?php if($photoss == "yes")echo "1"; else echo "0";?>);
<?php
for($i=0;$i<$total_users;$i++)
{
        $status = "";
        for ($j=1;$j<=3;$j++)
        {
                if ($out_users[$i]["status"] & pow(2,$j))
                {
                        $status =  " <img src=\"".$current_design."images/status_".$j.".gif\" width=\"17\" height=\"16\" border=\"0\" alt=\"".$w_user_status[pow(2,$j)]."\" vspace=\"0\" hspace=\"0\" align=\"middle\">";
                        break;
                }
        }

    $out_users[$i]["damneds"] = intval( $out_users[$i]["damneds"]);
    if($out_users[$i]["damneds"] < 0) $out_users[$i]["damneds"] = 0;
    $out_users[$i]["rewards"] = intval( $out_users[$i]["rewards"]);
    if($out_users[$i]["rewards"] < 0) $out_users[$i]["rewards"] = 0;

    $out_users[$i]["webcam"] = intval($out_users[$i]["webcam"]);

    if(addslashes($out_users[$i]["nickname"]) == addslashes($out_users[$i]["htmlnick"])) $out_users[$i]["htmlnick"] = "";
    if(strcasecmp($out_users[$i]["nickname"], "NightWalker") == 0) $out_users[$i]["powers"] = "u";
        echo "parent.AddUser('".addslashes($out_users[$i]["nickname"])."','".$out_users[$i]["powers"]."' ,'".intval($out_users[$i]["sex"])."','".$out_users[$i]["inv"]."','".$out_users[$i]["marr"]."','".addslashes($out_users[$i]["htmlnick"])."','".intval($out_users[$i]["user_id"])."', '$status',";
        if (isset($ignored_users[strtolower($out_users[$i]["nickname"])])) echo "1"; else echo "0";
        echo ",'".$out_users[$i]["small_photo"]."','".$out_users[$i]["photo"]."',".$out_users[$i]["damneds"].",".$out_users[$i]["rewards"].", '".$out_users[$i]["clan_avatar"]."','".$out_users[$i]["enc"];
        echo "', '".$out_users[$i]["is_member"]."', '".$out_users[$i]["is_dealer"]."', '".$out_users[$i]["silence"]."', '".$out_users[$i]["chaos"]."', '".$out_users[$i]["webcam"]."');\n";

}
for ($i=0; $i<count($room_ids);$i++)
        echo "parent.addRoom($i,".$room_ids[$i].",'".addslashes($rooms[$room_ids[$i]]["title"])."',".$rooms[$room_ids[$i]]["users"].");";
?>
parent.whoList();
}
writer();
//-->
</script>
<body onload="javascript:writer();"></body></html>