<?php
function RenderHeader() {
global $file_path;
         ?>
<style>
td, div {
        font-family: Georgia, Verdana, Arial, Helvetica, sans-serif, Tahoma;
        font-size: 10px;
        color: white;
}</style>

<table id="Table_01" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
                <td width="50%" height="276" alt="" bgcolor="#265d92"></td>
                <td><img src="images/admin_02.gif" width="667" height="276" alt=""></td>
                <td width="50%" height="276" bgcolor="#265d92"></td>
        </tr>
        <tr>
                <td colspan="3" width="100%" height="100%" bgcolor="#265d92">

<?php
}
function RenderFooter() {
?>
                </td>
        </tr>
        <tr>
                <td colspan="3" width="100%" bgcolor="#265d92" align="center" style="font-size: 12 px; font-family: Tahoma;">
                 <b><a href="http://vocplus.creatiff.com.ua/" target="_blank"><font color=white>VOC++ Business Special Edition</font></a></b> &copy; 2004-2006 by <a href="http://www.creatiff.com.ua/" target="_blank"><font color=white>CREATIFF Design</font></a><br>
                                  Powered by <a href="http://vochat.com/" target="_blank"><font color=white>Voodoo Chat</font></a> OpenSource, <a href="http://www.opensource.org/licenses/qtpl.php" target="_blank"><font color=white>QPL</font></a> &copy; Vlad Vostrykh
         </td></tr>
</table>
<!-- End ImageReady Slices -->
</body>
</html>
<?php } ?>