function ping() {
    nChannelTimeout = 1;
}

function openPrivatePopup(Nick, NickID) {
var i = 0;

    for(i = 0; i < arrPopupsSize; i++) {
        if(arrPopups[i].Nick == Nick) return;
    }

    arrPopups[arrPopupsSize]           = { Nick : Nick, Name : NickID, Loaded: false, Handle : -1 };
    arrPopups[arrPopupsSize].Handle    = window.open(chatURL + 'voc_popup_opener.php?session='+ session +'&win_id='+NickID, NickID);

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
   for(i=0; i<_asize; i++)
   {
      delete arr[i];
   }
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

function AddUser(uNick, uState, uGender, uInvis, uMarr, NickColor, uUID, uStatus, uIgn, uAvatar, uPhoto, uDamneds, uRewards, uClanAvatar, uEnc) {
    var i = 0;

        if(uState == 'm' || uState == 'a') {
               arrAdmins[arrAdminsSize] = {Nick:uNick, State:
uState, Gender: uGender, Invis: uInvis, Marr: uMarr, ForeColor: NickColor, Status: uStatus, Avatar: uAvatar, UID: uUID, Ign: uIgn, Photo: uPhoto, Damneds: uDamneds, Rewards: uRewards, ClanAvatar: uClanAvatar, Enc: uEnc};
                arrAdminsSize++;
                return;
        }

        if(uState == 'c') {
               arrClan[arrClanSize] = {Nick:uNick, State:
uState, Gender: uGender, Invis: uInvis, Marr: uMarr, ForeColor: NickColor, Status: uStatus, Avatar: uAvatar, UID: uUID, Ign: uIgn, Photo: uPhoto, Damneds: uDamneds, Rewards: uRewards, ClanAvatar: uClanAvatar, Enc: uEnc};
                arrClanSize++;
                return;
        }

        if(uGender == '2') {
                        arrGirls[arrGirlsSize] = {Nick:uNick, State:
uState, Gender: uGender, Invis: uInvis, Marr: uMarr, ForeColor: NickColor, Status: uStatus, Avatar: uAvatar, UID: uUID, Ign: uIgn, Photo: uPhoto, Damneds: uDamneds, Rewards: uRewards, ClanAvatar: uClanAvatar, Enc: uEnc};
                arrGirlsSize++;
                }
        else if (uGender == '1' || uGender == '0') {
                arrBoys[arrBoysSize] = {Nick:uNick, State: uState, Gender:
uGender, Invis: uInvis, Marr: uMarr, ForeColor: NickColor, Status: uStatus, Avatar: uAvatar, UID: uUID, Ign: uIgn, Photo: uPhoto, Damneds: uDamneds, Rewards: uRewards, ClanAvatar: uClanAvatar, Enc: uEnc};
                arrBoysSize++;
                }
        else {
                arrHim[arrHimSize] = {Nick:uNick, State: uState, Gender:
uGender, Invis: uInvis, Marr: uMarr, ForeColor: NickColor, Status: uStatus, Avatar: uAvatar, UID: uUID, Ign: uIgn, Photo: uPhoto, Damneds: uDamneds, Rewards: uRewards, ClanAvatar: uClanAvatar, Enc: uEnc};
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
        DrawMessage('voc_shower_priv', nMsg);

    //if popup is opened?
    if(Usr != myNick) {
     for(i = 0; i < arrPopupsSize; i++) {
         if(arrPopups[i].Nick == Usr && UsrTo == myNick) {
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
                        for(i = 0; i < hdrLine.length) {
                                write(hdrLine[i]+'\n');
                        }
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
                        for(i = 0; i < hdrLine.length) {
                                write(hdrLine[i]+'\n');
                        }

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