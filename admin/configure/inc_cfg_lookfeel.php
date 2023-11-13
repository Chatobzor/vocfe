<?php
if (!defined("_VOC_CONFIG_")) {echo "stop";exit;}
		echo "<input type=\"hidden\" name=\"session\" value=\"".$session."\">";
        echo "<input type=\"hidden\" name=\"lang\" value=\"".$lang."\">";
		echo "<table border=\"0\" width=\"500\">";

		echo "<tr><td>$adm_system_language: </td><td>";
		$handle = opendir($file_path."languages/");
		if (!is_array($allowed_langs)) $allowed_langs = array();
		$al_langs = "<select name=\"allowed_langs[]\" class=\"input\" multiple>";

		echo "<select name=\"language\" class=\"input\">";
		while (false !== ($tmp_file = readdir($handle))) {
			if (substr($tmp_file,0,4)!="help" and is_file($file_path."languages/".$tmp_file)) {
				$lang_name = substr($tmp_file,0,strpos($tmp_file,"."));
				echo "<option value=\"$lang_name\"";
				if($lang_name == $language) echo " selected";
				echo ">$lang_name</option>\n";
				$al_langs .= "<option value=\"".$lang_name."\"";
				if (in_array($lang_name, $allowed_langs)) $al_langs .= " selected";
				$al_langs .= ">".$lang_name."</option>\n";
			}
		}
		echo "</select></td></tr>";
		closedir($handle);
		echo "<tr><td colspan=\"2\" class=tip>*$adm_sys_lang_note.</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td>$adm_avail_lang:</td><td>";
		echo $al_langs."</td></tr>";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_avail_lang_note.</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td>$adm_charset	: </td><td><input type=\"text\" name=\"charset\" class=\"input\"size=\"10\" value=\"".str_replace("\"","&quot;", $charset)."\"></td></tr>";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_charset_note.</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		$handle = opendir($file_path."designes/");
		echo "<tr><td>$adm_chat_designes: </td><td>";
		echo "<select name=\"designes[]\" multiple class=\"input\">";

		$conf_def_design = "<select name=\"default_design\" class=\"input\">\n";
		while (false !== ($tmp_file = readdir($handle)))
		{
			if ($tmp_file!="." and $tmp_file != "..")
			{
				$conf_def_design .= "<option value=\"$tmp_file\"";
				if($tmp_file == $default_design) $conf_def_design .= " selected";
				$conf_def_design .= ">$tmp_file</option>\n";

				echo "<option value=\"$tmp_file\"";
				if (in_array($tmp_file,$designes)) echo " selected";
				echo ">$tmp_file</option>\n";
			}
		}
		$conf_def_design .= "</select>";
		closedir($handle);
		echo "</select></td></tr>\n";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_designes_note.</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td><nobr>$adm_default_design: </nobr></td><td>$conf_def_design</td></tr>\n";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_def_des_note.</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

       	echo "<tr><td colspan=2><input type=\"checkbox\" value=1 name=\"allow_multiply\" ";
        if($allow_multiply) echo "checked";
        echo ">";
		echo $adm_allow_multipl.".</td></tr>";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_allow_mul_note.</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td colspan=\"2\"><input type=\"hidden\" name=\"priv_frame\" value=\"1\"";
		echo "></td></tr>";
		echo "<tr><td colspan=\"2\">$adm_show_sp_note.".
			"</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td>$adm_keep_whispering: </td><td><select name=\"keep_whisper\" class=\"input\">";
		echo "<option value=\"0\"";
		if (!$keep_whisper) echo " selected";
		echo ">$adm_no</option>";
		echo "<option value=\"1\"";
		if ($keep_whisper) echo " selected";
		echo ">$adm_yes</option>";
		echo "</select></td></tr>";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_keep_note</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td>$adm_user_color: </td><td><select class=\"input\" name=\"colorize_nicks\">";
		echo "<option value=\"0\"";if (!$colorize_nicks)echo " selected";echo ">$adm_no</option>\n";
		echo "<option value=\"1\"";if ($colorize_nicks)echo " selected";echo ">$adm_yes</option>\n";
		echo "</select></td></tr>\n";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_user_color_note</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td colspan=\"2\">$adm_message_formats:</td></tr>";
		echo "<tr><td colspan=\"2\">".
			"<table border=\"0\"><tr><td><b>$adm_code</b></td><td><b>$adm_action</b></td></tr>".
			"<tr><td>[MESSAGE]</td><td>$adm_act_msg_note;</td></tr>".
			"<tr><td>[NICK]</td><td>$adm_act_nick_note;</td></tr>".
			"<tr><td>[NICK_WO_TAGS]</td><td>$adm_act_nick_wo_tag;</td></tr>".
			"<tr><td>[TO]</td><td>$adm_act_to_note;</td></tr>".
			"<tr><td>[PRIVATE]</td><td>$adm_private_note;</td></tr>".
			"<tr><td>[HOURS]</td><td>$adm_hours_time_note;</td></tr>".
			"<tr><td>[MIN]</td><td>$adm_mins_time_note;</td></tr>".
			"<tr><td>[SEC]</td><td>$adm_mins_time_note;</td></tr>".
			"<tr><td>[AVATAR]</td><td>$adm_avatar_note.</td></tr>".
			"</table></td></tr>";


		echo "<tr><td colspan=\"2\">$adm_normal_message:</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"message_fromme\" value=\"".str_replace("\"","&quot;",$message_fromme)."\"><br>";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";


		echo "<tr><td colspan=\"2\">$adm_private_author:</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"private_message_fromme\" value=\"".str_replace("\"","&quot;",$private_message_fromme)."\"><br>";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";


		echo "<tr><td colspan=\"2\">$adm_normal_all:</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"message_format\" value=\"".str_replace("\"","&quot;",$message_format)."\"><br>";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td colspan=\"2\">$adm_private_to:</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"private_message\" value=\"".str_replace("\"","&quot;",$private_message)."\"><br>";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td colspan=\"2\">$adm_whisper_to_som:</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input type=\"text\" size=\"55\" class=\"input\" name=\"private_hidden\" value=\"".str_replace("\"","&quot;",$private_hidden)."\"><br>";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_whisper_note.</td></tr>\n";
        echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
        ?>
<script language="Javascript">
<!--
function set_mf_simple() {
	if (confirm('<?php echo $adm_format_reset;?> ("<?php echo $adm_reset_simple;?>")?')) {
		with (document.forms[0]) {
			message_fromme.value = '<small>([HOURS]:[MIN]:[SEC])</small> <b>[NICK]</b>: [MESSAGE]';
			message_format.value = message_fromme.value;
			private_message_fromme.value = '<small>([HOURS]:[MIN]:[SEC])</small> <b>[[NICK] -&gt; [TO]]</b>: [MESSAGE]';
			private_message.value = private_message_fromme.value;
			private_hidden.value = '<small>([HOURS]:[MIN]:[SEC])</small> <b>[NICK]</b>: <i><small> [PRIVATE] </small></i>';
		}
	}
}


function set_mf_clickable() {
	if (confirm('<?php echo $adm_format_reset;?> ("<?php echo $adm_clickable_nicks;?>")?')) {
		with (document.forms[0]) {
			message_fromme.value = '<small><a style=\'{cursor: pointer}\' onClick="javascript:parent.addPic(\' див. [HOURS]:[MIN]:[SEC] \');">[HOURS]:[MIN]:[SEC]</a></small><span class="hu"><a style=\'text-decoration: underline\' style=\'{cursor: pointer}\' onClick="javascript:parent.Whisper(\'[NICK_WO_TAGS]\');"><b>[NICK]</b></a>: [MESSAGE]</span>';
			message_format.value = '<small><a style=\'{cursor: pointer}\' onClick="javascript:parent.addPic(\' див. [HOURS]:[MIN]:[SEC] \');">[HOURS]:[MIN]:[SEC]</a></small><a style=\'text-decoration: underline\' style=\'{cursor: pointer}\' onClick="javascript:parent.Whisper(\'[NICK_WO_TAGS]\');"><b>[NICK]</b></a>: [MESSAGE]';
			private_message_fromme.value = '<small><a style=\'{cursor: pointer}\' onClick="javascript:parent.addPic(\' див. [HOURS]:[MIN]:[SEC] \');">[HOURS]:[MIN]:[SEC]</a></small> <b>[<a style=\'text-decoration: none\' style=\'{cursor: pointer}\' onClick="javascript:parent.Whisper(\'[NICK_WO_TAGS]\');">[NICK]</a>-><a style=\'text-decoration: none\' style=\'{cursor: pointer}\' onClick="javascript:parent.Whisper(\'[TO]\');">[TO]</a>]</b>: [MESSAGE]';
			private_message.value = private_message_fromme.value;
			private_hidden.value = '';
		}
	}
}
//-->
</script>
<tr><td colspan="2"><b><?php echo $adm_format_reset; ?></b>:<br>
<a href="javascript:set_mf_simple();"><?php echo $adm_reset_simple; ?></a><br>
<a href="javascript:set_mf_clickable();"><?php echo $adm_clickable_nicks; ?></a><br>
</td></tr>
        <?php
        echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "<tr><td colspan=\"2\">$adm_highligt_nick: </td></tr>";
		echo "<tr><td>$adm_tag_before: </td><td><input type=\"text\" class=\"input\" size=\"10\" name=\"nick_highlight_before\" value=\"".str_replace("\"","&quot;",$nick_highlight_before)."\"></td></tr>";
		echo "<tr><td>$adm_tag_after: </td><td><input type=\"text\" class=\"input\" size=\"10\" name=\"nick_highlight_after\" value=\"".str_replace("\"","&quot;",$nick_highlight_after)."\"></td></tr>\n";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_highlight_note</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td colspan=\"2\">$adm_high_inside: </td></tr>";
		echo "<tr><td>$adm_tag_before: </td><td><input type=\"text\" class=\"input\" size=\"10\" name=\"str_w_n_before\" value=\"".str_replace("\"","&quot;",$str_w_n_before)."\"></td></tr>";
		echo "<tr><td>$adm_tag_after: </td><td><input type=\"text\" class=\"input\" size=\"10\" name=\"str_w_n_after\" value=\"".str_replace("\"","&quot;",$str_w_n_after)."\"></td></tr>\n";
		echo "<tr><td colspan=\"2\" class=tip>*$adm_high_inside_not.</td></tr>";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";

		echo "<tr><td colspan=\"2\">$adm_enable_modify:</td></tr>";
		echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"enabled_b_style\"";
		if ($enabled_b_style == 1) echo " checked";
		echo "> $adm_enable_bold;</td></tr>";

		echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"enabled_i_style\"";
		if ($enabled_i_style == 1) echo " checked";
		echo "> $adm_enable_italic;</td></tr>";

		echo "<tr><td colspan=\"2\"><input type=\"checkbox\" name=\"enabled_u_style\"";
		if ($enabled_u_style == 1) echo " checked";
		echo "> $adm_enable_underlin.</td></tr>";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "</table>\n";
?>