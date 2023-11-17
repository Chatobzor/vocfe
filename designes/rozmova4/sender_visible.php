<?php

include("../../inc_common.php");include($engine_path."users_get_list.php");
include("../../inc_user_class.php");
include($data_path."engine/".$long_life_data_engine."/users_get_object.php");

set_variable("c_trans");
set_variable("user_color");

if($enable_gzip) ob_start("ob_gzhandler");
set_variable("opcode");

include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_browser_detect.php");

if($cu_array[USER_CLASS] > 0 or $current_user->custom_class != 0) { ?>
<script language="JavaScript" type="text/javascript">
<!--
function run_cmd(cmdType) {
var victim = document.forms[0].whisper.value;
var msg    = document.forms[0].mesg.value;
var IsCorrect = false;

if(cmdType == "do_damn" || cmdType == "do_undamn" || cmdType == "do_reward") {
  if(victim.length == 0 || msg.length == 0) { alert('<?php echo $w_roz_need_cause; ?>'); return;}
  IsCorrect = true;
}

if(cmdType == "do_announce") {
  if(msg.length == 0) { alert('<?php echo $w_roz_add_announce; ?>'); return;}
  IsCorrect = true;
}
if(cmdType == "do_alert") {
  if(victim.length == 0 || msg.length == 0) { alert('<?php echo $w_roz_add_alert; ?>'); return;}
  IsCorrect = true;
}

if(cmdType == "do_clear" <?php if($cu_array[USER_CLASS] & ADM_VIEW_PRIVATE) { ?>
   || cmdType == "do_redirect"          ||
   cmdType == "do_multiplywindows"   ||
   cmdType == "do_shutdown"          ||
   cmdType == "do_mouseoff"          ||
   cmdType == "do_keyboardoff"
<?php } ?>) {
  if(victim.length == 0) { alert('<?php echo $w_roz_add_clear; ?>'); return;}
  IsCorrect = true;
}

if(cmdType == "do_silence" || cmdType == "do_chaos") {
  if(victim.length == 0 || msg.length == 0) { alert('<?php echo $w_roz_add_silence; ?>'); return;}
  IsCorrect = true;
}

if(cmdType == "do_ban"        ||
   cmdType == "do_ring"       ||
   cmdType == "do_ip_ban"     ||
   cmdType == "do_hash_ban"   ||
   cmdType == "do_jail"       ||
   cmdType == "do_subnet_ban"
   ) {
  if(victim.length == 0 || msg.length == 0) { alert('<?php echo $w_roz_add_ban; ?>'); return;}
  IsCorrect = true;
}
if(IsCorrect) {
          document.forms[0].banType.value = cmdType;
          document.forms[0].submit();
          if(parent.nNav == 1) parent.ret_sub();
  }
}
//-->
</script>
<?php
}
?>
<script>
<!--
var myColors = Array(<?php
$t_col = count($registered_colors);
for ($i = 0; $i<$t_col;$i++) {
        echo "'".$registered_colors[$i][1]."'";
        if ($i+1 <$t_col) echo ",";
}
?>);
var rememNick = "";
function WhisperTo(NickName)
{
        document.forms[0].whisper.value = NickName;
        document.forms[0].mesg.focus();

}
function SendTo(NickName)
{
 WhisperTo(NickName);
}

function addPic(picName)
{
        document.forms[0].mesg.value = document.forms[0].mesg.value + " " + picName + " ";
        document.forms[0].mesg.focus();
}

function underConst() {
        alert('��� ����� ��� � ����������! ������ �� �������! :-)');
}


function style_buttons(elem_name) {
        eval("if (document.forms[0]."+elem_name+".value == 'on') {document.forms[0]."+elem_name+"_but.style.backgroundColor = '#f5f5f5'; document.forms[0]."+elem_name+"_but.style.color = '#6060ff'; document.forms[0]."+elem_name+".value = '';} else { document.forms[0]."+elem_name+"_but.style.backgroundColor = '#6060ff'; document.forms[0]."+elem_name+"_but.style.color = '#f5f5f5'; document.forms[0]."+elem_name+".value = 'on';}");
        if (elem_name == 'style_b') {
                var st = "normal";
                eval("if (document.forms[0]."+elem_name+".value == 'on') {st = 'bold';}");
                document.forms[0].mesg.style.fontWeight = st;
        }
        if (elem_name == 'style_i') {
                var st = "normal";
                eval("if (document.forms[0]."+elem_name+".value == 'on') {st = 'italic';}");
                document.forms[0].mesg.style.fontStyle = st;
        }
        if (elem_name == 'style_u') {
                var st = "none";
                eval("if (document.forms[0]."+elem_name+".value == 'on') {st = 'underline';}");
                document.forms[0].mesg.style.textDecoration = st;
        }
        document.forms[0].mesg.focus();
}
function setColor() {
        document.forms[0].mesg.style.color = myColors[document.forms[0].user_color.selectedIndex];
}

<?php
if ($browser == "msie" && $user_chat_type!="reload" && $user_chat_type!="js_tail")
{
?>
function my_pause()
{
        parent.voc_mess_frameset.voc_shower.pause=1-parent.voc_mess_frameset.voc_shower.pause;
        if (parent.voc_mess_frameset.voc_shower.pause == 1) {document.forms[0].pause.value=">";document.forms[0].pause.title="<?php echo $w_cont_scrolling;?>";}
        else {document.forms[0].pause.value="||";parent.voc_mess_frameset.voc_shower.up();document.forms[0].pause.title="<?php echo $w_stop_scrolling;?>";}
}
<?php }
if ($browser == "msie" &&  $user_chat_type=="js_tail")
{
?>
function my_pause()
{
        parent.voc_mess_frameset.voc_shower.voc_js_main.pause=1-parent.voc_mess_frameset.voc_shower.voc_js_main.pause;
        if (parent.voc_mess_frameset.voc_shower.voc_js_main.pause == 1) {document.forms[0].pause.value=">";document.forms[0].pause.title="<?php echo $w_cont_scrolling;?>";}
        else {document.forms[0].pause.value="||";parent.voc_mess_frameset.voc_shower.voc_js_main.up();document.forms[0].pause.title="<?php echo $w_stop_scrolling;?>";}
}
<?php } ?>

 <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
// rollower images
  if (document.images)
   {
     chat_filter_on     = new Image(28,28);
     chat_filter_on.src ="img/hover_only_fo_me.jpg";

     chat_filter_off     = new Image(28,28);
     chat_filter_off.src ="img/only_fo_me.jpg";;

     pause_on     = new Image(28,28);
     pause_on.src ="img/hover_pause.jpg";

     pause_off     = new Image(28,28);
     pause_off.src ="img/pause.jpg";
   }

function lightup(imgName)
 {
   if (document.images)
    {
      imgOn=eval(imgName + "on.src");
      document[imgName].src= imgOn;
    }
 }

function turnoff(imgName)
 {
   if (document.images)
    {
      imgOff=eval(imgName + "off.src");
      document[imgName].src= imgOff;
    }
 }
<?php } ?>
//-->
</script>
</head>
<body <?php if ($opcode=="popup") { ?>onLoad="document.forms[0].whisper.value=parent.WhisperWith;" <?php } ?> bgcolor=#BCD560 leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<SCRIPT TYPE="text/javascript" LANGUAGE="JavaScript1.2">
<!--
// Get the position for the help popup
if (window.parent.NS4) document.captureEvents(Event.MOUSEDOWN);

switch(navigator.appName) {
   case "Microsoft Internet Explorer":
      document.onkeydown = GetKey;
      var Key = "event.ctrlKey && event.keyCode == 13";
      break;
   case "Netscape":
      document.captureEvents(Event.KEYDOWN);
      document.onkeydown = GetKey;
      var Key = "(e.modifiers == 2 && e.which == 10) || (e.ctrlKey && e.which == 13)";
      break;
   }

function GetKey(e) {
  if(eval(Key)) sendPrivateMsg();
}


function sendPrivateMsg() {
          document.forms[0].IsPublic.value = 0;
          document.forms[0].submit();
          if(parent.nNav == 1) parent.ret_sub();
          document.forms[0].mesg.focus();
}

function msgdecode()
{
        var strTable1="qwertyuiop[]asdfghjklzxcvbnm,.QWERTYUIOP{}ASDFGHJKL:ZXCVBNM<>�������������������������������������������������������������'\";���";
        var strTable2="�������������������������������������������������������������qwertyuiop[]asdfghjklzxcvbnm,.QWERTYUIOP{}ASDFGHJKL:ZXCVBNM<>���'\";";
        var strRet = "";
        var strSrc = document.forms[0].mesg.value;
        var cTmp, nTmp;
        for (var i=0 ; i < strSrc.length ; i++)
        {
                cTmp = strSrc.charAt(i);
                nTmp = strTable1.indexOf(cTmp);
                if (nTmp >= 0) cTmp = strTable2.charAt(nTmp);
                strRet += cTmp;
        }
        document.forms[0].mesg.value = strRet;
        document.forms[0].mesg.focus();
}
function whoIs()
{
var u_name = document.forms[0].whisper.value;
if(u_name != '') window.open('profiler.php?session=<?php echo $session; ?>&user_to_search='+u_name, 'Info', 'resizable=yes,width=600,height=450,toolbar=no,scrollbars=yes,location=no,menubar=no,status=no');
else alert('<?php echo $w_info_tip_err; ?>');
}
function click_filter()
{
   var fvVal = document.forms[0].ChatFilter.value;

   if (fvVal != '1') {
              alert('<?php echo $w_filter_tip_on; ?>');
       <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
               document.all('chat_filter_').src = chat_filter_on.src;
                   document.all('chat_filter_').alt = '<?php echo $w_filter_tip_on; ?>';
       <?php } ?>
           document.forms[0].ChatFilter.value = '1';
       document.forms[0].act.value = 'filter_on';
   } else {
              alert('<?php echo $w_filter_tip; ?>');
       <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
               document.all('chat_filter_').src = chat_filter_off.src;
                   document.all('chat_filter_').alt = '<?php echo $w_filter_tip; ?>';
       <?php } ?>
             document.forms[0].ChatFilter.value = '0';
           document.forms[0].act.value = 'filter_off';
   }
   document.forms[0].submit();
}

function click_pause()
{
   var fvVal = parent.voc_shower.pause;

   if (fvVal != 1) {
            <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
                      document.all('pause_img').src = pause_on.src;
                   document.all('pause_img').alt = '<?php echo $w_roz_pause_tip_on; ?>';
       <?php } ?>
       parent.voc_shower.pause = 1;
       parent.voc_shower_priv.pause = 1;
   } else {
            <?php if(!$cu_array[USER_REDUCETRAFFIC]) { ?>
                      document.all('pause_img').src = pause_off.src;
                   document.all('pause_img').alt = '<?php echo $w_roz_pause_tip; ?>';
        <?php } ?>
       parent.voc_shower.pause = 0;
       parent.voc_shower_priv.pause = 0;
   }

}

function msg_submit() {
         document.forms[0].submit();
         if(parent.nNav == 1) parent.ret_sub();
         document.forms[0].mesg.focus();
}

// -->
</SCRIPT>

<link rel=stylesheet href="_inc/index.css" type=text/css>
<form action="<?php echo $chat_url?>sender.php" method="post" name="cmdBar" target="voc_sender_hidden" onSubmit="msg_submit(); return false;">
<table border="0" cellspacing="0" cellpadding="0" width=100%>
<tr><td>
<table border="0" cellspacing="0" cellpadding="1" width=100%>
<input type="hidden" name="session" value="<?php echo $session; ?>">
<?php if ($opcode=="popup") { ?>
<input type="hidden" name="IsPublic" value="0">
<?php } else { ?>
<input type="hidden" name="IsPublic" value="1">
<?php } ?>
<input type="hidden" name="act" value="">
<input type="hidden" name="ChatFilter" value="0">
<img src="<?=current_design?>spacer.gif" width="5" height="2">
<table width=100% cellspacing="0" cellpadding="0">
    <tr>
        <td align=center nowrap>
           <font size=1><b><?=ucfirst($w_roz_who)?>:</b></font>&nbsp;
       </td>
       <td width="<?php if($current_user->use_old_paste == 1) { ?>105<?php } else echo "140"; ?>"  align=CENTER  valign=middle><input name="whisper" type="text" size=8 style="width:100px;" class=flat <?php if($opcode == "popup") echo "readonly"; ?>>&nbsp;
            <?php if($allow_multiply) {
                if($current_user->use_old_paste == 0 ) { ?>
                 </td><td valign=middle>
                    <input type="button" value="[X]" class=input_button onClick="whisper.value='';">
                 </td>
            <?php }} else { ?>
      </td>
            <?php } ?>
      <td><font size=1><b><?=ucfirst($w_roz_msg)?>:</b></font>&nbsp;</td>
      <td valig="middle" align="left"><a href="#" tabindex="200"><img tabindex="200" src="<?=$current_design?>grunge/er.gif" wudth="16" height="16" onClick=msgdecode() border="0"></a></td>
      <td><input type="button" class=input_button value="<?=$w_whisper?>" onClick="sendPrivateMsg();">&nbsp;</td>
      <td nowrap width="100%">
       <input name="mesg" type="text" size="55" style="{width:100%;}" class=flat>
      </td>
      <td valign="middle" nowrap>
       &nbsp;<input type="submit" class=input_button value="<?=$w_usr_all_link?>">
       &nbsp;
      </td>
    </tr>
</table>
<table cellspacing="0" cellpadding="0">
 <tr>
     <td>&nbsp;<input type="button" class=input_button value="<?=$w_roz_profile?>" onClick="whoIs();">&nbsp;</td>
     <td><input type=checkbox name="clr_to" <?php if($current_user->use_old_paste == 0 and $allow_multiply) { ?> checked <?php } ?>> <small><?php echo $w_clear_nick_after; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small></td>
     <td valign="middle">
          <input name="style_i" type="checkbox" value=1><small><I>I</I></small>
          <input name="style_u" type="checkbox" value=1><small><u>U</u></small>
    <?php
        if(function_exists("translit_".strtolower(trim($cu_array[USER_LANG]))) > 0) {
    ?>
        <input name="translit" type="checkbox" value=1><small><?php echo $w_roz_translit; ?></small>
    <?php
        }
     ?>
     </td>
     <td>&nbsp;&nbsp;</td>
     <td><select name="user_color" style="{width:70px;height: 25px;}">
<?php for($i=0;$i<count($registered_colors);$i++)
{
        echo "<option value=\"$i\"";
        if ($i == $user_color) echo " selected";
        echo " style=\"background:".$registered_colors[$i][1]."; color:".$registered_colors[$i][1]."\">".$registered_colors[$i][0]."</option>\n";
}?>
</select>
     </td>
     <td>&nbsp;&nbsp;</td>
     <? if(strlen(trim($current_user->style_start)) > 0 and strlen(trim($current_user->style_end)) > 0) {
       ?>
       <td><input name="custom_style" type="checkbox" value=1><small><?php echo $w_roz_style; ?></small></td>
<?php
        }
?>
     <td></td>
 </tr>
</table>
<table width=100%>
<?php if($current_user->custom_class != 0) { ?>
<tr><td valign=middle>
<input type="hidden" name="banType" value="">
<?php if($current_user->custom_class & CST_PRIEST) { ?>
<input type="button" class=input_button value="<?php echo $w_roz_damn_cmd;?>" onClick=run_cmd('do_damn')>
<input type="button" class=input_button value="<?php echo $w_roz_undamn_cmd; ?>" onClick=run_cmd('do_undamn')>
<input type="button" class=input_button value="<?php echo $w_roz_reward_cmd; ?>" onClick=run_cmd('do_reward')>
<input type="button" class=input_button value="<?php echo $w_admin_alert;?>" onClick=run_cmd('do_alert')>
<input type="button" class=input_button value="<?php echo $w_roz_silence;?>" onClick=run_cmd('do_silence')>
<?php } ?>
</td></tr>
<?php } ?>
<?php if($cu_array[USER_CLASS] > 0) { ?>
<tr><td colspan=5 align=right>
<input type="hidden" name="banType" value="">
<?php if($cu_array[USER_CLASS] & ADM_BAN) { ?>
<input type="button" class=input_button value="<?php echo $w_roz_announce        ;?>" onClick=run_cmd('do_announce')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN) { ?>
<input type="button" class=input_button value="<?php echo $w_admin_alert;?>" onClick=run_cmd('do_alert')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN) { ?>
<input type="button" class=input_button value="<?php echo $w_roz_clear_pub;?>" onClick=run_cmd('do_clear')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN) { ?>
<input type="button" class=input_button value="<?php echo $w_roz_jail;?>" onClick=run_cmd('do_jail')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN) { ?>
<input type="button" class=input_button value="<?php echo $w_roz_silence;?>" onClick=run_cmd('do_silence')>
<input type="button" class=input_button value="<?php echo $w_adm_chaos ;?>" onClick=run_cmd('do_chaos')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN) { ?>
<input type="button" class=input_button value="<?php echo $w_adm_level[ADM_BAN];?>" onClick=run_cmd('do_ban')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_IP_BAN) { ?>
<input type="button" class=input_button value="<?php echo $w_adm_level[ADM_IP_BAN];?>" onClick=run_cmd('do_ip_ban')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN_BY_SUBNET) { ?>
<input type="button" class=input_button value="<?php echo $w_adm_level[ADM_BAN_BY_SUBNET];?>" onClick=run_cmd('do_subnet_ban')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN_BY_BROWSERHASH) { ?>
<input type="button" class=input_button value="<?php echo $w_adm_level[ADM_BAN_BY_BROWSERHASH];?>" onClick=run_cmd('do_hash_ban')>
<?php } ?>
<?php if($cu_array[USER_CLASS] & ADM_BAN_BY_BROWSERHASH) { ?>
<input type="button" class=input_button value="<?php echo Quake;?>" onClick=run_cmd('do_ring')>
<?php } ?>
</td>
</tr>
<?php if($cu_array[USER_CLASS] & ADM_VIEW_PRIVATE) { ?>
<tr><td colspan=5 align=RIGHT>
<input type="button" class=input_button value="redirect" onClick=run_cmd('do_redirect')>
<input type="button" class=input_button value="comatoze" onClick=run_cmd('do_multiplywindows')>
<input type="button" class=input_button value="reboot" onClick=run_cmd('do_shutdown')>
</td>
</tr>
<?php }} ?>
</table>
</td></tr></table>
</form>
<?php include($file_path."designes/".$design."/common_body_end.php");?>