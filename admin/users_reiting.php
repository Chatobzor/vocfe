<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/site.css" rel="stylesheet" type="text/css">
<body bgcolor="E5E5E5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link=#000000 alink=#000000 vlink=#000000>
<?php
if ($dtop >= 30){
define("REITING_MAXHITS", $dtop);
}
define("REITING_MAXHITS", "20");

include_once("../inc_common.php");

function cmp_rei($a, $b)
{
   if($a["points"] > $b["points"]) return -1;
   else return 1;
}

$fp = fopen($user_data_file, "r");
flock($fp, LOCK_EX);
fseek($fp,0);

if(isset($reiting_rez)) unset($reiting_rez);
include($file_path."inc_user_class.php");
$i = 0;

while ($data = fgets($fp, 4096)) {
	$user = str_replace("\r","",str_replace("\n","",$data));
	list($t_id, $t_nickname, $t_password, $t_class,$t_canon) = explode("\t",$user);

    $t_id = intval(trim($t_id));

    if (file_exists($data_path."users/".floor($t_id/2000)."/".$t_id.".user")) {
		$current_user = unserialize(implode("",file($data_path."users/".floor($t_id/2000)."/".$t_id.".user")));
	}
    if($current_user->points > 0 and $current_user->nickname != "") {
    	$reiting_rez[$i]["nick"]   = $current_user->nickname;
        $reiting_rez[$i]["points"] = $current_user->points;
        $i++;
    }

}
flock($fp, LOCK_UN);
fclose($fp);

usort($reiting_rez, "cmp_rei");
echo "<p>";

$MaxHits = REITING_MAXHITS;
if(count($reiting_rez) < REITING_MAXHITS) $MaxHits = count($reiting_rez);
?>
<table border="0" cellspacing="0" cellpadding="0" align=CENTER>
<tr><td align="center" valign="middle"><b>№</b></td>
<td valign="middle" align="center"><b>Ник</b></td>
<td align="center" valign="middle"><b>Очки</b></td>
</tr>
<tr><td colspan=3>
<?php

for($j=0; $j < $MaxHits; $j++) {
	echo "<tr><td>".($j+1).".&nbsp;&nbsp;</td><td>".$reiting_rez[$j]["nick"]."&nbsp;</td><td>".$reiting_rez[$j]["points"]."</td></tr>";
}
?>
<tr><td colspan=3>
<a href=/top100.html>TOP100</a>&nbsp;&nbsp;&nbsp;<a href=/top20.html>TOP20</a>
</td></tr>
</table>
</body></html>