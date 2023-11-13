<?php include("check_session.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<?php
include("header.php");
include("../inc_common.php");

      set_variable("import_mysql_server");
          set_variable("import_mysql_user");
          set_variable("import_mysql_password");
          set_variable("import_mysql_db");
          set_variable("import_mysql_table_prefix");
?>
<body><center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<blockquote><span class=head><font color=Black>
<?php
set_variable("operation");
if($operation == "canon") echo $adm_generating_can.":";
else if ($operation == "shaman") echo $adm_shaman_search.":";
else if ($operation == "guardian") echo $adm_guardian_calib.":";
else if ($operation == "index") echo $adm_reconstruct_p.":";
else if ($operation == "import") echo $adm_mysql_import.":";
else if ($operation == "index_similar") echo $adm_gen_similar_table_pr.":";
else if ($operation == "index_register") echo $adm_register_prog.":";
?>
<script language="JavaScript1.2" type="text/javascript">
<!--//
// Timer Bar - Version 1.0
// Author: Brian Gosselin of http://scriptasylum.com
// Script featured on http://www.dynamicdrive.com
var loadedcolor='#265D92' ;       // PROGRESS BAR COLOR
var unloadedcolor='lightgrey';     // COLOR OF UNLOADED AREA
var bordercolor='navy';            // COLOR OF THE BORDER
var barheight=15;                  // HEIGHT OF PROGRESS BAR IN PIXELS
var barwidth=300;                  // WIDTH OF THE BAR IN PIXELS
var waitTime=10;

// THE FUNCTION BELOW CONTAINS THE ACTION(S) TAKEN ONCE BAR REACHES 100%.
// IF NO ACTION IS DESIRED, TAKE EVERYTHING OUT FROM BETWEEN THE CURLY BRACES ({})
// BUT LEAVE THE FUNCTION NAME AND CURLY BRACES IN PLACE.
// PRESENTLY, IT IS SET TO DO NOTHING, BUT CAN BE CHANGED EASILY.
// TO CAUSE A REDIRECT TO ANOTHER PAGE, INSERT THE FOLLOWING LINE:
// window.location="http://redirect_page.html";
// JUST CHANGE THE ACTUAL URL OF COURSE :)

var ns4=(document.layers)?true:false;
var ie4=(document.all)?true:false;
var blocksize=(barwidth-2)/waitTime;
var loaded=0;
var PBouter;
var PBdone;
var PBbckgnd;
var Pid=0;
var txt='';

function reInitBar(pass, count) {
 if(pass != 1) {
                waitTime=298;
    } else {
            waitTime=count;
         }
        blocksize=(barwidth-2)/waitTime;
}
function finish() {
    loaded=waitTime-1;
        incrCount();
}

if(ns4){
txt+='<table border=0 cellpadding=0 cellspacing=0><tr><td>';
txt+='<ilayer name="PBouter" visibility="hide" height="'+barheight+'" width="'+barwidth+'" onmouseup="hidebar()">';
txt+='<layer width="'+barwidth+'" height="'+barheight+'" bgcolor="'+bordercolor+'" top="0" left="0"></layer>';
txt+='<layer width="'+(barwidth-2)+'" height="'+(barheight-2)+'" bgcolor="'+unloadedcolor+'" top="1" left="1"></layer>';
txt+='<layer name="PBdone" width="'+(barwidth-2)+'" height="'+(barheight-2)+'" bgcolor="'+loadedcolor+'" top="1" left="1"></layer>';
txt+='</ilayer>';
txt+='</td></tr></table>';
}else{
txt+='<div id="PBouter" onmouseup="hidebar()" style="position:relative; visibility:hidden; background-color:'+bordercolor+'; width:'+barwidth+'px; height:'+barheight+'px;">';
txt+='<div style="position:absolute; top:1px; left:1px; width:'+(barwidth-2)+'px; height:'+(barheight-2)+'px; background-color:'+unloadedcolor+'; font-size:1px;"></div>';
txt+='<div id="PBdone" style="position:absolute; top:1px; left:1px; width:0px; height:'+(barheight-2)+'px; background-color:'+loadedcolor+'; font-size:1px;"></div>';
txt+='</div>';
}

document.write(txt);

function incrCount(){
//window.status="Loading...";
loaded++;
if(loaded<0)loaded=0;
if(loaded>=waitTime){
//clearInterval(Pid);
loaded=waitTime;
}
resizeEl(PBdone, 0, blocksize*loaded, barheight-2, 0);
}

function hidebar(){
clearInterval(Pid);
window.status='';
//if(ns4)PBouter.visibility="hide";
//else PBouter.style.visibility="hidden";
action();
}

//THIS FUNCTION BY MIKE HALL OF BRAINJAR.COM
function findlayer(name,doc){
var i,layer;
for(i=0;i<doc.layers.length;i++){
layer=doc.layers[i];
if(layer.name==name)return layer;
if(layer.document.layers.length>0)
if((layer=findlayer(name,layer.document))!=null)
return layer;
}
return null;
}

function progressBarInit(){
PBouter=(ns4)?findlayer('PBouter',document):(ie4)?document.all['PBouter']:document.getElementById('PBouter');
PBdone=(ns4)?PBouter.document.layers['PBdone']:(ie4)?document.all['PBdone']:document.getElementById('PBdone');
resizeEl(PBdone,0,0,barheight-2,0);
if(ns4)PBouter.visibility="show";
else PBouter.style.visibility="visible";
//Pid=setInterval('incrCount()',95);
}

function resizeEl(id,t,r,b,l){
if(ns4){
id.clip.left=l;
id.clip.top=t;
id.clip.right=r;
id.clip.bottom=b;
}else id.style.width=r+'px';
}
progressBarInit();
<?php if($operation == "canon") { ?>
window.onLoad = parent.frames['hp'].location.href= '<?php echo $chat_url;?>admin/generate_canon_nicks.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>';
<?php } else if($operation == "shaman") { ?>
window.onLoad = parent.frames['hp'].location.href= '<?php echo $chat_url;?>admin/generate_shamans_list.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>';
<?php } else if($operation == "index") { ?>
window.onLoad = parent.frames['hp'].location.href= '<?php echo $chat_url;?>admin/generate_indexes.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>';
<?php } else if($operation == "index_similar") { ?>
window.onLoad = parent.frames['hp'].location.href= '<?php echo $chat_url;?>admin/generate_similar_indexes.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>';
<?php } else if($operation == "import") { ?>
window.onLoad = parent.frames['hp'].location.href= '<?php echo $chat_url;?>admin/import_mysql.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>&import_mysql_server=<? echo $import_mysql_server; ?>&import_mysql_user=<? echo $import_mysql_user; ?>&import_mysql_password=<? echo $import_mysql_password; ?>&import_mysql_db=<? echo $import_mysql_db; ?>&import_mysql_table_prefix=<? echo $import_mysql_table_prefix; ?>';
<?php } else if($operation == "index_register") { ?>
window.onLoad = parent.frames['hp'].location.href= '<?php echo $chat_url;?>admin/register_users.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>';
<?php } else if($operation == "guardian") { ?>
window.onLoad = parent.frames['hp'].location.href= '<?php echo $chat_url;?>admin/calibrate_guardian.php?session=<? echo $session; ?>&lang=<?php echo $lang; ?>';
<?php } ?>
//-->
</script>
</body></html>