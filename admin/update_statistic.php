<?php

if (isset($HTTP_SERVER_VARS['SERVER_ADDR']))
{
        echo "you cannot update statistic thourgh web-interface";
        exit;
}
//error_reporting(E_ERROR);
require("D:/Temp/xampp/htdocs/inc_common.php");

$users_to_show = array();
$mess_to_show = array();
include($engine_path."users_get_list.php");
$now = my_time();

$two_days_before = date("Y-m-d",mktime(0,0,0,date("m",$now),date("d",$now)-2,date("Y",$now)));
@unlink($data_path."statistic/".$two_days_before.".stat");
@unlink($data_path."statistic/".$two_days_before.".mess");
@unlink($data_path."statistic/".$two_days_before."_mess.png");
@unlink($data_path."statistic/".$two_days_before."_users.png");

$max_users = 1;
$max_mess = 1;

$stat_file_prefix = date("Y-m-d",$now);
$stat_file = $data_path."statistic/".$stat_file_prefix.".stat";
$stat_mess_file = $data_path."statistic/".$stat_file_prefix.".mess";


#!!
$fp = fopen($data_path."mess_stat.dat", "a+");
flock($fp, LOCK_EX);
fseek($fp,0);
$normal_messages = intval(str_replace("\n","",@fgets($fp,1024)));
$private_messages = intval(str_replace("\n","",@fgets($fp,1024)));

ftruncate($fp,0);
fwrite($fp,"0\n0");
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

$fp = fopen($stat_mess_file, "a+");
flock($fp, LOCK_EX);
fseek($fp,0);
$mess_count = 0;

while($data = fgetcsv ($fp, 1000, "\t") ) {
        $mess_to_show[$mess_count]["time"] = $data[0];
        $mess_to_show[$mess_count]["normal"] = $data[1];
        $mess_to_show[$mess_count]["private"] = str_replace("\n","",$data[2]);

        $mess_count++;
}


$mess_to_show[$mess_count]["time"] = $now;
$mess_to_show[$mess_count]["normal"] = $normal_messages;
$mess_to_show[$mess_count]["private"] = $private_messages;


fwrite($fp,$now."\t".$normal_messages."\t".$private_messages."\n");
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
##



$fp = fopen($stat_file, "a+");
flock($fp, LOCK_EX);
fseek($fp,0);
$users_count = 0;
while($data = fgetcsv ($fp, 1000, "\t") ) {
        $users_to_show[$users_count]["time"] = $data[0];
        $ttt = str_replace("\n","",$data[1]);
        $users_to_show[$users_count]["num"] = $ttt;
        if ($ttt>$max_users)$max_users = $ttt;
        $users_count++;
}

$now = my_time();
$users_to_show[$users_count]["time"] = $now;
$users_to_show[$users_count]["num"] = count($users);
if($users_to_show[$users_count]["num"]>$max_users) $max_users = $users_to_show[$users_count]["num"];

fwrite($fp,$now."\t".count($users)."\n");
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

$start_time = mktime(0,0,0,date("m",$now),date("d",$now),date("Y",$now));



$users_count = 0;
$mess_count = 0;
$im = ImageCreate(700,320);
$backgr = imageColorAllocate($im, 255,255,255);
$line_color = imageColorAllocate($im, 0,0,192);
$axes_color = imageColorAllocate($im, 0,0,0);
$dash_color = imageColorAllocate($im, 192,192,192);
$middle_color = imageColorAllocate($im, 255,0,0);
$style = array ($dash_color, $dash_color, $dash_color, $dash_color, $backgr,$backgr);
imagesetstyle ($im, $style);


$im2 = ImageCreate(700,320);
$backgr2 = imageColorAllocate($im2, 255,255,255);
$axes_color2 = imageColorAllocate($im2, 0,0,0);
$dash_color2 = imageColorAllocate($im2, 192,192,192);
$total_color = imageColorAllocate($im2, 255,0,0);
$normal_color = imageColorAllocate($im2, 0,0,255);
$private_color = imageColorAllocate($im2, 0,255,0);
$style2 = array ($dash_color2, $dash_color2, $dash_color2, $dash_color2, $backgr2,$backgr2);
imagesetstyle ($im2, $style2);

$prev_time = $start_time;
$last_time = 0;
//first record has data for messages sice 23.58 till 24.00 yesterday
$mess_count = 1;

for ($i=300;$i<86401;$i+=300) {
        $records_in_period = 0;
        $priv_in_period = 0;
        $normal_in_period = 0;
        while($mess_to_show[$mess_count]["time"]<($start_time+$i) && $mess_count<count($mess_to_show)) {
                $records_in_period++;
                $last_time = $mess_to_show[$mess_count]["time"];
                $priv_in_period += $mess_to_show[$mess_count]["private"];
                $normal_in_period += $mess_to_show[$mess_count]["normal"];
                $mess_count++;
        }
        $total_in_period = $priv_in_period+$normal_in_period;
        $time_period = $last_time - $prev_time;
        $prev_time = $last_time;
        if ($records_in_period>0 && $time_period >0) {
                $normal_to_show = round($normal_in_period/$time_period*60*5)/5;
                $private_to_show = round($priv_in_period/$time_period*60*5)/5;
                $total_to_show = $normal_to_show+$private_to_show;
                if ($total_to_show>$max_mess)$max_mess = $total_to_show;
        }

}
//$mess_count = 0;
//first record has data for messages sice 23.58 till 24.00 yesterday
$mess_count = 1;

$prev_time = $start_time;
$total_messages = 0;
$total_messages_divider = 0;
$total_users = 0;
$total_users_divider = 0;
for ($i=300;$i<86401;$i+=300) {
        $records_in_period = 0;
        $total_in_period = 0;
        while($users_to_show[$users_count]["time"]<($start_time+$i) && $users_count<count($users_to_show)) {
                $records_in_period++;
                $total_in_period += $users_to_show[$users_count]["num"];
                $users_count++;
        }
        if ($records_in_period>0) {
                $to_show = round($total_in_period/$records_in_period);
                $total_users += $to_show;
                $total_users_divider++;
                imageRectangle($im, ($i/150)+53, 290, ($i/150)+53, 290-round($to_show/$max_users*280), $line_color);
        }
        $records_in_period = 0;
        $priv_in_period = 0;
        $normal_in_period = 0;
        while($mess_to_show[$mess_count]["time"]<($start_time+$i) && $mess_count<count($mess_to_show)) {
                $records_in_period++;
                $last_time = $mess_to_show[$mess_count]["time"];
                $priv_in_period += $mess_to_show[$mess_count]["private"];
                $normal_in_period += $mess_to_show[$mess_count]["normal"];
                $mess_count++;
        }
        $total_in_period = $priv_in_period+$normal_in_period;
        $time_period = $last_time - $prev_time;
        $prev_time = $last_time;
        if ($records_in_period>0 && $time_period >0) {

                $normal_to_show = round($normal_in_period/$time_period*60*5)/5;
                $private_to_show = round($priv_in_period/$time_period*60*5)/5;
                $total_to_show = $normal_to_show+$private_to_show;
                $total_messages += $total_to_show;
                $total_messages_divider++;
                imageRectangle($im2, ($i/150)+52, 290, ($i/150)+53, 290-round($total_to_show/$max_mess*280), $total_color);
                if ($normal_to_show>=$private_to_show) {

                        imageRectangle($im2, ($i/150)+52, 290, ($i/150)+53, 290-round($normal_to_show/$max_mess*280), $normal_color);
                        imageRectangle($im2, ($i/150)+52, 290, ($i/150)+53, 290-round($private_to_show/$max_mess*280), $private_color);
                }
                else {
                        imageRectangle($im2, ($i/150)+52, 290, ($i/150)+53, 290-round($private_to_show/$max_mess*280), $private_color);
                        imageRectangle($im2, ($i/150)+52, 290, ($i/150)+53, 290-round($normal_to_show/$max_mess*280), $normal_color);
                }
        }
}

for ($i=0;$i<25;$i++) {
        imageLine($im, 54+24*$i, 10, 54+24*$i, 290, IMG_COLOR_STYLED);
        imagestring ($im, 3, 50+24*$i, 290,  $i, $axes_color);
}

$pixels_per_user = 280/$max_users;
if ($pixels_per_user>20)
        $users_increment = $pixels_per_user;
else {
        $users_increment = floor(40/$pixels_per_user)*$pixels_per_user ;
        $us_step = $users_increment/$pixels_per_user;
        $us_step = floor($us_step / 10 ) * 10;
        if ($us_step == 0) $us_step = 5;
        $users_increment =  $us_step*$pixels_per_user;
}

for ($i=$users_increment;$i<280;$i+=$users_increment) {
        imageLine($im, 54, 290-round($i), 630, 290-round($i), IMG_COLOR_STYLED);
        imagestring ($im, 3,  30, 285-round($i),  $i/$pixels_per_user, $axes_color);
}




$pixels_per_mess = 280/$max_mess;
if ($pixels_per_mess>20)
        $mess_increment = $pixels_per_mess;
else {
        $mess_increment = floor(40/$pixels_per_mess)*$pixels_per_mess;
        $me_step = $mess_increment/$pixels_per_mess;
        $me_step = floor($me_step/10) * 10;
        if ($me_step == 0) $me_step = 5;
        $mess_increment = $me_step*$pixels_per_mess;
}

for ($i=$mess_increment;$i<280;$i+=$mess_increment) {
        imageLine($im2, 54, 290-round($i), 630, 290-round($i), IMG_COLOR_STYLED);
        imagestring ($im2, 3,  30, 285-round($i),  $i/$pixels_per_mess, $axes_color2);
}

for ($i=0;$i<25;$i++) {
        imageLine($im2, 54+24*$i, 10, 54+24*$i, 290, IMG_COLOR_STYLED);
        imagestring ($im2, 3, 50+24*$i, 290,  $i, $axes_color2);
}

$style = array ($middle_color, $middle_color, $middle_color, $middle_color, $backgr, $middle_color, $backgr);
imagesetstyle ($im, $style);

$style2 = array ($total_color, $total_color, $total_color, $total_color, $backgr, $total_color, $backgr);
imagesetstyle ($im2, $style2);

if ($total_messages_divider>0) {
        $aver_y = 290-round($total_messages/$total_messages_divider/$max_mess*280);
        imageLine($im2, 54, $aver_y, 630, $aver_y, IMG_COLOR_STYLED);
        imageString($im2, 4, 636, $aver_y-23, "average:", $total_color);
        imageString($im2, 4, 636, $aver_y+5, "mess/min", $total_color);
        imageString($im2, 5, 640, $aver_y-8, round($total_messages/$total_messages_divider*100)/100, $total_color);

}
if ($total_users_divider>0) {
        imageLine($im, 54, 290-round($total_users/$total_users_divider/$max_users*280), 630, 290-round($total_users/$total_users_divider/$max_users*280), IMG_COLOR_STYLED);

        $aver_y = 290-round($total_users/$total_users_divider/$max_users*280);
        imageLine($im, 54, $aver_y, 630, $aver_y, IMG_COLOR_STYLED);
        imageString($im, 4, 636, $aver_y-23, "average:", $middle_color);
        imageString($im, 4, 636, $aver_y+5, "users", $middle_color);
        imageString($im, 5, 640, $aver_y-8, round($total_users/$total_users_divider*100)/100, $middle_color);

}


imageRectangle($im,53,10,631,290,$axes_color);
imageRectangle($im2,53,10,631,290,$axes_color2);

imagepng($im,$data_path."statistic/".$stat_file_prefix."_users.png");
imageDestroy($im);

imagepng($im2,$data_path."statistic/".$stat_file_prefix."_mess.png");
imageDestroy($im2);
?>