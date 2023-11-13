<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}
$error = 0;
$conf_file_path = realpath("../");

echo "<input type=\"hidden\" name=\"session\" value=\"$session\">\n";
echo "$adm_try_to_locate <b>inc_common.php</b>:<br>";
if (!isset($conf_inc_config)) $conf_inc_config = $conf_file_path."/inc_common.php";
$can_write = is_writeable($conf_inc_config);
$is_file = is_file($conf_inc_config);
if (!$is_file) {echo "<b>$adm__not_found:</b>";$error=1;} else echo "<b>$adm_found:</b>";
echo "&nbsp;<input type=\"text\" name=\"conf_inc_config\" value=\"$conf_inc_config\" class=\"input\" size=\"50\"><br>";
if  ($can_write)
	echo  "$adm_writeable, <b>Ok</b>";
else {
	echo "<b><font color=\"red\" size=\"+1\">$adm_cannot_write <b>inc_common.php</b></font></b>! $adm_webserver";
	$error = 1;
}
echo "<br>$adm_incorrect_tip.<hr>";


echo "$adm_try_to_locate <b>data</b>-$adm_directory:<br>";
$config_lines = @file($conf_inc_config);
if (!isset($conf_data_path))  {
	for ($i=0;$i<count($config_lines);$i++)
		if (strpos($config_lines[$i],"=")) {
			list($param,$value) = split("=",$config_lines[$i]);
			if (trim($param) == "\$data_path") eval($config_lines[$i]);
		}
	if (is_dir(realpath($data_path))) $conf_data_path = $data_path;
}
if (!isset($conf_data_path)) $conf_data_path = realpath($conf_file_path."/../data")."/";


$is_dir = is_dir($conf_data_path);
if (!$is_dir) {echo "<b><font color=\"red\" size=\"+1\">$adm_not_found:</font></b>";$error=1;} else echo "<b>$adm_found	:</b>";
echo "&nbsp;<input type=\"text\" name=\"conf_data_path\" value=\"$conf_data_path\" class=\"input\" size=\"50\"><br>";

echo "<br><b>$adm_note: $adm_slash_tip</b><br>$adm_incorrect_tip.<hr>";
echo "$adm_try_to_locate <b>voc.conf</b>:<br>\n";
$real_name =realpath($conf_data_path."voc.conf");
$can_write = is_writeable($real_name);
$is_file = is_file($real_name);
if (!$is_file) {echo "<b>voc.conf -- $adm_not_found:</b>";$error=1;} else echo "<b>$adm_found:</b>";
echo "&nbsp;".$conf_data_path."voc.conf<br>";
if  ($can_write)
	echo  "$adm_writeable, <b>Ok</b>";
else {
	echo "<b><font color=\"red\" size=\"+1\">$adm_cannot_write <b>voc.conf</b></font></b>! $adm_webserver";
	$error = 1;
}
echo "<hr>";

?>