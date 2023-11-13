<?php
function RenderHeader() {
global $file_path;
         ?>
<style>
td {
        font-family: Verdana, Arial, Helvetica, sans-serif, Tahoma;
        font-size: 10px;
}</style>
<TABLE width="100%" height="100%" BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#E3E3ED">
        <TR><td width=100% height="100%" valign="top">
              <TABLE WIDTH=100% height="100%" BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#E3E3ED">
                 <tr>
                    <td width="100%" height="95" align=center><img src="images/voc_pro2_logo_2.gif" width="374" height="95"></td>
                 </tr>
                 <tr>
                    <td align=center>
                    <!-- plugins-->
                    <b>Plug-ins:</b> <?php
$plug_arr = array();
if (is_dir($file_path."plugins")) {
   if ($dh = opendir($file_path."plugins")) {
       while (($file = readdir($dh)) !== false) {
           if($file != "." && $file != "..") {
                   if(is_dir($file_path."plugins/".$file)) {
                   //Plugin dir found
                   //trying to load config
                     if(is_file($file_path."plugins/".$file."/config.php")) {
                          include($file_path."plugins/".$file."/config.php");
                          $plug_arr[] =$VOCPlugin_Name." ".$VOCPlugin_Version;
                           }
                   }
           }
       }
       closedir($dh);
   }
}
sort($plug_arr, SORT_STRING);
for($i = 0; $i < count($plug_arr); $i++) {
    echo $plug_arr[$i];
    if($i < count($plug_arr) - 1) echo ", ";
}
?>
                    </td>
                 </tr>
                 <tr><td height="100%" valign="middle">
<?php }
function RenderFooter() {
global $lang;
 ?>
                 </td></tr>
                 <tr><td  align="center">
                     <b><a href="http://vocplus.creatiff.com.ua/" target="_blank" class=desc style="{ font-size: 10px; color: #000000}">VOC++ Valentine Edition Pro II</a></b> &copy; 2004-2005 by <a href="http://www.creatiff.com.ua/" target="_blank" class=desc style="{ font-size: 10px; color: #000000}">CREATIFF Design</a><br>
                                  Powered by <a href="http://vochat.com/" target="_blank" class=desc style="{ font-size: 10px; color: #000000}">Voodoo Chat</a> OpenSource, <a href="http://www.opensource.org/licenses/qtpl.php" style="{ font-size: 10px; color: #000000}" target="_blank" class=desc>QPL</a> &copy; Vlad Vostrykh
                 </td></tr>
              </table>
           </td>
           <TD width="267" valign="top" height="100%">
              <TABLE WIDTH=267 height=100% BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#E3E3ED">
                 <tr>
                    <td width="267" height="53"><img src="images/voc_pro2_04.jpg" width="267" height="53"></td>
                 </tr>
                 <tr>
                    <td width="267" background="images/voc_pro2_scns.jpg" valign="top">
                      <blockquote>
                        <script language="JavaScript" src="http://scns.vocplus.creatiff.com.ua/scns.php?lng=<?php echo $lang;?>"></script>
                      </blockquote>
                    </td>
                 </tr>
                 <tr>
                    <td width="267" height="345"><img src="images/voc_pro2_girl.jpg" width="267" height="345"></td>
                 </tr>
              </table></td></TR></TABLE></BODY></HTML>
<?php } ?>