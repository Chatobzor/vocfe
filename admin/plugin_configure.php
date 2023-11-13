<?php
include("check_session.php");
include("../inc_common.php");
include("header.php");
include("config_parser.php");
set_variable("plugin");

if(is_file($file_path."plugins/".$plugin."/config.php")) {
   include($file_path."plugins/".$plugin."/config.php");
} else {
?>
<p>
<span class=tip><font size="5" color=black><?php echo str_replace("#", $plugin, $adm_plugin_not_found); ?>.</font></span></center>
</body>
</html>
<?
exit;
}

set_variable("operation");


if($operation == "modify_plugin") {
  $cfg1 = new VOCPlugin_Config;
  $cfg1->parseFile($file_path."plugins/".$plugin."/config.php");

  for($i = 0; $i < count($cfg1->cfgVars); $i++) {
      if(($cfg1->cfgVars[$i]["type"] == "variable" or $cfg1->cfgVars[$i]["type"] == "definition") and
         strpos($cfg1->cfgVars[$i]["name"], "VOCPlugin") === false) {
         set_variable("var_".$cfg1->cfgVars[$i]["name"]);
         $check_name = "var_".$cfg1->cfgVars[$i]["name"];

         if(trim($$check_name) != $cfg1->cfgVars[$i]["value"]) $cfg1->cfgVars[$i]["value"] = trim($$check_name);
      }
  }
  $cfg1->writeFile();
  echo "<center><b style=\"font-family:Verdana\">$adm_saving_data: OK</b></center>\n";
  echo "</body></html>";
  exit;
}

echo "<nobr><center><h4 style=\"color:#265D92;font-family:Verdana\">$adm_configuration";
if($VOCPlugin_Layout == "YES") {
  echo "<br><a href=\"".$chat_url."plugins/".$plugin."/layout.php?session=$session&lang=$lang\" style=\"color:#265D92;font-family:Verdana\"><h4>$adm_plugin_more</h4></a>";
}
echo "</h4></nobr></center>\n";
$cfg = new VOCPlugin_Config;
$cfg->parseFile($file_path."plugins/".$plugin."/config.php");
?>
<center>
<form action="plugin_configure.php" method=POST>
<input type="hidden" name="session" value="<?php echo $session; ?>">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<input type="hidden" name="plugin" value="<?php echo $plugin; ?>">
<input type="hidden" name="operation" value="modify_plugin">
<table width="90%" cellpadding=4 cellspacing=0>
<?php
  for($i=0; $i < count($cfg->cfgVars); $i++) {
      if(($cfg->cfgVars[$i]["type"] == "variable" or $cfg->cfgVars[$i]["type"] == "definition") and
         strpos($cfg->cfgVars[$i]["name"], "VOCPlugin") === false) {
  ?>
<tr><td align=RIGHT><?php echo $cfg->cfgVars[$i]["name"].":" ?></td><td><input type=text size=50 class="input" name="var_<?php echo $cfg->cfgVars[$i]["name"];?>" value="<?php  echo str_replace("\"","&quot;",$cfg->cfgVars[$i]["value"]); ?>"></td></tr>
<?php }
 } ?>
</table>
 <input type=submit class=button value=" OK ">
 </form>

</body>
</html>