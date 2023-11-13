<?php

function Gradient_InitPlugin() {
   installEventHandler(EVENT_HTML_MESSAGE,  "Gradient_Do");
}

function Gradient_Do($UserNick, &$Msg) {
  global $custom_style, $is_regist, $is_regist_complete, $file_path, $data_path, $ld_engine_path;
  global $registered_colors, $default_color;

  $_pre_msg=$Msg;

  $custom_style = intval(trim($custom_style));

 if($custom_style == 1 and $is_regist_complete) {


    include_once($file_path."inc_user_class.php");
    include($ld_engine_path."users_get_object.php");

    if($current_user->plugin_info["gradient_color_start"] != $current_user->plugin_info["gradient_color_end"]) {

       if ($current_user->plugin_info["gradient_color_start"]=="") {$current_user->plugin_info["gradient_color_start"]=$default_color;}
       if (($current_user->plugin_info["gradient_color_start"] < 0) or ($current_user->plugin_info["gradient_color_start"] >= count($registered_colors))) {$current_user->plugin_info["gradient_color_start"]=$default_color;}

       if ($current_user->plugin_info["gradient_color_end"]=="") {$current_user->plugin_info["gradient_color_end"]=$default_color;}
       if (($current_user->plugin_info["gradient_color_end"] < 0) or ($current_user->plugin_info["gradient_color_end"] >= count($registered_colors))) {$current_user->plugin_info["gradient_color_start"]=$default_color;}

       $start_color = $registered_colors[$current_user->plugin_info["gradient_color_start"]][1];
       $end_color = $registered_colors[$current_user->plugin_info["gradient_color_end"]][1];

       $sc = parse_rgb_text($start_color);
       $ec = parse_rgb_text($end_color);

       $Msg = gradtext($Msg, $sc['r'], $sc['g'], $sc['b'], $ec['r'], $ec['g'], $ec['b']);
	 $Msg=$Msg."<div style=\"display:none\">$_pre_msg</div>";
    }
  }

}
function parse_rgb_text($RGB){
        $RGB = str_replace("#", "", $RGB);
        $ret['r']=hexdec($RGB[0].$RGB[1]);
        $ret['g']=hexdec($RGB[2].$RGB[3]);
        $ret['b']=hexdec($RGB[4].$RGB[5]);
        return $ret;
}

function gradtext($text, $sr, $sg, $sb, $er, $eg, $eb) {
               function tohex($int){
                       $res=dechex($int);
                       if(strlen($res)<2){
                               $res="0".$res;
                       }
                       return $res;
               }
               function findSpec($str){
                       $s_l=strlen($str);
                       $started=false;
                       $coord=0;
                       for($i=0;$i<$s_l;$i++){
                               if(($str[$i]=="&")&&(!$started)){
                                       $coord=$i;
                                       $started=true;
                               }elseif(($str[$i]==";")&&($started)){
                                       $array[$coord]=array('text'=>substr($str,$coord,($i-$coord)+1));
                                       $started=false;
                               }
                       }
                       return $array;
               }
       $s_l=strlen($text);
       $begin = true;

       for($i=0; $i <=$s_l; $i++) {
               if ($text[$i]=="<" && $begin) {
                       $tmp[$i] = false;
                       $begin = false;
               } elseif (!$begin) {
                       $tmp[$i] = false;
                       if ($text[$i]==">") {
                               $begin = true;
                       }
               } else {
                       $tmp[$i] = true;
                       $tmpCount++;
               }
       }
       $dr = (($er - $sr)/ ($tmpCount-2));
       $dg = (($eg - $sg)/ ($tmpCount-2));
       $db = (($eb - $sb)/ ($tmpCount-2));
       $cr=$sr;
       $cg=$sg;
       $cb=$sb;
       $spec=findSpec($text);
       for($i=0; $i <$s_l; $i++) {
               if ($tmp[$i]) {
                       if(!empty($spec[$i]['text'])){
                               $sc_t=$spec[$i]['text'];
                               $result.="<font color=\"#".tohex($cr).tohex($cg).tohex($cb)."\">";
                               $result.=$sc_t."</font>";
                               $i+=(strlen($sc_t)-1);
                       }else{
                               $result.="<font color=\"#".tohex($cr).tohex($cg).tohex($cb)."\">";
                               $result.=$text[$i]."</font>";
                       }
                       $cr+= $dr; $cg+= $dg; $cb+= $db;
               } else {
                       $result.=$text[$i];
               }

       }
       return $result;
}
?>