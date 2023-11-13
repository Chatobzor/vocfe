<?php if (!defined("_COMMON_")) {echo "stop";exit;}
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");
include($engine_path."users_get_list.php");

// DD addon
define("LINES_PER_PAGE", 5);
define("SMILEYS_PER_LINE", 4);

if(!isset($DbLink)) {
include_once($file_path."admin/config.php");
define("C_DB_NAME", DB_NAME);
define("C_DB_USER", DB_USER);
define("C_DB_PASS", DB_PASS);
include_once($file_path."admin/mysql.lib.php3");
$DbLink = new DB;
}
/// DD modification start
// initally (re)load the common smileyset
?>
<script language="JavaScript">
function ReloadSmileBar() {
        if(window.opener != null) {
       if(window.opener.ico != null) window.opener.ico.location.reload();
    }
}
</script>
<?php
set_variable("action");
set_variable("com_sm_name");
set_variable("page");

$page = intval($page);

$UserUID = -1;

// prevent possible SQL injection
$com_sm_name = eregi_replace("'", "", $com_sm_name);
$com_sm_name = eregi_replace(";", "", $com_sm_name);
$com_sm_name = eregi_replace("SELECT", "", $com_sm_name);
$com_sm_name = eregi_replace("DELETE", "", $com_sm_name);
$com_sm_name = eregi_replace("UPDATE", "", $com_sm_name);
$com_sm_name = eregi_replace("ALTER", "", $com_sm_name);
$com_sm_name = htmlspecialchars($com_sm_name);

for ($i=0;$i<count($users);$i++) {
        $user_array = explode("\t",$users[$i]);
        if ($user_array[USER_SESSION] == $session) {
          $UserUID = $user_array[USER_REGID];
      break;
        }
}

if($UserUID != -1) {

if ($action == "make_common") {
        $DbLink->query("DELETE FROM smileys WHERE s_name = '$com_sm_name' AND uid = '$UserUID';");

    $url = "";
    for ($i=0;$i<count($pic_phrases);$i++) {
            if($pic_phrases[$i] == $com_sm_name) {
                $url = $pic_urls[$i];
            break;
        }
    }
    if($url != "") $DbLink->query("INSERT INTO smileys (s_name, url, uid) VALUES ('$com_sm_name','$url', '$UserUID');");
    ?><script>ReloadSmileBar();</script><?php
}

if ($action == "unset_common") {
        $DbLink->query("DELETE FROM smileys WHERE s_name = '$com_sm_name' AND uid = '$UserUID';");
}
// end of if if($UserUID != -1) {
}

// Loading the master set
$DbLink->query("SELECT s_name, url FROM smileys WHERE uid = '-1';");
$MaxSmileys = $DbLink->num_rows();

if(isset($SysSmTbl)) unset($SysSmTbl);

for($i = 0;$i<$MaxSmileys; $i++) {
        list($name, $url) = $DbLink->next_record();
        $SysSmTbl[$i]["name"] =  $name;
}

// Loading the user set
$DbLink->query("SELECT s_name, url FROM smileys WHERE uid = '$UserUID';");
$MaxSmileys = $DbLink->num_rows();

if(isset($SmTbl)) unset($SmTbl);

for($i = 0;$i<$MaxSmileys; $i++) {
        list($name, $url) = $DbLink->next_record();
        $SmTbl[$i]["name"] =  $name;
          $SmTbl[$i]["url"] =  $url;
}
/// DD modification end
// End of DD addon

if ($browser == "msie") {
?>
<script>
function ReloadSmileBar() {
        if(window.opener != null) {
       if(window.opener.ico != null) window.opener.ico.location.reload();
    }
}

function e(){
        parent.moveFromBoard(event,1);
}
document.onmousemove=e;</script>
<?php } include($file_path."designes/".$design."/common_body_start.php");
?>
<div align=CENTER>
<?php
     $MaxPages = floor(count($pic_phrases)/(LINES_PER_PAGE*SMILEYS_PER_LINE))+0.5;
     for($i = 0; $i < $MaxPages; $i++) {
             if($i == $page) {
                echo " <b>[".($i+1)."]</b> ";
        }
        else {
                   echo " [<a href='".$chat_url."pictures.php?action=make_common&session=$session&page=$i'>".($i+1)."</a>] ";
        }
     }
?>
</div>
<table border=1>
<tr>
<?php         for($j=0; $j< 4; $j++) { ?>
<td><?php echo $w_symbols; ?></td><td><?php echo $w_picture;?></td><td><?php echo $w_favor_smile ;?></td>
<?php
                }
echo "</tr>";

if($page == "") $page = 0;


for ($i=0;$i<LINES_PER_PAGE;$i++) {
        echo "<tr>";
        for($j=0; $j< SMILEYS_PER_LINE; $j++) {
            if(($page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j) >= count($pic_phrases)) break;
                  echo "<td>".$pic_phrases[$page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j]."</td><td><a href=\"javascript:addPic('".$pic_phrases[$page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j]."');\" target=\"voc_sender\">".$pic_urls[$page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j]."</a></td><td align=CENTER ";

         $IsSystem = 0;
         for($sm_i = 0; $sm_i < count($SysSmTbl); $sm_i++) {
                        if($SysSmTbl[$sm_i]["name"] == $pic_phrases[$page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j]) {
                          echo "bgcolor=\"#FFCCCC\"><b>$w_system_smile</b>";
              $IsSystem = 1;
              break;
            }
          }

     if(!$IsSystem) {
         $IsFound = 0;
         for($sm_i = 0; $sm_i < count($SmTbl); $sm_i++) {
                        if($SmTbl[$sm_i]["name"] == $pic_phrases[$page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j]) {
                          echo "bgcolor=\"#FAFAFA\"><b>$w_favor_yes</b><br>(<a href='../../pictures.php?action=unset_common&page=$page&session=$session&com_sm_name=".$pic_phrases[$page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j]."'>$w_favor_rem</a>)";
              $IsFound = 1; break;
            }
          }

         if(!$IsFound) {
              echo ">$w_favor_no<br>(<a href='".$chat_url."pictures.php?action=make_common&session=$session&page=$page&com_sm_name=".$pic_phrases[$page*(LINES_PER_PAGE*SMILEYS_PER_LINE)+$i*SMILEYS_PER_LINE+ $j]."'>$w_favor_add</a>)";
         }
     }
         echo "</td>";
    }
           echo "</tr>\n";
}
?>
</table>
<div align=CENTER>
<?php
     $MaxPages = floor(count($pic_phrases)/(LINES_PER_PAGE*SMILEYS_PER_LINE))+0.5;
     for($i = 0; $i < $MaxPages; $i++) {
             if($i == $page) {
                echo " <b>[".($i+1)."]</b> ";
        }
        else {
                   echo " [<a href='".$chat_url."pictures.php?action=make_common&session=$session&page=$i'>".($i+1)."</a>] ";
        }
     }
?>
</div>
<?php include($file_path."designes/".$design."/common_body_end.php");?>