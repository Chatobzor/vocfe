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
set_variable("plugin_".$plugin);

$check_name = "plugin_".$plugin;

if($operation == "modify_general") {
  $cfg = new VOCPlugin_Config;
  $cfg->parseFile($file_path."plugins/".$plugin."/config.php");

  for($i = 0; $i < count($cfg->cfgVars); $i++) {
      if($cfg->cfgVars[$i]["type"] == "variable" and $cfg->cfgVars[$i]["name"] == "VOCPlugin_Enabled") {
         if(intval($$check_name) == 1)  $cfg->cfgVars[$i]["value"] = "YES";
         else $cfg->cfgVars[$i]["value"] = "NO";
         $VOCPlugin_Enabled = $cfg->cfgVars[$i]["value"];
         break;
      }
  }
  $cfg->writeFile();
}

echo "<center><h2 style=\"color:#265D92;font-family:Verdana\">$VOCPlugin_Name / $VOCPlugin_Language ($VOCPlugin_Version)</h2></center>\n";
?>
<center><table width="90%" cellpadding=4 cellspacing=0>
<tr><td align=RIGHT><?php echo $adm_plugin_language; ?>:</td><td><b><?php echo $VOCPlugin_Language; ?></b></td></tr>
<tr><td align=RIGHT><?php echo $adm_plugin_author; ?>:</td><td><b><?php echo $VOCPlugin_Author; ?></b></td></tr>
<tr><td align=RIGHT><?php echo $adm_plugin_description; ?>:</td><td><b><?php echo $VOCPlugin_Description; ?></b></td></tr>
<tr><td align=RIGHT><?php echo $adm_plugin_eng_supp; ?>:</td><td><b><?php echo $VOCPlugin_Eng_Suppoted; ?></b></td></tr>
<form action="plugin_view.php" method=POST>
<input type="hidden" name="session" value="<?php echo $session; ?>">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<input type="hidden" name="plugin" value="<?php echo $plugin; ?>">
<input type="hidden" name="operation" value="modify_general">
<tr><td align=RIGHT><input type=checkbox name="plugin_<?php echo $plugin;?>" value=1 <?php if($VOCPlugin_Enabled == "YES") echo "checked"; ?>></td><td><b><?php echo $adm_plugin_enabled;?> <input type="submit" class=button_small value=" OK "></b></td></tr>
</form>
</table>
</body>
</html>