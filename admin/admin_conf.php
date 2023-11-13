<?php $configuration = 1;
include "check_session.php";
include "../inc_to_canon_nick.php";
define("_VOC_CONFIG_",1);
include("header.php");
?>
<table cellpadding="3"><tr><td>
<?
if (!isset($step)) $step = 0;
else $step = intval($step);
if (!isset($save_level)) $save_level = 0;
if ($step >0){
	include "../inc_common.php";
	include("configure/inc_cfg_save_voc.php");
}
error_reporting(E_ALL);
clearstatcache();
$error = 0;


switch ($step)
{
	case 0:
		echo "<h3>$adm_main_pathes</h3>";
		echo "<form method=\"post\" action=\"admin_conf.php\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
		include("configure/inc_cfg_step_1.php");

		echo "<input type=\"submit\" value=\"$adm_save\" class=\"button\">";
		if (!$error)
			echo "  <b>$adm_looks_ok</b>";
		echo "<input type=\"hidden\" name=\"step\" value=\"0\">";
		echo "<input type=\"hidden\" name=\"save_level\" value =\"1\">";
		echo "</form>";
	break; //end of case 0
	case 1:
		echo "<h3>$adm_daemon_settings</h3>";
		echo "<form method=\"post\" action=\"admin_conf.php\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
		include("configure/inc_cfg_daemon.php");
		echo "<input type=\"hidden\" name=\"step\" value=\"1\">";
		echo "<input type=\"hidden\" name=\"save_level\" value =\"2\">";
		echo "<input type=\"submit\" value=\"$adm_save\" class=\"button\">";
		echo "</form>";
	break;
	case 2:
		echo "<h3>$adm_engines</h3>";
		echo "<form method=\"post\" action=\"admin_conf.php\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
		include("configure/inc_cfg_engine.php");
		echo "<input type=\"hidden\" name=\"step\" value=\"2\">";
		echo "<input type=\"hidden\" name=\"save_level\" value =\"3\">";
		echo "<input type=\"submit\" value=\"$adm_save\" class=\"button\">";
		echo "</form>";
	break;
	case 3:
		echo "<h3>$adm_options_and_lim</h3>";
		echo "<form method=\"post\" action=\"admin_conf.php\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
		include("configure/inc_cfg_limits.php");
		echo "<input type=\"hidden\" name=\"step\" value=\"3\">";
		echo "<input type=\"hidden\" name=\"save_level\" value =\"4\">";
		echo "<input type=\"submit\" value=\"$adm_save\" class=\"button\">";
		echo "</form>";
	break;
	case 4:
		echo "<h3>$adm_user_access_lim</h3>";
		echo "<form method=\"post\" action=\"admin_conf.php\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
		include("configure/inc_cfg_access.php");
		echo "<input type=\"hidden\" name=\"step\" value=\"4\">";
		echo "<input type=\"hidden\" name=\"save_level\" value =\"5\">";
		echo "<input type=\"submit\" value=\"$adm_save\" class=\"button\">";
		echo "</form>";
	break;
	case 5:
		echo "<h3>$adm_add_features</h3>";
		echo "<form method=\"post\" action=\"admin_conf.php\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
		include("configure/inc_cfg_features.php");
		echo "<input type=\"hidden\" name=\"step\" value=\"5\">";
		echo "<input type=\"hidden\" name=\"save_level\" value =\"6\">";
		echo "<input type=\"submit\" value=\"$adm_save\" class=\"button\">";
		echo "</form>";
	break;
	case 6:
		echo "<h3>$adm_look_and_feel</h3>";
		echo "<form method=\"post\" action=\"admin_conf.php\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\">";
		include("configure/inc_cfg_lookfeel.php");
		echo "<input type=\"hidden\" name=\"step\" value=\"6\">";
		echo "<input type=\"hidden\" name=\"save_level\" value =\"7\">";
		echo "<input type=\"submit\" value=\"$adm_save\" class=\"button\">";
		echo "</form>";
	break;

}

?>
</td></tr></table>
</body>
</html>