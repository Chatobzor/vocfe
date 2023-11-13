<?php
$lang = "";
while (list($var, $val) = each($_GET)) $$var  = $val;
while (list($var, $val) = each($_POST)) $$var  = $val;

//DD

if(!is_file("languages/".$lang.".php")) {
  if($lang == "") $lang="admin-ru";
  include("../inc_common.php");
  if(!is_file($file_path."admin/languages/".$lang.".php")) {
          echo "<font style=\"color:#666666;font-family:Verdana\"><b>No language packs detected! Please reinstall VOC++!</b></font>";
          exit;
  }
  else {
          chdir($file_path."admin");
          include($file_path."admin/languages/".$lang.".php");
  }
}
else include($file_path."languages/".$lang.".php");

$to_write = "";
$admin_exists = 0;
$admin_users = array();
if (isset($configuration))
{
        $script_name = "configure.php";
        $confword = $adm_configuration;
}
else
{
        $script_name = "index.php";
        $confword = $adm_administration;
}
$file = file("sessions.php");

$accepted  = (strpos($file[0],"accepted-voc++"))? 1:0;
unset($file);
if (isset($operation)) {
        if ($operation == "login") {
                include("admin_users.php");
                if ($login == "admin" and $password=="1234") {
                        include "header.php";
                        echo "<span class=desc>$adm_change_default</span>";
                        echo "</body></html>";
                        exit;
                }
                for ($i=0;$i<count($admin_users);$i++) {
                        if (($admin_users[$i]["nickname"] == $login) and($admin_users[$i]["password"] == $password)) {
                                $session = md5(uniqid(rand()));
                                $to_write = time()."\t".$session."\n";
                                $admin_exists = 1;
                        }
                }
        }else if ($operation == "accept") {
                $fp = fopen("sessions.php", "w") or die("$adm_cannot_open <b>sessions.php</b>");
                flock($fp, LOCK_EX);
                fwrite($fp,"<?php //accepted-voc++\n?>") or die("$adm_cannot_write <b>sessions.php</b>");
                fflush($fp);
                flock($fp, LOCK_UN);
                fclose($fp);
                $accepted = 1;
        }
}
if (isset($session))
{
        $fp = fopen("sessions.php", "a+") or die("$adm_cannot_open <b>sessions.php</b>");
        flock($fp, LOCK_EX);
        fseek($fp,0);
        while($data = fgetcsv ($fp, 1000, "\t") )
        {
                if (($data[0]+600) > time())
                {
                        $data[1] = str_replace("\r","",str_replace("\n","",$data[1]));
                        if ($session == $data[1])
                        {
                                $admin_exists = 1;
                                $to_write .= time()."\t".$data[1]."\n";
                        }
                        else $to_write .= $data[0]."\t".$data[1]."\n";
                }
        }
        ftruncate($fp,0);
        if ($accepted) fwrite($fp,"<?php //accepted-voc++\n".$to_write."?>") or die("$adm_cannot_write <b>sessions.php</b>");
        else fwrite($fp,"<?php\n".$to_write."?>") or die("$adm_cannot_write <b>sessions.php</b>");
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
}

#if (isset($operation))
#        if ($operation == "login")
#        {
#                header("location: $P");
#                exit;
#        }


if (!$admin_exists)
{
        include "header.php";
        if (!is_writeable("sessions.php")){
                echo "<span class=desc style=\"font-size:11px; color:#666666;font-family:Verdana\"><b>$adm_note:</b> $adm_permissions <b>sessions.php</b><br> $adm_webserver.<br></span>";
                echo "</body></html>";
                exit;
        }
        if (!$accepted) {
        include_once("theme3.php");
        RenderHeader();
    ?>
    <div align=CENTER style="font-size:11px; color:#666666;font-family:Verdana">
    <h2 align=center style="color: white;font-family: Georgia"><?php echo $confword; ?></h3>
<?php
                $handle = opendir($file_path."languages/");
                if (!is_array($allowed_langs)) $allowed_langs = array();
        $AtFirst = true;
                  while (false !== ($tmp_file = readdir($handle))) {
                        if (substr($tmp_file,0,4)!="help" and is_file($file_path."languages/".$tmp_file)) {
                    include("languages/".$tmp_file);
                       $lang_name = substr($tmp_file,0,strpos($tmp_file,"."));
                if($AtFirst) { echo " | "; $AtFirst = false;}
                              echo "<a href=\"$script_name?lang=$lang_name\"><b><font color=white>$adm_lang</font></b></a> | ";
                        }
                }
closedir($handle);
if($lang != "") include("languages/".$lang.".php");
?>
<p><span align=CENTER class=desc style="background-color:#990000"><font color=#FFFFFF><b><?php echo $adm_first_run; ?>.</b></font></span></div>
<div align=CENTER><form method="post" action="configure.php" target="_top">
        <input type="hidden" name="operation" value="accept">
        <input type="hidden" name="configuration" value="1">
          <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        <textarea cols= "60" rows="5" class="input">
        <?php readfile("./license");?>
        </textarea><br><br>
        <input type="submit" value="<?php echo $adm_accept; ?>" class="button"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input type="button" value="<?php echo $adm_dont_accept; ?>" onClick="javascript:window.location='http://vocplus.creatiff.com.ua/';" class="button">
        </form></div>
<?php
        RenderFooter();
                exit;
        }
        include("../inc_common.php");
        include_once("theme3.php");
        RenderHeader();
    ?>
    <div align=CENTER>
    <h2 align=center style="color: white; font-family: Georgia"><?php echo $confword; ?></h3><div align=CENTER style="color: white; font-family: Tahoma; font-size: 12px;">
<?php
      echo $adm_choose_language.': ';

                $handle = opendir($file_path."admin/languages/");
                if (!is_array($allowed_langs)) $allowed_langs = array();
        $AtFirst = true;
                  while (false !== ($tmp_file = readdir($handle))) {
                        if (substr($tmp_file,0,4)!="help" and is_file($file_path."admin/languages/".$tmp_file)) {
                    include($file_path."admin/languages/".$tmp_file);
                       $lang_name = substr($tmp_file,0,strpos($tmp_file,"."));
                if($AtFirst) { echo " | "; $AtFirst = false;}
                              echo "<a href=\"$script_name?lang=$lang_name\"><b><font color=white>$adm_lang</font></b></a> | ";
                        }
                }
closedir($handle);
if($lang != "") include("languages/".$lang.".php");
?>
</div>
<form method="post" action="<?php echo $script_name;?>" target="_top">
<input type="hidden" name="operation" value="login">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<Table Border="0" CellSpacing="0" CellPadding="2" bordercolor=#265D92>
<Tr><Td Width="300" align="center" colspan=2 class=desc><font color=#FFFFFF face="Tahoma, Verdana"><?php echo $adm_login_promt; ?></font></Td></Tr>
<Tr><Td Width="300" align="center" colspan=2 class=desc>&nbsp;</Td></Tr>
<Tr><Td Width="80" align="center" class=mes><?php echo $adm_login; ?>:</Td><Td Width="220" align="center"><input type="text" name="login" class=input></Td></Tr>
<Tr><Td Width="80" align="center" class=mes><?php echo $adm_password; ?>:</Td><Td Width="220" align="center"><input type="password" name="password" class=input></Td></Tr>
<Tr><Td Width="300" align="right" colspan=2><input type="submit" value="<?php echo $adm_login_do; ?>" class=button></Td></Tr>
</Table>
</form>
</div>
<?php
        RenderFooter();
exit();
}

?>