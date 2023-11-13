<?php if (!defined("_COMMON_")) {echo "stop";exit;}
if ($IsModer == 1 and $IP != "")
{
if (substr($IP, 0, 1) == "p") $IP = substr($IP, 1);

include("whois/main.whois");
$domain = $IP;

$pos = strpos($IP, ":");

// Note our use of ===.  Simply == would not work as expected
// because the position of 'a' was the 0th (first) character.
if ($pos === false) {
    }
    else {
    $domain = substr($IP, 0, $pos);
}

$whois = new Whois($domain);
$result = $whois->Lookup();
	?>
<table cellspacing="0" cellpadding="10" border="0" id="resultTable" align ='center'>
<th colspan='2'><b><?php echo $result['regrinfo']['network']['host_name']; ?></b> (<?php echo $IP; ?>)</th>
<tr valign='top'><td><img src='<?php echo $current_design."img/1.gif"; ?>' width=32 height=32 border=0 alt=''><br></td>
<td align=right><b> <?php echo $result['regrinfo']['network']['inetnum']; ?></b><br>
<?php echo $result['regrinfo']['network']['name'];?><br>
<?php
if(is_array($result['regrinfo']['owner']['organization'])) {
    for($i = 0; $i < count($result['regrinfo']['owner']['organization']); $i++) {
		echo $result['regrinfo']['owner']['organization'][$i];
		echo "<br>";
	}
}
else echo $result['regrinfo']['owner']['organization']."<br>";
echo $result['regrinfo']['network']['country']; ?><br>
<br>
</td></tr>
<?php if ($result['regrinfo']['admin']['name'] != "") { ?>
<tr valign='top'><td><img src='<?php echo $current_design."img/2.gif"; ?>' width=32 height=32 border=0 alt=''><br></td>
<td align=right><b><?php echo $result['regrinfo']['admin']['name']; ?></b><br>
<?php
for($i = 0; $i < count($result['regrinfo']['admin']['address']); $i++) {
echo $result['regrinfo']['admin']['address'][$i];
echo "<br>";
}
if($result['regrinfo']['admin']['phone'] != "") {
	echo $result['regrinfo']['admin']['phone']."<br>";
}
if($result['regrinfo']['admin']['fax'] != "") {
	echo $result['regrinfo']['admin']['fax']."<br>";
}
 ?>
<br>
<?php if($result['regrinfo']['admin']['email'] != "") {
?>
<a href='mailto:<?php echo $result['regrinfo']['admin']['email']; ?>'><?php echo $result['regrinfo']['admin']['email']; ?></a><br>
<?php
}
 ?>
</td></tr>
<?php
}
?>
<?php if ($result['regrinfo']['tech']['name'] != "" and $result['regrinfo']['tech']['name'] != $result['regrinfo']['admin']['name']) { ?>
<tr valign='top'><td><img src='<?php echo $current_design."img/2.gif"; ?>' width=32 height=32 border=0 alt=''><br></td>
<td align=right><b><?php echo $result['regrinfo']['tech']['name']; ?></b><br>
<?php
for($i = 0; $i < count($result['regrinfo']['tech']['address']); $i++) {
echo $result['regrinfo']['tech']['address'][$i];
echo "<br>";
}
if($result['regrinfo']['tech']['phone'] != "") {
	echo $result['regrinfo']['tech']['phone']."<br>";
}
if($result['regrinfo']['tech']['fax'] != "") {
	echo $result['regrinfo']['tech']['fax']."<br>";
}
if($result['regrinfo']['tech']['email'] != "") {
?>
<a href='mailto:<?php echo $result['regrinfo']['tech']['email']; ?>'><?php echo $result['regrinfo']['tech']['email']; ?></a><br>
<?php
}
 ?>
</td></tr>
<?php
}
?>
</table>
<?php
}
?>
