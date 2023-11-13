<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//RU//4.0">
<HTML>
<HEAD>
<META name=robots content=all>
<meta name=Copyright content="Creatiff VOC++ (Voodoo Chat extension)">
<meta name=Author content="Emin Sadykhov, DareDEVIL">
<?php
        if($charset == "") $charset = "windows-1251";
?>
<META HTTP-EQUIV=Content-Type CONTENT="text/html; charset=<?php echo $charset; ?>">
<TITLE>VOC++ administration</TITLE>
<style type="text/css">
<!--
body, td, a, div
{
font-family: Tahoma, Verdana;
}
.new
{
font-family: Tahoma, Verdana;
font-size:12px;
color:red;
font-weight:bold;
test-align:justify;
}
.box
{
    color:black;
}
a:link
{font-size:12px; color: #265d92}
a:visited
{font-size:12px;color: #265d92}
a:hover
{text-decoration:none;font-size:12px;color: #ecaa0b}
a.menu:link
{font-weight:bold;font-size:11px;
text-decoration:none;color:black}
a.menu:visited
{font-weight:bold;font-size:11px;
text-decoration:none;color:black}
a.menu:hover
{font-weight:bold;font-size:11px;
text-decoration:none;color:red}
a.desc:link
{font-weight:bold;font-size:11px;
}
a.desc:visited
{font-weight:bold;font-size:11px;
}
a.desc:hover
{font-weight:bold;font-size:11px;
text-decoration:none;color:red}
.tip
{
font-family:Verdana,tahoma;
font-size:10px;
}
td
{
font-family:Verdana,tahoma;
font-size:12px;
}
.mes
{
font-family: Tahoma, Verdana;
font-size:12px;
color: white;
font-weight:bold;
test-align:justify;
}
.desc
{
font-family:Verdana,tahoma;
font-size:11px;
color:black;
font-weight:bold;
}

.dat
{
font-family:Verdana,tahoma;
font-size:12px;
color:red;
font-weight:bold;
}
.txt
{
font-family:Verdana,tahoma;
font-size:14px;
color:black;
}
.head
{
font-family:Verdana,tahoma;
font-size:14px;
color:white;
font-weight:bold;
}
.button
{
    font-family:Verdana;
        font-size:12px;
        font-weight:bold;
        color:#FFFFFF;
        background:#265D92;
        border: black;
        border-style: solid;
        border-top-width: 0px;
        border-right-width: 0px;
        border-bottom-width: 0px;
        border-left-width: 0px;
    width:100px;
    cursor:hand;
}
.button_small
{
    font-family:Verdana;
        font-size:12px;
        font-weight:bold;
        color:#FFFFFF;
        background:#265D92;
        border: black;
        border-style: solid;
        border-top-width: 0px;
        border-right-width: 0px;
        border-bottom-width: 0px;
        border-left-width: 0px;
    cursor:hand;
}
.dd
{
    font-family:Verdana;
        font-size:11px;
        font-weight:bold;
        color:#666666;
        background:#FFFFFF;
        border: navy;
        border-style: solid;
        border-top-width: 1px;
        border-right-width: 1px;
        border-bottom-width: 1px;
        border-left-width: 1px;
    width:40px;
    cursor:hand;
}
.sb
{
    font-family:Verdana;
        font-size:10px;
        font-weight:bold;
        color:#666666;
        background:#FFFFFF;
        border: #666666;
        border-style: solid;
        border-top-width: 1px;
        border-right-width: 1px;
        border-bottom-width: 1px;
        border-left-width: 1px;
    width:20px;
    cursor:hand;
}
.input,.textarea,.select
{
    font-family:Verdana;
        font-size:12px;
        font-weight:bold;
        color:#265D92;
        background:#FFFFFF;
        border: #265D92;
        border-style: solid;
        border-top-width: 1px;
        border-right-width: 1px;
        border-bottom-width: 1px;
        border-left-width: 1px;
 }
-->
</style>
</HEAD>
<body LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0 bottommargin=0 rightmargin=0>