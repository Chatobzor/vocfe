<?php if (!defined("_COMMON_")) {echo "stop";exit;}
?>
<bgsound name = "pvt_sound" id = "pvt_sound" src ="">
<script language="javascript">
<!--
var arrBoys                     = new Array;
var arrGirls                    = new Array;
var arrHim                      = new Array;
var arrAdmins                   = new Array;
var arrClan                     = new Array;
var arrExCmd                    = new Array;
var arrBoysSize                 = 0;
var arrGirlsSize                = 0;
var arrHimSize                  = 0;
var arrAdminsSize               = 0;
var arrClanSize                 = 0;
var arrExCmdSize                = 0;

var room_ids                    = new Array;
var room_names                  = new Array;
var room_users                  = new Array;
var user_status;
var inChat;
var user_status;
var current_room;
var photos;
var pho_word;
var voc_powers                  = 0;
var voc_invis                   = 0;
var voc_channels_ok             = 0;
var nTimerRing                  = 0;
//some functions kindly given by AVANPORT Studio
var nNav                        = 0;
var isIECompatible              = 0;
var isMaxthon                   = 0;
var nTimerGiveMe                = 0;
var nTimerSmileys               = 0;
var smFrameOk                   = 0;

//popup privates
//introduced in Valentine Edition Pro
var arrPopupsSize                = 0;
var arrPopups                    = new Array;
var IsNewPM                      = false;
//anti-disconnect edition
//introdused in Valentine Edition Pro II
var nChannelTimeout              = 0;
var bPlaySound                   = <?=intval($current_user->play_sound);?>;

function ping() {
    nChannelTimeout = 1;
}

function openPrivatePopup(Nick, NickID) {
var i = 0;

    for(i = 0; i < arrPopupsSize; i++) {
        if(arrPopups[i].Nick == Nick) return;
    }

    arrPopups[arrPopupsSize]           = { Nick : Nick, Name : NickID, Loaded: false, Handle : -1 };
    arrPopups[arrPopupsSize].Handle    = window.open('<?php echo $chat_url; ?>voc_popup_opener.php?session=<?php echo $session; ?>&win_id='+NickID, NickID);

    if (arrPopups[arrPopupsSize].Handle.opener == null)  arrPopups[arrPopupsSize].Handle.opener = self;

    arrPopupsSize++;
}

function whoAmIPopup(NickID) {
 var tmpHandle;
 var i = 0;
 var IsWindowFound = false;

 for(i = 0; i < arrPopupsSize; i++) {
        if(arrPopups[i].Name == NickID) {
           IsWindowFound       = true;
           tmpHandle           = arrPopups[i].Handle;
           arrPopups[i].Loaded = true;
           break;
        }
  }
 if(!IsWindowFound) return;

 tmpHandle.loadInitialNick(arrPopups[i].Nick);
}

function ClosePopup(NickID) {
 var tmpHandle;
 var i = 0, idx = -1;
 var IsWindowFound = false;

 for(i = 0; i < arrPopupsSize; i++) {
        if(arrPopups[i].Name == NickID) {
           IsWindowFound       = true;
           tmpHandle           = arrPopups[i].Handle;
           arrPopups[i].Loaded = false;
           idx                 = i;
           break;
        }
  }
 if(!IsWindowFound) return;

 for(i = idx; i < arrPopupsSize-1;i++) {
        arrPopups[i] = arrPopups[i+1];
 }
 arrPopupsSize--;
}

////////////////////////////////

function checkNavigator()
{
<?php
  if(is_file($file_path."designes/".$design."/browser/phpSniff.class.php")) {
       $IsSniff = true;
       include ($file_path."designes/".$design."/browser/phpSniff.class.php");

       $sniffer_settings = array('check_cookies'=>false,
                                 'default_language'=>"",
                                 'allow_masquerading'=>false);

       $sniff = new phpSniff($current_user->user_agent, $sniffer_settings);
     }
     else $IsSniff = false;
         if($IsSniff) {

         if(intval($daemon_type) == 2) {
             echo "isIECompatible = 1;\n";
             if( !strcasecmp($sniff->property('browser'),"IE")
                 or !strcasecmp($sniff->property('browser'),"MSIE")
                 or !strcasecmp($sniff->property('browser'),"FX")
             ) echo "nNav = 1;\n";
             else if(!strcasecmp($sniff->property('browser'),"OP")) {
                  if(floatval($sniff->property('version') >= 7.54)) {
                  echo "nNav = 1;\n";
                  }
                  else {
                        echo "nNav = 2;\n";
                  }
             }
             else {
                      echo "nNav = 2;\n";
             }
         }
         else {
         if( !strcasecmp($sniff->property('browser'),"IE")
             or !strcasecmp($sniff->property('browser'),"MSIE")) {
              echo "isIECompatible = 1; nNav = 1;\n";
         }
         else if(!strcasecmp($sniff->property('browser'),"FX")) {
              echo "isIECompatible = 0; nNav = 1;\n";
         }
         else if(!strcasecmp($sniff->property('browser'),"OP")) {
           if(floatval($sniff->property('version') >= 7.54)) {
             echo "isIECompatible = 0; nNav = 1;\n";
           }
           else {
            echo "isIECompatible = 0;\n nNav = 2;\n";
           }
         }
         else { echo "isIECompatible = 0;\n nNav = 2;\n"; }
         }
     }
     else { echo "isIECompatible = 0;\n nNav = 2;\n"; }
  ?>
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
function ini(total, inChatPhrase, u_s, n_room, cur_r, p) {
        inChat = inChatPhrase;
        user_status = u_s;
        current_room = cur_r;
        photos = p;
        if (photos == 1) {pho_word = "yes";} else {pho_word = "no";}
}

function addRoom(id, r_id, r_name, r_p) {
        room_ids[id] = r_id;
        room_names[id] = r_name;
        room_users[id] = r_p;
}

function _rmArr(arr, _asize) {
   arr.length = 0;
}
function RemoveAll() {
   _rmArr(arrBoys, arrBoysSize);
   _rmArr(arrGirls, arrGirlsSize);
   _rmArr(arrHim, arrHimSize);
   _rmArr(arrAdmins, arrAdminsSize);
   _rmArr(arrClan, arrClanSize);

   arrBoysSize         = 0;
   arrGirlsSize        = 0;
   arrHimSize          = 0;
   arrAdminsSize       = 0;
   arrClanSize         = 0;
}

function AddUser(uNick, uState, uGender, uInvis, uMarr, NickColor, uUID, uStatus, uIgn, uAvatar, uPhoto, uDamneds, uRewards, uClanAvatar, uEnc, uMem, uDealer, uSilence, uChaos, uVideo) {
    var i = 0;

        if(uState == 'm' || uState == 'a') {
               arrAdmins[arrAdminsSize] = {Nick:uNick,
                                           State: uState,
                                           Gender: uGender,
                                           Invis: uInvis,
                                           Marr: uMarr,
                                           ForeColor: NickColor,
                                           Status: uStatus,
                                           Avatar: uAvatar,
                                           UID: uUID,
                                           Ign: uIgn,
                                           Photo: uPhoto,
                                           Damneds: uDamneds,
                                           Rewards: uRewards,
                                           ClanAvatar: uClanAvatar,
                                           Enc: uEnc,
                                           Member: uMem,
                                           Dealer: uDealer,
                                           Silence: uSilence,
                                           Chaos: uChaos,
                                           Video: uVideo };
                arrAdminsSize++;
                return;
        }

        if(uState == 'c') {
               arrClan[arrClanSize] = {Nick:uNick,
                                       State: uState,
                                       Gender: uGender,
                                       Invis: uInvis,
                                       Marr: uMarr,
                                       ForeColor: NickColor,
                                       Status: uStatus,
                                       Avatar: uAvatar,
                                       UID: uUID,
                                       Ign: uIgn,
                                       Photo: uPhoto,
                                       Damneds: uDamneds,
                                       Rewards: uRewards,
                                       ClanAvatar: uClanAvatar,
                                       Enc: uEnc,
                                       Member: uMem,
                                       Dealer: uDealer,
                                       Silence: uSilence,
                                       Chaos: uChaos,
                                       Video: uVideo};
                arrClanSize++;
                return;
        }

        if(uGender == '2') {
                        arrGirls[arrGirlsSize] = {Nick:uNick,
                                                  State: uState,
                                                  Gender: uGender,
                                                  Invis: uInvis,
                                                  Marr: uMarr,
                                                  ForeColor: NickColor,
                                                  Status: uStatus,
                                                  Avatar: uAvatar,
                                                  UID: uUID,
                                                  Ign: uIgn,
                                                  Photo: uPhoto,
                                                  Damneds: uDamneds,
                                                  Rewards: uRewards,
                                                  ClanAvatar: uClanAvatar,
                                                  Enc: uEnc,
                                                  Member: uMem,
                                                  Dealer: uDealer,
                                                  Silence: uSilence,
                                                  Chaos: uChaos,
                                                  Video: uVideo};
                arrGirlsSize++;
                }
        else if (uGender == '1' || uGender == '0') {
                arrBoys[arrBoysSize] = {Nick:uNick,
                                        State: uState,
                                        Gender: uGender,
                                        Invis: uInvis,
                                        Marr: uMarr,
                                        ForeColor: NickColor,
                                        Status: uStatus,
                                        Avatar: uAvatar,
                                        UID: uUID,
                                        Ign: uIgn,
                                        Photo: uPhoto,
                                        Damneds: uDamneds,
                                        Rewards: uRewards,
                                        ClanAvatar: uClanAvatar,
                                        Enc: uEnc,
                                        Member: uMem,
                                        Dealer: uDealer,
                                        Silence: uSilence,
                                        Chaos: uChaos,
                                        Video: uVideo};
                arrBoysSize++;
                }
        else {
                arrHim[arrHimSize] = { Nick:uNick,
                                       State: uState,
                                       Gender: uGender,
                                       Invis: uInvis,
                                       Marr: uMarr,
                                       ForeColor: NickColor,
                                       Status: uStatus,
                                       Avatar: uAvatar,
                                       UID: uUID,
                                       Ign: uIgn,
                                       Photo: uPhoto,
                                       Damneds: uDamneds,
                                       Rewards: uRewards,
                                       ClanAvatar: uClanAvatar,
                                       Enc: uEnc,
                                       Member: uMem,
                                       Dealer: uDealer,
                                       Silence: uSilence,
                                       Chaos: uChaos,
                                       Video: uVideo};
                arrHimSize++;
        }

}

function sortByNick(a, b) {
    var x = a.Nick.toLowerCase();
    var y = b.Nick.toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function SortUserList(Gender) {
 if(Gender == 'c') { arrClan.sort(sortByNick); return;}
 if(Gender == 'a' || Gender == 'm') { arrAdmins.sort(sortByNick); return;}
 if(Gender == '2') { arrGirls.sort(sortByNick); return;}
 else if(Gender == '1' || Gender == '0') { arrBoys.sort(sortByNick); return;}
 else { arrHim.sort(sortByNick); }
}

function DisplayRewards(Rewards) {
var nViewed = 0, j;
var nRed, nSilver, nGold;

    nGold         = Math.ceil(Math.floor(Rewards/9));
    nSilver = Math.ceil(Math.floor((Rewards - nGold*9)/3));
    nRed         = Rewards - nGold*9 - nSilver*3;

    <?php if($cu_array[USER_REDUCETRAFFIC]) {
                    $goldImg        = "<font color=orange><b>A</b></font>";
                $silverImg         = "<font color=gray><b>A</b></font>";
                $simpleImg         = "<font color=red><b>A</b></font>";
             } else {
                $goldImg         = "<img width=18 height=18 src=\"".$current_design."img/amul_orange.gif\">";
                $silverImg         = "<img width=18 height=18 src=\"".$current_design."img/amul_gray.gif\">";
                $simpleImg         = "<img width=18 height=18 src=\"".$current_design."img/amul_red.gif\">";
                   } ?>

   with (window.frames['voc_who_visible'].document) {
            for(j = 0; j < nGold; j++) if(nViewed < 3) { write('<td><?php echo $goldImg; ?></td>'); nViewed++; }
            for(j = 0; j < nSilver; j++) if(nViewed < 3) { write('<td><?php echo $silverImg; ?></td>'); nViewed++;}
            for(j = 0; j < nRed; j++) if(nViewed < 3) { write('<td><?php echo $simpleImg; ?></td>'); nViewed++;}
   }
}

function RenderUser(User, IsAdmin, MyClan) {
    var tmpHdr;

    with (window.frames['voc_who_visible'].document) {
                    write('<tr><td bgcolor="#F1F1F1"><div align="left"><table cellspacing="2" cellpadding="0"><tr>');
                 write('<style>td { font-family: Verdana, Arial; font-size: 11px;}</style>\n');
          if(User.Invis == '1') write('<td>[*]</td>');


          <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
           if(User.Ign == '0') write('<td><a href="<?php echo $chat_url;?>who.php?session=<?php echo $session; ?>&add_to_ignor_enc=' + User.Enc +'"><img src="<?php echo $current_design; ?>img/utalk.gif" border=0></a></td>');
                    else write('<td><a href="<?php echo $chat_url;?>who.php?session=<?php echo $session; ?>&remove_from_ignor_enc=' + User.Enc +'"><img src="<?php echo $current_design; ?>img/uignore.gif" border=0></a></td>');
         <?php }  else { ?>
         if(User.Ign == '0') write('<td class=\"text_traff\"><a href="<?php echo $chat_url;?>who.php?session=<?php echo $session; ?>&add_to_ignor_enc=' + User.Enc +'"><?php echo $w_2ignor; ?></a></td>');
             else write('<td class=\"text_traff\"><a href="<?php echo $chat_url;?>who.php?session=<?php echo $session; ?>&remove_from_ignor_enc=' + User.Enc +'"><font color=Red><?php echo $w_2visible; ?></font></a></td>');
          <?php } ?>

    if(User.Chaos == '1') {
                    write('<td><img src=\"<?=$current_design?>images/chaos.gif\" alt=\"<?=$w_adm_chaos?>\"></td>');
    }
    else {

         <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>

         tmpHdr =  'parent.openPrivatePopup(\''+ User.Nick +'\', \'Cht_Private_'+ User.UID +'\')';

         if(User.ClanAvatar == '' || MyClan) {
                            if(User.Gender == '0' || User.Gender == '2') write('<td><a href="javascript:;" onclick="javascript:'+ tmpHdr +';"><img border=0 src="<?php echo $current_design; ?>img/female.jpg"></a></td>');
                                else if(User.Gender == '1') write('<td><a href="javascript:;" onclick="javascript:'+ tmpHdr +';"><img src="<?php echo $current_design; ?>img/male.jpg" border=0></a></td>');
                            else {
                           write('<td><a href="javascript:;" onclick="javascript:'+ tmpHdr +';"><img src="<?php echo $current_design; ?>img/gender_none.gif" border=0></a></td>');
                                }
         }
         else {
                   write('<td><a href="javascript:;" onclick="javascript:'+ tmpHdr +';"><img src="'+ User.ClanAvatar +'" border=0></a></td>');
         }
         <?php } ?>
    }

    <?php if($cu_array[USER_REDUCETRAFFIC]) { ?>
         write('<td>[<a href="<?php echo $chat_url;?>fullinfo.php?session=<?php echo $session;?>&user_id='+ User.UID +'" target="_blank">?</a>]</td>');
    <?php } ?>
    <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
            if(User.Photo == '1') write('<td><a href="<?php echo $chat_url;?>fullinfo.php?session=<?php echo $session;?>&user_id='+ User.UID +'" target="_blank"><img src="<?php echo $current_design; ?>img/have_photo.jpg" border=0></a></td>');
            else write('<td><a href="<?php echo $chat_url;?>fullinfo.php?session=<?php echo $session;?>&user_id='+ User.UID +'" target="_blank"><img src="<?php echo $current_design; ?>images/no_photo.jpg" border=0></a></td>');
    <?php } ?>

    write('<td><a href="javascript:;" onClick="parent.Whisper(\'');
    write(User.Nick);
    write('\', false);">');
    if(User.ForeColor != '') write(User.ForeColor);
    else write('<font color=#000000>'+User.Nick+'</font>');
    write('</a></td>');

    if(User.Silence == '1') write('<td><img src=\"<?=$current_design?>images/silence.gif\"></td>');
    if(User.Member == '1') write('<td><img alt="VIP" src="<?=$current_design?>main/vip_litle.gif" border=0></td>');

    if(User.Video == '1') {
         tmpHdr =  'parent.openPrivatePopup(\''+ User.Nick +'\', \'Cht_Private_'+ User.UID +'\')';
         write('<td><a href="javascript:;" onclick="javascript:'+ tmpHdr +';"><img src="<?php echo $current_design; ?>images/webcam.gif" border=0 alt="WebCam"></a></td>');
    }

    <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
            if(User.Marr != '') write('<td><img src="<?php echo $current_design; ?>img/rings.gif"></td>');
     <?php } else { ?>
            if(User.Marr != '') write('<td><font color=yellow><b>&deg;</b></font></td>');
    <?php }?>


    if(User.Dealer  == '1') write('<td><img src=\"<?=$current_design?>images/dealer.gif\"></td>');

    write('<td>' + User.Status + '</td>');

    <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
            if(User.Marr == '1') write('<td><img src="<?php echo $current_design; ?>img/rings.gif"></td>');
    <?php } else { ?>
            if(User.Marr == '1') write('<td><font color=yellow><b>&deg;</b></font></td>');
    <?php }?>

    if(!IsAdmin) { DisplayRewards(User.Rewards);
                                   for(j = 0; j < User.Damneds; j++) {
                                   <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>write('<td><img width=18 height=18 src="<?php echo $current_design; ?>img/amul_curse.gif"></td>'); <?php } else { ?>
                        write('<td><b>+</b></td>');
                        <?php } ?>
                    }
                 }
    write('</tr></table></div></td></tr>\n');

    }
}

function whoList() {
var i;
var nRed, nSilver, nGold;

SortUserList('0');
SortUserList('1');
SortUserList('2');
SortUserList('m');
SortUserList('3');
SortUserList('c');

with (window.frames['voc_who_visible'].document) {

open("text/html", "");

write('<html><head><title>UserList</title>\n');
write('<meta http-equiv="Content-Type" content="text/html; charset=<?php echo DEFAULT_CHARSET; ?>">\n');
write('<link rel="STYLESHEET" type="text/css" href="<?php echo $current_design?>style.css">\n');
write('</head><body bgcolor="#f1f1f1" text="#000000" LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>\n');
write('<style>td { font-family: Verdana, Arial; font-size: 11px;}</style>\n');
    <?php if($cu_array[USER_REDUCETRAFFIC]) { ?> write('<style>.text_traff { font-family: Verdana, Arial; font-size: 11px;}</style>\n'); <?php } ?>
writeln('<script>\n<!--\nfunction info(u_name)\n{');
<?php
if ($browser == "msie" && $chat_type!="reload") {
?>
                writeln('with(window.voc_mess_frameset.message_box.document)\n        {\n                open(\'text/html\',\'replace\');\n                close();\n        }');
                writeln('window.parent.voc_mess_frameset.message_box.document.location.href = \'<?php echo $chat_url;?>fullinfo.php?session=<?php echo $session;?>&user_id=\'+u_name;');
                writeln('window.parent.voc_mess_frameset.show_box();');
<?php }else{ ?>
                writeln('window.open(\'<?php echo $chat_url;?>fullinfo.php?session=<?php echo $session;?>&user_id=\'+u_name, \'Info\', \'resizable=yes,width=600,height=350,toolbar=no,scrollbars=yes,location=no,menubar=no,status=no\');');
<?php } ?>
                writeln('}\n//-->\n<'+'/script>');

<?php
echo "write('";
        eval('?>'.str_replace("'","\\'",str_replace("\r","",str_replace("\n","\\n",implode('',file($file_path."designes/".$design."/common_body_start.php"))))));
echo        "\\n');\n";
?>

write('<table width="100%" border="0" cellspacing="0" cellpadding="0">\n');
write('<tr><td height=20 bgcolor="#7EC63E"> <div align="center"><b><a href="javascript:;" onClick="parent.Whisper(\'<?php echo $sw_usr_all_link; ?>\');"><font color="#FFFFFF"><?php echo $w_usr_all; ?></a> (');
write(arrAdminsSize + arrClanSize + arrBoysSize + arrGirlsSize + arrHimSize);
write(')</b></font>');
if(IsNewPM) write('&nbsp;<a href="#" onClick="javascript:parent.window.frames[\'menu\'].open_win(\'<?php echo $chat_url."board_list.php?session=$session"; ?>\',\'help\');"><img src="<?php echo $current_design; ?>img/newpm.gif" width=22 heigh=10 border=0></a>');
write('</div>\n');
write('</td></tr>\n');
if(arrAdminsSize || voc_powers == 1) {
   write('<tr><td bgcolor="#FB400D" height=20><div align="center"><b>\n');
   write('<a href="javascript:;" onClick="parent.Whisper(\'<?php echo $sw_usr_adm_link;?>\');"><font color="#FFFFFF"><?php echo $w_usr_adm; ?></a> (');
   write(arrAdminsSize);
   write(')</font></b></div>\n');
   write('</td></tr>\n');

        for(i = 0; i < arrAdminsSize; i++) {
                      RenderUser(arrAdmins[i], 1, 0);
        }
}
write('<tr><td height=20 bgcolor="#FFB900"> <div align="center"><b><a href="javascript:;" onClick="parent.Whisper(\'<?php echo $sw_usr_shaman_link; ?>\');"><font color="#FFFFFF"><?php echo $w_usr_shaman; ?></a>');
write('</b></font></div>\n');
write('</td></tr>\n');

<?php
        if($cu_array[USER_CLANID] > 0) {
?>
write('<tr><td height=20 bgcolor="#BCD560" align=center valign=middle> ');

if(arrClanSize > 0) {
   if(arrClan[0].ClanAvatar != '') write('<table align=center cellspacing=0 cellpadding=0><tr><td width=20 align=center valign=middle><img src = "'+ arrClan[0].ClanAvatar + '" border = 0></td><td>');
}

write('<b><a href="javascript:;" onClick="parent.Whisper(\'<?php echo $sw_usr_clan_link;?>\');"><font color="#FFFFFF"><?php echo $w_usr_clan; ?></a> (');
write(arrClanSize);
write(')</b>\n');
write('</font></td></tr>\n');

if(arrClanSize > 0) {
         if(arrClan[0].ClanAvatar != '') write('</table></td></tr>');
}


        for(i = 0; i < arrClanSize; i++) {
                        RenderUser(arrClan[i], 0, 1);
            }
<?php
        }
?>

if(arrGirlsSize) {
   write('<tr><td bgcolor="#ff02bf" height=20><div align="center">\n');
   write('<b><a href="javascript:;" onClick="parent.Whisper(\'<?php echo $sw_usr_girls_link;?>\');"><font color="#FFFFFF"><?php echo $w_usr_girls; ?></a> (');
   write(arrGirlsSize);
   write(')</b>\n');
   write('</font></div></td></tr>\n');

        for(i = 0; i < arrGirlsSize; i++) {
                        RenderUser(arrGirls[i], 0, 0);
            }
}
if(arrBoysSize) {
   write('<tr> <td height=20 bgcolor="#FD6801"><div align="center">\n');
   write('<b><a href="javascript:;" onClick="parent.Whisper(\'<?php echo $sw_usr_boys_link;?>\');"><font color="#FFFFFF"><?php echo $w_usr_boys; ?></a> (');
   write(arrBoysSize);
   write(')</b>\n');
   write('</font></div></td></tr>\n');

   for(i = 0; i < arrBoysSize; i++) {
        RenderUser(arrBoys[i], 0, 0);
   }
}
if(arrHimSize) {
   write('<tr> <td bgcolor="#666666" height=20><div align="center">\n');
   write('<b><a href="javascript:;" onClick="parent.Whisper(\'<?php echo $sw_usr_they_link;?>\');"><font color="#FFFFFF"><?php echo $w_usr_they; ?></a> (');
   write(arrHimSize);
   write(')</b>\n');
   write('</font></div></td></tr>\n');

   for(i = 0; i < arrHimSize; i++) {
        RenderUser(arrHim[i], 0, 0);
   }
}
        write('</table></body></html>');
        close();
      }
with (window.frames['voc_rooms'].document) {
               open("text/html", "");

               write('<html><head><title>UserList</title>\n');
               write('<meta http-equiv="Content-Type" content="text/html; charset=<?php echo DEFAULT_CHARSET; ?>">\n');
               write('<link rel="STYLESHEET" type="text/css" href="<?php echo $current_design?>style.css">\n');
               write('</head><body bgcolor="#f1f1f1" text="#000000" LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>\n');
               write('<style>td { font-family: Verdana, Arial; font-size: 11px;}</style>\n');
               write('<table width="100%" border="0" cellspacing="0" cellpadding="0">\n');

                writeln('<form method="get" action="<?php echo $chat_url;?>who.php" target="voc_who"><tr><td align="center"');
                if (photos == 1) {
                        writeln('<input type="hidden" name="photoss" value="yes">');
                }
                else {
                        writeln('<input type="hidden" name="photoss" value="no">');
                }
                writeln('<input type="hidden" name="session" value="<?php echo $session;?>">');
                writeln('<b><?php echo $w_your_status;?>:</b><br><select name="update_status" class="input_button">');
                write('<option value="<?php echo ONLINE;?>"');
                if (<?php echo ONLINE;?> == user_status) write(' selected');
                writeln('><?php echo $w_user_status[ONLINE];?></option>');
                write('<option value="<?php echo AWAY;?>"');
                if (<?php echo AWAY;?> == user_status) write(' selected');
                writeln('><?php echo $w_user_status[AWAY];?></option>');
                write('<option value="<?php echo NA;?>"');
                if (<?php echo NA;?> == user_status) write(' selected');
                writeln('><?php echo $w_user_status[NA];?></option>');
                write('<option value="<?php echo DND;?>"');
                if (<?php echo DND;?> == user_status) write(' selected');
                writeln('><?php echo $w_user_status[DND];?></option>');
                writeln('</select>');
                writeln('<input type="submit" value="OK" class="input_button">');
                writeln('</td></tr></form>');

        // Invisibility
        if(voc_powers == 1) {
                writeln('<form method="get" action="<?php echo $chat_url;?>who.php" target="voc_who"><tr><td align="center" colspan="2">');
                if (photos == 1) {
                        writeln('<input type="hidden" name="photoss" value="yes">');
                }
                else {
                        writeln('<input type="hidden" name="photoss" value="no">');
                }
                writeln('<input type="hidden" name="session" value="<?php echo $session;?>">');
                writeln('<?php echo $w_invisibility; ?>: <select name="update_invis" class="input_button">');
                write('<option value=1');
                if (voc_invis == 1) write(' selected');
                writeln('><?php echo $w_favor_yes; ?></option>');
                write('<option value=0');
                if (voc_invis == 0) write(' selected');
                writeln('><?php echo $w_favor_no; ?></option>');
                writeln('</select>');
                writeln('<input type="submit"  value="OK" class="input_button">');
                writeln('</td></tr></form>');
        }
               if(room_ids.length>1) {
                        writeln('<form method="post" action="<?php echo $chat_url;?>voc.php" target="_parent"><tr><td align="center">');
                        writeln('<input type="hidden" name="session" value="<?php echo $session;?>">');
                        writeln('<b><?php echo $w_select_room;?>:</b><br><select name="room" class="input_button">');
                        for (var i=0;i<room_ids.length;i++) {
                                write('<option value="'+room_ids[i]+'"');
                                if (room_ids[i] == current_room) write(' selected');
                                writeln('>'+room_names[i]+'('+room_users[i]+')</option>');
                        }
                        writeln('</select>&nbsp;<input type="submit" class=input_button value="OK">\n</td></tr></form>');
                }

        write('</table></body></html>');
        close();
    }

}
// End of userlist manipulation

<?php
if ($browser == "msie" && $chat_type!="reload")
{
?>
var inited = 0;

function rel() {
        var pho_word = 'no';
        window.frames['voc_who'].document.location.href='<?php echo $chat_url."who.php?session=$session";?>&photoss='+pho_word;
        window.setTimeout('rel()',120000);
}
window.setTimeout('rel()',120000);

function st_ini() {
        try {
                window.voc_status_op.st_ini();
                inited = 1;
        }
        catch(e) {
                inited=0;
        }
}
function st_update() {
        if (inited == 1) {window.voc_status_op.st_update();}
        else {st_ini();}
}
<?php } else {?>
function st_ini() {
}
function st_update() {
}
<?php }?>

function RunSysCmd(cmdLine, cType, cTime) {

  for(i = 0; i < arrExCmdSize; i++) {
    if(arrExCmd[i].Type == cType && arrExCmd[i].timeEx == cTime) return;
  }

  arrExCmd[arrExCmdSize] = { Type: cTime, timeEx: cTime };
  arrExCmdSize++;

  eval(cmdLine);
}

function addPic(What) {
  window.frames['voc_sender'].document.forms[0].mesg.focus();
  window.frames['voc_sender'].document.forms[0].mesg.value = window.voc_sender.document.forms[0].mesg.value + What;
}

function Whisper(What) {
       window.frames['voc_sender'].document.forms[0].mesg.focus();
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

}

function checkConnection() {
       if(isIECompatible) {
        window.setTimeout("checkConnection()", 120000);

              if(nChannelTimeout) nChannelTimeout = 0;
              else {
                 CloseFrame('voc_shower_priv');
                 CloseFrame('voc_shower');
                 window.voc_shower_php.document.location.href='<?php echo $shower;?>';
                 OpenFrame('voc_shower_priv');
                 OpenFrame('voc_shower');
              }
       }
}

function giveMeSmileys() {

  if(!smFrameOk) {
    smFrameOk = 1;
    window.frames['voc_who'].document.location.href='<?php echo $chat_url."who.php?session=$session";?>';
    window.voc_sender.document.location.href='<?php echo $current_design;?>sender_visible.php?session=<?php echo $session;?>&user_color=<?php echo $user_color; ?>';
    window.frames['top_top'].document.location.href='<?php echo $current_design;?>top.php';
    window.frames['top_banner'].document.location.href='<?=$current_design?>remote_rbs.php';
    window.frames['menu'].document.location.href='<?php echo $chat_path;?>navibar.php?session=<?php echo $session;?>';
    window.frames['menu_public'].document.location.href='<?php echo $chat_path;?>menu_public.php?session=<?php echo $session;?>';
    window.frames['menu_private'].document.location.href='<?php echo $chat_path;?>menu_private.php?session=<?php echo $session;?>';
    window.frames['voc_alerter'].document.location.href='<?php echo $chat_url;?>alerter.php?session=<?php echo $session; ?>';

     <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
            window.voc_smileys.document.location.href='<?php echo $current_design;?>smileys.php?session=<?php echo $session;?>';
     <?php } ?>

     window.setTimeout("checkConnection()", 120000);
  }
}


function giveMeChat() {

    if(voc_channels_ok == 0) {

    window.setTimeout("giveMeSmileys()", 3000);

    checkNavigator();


<?php if ($chat_type=="tail") {
             ?>
           if(!isIECompatible) {
                      window.voc_shower.document.location.href='<?php echo $shower;?>&t=n';
                      window.voc_shower_priv.document.location.href='<?php echo $shower;?>&t=p';
           }
           else {
                     window.voc_shower_php.document.location.href='<?php echo $shower;?>';
                     OpenFrame('voc_shower_priv');
                     OpenFrame('voc_shower');
                     LoadMyPrivate();
                 }
<?php } else {?>
        window.voc_shower_php.document.location.href='<?php echo $shower;?>';
        OpenFrame('voc_shower_priv');
        OpenFrame('voc_shower');
        LoadMyPrivate();
<?php } ?>
        voc_channels_ok = 1;
    }
}

function clear_pub()
{
        if (confirm("<?php echo $w_roz_clear_pub_all; ?>"))
        {
                <?php if ($chat_type=="tail") { ?>
                       if(!isIECompatible) window.voc_shower.document.location.href='<?php echo $shower;?>&t=n';
                       else { CloseFrame('voc_shower'); arrSizePub = 0; Redraw('voc_shower');
                       }
                <?php } else { ?>
                       CloseFrame('voc_shower');
                       arrSizePub = 0;
                       Redraw('voc_shower');
                <?php } ?>
        }
}

function clear_priv()
{
           if (confirm("<?php echo $w_roz_clear_priv; ?>"))
        {
                <?php if ($chat_type=="tail") { ?>
                       if(!isIECompatible) window.voc_shower_priv.document.location.href='<?php echo $shower;?>&t=p';
                       else { CloseFrame('voc_shower_priv');arrSizePriv = 0; Redraw('voc_shower_priv'); }
                <?php } else { ?>
                        CloseFrame('voc_shower');
                        arrSizePriv = 0;
                        Redraw('voc_shower_priv');
                <?php } ?>
          }
}

var nBannerShow = 0;
function ret_sub() {
        with(window.voc_sender.document.forms[0]) {
            IsPublic.value = '1';
            act.value = '';
            if (clr_to.checked) whisper.value = '';
            mesg.value = '';
            <?php if($cu_array[USER_CLASS] > 0 or $cu_array[USER_CUSTOMCLASS] != 0) { ?>
                     banType.value='';
            <?php } ?>
            mesg.focus();
        }
            nBannerShow++;
            if(nBannerShow == 1)  {
               window.frames['top_banner'].show();
               nBannerShow = 0;
            }
}

window.setTimeout('rotate_banner()',300000);

function rotate_banner() {
        window.frames['top_banner'].show();
        window.setTimeout('rotate_banner()',300000);
}



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

//bufferization
var isBufferAllowed = true;
var arrBufPub       = new Array;
var arrBufPriv      = new Array;
var arrBufPubSize   = 0;
var arrBufPrivSize  = 0;
var MaxBufMsgs      = 10;

if(!isIECompatible) isBufferAllowed = false;

function buf2Screen() {
    if(isBufferAllowed) {
       if(arrBufPub) {
          for(i = 0; i < arrBufPubSize; i++) {
            window.frames['voc_shower'].document.write(arrBufPub[i]+'<br>');
          }
           window.frames['voc_shower'].document.write('<script>up();');
           window.frames['voc_shower'].document.write('<'+'/script'+'>');
           arrBufPub.length = 0;
           arrBufPubSize    = 0;
       }
       if(arrBufPub) {
          for(i = 0; i < arrBufPrivSize; i++) {
            window.frames['voc_shower_priv'].document.write(arrBufPriv[i]+'<br>');
          }
           window.frames['voc_shower_priv'].document.write('<script>up();');
           window.frames['voc_shower_priv'].document.write('<'+'/script'+'>');
           arrBufPriv.length = 0;
           arrBufPrivSize    = 0;
       }

       window.setTimeout('buf2Screen()',500);
    }
}
if(isBufferAllowed) window.setTimeout('buf2Screen()',500);

var hdrLine1 = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=<?php echo DEFAULT_CHARSET; ?>">\n';
var hdrLine2 = '<style> body, td {font-family: <?=$fonts_arr[intval($current_user->plugin_info["font_face"])] ?>; font-size: <?=$fonts_sizes_arr[intval($current_user->plugin_info["font_size"])] ?>%; color:black;}a,a:visited,a:hover{ color:black;}\n';
var hdrLine3 = 'small {font-size: 11px; color:#555555;} a.nick, a.nick:visited {text-decoration: none; } a.nick:hover { color:#6060ff; text-decoration: none;}\n';
var hdrLine4 = '.hs { background-color: #dadada; } .hu { background-color: #BDD6A9;} .ha { background-color: #FFB9A1;} .topic {  font-size:16px; font-weight:bold; color:#555555;}\n';
var hdrLine5 = '</style>\n';
var hdrLine6 = '<script language="javascript">\n var pause = 0;\n function up()\n {\nif (pause == 0)\n { \nscrollTo(0,10000000);\n} \n}\n </'+'script'+'>\n</head><body bgcolor="#fafafa" marginwidth="2" marginheight="2" topmargin="2" leftmargin="2" >\n';
var hdrEnd   = '</body></html>';
var hdrTopic = '<?php echo str_replace("\n", "", $rooms[$room_id]["topic"]); ?>';


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

function AddMsgToPriv(nMsg, Usr, UsrTo) {
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
        arrMsgPriv[arrSizePriv-1] = {Msg: nMsg, Nick: Usr, Whisper: UsrTo};
    }
    else {
        arrMsgPriv[arrSizePriv] = {Msg: nMsg, Nick: Usr, Whisper: UsrTo};
        arrSizePriv++;
    }

    if(bPlaySound && Usr != '<?=$cu_array[USER_NICKNAME]?>')  pvt_sound.src = '<?=$current_design?>sound/sound.wav';

    DrawMessage('voc_shower_priv', nMsg);

    //if popup is opened?
    if(Usr != '<?php echo $cu_array[USER_NICKNAME]; ?>') {
     for(i = 0; i < arrPopupsSize; i++) {
         if(arrPopups[i].Nick == Usr && UsrTo == '<?php echo $cu_array[USER_NICKNAME]; ?>') {
               IsWindowFound       = true;
               tmpHandle           = arrPopups[i].Handle;
               idx                 = i;
               break;
          }
     }
    if(IsWindowFound) tmpHandle.AddMsgToPriv(nMsg, Usr);
   }
   else {
    for(i = 0; i < arrPopupsSize; i++) {
         if(arrPopups[i].Nick == UsrTo) {
               IsWindowFound       = true;
               tmpHandle           = arrPopups[i].Handle;
               idx                 = i;
               break;
          }
     }
    if(IsWindowFound) tmpHandle.AddMsgToPriv(nMsg, Usr);
   }

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
      if(!isBufferAllowed) {
           window.frames[frameName].document.write(Msg+'<br>');
           window.frames[frameName].document.write('<script>up();');
           window.frames[frameName].document.write('<'+'/script'+'>');
          }
       else {
          if(frameName == 'voc_shower') {
             arrBufPub[arrBufPubSize] = Msg;
             arrBufPubSize++;
          }
          else {
             arrBufPriv[arrBufPrivSize] = Msg;
             arrBufPrivSize++;
          }
       }
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

function LoadMyPrivate() {
<?php
    include($data_path."engine/files/user_private_get_messages.php");
    ksort($priv_messages, SORT_NUMERIC);
    reset($priv_messages);

    if($current_user->play_sound == 1) {
    ?>
      bPlaySound = 0;
    <?php
    }

    for($i=0; $i< count($priv_messages); $i++)  {
            list($time1, $message) = each($priv_messages);
            echo $priv_messages[$time1]."\n";
    }

    if($current_user->play_sound == 1) {
    ?>
      bPlaySound = 1;
      <?php if(count($priv_messages)) { ?>
        pvt_sound.src = '<?=$current_design?>sound/sound.wav';
    <?php
            }
    }

?>
}

<?php } ?>

//-->
</script>
</head>

<frameset rows="0,20,72,*,0,0" framespacing="1" scrolling="no" frameborder="YES" bordercolor="#3D4976" onLoad="giveMeChat();">
<frame name="voc_shower_php" src="<?php echo $current_design; ?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
<frame name="menu" src="<?php echo $current_design; ?>blank.html" scrolling=no frameborder="0">
 <frameset cols="444,*" framespacing="0" scrolling="no" frameborder="no" bordercolor="#3D4976" >
  <frame name="top_top" src="<?php echo $current_design; ?>blank.html" marginwidth="0" marginheight="0" scrolling="no" frameborder="0">
  <frame name="top_banner" src="<?php echo $current_design; ?>blank.html" marginwidth="0" marginheight="0" scrolling="no" frameborder="0">
 </frameset>
        <frameset cols="*,<?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>50, <?php } ?>200,0" bordercolor="#3D4976" framespacing="1" frameborder="YES" scrolling=auto>
      <frameset rows="24,*" bordercolor="#3D4976" >
         <frame name="menu_public" src="<?php echo $current_design; ?>blank.html" scrolling=no frameborder="0">
       <?php //if this is a pre-moderated room and the current user is a moderator, then show him list of messages which have to be checked
                if($cu_array[USER_CLASS] > 0 && $ar_rooms[$room_id][ROOM_PREMODER]==1) {?>
                   <frameset rows="40%,30%, 30%, 80" bordercolor="#3D4976" framespacing="3">
                                <frame name="voc_shower" src="<?php echo $current_design; ?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
                                <frame name="voc_shower_priv" src="<?php echo $current_design; ?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
                                <frame src="<?php echo $chat_url;?>approve.php?session=<?php echo $session;?>" name="voc_approve" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
                                <frame src="<?php echo $current_design;?>blank.html" name="voc_sender" scrolling="no" frameborder="0">
                        </frameset>
<?php }
        else {
            if($cu_array[USER_CLASS] > 0 or $cu_array[USER_CUSTOMCLASS] != 0) {
         ?>
                       <frameset name="pvt_frameset" rows="*, 30%, 80" bordercolor="#3D4976" framespacing="3">
         <?php } else { ?>
                      <frameset name="pvt_frameset" rows="*, 30%, 60" bordercolor="#3D4976" framespacing="3">
         <?php } ?>
                                <frame name="voc_shower" src="<?php echo $current_design;?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
                                <frameset rows="24,*" bordercolor="#3D4976" framespacing="1">
                                     <frame name="menu_private" src="<?php echo $current_design;?>blank.html" marginwidth="0" marginheight="0" scrolling="no" frameborder="0">
                                     <frame name="voc_shower_priv" src="<?php echo $current_design;?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
                                </frameset>
                                <frame src="<?php echo $current_design;?>blank.html" name="voc_sender" scrolling="no" frameborder="0">
                       </frameset>
         <?php
         }
         ?>
                </frameset>
         <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
               <frame src="<?php echo $current_design;?>status_blank.php?session=<?php echo $session;?>" name="voc_smileys" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
        <?php } ?>
        <?php if($cu_array[USER_CLASS] > 0) { ?>
           <frameset rows="*, 85" bordercolor="#3D4976" framespacing="1" frameborder="YES" scrolling=auto>
        <?php } else { ?>
           <frameset rows="*, 65" bordercolor="#3D4976" framespacing="1" frameborder="YES" scrolling=auto>
        <?php } ?>
                <frame src="<?php echo $current_design;?>status_blank.php?session=<?php echo $session;?>" name="voc_who_visible" marginwidth="0" marginheight="0" frameborder="0">
                <frame src="<?php echo $current_design;?>status_blank.php?session=<?php echo $session;?>" name="voc_rooms" marginwidth="0" marginheight="0" frameborder="0">
           </frameset>
        <frame name="voc_who" src="<?php echo $current_design;?>blank.html" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
        </frameset>

     <frame name="voc_sender_hidden" src="" scrolling=no noresize frameborder="0">
     <frame src="<?php echo $current_design;?>blank.html" name="voc_alerter" scrolling="no" frameborder="0">
</frameset>
<noframes>
</noframes>
<script language="JavaScript">
 nTimerGiveMe = window.setTimeout('giveMeChat()',500);
</script>
</html>