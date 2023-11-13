<?php include("check_session.php");?>
<?php
///////////////////
// File name: conv.php
// Author: Emin Sadykhov aka Sirius
// Questions to emin@azrabita.net
////////////////////

include("header.php");
include("../inc_common.php");
$int_smiles = $file_path."converts/";
$ext_smiles = $images_url."converts/";
/* commented by Voodoo -- I've include the same code into the check_session.php
if (isset($HTTP_POST_VARS))
{
while(list($name,$value) = each($HTTP_POST_VARS))
{
$$name = $value;
};
};*/

/// DD modification start
/// We must load the DBMS driver
set_variable("url");
set_variable("com_sm_name");

if(!isset($DbLink)) {
        include_once("config.php");
        define("C_DB_NAME", DB_NAME);
        define("C_DB_USER", DB_USER);
        define("C_DB_PASS", DB_PASS);
        include_once("mysql.lib.php3");
        $DbLink = new DB;
}
/// DD modification end

if(!isset($action)) $action = "";
if(!isset($see_again)) $see_again = "";

if ($action == "make_common") {
        $DbLink->query("DELETE FROM smileys WHERE s_name = '$com_sm_name' AND uid = '-1';");
          $DbLink->query("INSERT INTO smileys (s_name, url, uid) VALUES ('$com_sm_name','$url',-1);");
}

if ($action == "unset_common") {
        $DbLink->query("DELETE FROM smileys WHERE s_name = '$com_sm_name' AND uid = '-1';");
}

$phrase = array();
if ($action == "convert")
{
$snum=0;
    for ($i=0; $i<=$filenumber;$i++)
   {
        if (!empty($s[$i]))
        {
        $s[$i]=str_replace("\t","",$s[$i]);
        $path[$i] = str_replace("\t","", $path[$i]);

        $tag_b = "<img src=\"";
        $tag_e = "\" border=\"0\">";
        $path[$i] = $tag_b.$path[$i].$tag_e;
        $phrase[count($phrase)] = $s[$i]."\t".$path[$i];

        if(trim($s[$i]) == "") {
                $DbLink->query("DELETE FROM smileys WHERE url = '".$path[$i]."'");
        }
        else {
             $DbLink->query("UPDATE smileys SET s_name = '".$s[$i]."' WHERE url = '".$path[$i]."'");
        }
        $fp = fopen($converts_file,"w");
        fwrite($fp, implode("\n", $phrase));
        $snum++;
        fclose($fp);
     }
}
echo "<center><font size=5 face=\"Verdana, Tahoma\" color=\"#265D92\" align=CENTER>$adm_congratulations!</font><br>";
echo "<font size=\"2\" color=\"black\" face=\"Verdana\">".$snum." $adm_smileys_writed</font><br></center>";
if ($see_again != "on")
{
exit;
}
}
?>
<div align=center><font size=5 face="Verdana, Tahoma" color="#265D92" align=CENTER><b><?php echo $adm_sm_convert; ?></b></font></div>
<center><font size="1" color="red" face="Verdana"><b><?php echo $adm_sm_note." (".$file_path."converts)";?></b><br></font></center>
<blockquote><font size="2" color="black" face="Verdana">
<? echo $adm_sm_instructions ;?>
</font></blockquote>
<?php

/// DD modification start
// initally (re)load the common smileyset
$DbLink->query("SELECT s_name, url FROM smileys WHERE uid = '-1';");
$MaxSmileys = $DbLink->num_rows();

if(isset($SmTbl)) unset($SmTbl);

for($i = 0;$i<$MaxSmileys; $i++) {
        list($name, $url) = $DbLink->next_record();
        $SmTbl[$i]["name"] =  $name;
          $SmTbl[$i]["url"] =  $url;
}
/// DD modification end

$handle=opendir("$int_smiles");
$filenumber = 0;

while (false!==($file = readdir($handle))) {
   if ($file != "." && $file != ".." && $file != "to_remove") {
       $smilefile[$filenumber] = substr($file,0,strpos($file,'.'));
       $filenumber++;
   }
}
closedir($handle);

if ($filenumber == 0)
{
echo "<br><br><span class=head>$adm_nofiles</span><br><br>";
die;
}

echo "<form method=\"post\" action=\"conv.php?action=convert\">\n";
echo "<input type=\"hidden\" name=\"lang\" value=\"".$lang."\"><center>\n";
echo "<input type=\"hidden\" name=\"session\" value=\"".$session."\"><center>\n";
echo "<span class=dat>".$filenumber."</span> <span class=mes>$adm_smileys_in_dir</span></center>";
/// DD modification start (added a 'Common' column)
echo "<table align=CENTER width=90% border=1 cellpadding=4 cellspacing=0 bordercolor=#265D92><tr align=center class=mes><td width=10 bgcolor=#265D92><font color=White>¹</font></td><td bgcolor=#265D92><font color=White>$adm_status</font></td><td bgcolor=#265D92><font color=White>$adm_smile</font></td><td bgcolor=#265D92><font color=White>$adm_smile_promt</font></td><td bgcolor=#265D92><font color=White>$adm_smile_common</font></td></tr>";
/// DD modification end

//added block
$file = file("$converts_file");
$count = count($file);
if ($count != 0)
{
for ($j = 0; $j <$count; $j++)
{
list ($sm[$j],$pt[$j]) = explode ("\t",$file[$j]);
}
for ($i = 0; $i < $filenumber; $i++)
{
  $tag_b = "<img src=\"";
  $tag_e = "\" border=\"0\">";
  $nowfile[$i]=$tag_b.$ext_smiles.$smilefile[$i].".gif".$tag_e;
  $old=0;
  $i_for_user=$i+1;
  for ($j = 0; $j <$count; $j++)
  {

   if (trim($pt[$j]) == trim($nowfile[$i]))
   {
    echo "<tr align=center class=mes><td width=10>".$i_for_user."</td><td>$adm_status_old</td>
         <td>
         <img src=\"".$ext_smiles.$smilefile[$i].".gif\"
         border=\"0\"></td><td>
         <input type=text name=\"s[".$i."]\" value=\"".trim($sm[$j])."\" class=input>
         <input type=hidden name=\"path[".$i."]\"value=\"".$ext_smiles.$smilefile[$i].".gif\"></td><td>";
         /// DD modification start
         $IsFound = 0;
         for($sm_i = 0; $sm_i < count($SmTbl); $sm_i++) {
                        if($SmTbl[$sm_i]["name"] == trim($sm[$j])) {
                          echo "$adm_smile_yes (<a href='conv.php?lang=$lang&action=unset_common&session=$session&com_sm_name=".trim($sm[$j])."&url=".$ext_smiles.$smilefile[$i].".gif'>$adm_smile_undo</a>)";
              $IsFound = 1; break;
            }
          }

         if(!$IsFound) {
              echo "$adm_smile_no  (<a href='conv.php?lang=$lang&action=make_common&session=$session&com_sm_name=".trim($sm[$j])."&url=".$ext_smiles.$smilefile[$i].".gif'>$adm_smile_make</a>)";
         }
         echo "</td></tr>";
         // DD modification end
         $old=1;
    }
  }
  if ($old == 0)
  {
  echo "<tr align=center class=new><td width=10>".$i_for_user."</td><td>$adm_status_new</td>
  <td>
  <img src=\"".$ext_smiles.$smilefile[$i].".gif\"
  border=\"0\"></td><td>
  <input type=text name=\"s[".$i."]\" value=\"*".$smilefile[$i]."*\" class=input>
  <input type=hidden name=\"path[".$i."]\"
  value=\"".$ext_smiles.$smilefile[$i].".gif\"></td><td>";

 /// DD modification start
 /*
         for($sm_i = 0; $sm_i < count($SmTbl); $sm_i++) {
                        if($SmTbl[$sm_i] == $smilefile[$j]) {
                          echo "YES (<a href='conv.php?action=unset_common&session=$session&com_sm_name=*".$smilefile[$i]."&url=".$ext_smiles.$smilefile[$i].".gif'>unset</a>)";
            }
            else {
              echo "NO (<a href='conv.php?action=make_common&session=$session&com_sm_name=*".$smilefile[$i]."&url=".$ext_smiles.$smilefile[$i].".gif'>make</a>)";
            }
         }
   */
         echo "</td></tr>";
  // DD modification end

  echo "</tr>";
  }
}
}
else
{
for ($i = 0; $i < $filenumber; $i++)
{
$i_for_user=$i+1;
echo "<tr align=center class=new><td width=10>".$i_for_user."</td><td>$adm_status_new</td>
<td>
<img src=\"".$ext_smiles.$smilefile[$i].".gif\"
border=\"0\"></td><td>
<input type=text name=\"s[".$i."]\" value=\"*".$smilefile[$i]."*\">
<input type=hidden name=\"path[".$i."]\"
value=\"".$ext_smiles.$smilefile[$i].".gif\"></td><td>";
//echo "NO (<a href='conv.php?action=make_common&session=$session&com_sm_name=".$smilefile[$i]."&url=".$ext_smiles.$smilefile[$i].".gif'>make</a>)";
echo "</td></tr>";
}

}
$i=$j=0;
echo "<tr align=center class=new><td colspan=5><input class=box type=\"checkbox\" name=\"see_again\" checked style=\"width:20px\"> <span class=mes>$adm_smile_see_again</span></td></tr></table>
<input type=\"hidden\" name=\"filenumber\" value=\"".$filenumber."\"><center><br>
<input type=\"Submit\" value=\"$adm_smile_save_upl\" class=button_small></center>\n";
echo "</form>\n";
?>
</body>
</html>