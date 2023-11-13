<?php include("check_session.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<?php
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<blockquote><span class=head><font color=Black>
<?php
$dummy = " ";
for($i = 0; $i < 128; $i++) $dummy .= "&nbsp;\n";
$curr  = 0;

include("../inc_common.php");
include("../inc_user_class.php"); 
if ($long_life_data_engine == "mysql") {
	include_once($ld_engine_path."inc_connect.php");
}
include("../inc_to_canon_nick.php");
if ($long_life_data_engine == "files") {
	$users = array();
	$users = file($user_data_file);
    $pass = count($users) / 298;
    if($pass < 1) $pass = 1;

    echo "<script>parent.frames['wr'].reInitBar($pass, ".count($users).");</script>\n";

    $shamans_list = array();

    flush();
    ob_end_flush();

	for ($i=0; $i<count($users);$i++)
	{
        echo " ";
       	if(intval($i - $curr) >= intval($pass)) {
        	$curr = $i;
    		//echo $dummy;
	        echo "<script>parent.frames['wr'].incrCount();</script>\n";
            ob_flush();
        }

		$user = str_replace("\n","",$users[$i]);
		list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user);
	    $t_id = intval(trim($t_id));

    	if (file_exists($data_path."users/".floor($t_id/2000)."/".$t_id.".user")) {
			$current_user = unserialize(implode("",file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")));
		}
        if($current_user->custom_class & CST_PRIEST) {
        	$shamans_list[] = array("nickname" => $t_nickname,
            						"id"=> $t_id);
        }
	}
    echo count($shamans_list);

    if(count($shamans_list) > 0) {
       	    $fp = fopen($data_path."shamans_list.tmp", "wb");
            if($fp) {
	            for($i = 0; $i < count($shamans_list); $i++) {
    	        	fwrite($fp,$shamans_list[$i]["id"]."\t".$shamans_list[$i]["nickname"]."\n");
        	    }
            fclose($fp);
            }
    }

    echo "<script>parent.frames['wr'].finish();</script>";
    echo "<script>parent.frames['wr'].location.href='".$chat_url."admin/shamans_list.php?session=$session&lang=$lang';</script>";
} else if ($long_life_data_engine == "mysql") {
	$m_result = mysql_query("select id, nick from ".$mysql_table_prefix."users") or die("database error<br>".mysql_error());
	while($row = mysql_fetch_array($m_result, MYSQL_NUM)) {
		$canon_nick = addslashes(to_canon_nick($row[1]));
		mysql_query("update ".$mysql_table_prefix."users set canon_nick='".$canon_nick."' where id=".$row[0]) or die("database error<br>".mysql_error());
	}
	mysql_free_result($m_result);
}
?>
</font></blockquote>
</td></tr></table></center>
</body>
</html>