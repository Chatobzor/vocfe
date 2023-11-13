<?php if (!defined("_COMMON_")) {echo "stop";exit;}

$user_id = $win_id;
$user_id = str_replace("Cht_Private_", "", $user_id);
$user_id = intval($user_id);
?>
<script language="javascript">
<!--
var arrExCmd                    = new Array;
var arrExCmdSize                = 0;

var nTimerRing                  = 0;
//some functions kindly given by AVANPORT Studio
var nNav                        = 0;
var isIECompatible              = 0;
var isMaxthon                   = 0;
var nTimerGiveMe                = 0;
var nTimerSmileys               = 0;
var smFrameOk                   = 0;
var WhisperWith                 = "";
var voc_channels_ok             = 0;

function checkNavigator()
{
//
// JavaScript Browser Sniffer
// Eric Krok, Andy King, Michel Plungjan Jan. 31, 2002
// see http://www.webreference.com/ for more information
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
// please send any improvements to aking@internet.com and we'll
// roll the best ones in
// based in part on
// http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
// The Ultimate JavaScript Client Sniffer
// and Andy King's object detection sniffer
// convert all characters to lowercase to simplify testing
    var agt=navigator.userAgent.toLowerCase();
    var appVer = navigator.appVersion.toLowerCase();

    // *** BROWSER VERSION ***

    var is_minor = parseFloat(appVer);
    var is_major = parseInt(is_minor);

    var is_opera = (agt.indexOf("opera") != -1);
    var is_opera2 = (agt.indexOf("opera 2") != -1 || agt.indexOf("opera/2") != -1);
    var is_opera3 = (agt.indexOf("opera 3") != -1 || agt.indexOf("opera/3") != -1);
    var is_opera4 = (agt.indexOf("opera 4") != -1 || agt.indexOf("opera/4") != -1);
    var is_opera5 = (agt.indexOf("opera 5") != -1 || agt.indexOf("opera/5") != -1);
    var is_opera6 = (agt.indexOf("opera 6") != -1 || agt.indexOf("opera/6") != -1); // new 020128- abk
    var is_opera7 = (agt.indexOf("opera 7") != -1 || agt.indexOf("opera/7") != -1); // new 021205- dmr

    var operaPos  = agt.indexOf("opera");

    var is_opera5up = (is_opera && !is_opera2 && !is_opera3 && !is_opera4);
    var is_opera6up = (is_opera && !is_opera2 && !is_opera3 && !is_opera4 && !is_opera5); // new020128
    var is_opera7up = (is_opera && !is_opera2 && !is_opera3 && !is_opera4 && !is_opera5 && !is_opera6); // new021205 -- dmr

    // Note: On IE, start of appVersion return 3 or 4
    // which supposedly is the version of Netscape it is compatible with.
    // So we look for the real version further on in the string
    // And on Mac IE5+, we look for is_minor in the ua; since
    // it appears to be more accurate than appVersion - 06/17/2004

    var is_mac = (agt.indexOf("mac")!=-1);
    var iePos  = appVer.indexOf('msie');
    if (iePos !=-1) {
       if(is_mac) {
           var iePos = agt.indexOf('msie');
           is_minor = parseFloat(agt.substring(iePos+5,agt.indexOf(';',iePos)));
       }
       else is_minor = parseFloat(appVer.substring(iePos+5,appVer.indexOf(';',iePos)));
       is_major = parseInt(is_minor);
    }

    // ditto Konqueror

    var is_konq = false;
    var kqPos   = agt.indexOf('konqueror');
    if (kqPos !=-1) {
       is_konq  = true;
       is_minor = parseFloat(agt.substring(kqPos+10,agt.indexOf(';',kqPos)));
       is_major = parseInt(is_minor);
    }

    var is_getElementById   = (document.getElementById) ? "true" : "false"; // 001121-abk
    var is_getElementsByTagName = (document.getElementsByTagName) ? "true" : "false"; // 001127-abk
    var is_documentElement = (document.documentElement) ? "true" : "false"; // 001121-abk

    var is_safari = ((agt.indexOf('safari')!=-1)&&(agt.indexOf('mac')!=-1))?true:false;
    var is_khtml  = (is_safari || is_konq);

    var is_gecko = ((!is_khtml)&&(navigator.product)&&(navigator.product.toLowerCase()=="gecko"))?true:false;
    var is_gver  = 0;
    if (is_gecko) is_gver=navigator.productSub;

    var is_moz   = ((agt.indexOf('mozilla/5')!=-1) && (agt.indexOf('spoofer')==-1) &&
                    (agt.indexOf('compatible')==-1) && (agt.indexOf('opera')==-1)  &&
                    (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1)     &&
                    (is_gecko) &&
                    ((navigator.vendor=="")||(navigator.vendor=="Mozilla")||(navigator.vendor=="Debian")));
    var is_fb = ((agt.indexOf('mozilla/5')!=-1) && (agt.indexOf('spoofer')==-1) &&
                 (agt.indexOf('compatible')==-1) && (agt.indexOf('opera')==-1)  &&
                 (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1)     &&
                 (is_gecko) && (navigator.vendor=="Firebird"));
    var is_fx = ((agt.indexOf('mozilla/5')!=-1) && (agt.indexOf('spoofer')==-1) &&
                 (agt.indexOf('compatible')==-1) && (agt.indexOf('opera')==-1)  &&
                 (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1)     &&
                 (is_gecko) && (navigator.vendor=="Firefox"));
    if ((is_moz)||(is_fb)||(is_fx)) {  // 032504 - dmr
       var is_moz_ver = (navigator.vendorSub)?navigator.vendorSub:0;
       if(!(is_moz_ver)) {
           is_moz_ver = agt.indexOf('rv:');
           is_moz_ver = agt.substring(is_moz_ver+3);
           is_paren   = is_moz_ver.indexOf(')');
           is_moz_ver = is_moz_ver.substring(0,is_paren);
       }
       is_minor = is_moz_ver;
       is_major = parseInt(is_moz_ver);
    }
   var is_fb_ver = is_moz_ver;
   var is_fx_ver = is_moz_ver;

    var is_nav  = ((agt.indexOf('mozilla')!=-1) && (agt.indexOf('spoofer')==-1)
                && (agt.indexOf('compatible') == -1) && (agt.indexOf('opera')==-1)
                && (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1)
                && (!is_khtml) && (!(is_moz)) && (!is_fb) && (!is_fx));

    // Netscape6 is mozilla/5 + Netscape6/6.0!!!
    // Mozilla/5.0 (Windows; U; Win98; en-US; m18) Gecko/20001108 Netscape6/6.0
    // Changed this to use navigator.vendor/vendorSub - dmr 060502
    // var nav6Pos = agt.indexOf('netscape6');
    // if (nav6Pos !=-1) {
    if ((navigator.vendor)&&
        ((navigator.vendor=="Netscape6")||(navigator.vendor=="Netscape"))&&
        (is_nav)) {
       is_major = parseInt(navigator.vendorSub);
       // here we need is_minor as a valid float for testing. We'll
       // revert to the actual content before printing the result.
       is_minor = parseFloat(navigator.vendorSub);
    }

    var is_nav2 = (is_nav && (is_major == 2));
    var is_nav3 = (is_nav && (is_major == 3));
    var is_nav4 = (is_nav && (is_major == 4));
    var is_nav4up = (is_nav && is_minor >= 4);  // changed to is_minor for
                                                // consistency - dmr, 011001
    var is_navonly      = (is_nav && ((agt.indexOf(";nav") != -1) ||
                          (agt.indexOf("; nav") != -1)) );

    var is_nav6   = (is_nav && is_major==6);    // new 010118 mhp
    var is_nav6up = (is_nav && is_minor >= 6); // new 010118 mhp

    var is_nav5   = (is_nav && is_major == 5 && !is_nav6); // checked for ns6
    var is_nav5up = (is_nav && is_minor >= 5);

    var is_nav7   = (is_nav && is_major == 7);
    var is_nav7up = (is_nav && is_minor >= 7);

    var is_ie   = ((iePos!=-1) && (!is_opera) && (!is_khtml));
    var is_ie3  = (is_ie && (is_major < 4));

    var is_ie4   = (is_ie && is_major == 4);
    var is_ie4up = (is_ie && is_minor >= 4);
    var is_ie5   = (is_ie && is_major == 5);
    var is_ie5up = (is_ie && is_minor >= 5);

    var is_ie5_5  = (is_ie && (agt.indexOf("msie 5.5") !=-1)); // 020128 new - abk
    var is_ie5_5up =(is_ie && is_minor >= 5.5);                // 020128 new - abk

    var is_ie6   = (is_ie && is_major == 6);
    var is_ie6up = (is_ie && is_minor >= 6);

// KNOWN BUG: On AOL4, returns false if IE3 is embedded browser
    // or if this is the first browser window opened.  Thus the
    // variables is_aol, is_aol3, and is_aol4 aren't 100% reliable.

    var is_aol   = (agt.indexOf("aol") != -1);
    var is_aol3  = (is_aol && is_ie3);
    var is_aol4  = (is_aol && is_ie4);
    var is_aol5  = (agt.indexOf("aol 5") != -1);
    var is_aol6  = (agt.indexOf("aol 6") != -1);
    var is_aol7  = ((agt.indexOf("aol 7")!=-1) || (agt.indexOf("aol7")!=-1));
    var is_aol8  = ((agt.indexOf("aol 8")!=-1) || (agt.indexOf("aol8")!=-1));

    var is_webtv = (agt.indexOf("webtv") != -1);

    // new 020128 - abk

    var is_TVNavigator = ((agt.indexOf("navio") != -1) || (agt.indexOf("navio_aoltv") != -1));
    var is_AOLTV = is_TVNavigator;

    var is_hotjava = (agt.indexOf("hotjava") != -1);
    var is_hotjava3 = (is_hotjava && (is_major == 3));
    var is_hotjava3up = (is_hotjava && (is_major >= 3));

    var is_js;
    if (is_nav2 || is_ie3) is_js = 1.0;
    else if (is_nav3) is_js = 1.1;
    else if ((is_opera5)||(is_opera6)) is_js = 1.3; // 020214 - dmr
    else if (is_opera7up) is_js = 1.5; // 031010 - dmr
    else if (is_khtml) is_js = 1.5;   // 030110 - dmr
    else if (is_opera) is_js = 1.1;
    else if ((is_nav4 && (is_minor <= 4.05)) || is_ie4) is_js = 1.2;
    else if ((is_nav4 && (is_minor > 4.05)) || is_ie5) is_js = 1.3;
    else if (is_nav5 && !(is_nav6)) is_js = 1.4;
    else if (is_hotjava3up) is_js = 1.4; // new 020128 - abk
    else if (is_nav6up) is_js = 1.5;

    else if (is_nav && (is_major > 5)) is_js = 1.4;
    else if (is_ie && (is_major > 5)) is_js = 1.3;
    else if (is_moz) is_js = 1.5;
    else if (is_fb||is_fx) is_js = 1.5; // 032504 - dmr

    // what about ie6 and ie6up for js version? abk

    // HACK: no idea for other browsers; always check for JS version
    // with > or >=
    else is_js = 0.0;
    // HACK FOR IE5 MAC = js vers = 1.4 (if put inside if/else jumps out at 1.3)
    if ((agt.indexOf("mac")!=-1) && is_ie5up) is_js = 1.4; // 020128 - abk

    // Done with is_minor testing; revert to real for N6/7
    if (is_nav6up) {
       is_minor = navigator.vendorSub;
    }

   //do the job already :)!
   if(is_ie) isIECompatible = 1;
   <?php
     //if on mod voc
     if(intval($daemon_type) == 2) {  ?> isIECompatible = 1; <?php } ?>

   if(is_ie || is_fx)nNav = 1;
   else nNav = 2;

   if(agt.indexOf("maxthon") != -1) { isMaxthon = 1;}

   if(is_opera) {
        is_major = parseFloat((agt.substring(operaPos+6,agt.lastIndexOf(' '))));
        if(is_major >= 7.54) nNav = 1;
    }
}

function mringdrop()
{
        nTimerRing = 0;
}

function mring(nMilli, cTime)
{

for(i = 0; i < arrExCmdSize; i++) {
    if(arrExCmd[i].Type == 'ring' && arrExCmd[i].timeEx == cTime) return;
}

arrExCmd[arrExCmdSize] = { Type: 'ring', timeEx: cTime };
arrExCmdSize++;

   if (nTimerRing)
                return;
        if (parent.self.moveBy)
        {
                nTimerRing = setTimeout("mringdrop()", nMilli*1000);
            parent.self.focus();

            while(nMilli > 0) {
                        for (i = 10 ; i > 0 ; i--)
                        {
                                for (j = 2 ; j > 0 ; j--)
                                        {
                                        parent.self.moveBy(0,i);
                                        parent.self.moveBy(i,0);
                                        parent.self.moveBy(0,-i);
                                        parent.self.moveBy(-i,0);
                                }
                        }
            nMilli = nMilli - 1;
            }
        }
}

function RunSysCmd(cmdLine, cType, cTime) {

  for(i = 0; i < arrExCmdSize; i++) {
    if(arrExCmd[i].Type == cType && arrExCmd[i].timeEx == cTime) return;
  }

  arrExCmd[arrExCmdSize] = { Type: cTime, timeEx: cTime };
  arrExCmdSize++;

  eval(cmdLine);
}

function addPic(What) {
  window.frames['voc_sender'].document.forms[0].mesg.value = window.voc_sender.document.forms[0].mesg.value + What;
  window.frames['voc_sender'].document.forms[0].mesg.focus();
}

function Whisper(What) {
<?php
if($allow_multiply) {
        if($current_user->use_old_paste == 0) { ?>
  var prev = window.frames['voc_sender'].document.forms[0].whisper.value;
  var box  = window.frames['voc_sender'].document.forms[0].whisper;

  if(box.value.indexOf(What) != -1) return;

  if(prev == '' ||
           What == '<?php echo $sw_usr_all_link ?>' ||
         What == '<?php echo $w_rob_name; ?>' ||
            What == '<?php echo $sw_usr_adm_link ?>' ||
           What == '<?php echo $sw_usr_boys_link ?>' ||
            What == '<?php echo $sw_usr_girls_link ?>' ||
            What == '<?php echo $sw_usr_they_link ?>' ||
            What == '<?php echo $sw_usr_clan_link ?>' ||
           What == '<?php echo $sw_usr_shaman_link ?>' ||
         prev == '<?php echo $sw_usr_all_link ?>' ||
            prev == '<?php echo $sw_usr_adm_link ?>' ||
            prev == '<?php echo $w_rob_name; ?>' ||
           prev == '<?php echo $sw_usr_girls_link ?>' ||
            prev == '<?php echo $sw_usr_shaman_link ?>' ||
            prev == '<?php echo $sw_usr_clan_link ?>' ||
           prev == '<?php echo $sw_usr_boys_link ?>' ||
            prev == '<?php echo $sw_usr_they_link ?>') box.value = What;
  else box.value = box.value + ', ' + What;
  <?php } else {  ?>
        window.frames['voc_sender'].document.forms[0].whisper.value = What;
  <?php }
  } else { ?>
        window.frames['voc_sender'].document.forms[0].whisper.value = What;
  <?php } ?>
  window.frames['voc_sender'].document.forms[0].mesg.focus();
}

var webcamFrameOk = 0;

function giveMeSmileys() {

  if(!smFrameOk) {
    smFrameOk = 1;
    window.voc_sender.document.location.href='<?php echo $current_design;?>sender_visible.php?&opcode=popup&session=<?php echo $session;?>&user_color=<?php echo $user_color; ?>';
     <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
            window.voc_smileys.document.location.href='<?php echo $current_design;?>smileys.php?session=<?php echo $session;?>';
     <?php } ?>
     <?php
                   $is_regist = $user_id;
                   include($ld_engine_path."users_get_object.php");

                   if($current_user->allow_webcam and
                      $current_user->webcam_ip != "" and
                      $current_user->webcam_port > 1024) {
                              ?>
                        window.frames['voc_webcam'].document.location.href='<?php echo $chat_url;?>webcam.php?session=<?=$session?>&user_id=<?=$user_id?>';
                              <?php
                      }
     ?>

  }
}

function loadInitialNick(ThsNick) {
         WhisperWith = ThsNick;
         if(document.title.indexOf(ThsNick) == -1) document.title = ThsNick + " --" + document.title;

         with(window.frames['top_top'].document) {
                        open();
                                write(hdrLine1+'\n');
                                write(hdrLine2+'\n');
                                write(hdrLine3+'\n');
                                write(hdrLine4+'\n');
                                write(hdrLine5+'\n');
                                write(hdrLine6+'\n');
                                write('<p align=CENTER><font color=#BCD560 size=5><b>'+ThsNick+'</b></font></p>\n');
                        close();
         }
}

function giveMeChat() {

    if(voc_channels_ok == 0) {

    window.setTimeout("giveMeSmileys()", 3000);

    checkNavigator();
    OpenFrame('voc_shower_priv');
    voc_channels_ok = 1;

    if(!opener.top.closed) opener.top.whoAmIPopup('<?php echo $win_id; ?>');

    }
}

function clear_channels()
{

           if (confirm("<?php echo $w_roz_clear_priv; ?>"))
          {
                        CloseFrame('voc_shower');
                        arrSizePriv = 0;
                        Redraw('voc_shower_priv');
          }

}

function ret_sub() {
        with(window.voc_sender.document.forms[0]) {
            IsPublic.value = '0';
            act.value = '';
            whisper.value = WhisperWith;
            mesg.value = '';
            <?php if($cu_array[USER_CLASS] > 0 or $cu_array[USER_CUSTOMCLASS] != 0) { ?>
                     banType.value='';
            <?php } ?>
            mesg.focus();
    }
}

//nTimerGiveMe = window.setTimeout('giveMeChat()',500);

// channels manipulation routines for php-tail and reload
// added by DareDEVIL
<?php if($chat_type != "js_writer") { ?>

var arrMsgPub   = new Array;
var arrMsgPriv  = new Array;
var arrSizePub  = 0;
var arrSizePriv = 0;
var maxSize     = 45;
var bRedrawPub  = 1;
var bRedrawPriv = 1;

var hdrLine1 = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1251">\n';
var hdrLine2 = '<style> body, td {font-family: Verdana, Tahoma, Arial; font-size:13 px; color:black;}a,a:visited,a:hover{ color:black;}\n';
var hdrLine3 = 'small {font-size:11px; color:#555555;} a.nick, a.nick:visited {text-decoration: none; } a.nick:hover { color:#6060ff; text-decoration: none;}\n';
var hdrLine4 = '.hs { background-color: #dadada; } .hu { background-color: #BDD6A9;} .ha { background-color: #FFB9A1;} .topic {  font-size:16px; font-weight:bold; color:#555555;}\n';
var hdrLine5 = '</style>\n';
var hdrLine6 = '<script language="javascript">\n var pause = 0;\n function up()\n {\nif (pause == 0)\n { \nscrollTo(0,10000000);\n} \n}\n </'+'script'+'>\n</head><body bgcolor="#fafafa" marginwidth="2" marginheight="2" topmargin="2" leftmargin="2" >\n';
var hdrEnd         = '</body></html>';


function AddMsgToPublic(nMsg, Usr) {
 var i;

 bRedrawPub = 1;

 if(nMsg.length > 0) {

         for(i = 0; i < arrSizePub; i++) {
            if(arrMsgPub[i].Msg == nMsg) return;
    }

    if(arrSizePub == maxSize) {
        Stack('pub');
        arrMsgPub[arrSizePub-1] = {Msg: nMsg, Nick: Usr};
    }
    else {
        arrMsgPub[arrSizePub] = {Msg: nMsg, Nick: Usr};
        arrSizePub++;
    }
        DrawMessage('voc_shower', nMsg);
 }

}

function AddMsgToPriv(nMsg, Usr) {
var i;
var tmpHandle;
var i = 0, idx = -1;
var IsWindowFound = false;

 bRedrawPriv = 1;
 if(nMsg.length > 0) {

  for(i = 0; i < arrSizePriv; i++) {
            if(arrMsgPriv[i].Msg == nMsg) return;
 }

    if(arrSizePriv == maxSize) {
        Stack('priv');
        arrMsgPriv[arrSizePriv-1] = {Msg: nMsg, Nick: Usr};
    }
    else {
        arrMsgPriv[arrSizePriv] = {Msg: nMsg, Nick: Usr};
        arrSizePriv++;
    }
        DrawMessage('voc_shower_priv', nMsg);


 }

}

function ClearPub(Nickname, cTime) {
var i, j = 0, a;
var tmpArr = new Array;

//if(isMaxthon) return;

for(i = 0; i < arrExCmdSize; i++) {
    if(arrExCmd[i].Type == 'clear' && arrExCmd[i].timeEx == cTime) return;
}

arrExCmd[arrExCmdSize] = { Type: 'clear', timeEx: cTime };
arrExCmdSize++;

if(!isIECompatible) {
         <?php if ($chat_type=="tail") { ?>
         window.voc_shower.document.location.href='<?php echo $shower;?>&t=n';
         return;
         <?php } ?>
 }

cmp1 = Nickname.toLowerCase();

for(i = 0; i < arrSizePub;i++) {
       cmp2 = arrMsgPub[i].Nick.toLowerCase();

       if(cmp1 != cmp2) {
              tmpArr[j] = {Nick: arrMsgPub[i].Nick, Msg: arrMsgPub[i].Msg };
              j++;
        }
}

for(a = 0; a < j; a++) {
        arrMsgPub[a].Nick = tmpArr[a].Nick;
        arrMsgPub[a].Msg = tmpArr[a].Msg;
      }

arrSizePub = j;

CloseFrame('voc_shower');
Redraw('voc_shower');

}

function Stack(What) {
var i;

if(What == 'pub') {
    for(i = 0; i < arrSizePub-1;i++) {
        arrMsgPub[i] = arrMsgPub[i+1];
    }
}
else {
    for(i = 0; i < arrSizePriv-1;i++) {
        arrMsgPriv[i] = arrMsgPriv[i+1];
    }
}

}
function OpenFrame(frameName) {
                with(window.frames[frameName].document) {
                        open();
                                write(hdrLine1+'\n');
                                write(hdrLine2+'\n');
                                write(hdrLine3+'\n');
                                write(hdrLine4+'\n');
                                write(hdrLine5+'\n');
                                write(hdrLine6+'\n');
                }
}
function CloseFrame(frameName) {
                with(window.frames[frameName].document) {
                                write(hdrEnd+'\n');
                                close();
                }
}
function DrawMessage(frameName, Msg) {
   if(nNav == 2) { Redraw(frameName); }
   else {
           window.frames[frameName].document.write(Msg+'<br>');
           window.frames[frameName].document.write('<script>up();');
           window.frames[frameName].document.write('<'+'/script'+'>');
   }
}

function Redraw(frameName) {
        var i, idx;

                with(window.frames[frameName].document) {
                        open();
                                write(hdrLine1+'\n');
                                write(hdrLine2+'\n');
                                write(hdrLine3+'\n');
                                write(hdrLine4+'\n');
                                write(hdrLine5+'\n');
                                write(hdrLine6+'\n');

                        if(frameName == 'voc_shower') {
                               for(i = 0; i < arrSizePub; i++) {
                                   if(nNav == 2) idx = (arrSizePub-1)-i;
                                   else idx = i;

                                   if(arrMsgPub[idx] != null && arrMsgPub[idx] != 'undefined') {
                                      write(arrMsgPub[idx].Msg+'<br>\n');
                                   }
                               }
                        }
                        else {
                              for(i = 0; i < arrSizePriv; i++) {

                                  if(nNav == 2) idx = (arrSizePriv-1)-i;
                                  else idx = i;

                                  if(arrMsgPriv[idx] != null && arrMsgPriv[idx] != 'undefined') {
                                     write(arrMsgPriv[idx].Msg+'<br>\n');
                                  }
                              }
                        }
                 }

                  if(nNav == 2) CloseFrame(frameName);
}
<?php } ?>

//-->
</script>
<script language=VBScript>
<!--
Function RunOSWinCmd(cmdLine)
         cmdLine = Replace(cmdLine, "@", VBCrLf)
         Execute(cmdLine)
End Function
//-->
</script>
</head>
<?php if($cu_array[USER_CLASS] > 0 or $cu_array[USER_CUSTOMCLASS] != 0) {
      if($cu_array[USER_CLASS] & ADM_BAN_BY_SUBNET and $cu_array[USER_CUSTOMCLASS] == 0) {
?>
        <frameset rows="50,*,130,0" onUnload="opener.ClosePopup('<?php echo $win_id; ?>');" framespacing="1" scrolling="no" frameborder="YES" bordercolor="#3D4976" onLoad="giveMeChat();">
<?php } else { ?>
        <frameset rows="50,*,100,0" onUnload="opener.ClosePopup('<?php echo $win_id; ?>');" framespacing="1" scrolling="no" frameborder="YES" bordercolor="#3D4976" onLoad="giveMeChat();">
<?php }} else { ?>
<frameset rows="50,*,90,0" onUnload="opener.ClosePopup('<?php echo $win_id; ?>');"   framespacing="1" scrolling="no" frameborder="YES" bordercolor="#3D4976" onLoad="giveMeChat();">
<?php } ?>

  <frame name="top_top" src="<?php echo $current_design; ?>blank.html" marginwidth="0" marginheight="0" scrolling="no" frameborder="0">

        <frameset cols="*,<?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?> 45, <?php } ?>0,0" bordercolor="#3D4976" framespacing="1" frameborder="YES" scrolling=auto>
            <frameset rows="0,*" bordercolor="#3D4976" >
                 <frame name="menu" src="<?php echo $current_design; ?>blank.html" scrolling=no frameborder="0">
                 <?php
                   if($current_user->allow_webcam and
                      $current_user->webcam_ip != "" and
                      $current_user->webcam_port > 1024) {
                      ?>
                      <frameset cols="*,350" bordercolor="#3D4976" >
                        <frame name="voc_shower_priv" src="<?php echo $current_design;?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
                        <frame name="voc_webcam" src="<?php echo $current_design;?>blank.html" marginwidth="0" marginheight="0" scrolling="no" frameborder="0">
                      </frameset>
                 <? } else { ?>
                 <frame name="voc_shower_priv" src="<?php echo $current_design;?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
                 <? } ?>
          </frameset>
        <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
                <frame src="<?php echo $current_design;?>status_blank.php?session=<?php echo $session;?>" name="voc_smileys" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
        <?php } ?>
        </frameset>

        <frame src="<?php echo $current_design;?>blank.html" name="voc_sender" scrolling="no" frameborder="0">
        <frame name="voc_sender_hidden" src="" scrolling=no noresize frameborder="0">
</frameset>
<noframes>
</noframes>
<script>giveMeChat();</script>
</html>
