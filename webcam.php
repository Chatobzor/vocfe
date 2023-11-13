<?php
include("inc_common.php");
include($engine_path."users_get_list.php");

if (!$exists)  {
        $error_text = "$w_no_user";
        include($file_path."designes/".$design."/error_page.php");
        exit;
}

set_variable("user_id");
$user_id = intval($user_id);

$is_regist = $user_id;

include("inc_user_class.php");
include($ld_engine_path."users_get_object.php");

if($current_user->allow_webcam and
   $current_user->webcam_ip != "" and
   $current_user->webcam_port > 1024)
 {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>
<body  bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" alink="#ff6600" link="#8b8b8b" vlink="#000000">
  <table width="100%" height="100%" border="0">
    <tr><td bgcolor="#EAEAEA" width="100%" height="100%" align="center" valign="middle">
<SCRIPT LANGUAGE="JavaScript">
<!--
/*******************************************************
FLASH DETECT 2.5
All code by Ryan Parman and mjac, unless otherwise noted.
(c) 1997-2004 Ryan Parman and mjac
http://www.skyzyx.com
*******************************************************/

// This script will test up to the following version.
flash_versions = 20;

// Initialize variables and arrays
var flash = new Object();
flash.installed=false;
flash.version='0.0';

// Dig through Netscape-compatible plug-ins first.
if (navigator.plugins && navigator.plugins.length) {
        for (x=0; x < navigator.plugins.length; x++) {
                if (navigator.plugins[x].name.indexOf('Shockwave Flash') != -1) {
                        flash.version = navigator.plugins[x].description.split('Shockwave Flash ')[1];
                        flash.installed = true;
                        break;
                }
        }
}

// Then, dig through ActiveX-style plug-ins afterwords
else if (window.ActiveXObject) {
        for (x = 2; x <= flash_versions; x++) {
                try {
                        oFlash = eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash." + x + "');");
                        if(oFlash) {
                                flash.installed = true;
                                flash.version = x + '.0';
                        }
                }
                catch(e) {}
        }
}

// Create sniffing variables in the following style: flash.ver[x]
// Modified by mjac
flash.ver = Array();
for(i = 4; i <= flash_versions; i++) {
        eval("flash.ver[" + i + "] = (flash.installed && parseInt(flash.version) >= " + i + ") ? true : false;");
}

errorimg1= 0;
function LoadImage1()
{
        uniq1 = Math.random();
        document.images.webcam1.src = "http://<?=$current_user->webcam_ip.":".$current_user->webcam_port?>/cam_1.jpg?uniq="+uniq1;
}
function ErrorImage1()
{
        errorimg1++;
        if (errorimg1>1){
              document.images.webcam1.onload = "";
              document.images.webcam1.src = "http://www.darkboard.net/webcam/offline.jpg";
              }else{
              uniq1 = Math.random();
            document.images.webcam1.src = "http://<?=$current_user->webcam_ip.":".$current_user->webcam_port?>/cam_1.jpg?uniq="+uniq1;
              }
}
function DoIt1()
{
        errorimg1=0;
        window.setTimeout("LoadImage1();", 300);
}

if(!flash.installed) document.write('<img border=1 src="http://www.darkboard.net/webcam/loading.jpg" id="webcam1" name="webcam1" onload="DoIt1()" onerror="ErrorImage1()" width=320 height=240 border=0 style="border-color:#000000; border-style:solid;">');
else {
    document.write('<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="320" HEIGHT="240" id="webcamXP" ALIGN="middle" />');
    document.write('<PARAM NAME="movie" VALUE="<?=$current_design?>webcamXP.swf?webcam=http://<?=$current_user->webcam_ip.":".$current_user->webcam_port?>/cam_1.jpg&refresh=15" />');
    document.write('<PARAM NAME="loop" VALUE="false" />');
    document.write('<PARAM NAME="menu" VALUE="false" />');
    document.write('<PARAM NAME="quality" VALUE="best" />');
    document.write('<PARAM NAME="scale" VALUE="noscale" />');
    document.write('<PARAM NAME="salign" VALUE="lt" />');
    document.write('<PARAM NAME="wmode" VALUE="opaque" />');
    document.write('<EMBED src="<?=$current_design?>webcamXP.swf?webcam=http://<?=$current_user->webcam_ip.":".$current_user->webcam_port?>/cam_1.jpg&refresh=15"  WIDTH="240" HEIGHT="180" NAME="webcamXP" QUALITY="best" WMODE="opaque" SCALE="noscale" SALIGN="lt" ALIGN="middle" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>');
    document.write('</OBJECT>');
}
//-->
</script>
    </td></tr>
  </table>
<?
}
include($file_path."designes/".$design."/common_body_end.php");
?>