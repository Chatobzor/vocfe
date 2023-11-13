<?php
include("inc_common.php");

define("REITING_MAXHITS", "50");

error_reporting(E_ALL);

function cmp_rei($a, $b)
{
   if($a["points"] > $b["points"]) return -1;
   else return 1;
}

$fp = fopen($data_path."similar_nicks.tmp", "r");
flock($fp, LOCK_EX);
fseek($fp,0);

if(isset($reiting_rez)) unset($reiting_rez);
$i = 0;

while ($data = fgets($fp, 4096)) {
        $user = str_replace("\r","",str_replace("\n","",$data));
        list($t_id, $t_nickname, $t_password, $t_email,$t_IP, $t_browser_hash, $t_cookie_hash, $t_points, $t_reiting) = explode("\t",$user);

       $t_id = intval(trim($t_id));

       if($t_points > 0 and $t_nickname != "") {
            $reiting_rez[$i]["nick"]   = $t_nickname;
            $reiting_rez[$i]["points"] = $t_reiting;
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
<td valign="middle" align="center"><b>Нік</b></td>
<td align="center" valign="middle"><b>Рейтинг</b></td>
</tr>
<tr><td colspan=3>
<?php

for($j=0; $j < $MaxHits; $j++) {
        echo "<tr><td>".($j+1).".&nbsp;&nbsp;</td><td>";
        if($j < 20) echo "<b>".$reiting_rez[$j]["nick"]."</b>";
        else echo $reiting_rez[$j]["nick"];
        echo "&nbsp;</td><td>".$reiting_rez[$j]["points"]."</td></tr>";
}
?>
<tr><td colspan=3>
</td></tr>
</table>