<?php
require_once("inc_common.php");
header("content-type:image/png");
header("Expires: Mon 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D d M Y H:i:s") . "GMT"); 
header("Cache-Control: no-cache must-revalidate");
header("Pragma: no-cache");

include("inc_user_class.php");
$is_regist = intval($HTTP_SERVER_VARS["QUERY_STRING"]);
$current_user = new User();
@include($ld_engine_path."users_get_object.php");
if ($current_user->enable_web_indicator == 0)
	readfile($data_path."statuses/disabled.png");
else {
	include($engine_path."user_status.php");
	if($exists) {
		if (file_exists($data_path."statuses/".floor($is_regist/2000)."/base_online_".$is_regist.".png"))
			readfile($data_path."statuses/".floor($is_regist/2000)."/base_online_".$is_regist.".png");
		else {
			if (function_exists('imagecreatefrompng')&&function_exists('imagettftext')) {
				$im = imageCreateFromPng($data_path."statuses/base_blank.png");
				$black = imagecolorallocate ($im, 0, 0, 0);
				$green = imagecolorallocate ($im, 0, 255, 0);
				$text = $current_user->nickname;
				
				$sizes = imagettfbbox(12,0,$data_path."statuses/font.ttf", $text);
				$dots = 0;
				while ($sizes[2]-$sizes[0]>70) {
					$text = substr($text,0,strlen($text)-1);
					$sizes = imagettfbbox(12,0,$data_path."statuses/font.ttf", $text);
					$dots = 1;
				}
				imagettftext ($im, 12, 0, 5, 26, $green, $data_path."statuses/font.ttf", $text);
				if ($dots) {
					imagesetpixel($im, 78, 26, $green);
					imagesetpixel($im, 80, 26, $green);
					imagesetpixel($im, 82, 26, $green);
				}
				if(!is_dir($data_path."statuses/".floor($is_regist/2000)))
					mkdir($data_path."statuses/".floor($is_regist/2000),0777);
				imagePng($im,$data_path."statuses/".floor($is_regist/2000)."/base_online_".$is_regist.".png");
				imageDestroy ($im);
			}
			if (file_exists($data_path."statuses/".floor($is_regist/2000)."/base_online_".$is_regist.".png"))
				readfile($data_path."statuses/".floor($is_regist/2000)."/base_online_".$is_regist.".png");
			else
				readfile($data_path."statuses/base_online.png");
		}
		
	}
	else  {
		if (file_exists($data_path."statuses/".floor($is_regist/2000)."/base_offline_".$is_regist.".png"))
			readfile($data_path."statuses/".floor($is_regist/2000)."/base_offline_".$is_regist.".png");
		else {
			if (function_exists('imagecreatefrompng')&&function_exists('imagettftext')) {
				$im = imageCreateFromPng($data_path."statuses/base_blank.png");
				$black = imagecolorallocate ($im, 0, 0, 0);
				$green = imagecolorallocate ($im, 255, 0, 0);
				$text = $current_user->nickname;
				
				$sizes = imagettfbbox(12,0,$data_path."statuses/font.ttf", $text);
				$dots = 0;
				while ($sizes[2]-$sizes[0]>70) {
					$text = substr($text,0,strlen($text)-1);
					$sizes = imagettfbbox(12,0,$data_path."statuses/font.ttf", $text);
					$dots = 1;
				}
				imagettftext ($im, 12, 0, 5, 26, $green, $data_path."statuses/font.ttf", $text);
				if ($dots) {
					imagesetpixel($im, 78, 26, $green);
					imagesetpixel($im, 80, 26, $green);
					imagesetpixel($im, 82, 26, $green);
				}
				if(!is_dir($data_path."statuses/".floor($is_regist/2000)))
					mkdir($data_path."statuses/".floor($is_regist/2000),0777);
				imagePng($im,$data_path."statuses/".floor($is_regist/2000)."/base_offline_".$is_regist.".png");

				imageDestroy ($im);
			}
			if (file_exists($data_path."statuses/".floor($is_regist/2000)."/base_offline_".$is_regist.".png"))
				readfile($data_path."statuses/".floor($is_regist/2000)."/base_offline_".$is_regist.".png");
			else
				readfile($data_path."statuses/base_offline.png");
		}
	}
}
?>