<?php
if (!defined("_COMMON_")) {
    echo "stop";
    exit;
}
//include($file_path."designes/".$design."/common_title.php");
//include($file_path."designes/".$design."/common_browser_detect.php");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo DEFAULT_CHARSET; ?>">
    <style>
        <!--
        a, td {
            font-family: Verdana, Arial;
            font-size: 10px;
            color: #3D4976;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            color: white
        }

        -->
    </style>
    <script language="JavaScript">
        <!--
        var fr_size = 30;

        function change_size(side) {
            if (side == 1) fr_size -= 4;
            if (side == 2) fr_size = 30;
            if (side == 0) fr_size += 4;
            if (fr_size < 10) {
                fr_size = 10;
            }
            if (fr_size > 90) {
                fr_size = 90;
            }
            var po = null;
            po = parent.document.getElementById('pvt_frameset');
            if (!po) po = parent.document.all('pvt_frameset');
            if (po) po.rows = "*," + fr_size + "%, 60";
            else alert('You have too exotic old browser to support =)');
        }

        function click_pause() {
            var fvVal = parent.voc_shower_priv.pause;

            if (fvVal != 1) {
                document.all('pause_img').src = pause_on.src;
                document.all('pause_img').alt = '<?php echo $w_roz_pause_tip_on; ?>';

                parent.voc_shower_priv.pause = 1;
            } else {

                document.all('pause_img').src = pause_off.src;
                document.all('pause_img').alt = '<?php echo $w_roz_pause_tip; ?>';
                parent.voc_shower_priv.pause = 0;
            }
        }

        if (document.images) {
            pause_on = new Image(24, 24);
            pause_on.src = "<?=$current_design?>grunge/pause_on.gif";

            pause_off = new Image(24, 24);
            pause_off.src = "<?=$current_design?>grunge/grunge_11.gif";
        }

        //-->
    </script>
</head>
<body bgcolor="#abd256" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
    <tr align="center">
        <td width="143" height="24" background="<?= $current_design ?>grunge/grunge_09.gif" align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<nobr>
                <b><font color=white><?= $w_whisper ?></font></a></b></nobr>&nbsp;&nbsp;
        </td>
        <td width="24" height="24"><a href="#" onClick="parent.clear_priv()"><img
                        src="<?= $current_design ?>grunge/grunge_10.gif" width="23" height="24" border="0"
                        alt="<?= $w_roz_clear_priv ?>"></a></td>
        <td width="24" height="24"><a href="#" onClick="click_pause()"><img border="0" name="pause_img" id="pause_img"
                        alt="<?= $w_roz_pause_tip ?>" src="<?= $current_design ?>grunge/grunge_11.gif" width="20"
                        height="24"></a></td>
        <td height="24" align="center"><a href="#" onClick="change_size(1);"><img
                        src="<?= $current_design ?>grunge/down.gif" height="24" width="24" border="0"></a>&nbsp;<a
                    href="#" onClick="change_size(0);"><img src="<?= $current_design ?>grunge/up.gif" height="24"
                        width="24" border="0"></a></td>
        <td width="224" height="24"><img src="<?= $current_design ?>grunge/grunge_14.gif" width="224" height="24"></td>
    </tr>
</table>
</body>
</html>