<?php
include("check_session.php");
include("header.php");
include("../inc_common.php");
if ($long_life_data_engine == "mysql")
{
	include_once($ld_engine_path."inc_connect.php");
}
function get_probability($id, $prob = 5)
{
	$html = "<td><select name=\"prob$id\" class=dd>\n";
	for ($i=0;$i<11;$i++)
	{
		$html .= "<option value=$i";
		if ($i == $prob) $html .= " selected";
		$html .= ">$i</option>\n";
	}
	$html .= "</select></td></tr>\n";
	return $html;
}

if (isset($maxID))  {
	unset($phrases);
	$phrases = array();
	for ($i=0; $i<=$maxID;$i++) {
		$tmpPhrase="phrase" . $i;
		$phrase = $$tmpPhrase;
		$phrase = str_replace("\t","",$phrase);
		$tmpAnswer = "answer" .$i;
		$answer = $$tmpAnswer;
		$answer = str_replace("\t","", $answer);
		$tmpProb = "prob".$i;
		$prob = intval($$tmpProb);
		if (get_magic_quotes_gpc()) {
			$phrase = stripslashes($phrase);
			$answer = stripslashes($answer);
		}
		if (($phrase != "") and ($answer != "")) {
			if ($long_life_data_engine == "files") $phrases[count($phrases)] = $phrase."\t".$answer."\t".$prob;
			else if ($long_life_data_engine == "mysql") $phrases[count($phrases)] = "('".addslashes($phrase)."', '".addslashes($answer)."','".addslashes($prob)."')";
		}
	}
	if ($long_life_data_engine == "files") {
		$fp = fopen($robotspeak_file,"w");
		fwrite($fp, implode("\n", $phrases));
		fclose($fp);
	} else if ($long_life_data_engine == "mysql") {
		mysql_query("delete from ".$mysql_table_prefix."robotspeak") or die("database error<br>".mysql_error());
		echo count($phrases);
		if (count($phrases))
			mysql_query("insert into ".$mysql_table_prefix."robotspeak values ".implode(", ",$phrases)) or die("database error<br>".mysql_error());
	}
}


?>
<center><h2 style="color:#265D92;font-family:Verdana"><?php echo $adm_robik_class;  ?></h2>
<font size="1" color="red" face="Verdana"><b><?php echo $adm_rob_note; ?></b></font>
<form method="post" action="cr.php">
<input type="hidden" name="session" value="<?php echo $session;?>">
<table width=100% border=0 cellpadding=0 cellspacing=0><tr align=center><td valign=top>
<table width=90% border=1 cellpadding=4 cellspacing=0 bordercolor=#CCCCCC><tr align=center class=mes><td bgcolor=#666666 align=CENTER><font color=White>¹</font></td><td bgcolor=#666666><font color=White><?php echo $adm_rob_questions; ?></font></td><td bgcolor=#666666><font color=White><?php echo $adm_rob_answers; ?></font<</td><td bgcolor=#666666><font color=White><?php echo $adm_rob_probability; ?></font></td></tr>
<?php
$ID = 0;
$stroka = "";
if ($long_life_data_engine == "files") {
	$phrases = file($robotspeak_file);
	for ($i=0;$i<count($phrases); $i++){
		$phrase = str_replace("\n","",$phrases[$i]);
		list($user_phrase, $robot_answer, $prob) = split("\t", $phrase);
		$user_phrase = str_replace("\"","&quot;", $user_phrase);
		$robot_answer = str_replace("\"","&quot;", $robot_answer);
		$stroka .= "<tr align=center class=mes><td width=10>".$ID."</td><td><input type=\"text\" name=\"phrase".$ID."\" value=\"".$user_phrase."\" class=input></td><td><input type=\"text\" name=\"answer".$ID."\" value=\"".$robot_answer."\" class=input></td>";
		$stroka .= get_probability($ID, $prob);
		$ID++;
	}
} else if ($long_life_data_engine == "mysql") {
	$m_result = mysql_query("select * from ".$mysql_table_prefix."robotspeak") or die("database error<br>".mysql_error());
	while ($row = mysql_fetch_row($m_result)) {
		$user_phrase = str_replace("\"","&quot;", $row[0]);
		$robot_answer = str_replace("\"","&quot;", $row[1]);
		$stroka .= "<tr align=center class=mes><td width=10>".$ID."</td><td><input type=\"text\" name=\"phrase".$ID."\" value=\"".$user_phrase."\" class=input></td><td><input type=\"text\" name=\"answer".$ID."\" value=\"".$robot_answer."\" class=input></td>";
		$stroka .= get_probability($ID, $row[2]);
		$ID++;
	}
}

	echo "<tr align=center class=mes><td width=10>$adm_new_word</td><td><input type=\"text\" name=\"phrase$ID\" class=input></td><td><input type=\"text\" name=\"answer$ID\" class=input></td>";
	echo get_probability($ID);

    echo $stroka;

	echo "</table>\n";
	echo "<br><input type=\"hidden\" name=\"maxID\" value=\"$ID\">";
   	echo "<input type=\"hidden\" value=\"$lang\" name=\"lang\">\n";
	echo "<input type=\"reset\" value=\"$adm_reset\" class=button> &nbsp; <input type=\"Submit\" value=\"$adm_save\" class=button>\n";
	echo "</form>\n";
?>
</td><tr><table>
</center>
</body></html>