<?php
include("inc_common.php");

define("REITING_MAXHITS", "50");

error_reporting(E_ALL);

function cmp_rei($a, $b)
{
   if($a["points"] > $b["points"]) return -1;
   else return 1;
}

$fp = fopen($data_path."similar_nicks.tmp", "r");
flock($fp, LOCK_EX);
fseek($fp,0);

if(isset($reiting_rez)) unset($reiting_rez);
$i = 0;

while ($data = fgets($fp, 4096)) {
        $user = str_replace("\r","",str_replace("\n","",$data));
        list($t_id, $t_nickname, $t_password, $t_email,$t_IP, $t_browser_hash, $t_cookie_hash, $t_points, $t_reiting, $t_money, $t_photo) = explode("\t",$user);

       $t_id = intval(trim($t_id));

       if($t_photo > 0 and $t_nickname != "") {
            $reiting_rez[$i]["nick"]   = $t_nickname;
            $reiting_rez[$i]["points"] = $t_photo;
            $reiting_rez[$i]["id"] = $t_id;
            $i++;
        }

}
flock($fp, LOCK_UN);
fclose($fp);

usort($reiting_rez, "cmp_rei");
echo "<p>";

$MaxHits = 20;
if(count($reiting_rez) < 20) $MaxHits = count($reiting_rez);
?>
<table border="0" cellspacing="0" cellpadding="0" align=CENTER>
<tr><td align="center" valign="middle"><b>№</b></td>
<td valign="middle" align="center"></td>
<td valign="middle" align="center"><b>Нік</b></td>
<td align="center" valign="middle"><b>Рейтинг фото</b></td>
</tr>
<tr><td colspan=3>
<?php

for($j=0; $j < $MaxHits; $j++) {

       $is_regist = $reiting_rez[$j]["id"];

       $pic_name = "" . $is_regist . ".big.gif";
       $newFileExtensions = "gif";
       if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
       if ($pic_name == "") {
           $pic_name = "" . $is_regist . ".big.jpg";
           if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
           else $newFileExtensions = "jpg";
        }
        if ($pic_name == "") {
            $pic_name = "" . $is_regist . ".big.jpeg";
            if (!file_exists($file_path."photos/".floor($is_regist/2000)."/$pic_name")) $pic_name="";
             else  $newFileExtensions = "jpg";
        }

        if($pic_name != "") {
            if(!file_exists($file_path."top20/top_".$j."_".$is_regist.".jpg")) {

                foreach (glob($file_path."top20/top_".$j."*.jpg") as $filename) {
                      unlink($filename);
                }

                list($roz_width, $roz_height, $type, $attr) = getimagesize($file_path."photos/".floor($is_regist/2000)."/$pic_name");
                if($type != 1 and $type != 2) $ok = 0;
                else $ok = 1;

                if($ok) {
                    copy($file_path."photos/".floor($is_regist/2000)."/$pic_name", $file_path."top20/top_".$j."_".$is_regist.".jpg");

                    //echo $file_path."photos/".floor($is_regist/2000)."/$pic_name => ".$file_path."top20/top_".$j."_".$is_regist.".jpg <br>";

                    chmod($file_path."top20/top_".$j."_".$is_regist.".jpg", 0777);


                    if(function_exists("imagecreatefromgif") and $newFileExtensions == "gif") {
                       $imgCanvas        = imagecreatefromgif($file_path."top20/top_".$j."_".$is_regist.".jpg");
                       $imgCanvas_width  = imagesx($imgCanvas);
                       $imgCanvas_height = imagesy($imgCanvas);
                       $gdSupport = true;
                    }
                    if(function_exists("imagecreatefromjpeg") and ($newFileExtensions == "jpg" or $newFileExtensions == "jpeg")) {
                       $imgCanvas        = imagecreatefromjpeg($file_path."top20/top_".$j."_".$is_regist.".jpg");
                       $imgCanvas_width  = imagesx($imgCanvas);
                       $imgCanvas_height = imagesy($imgCanvas);
                       $gdSupport = true;
                    }

                    $img = $imgCanvas;

                    if ($img and $gdSupport) {
                        # Get image size and scale ratio
                        $width = imagesx($img);
                        $height = imagesy($img);

                        $MAX_WIDTH  = 65;
                        $MAX_HEIGHT = 65;

                        $scale = min($MAX_WIDTH/$width, $MAX_HEIGHT/$height);

                        # If the image is larger than the max shrink it
                        if ($scale < 1) {
                                $new_width = floor($scale*$width);
                                $new_height = floor($scale*$height);

                                # Create a new temporary image
                                $tmp_img = imagecreatetruecolor($new_width, $new_height);

                                # Copy and resize old image into new image
                                imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                imagedestroy($img);
                                $img = $tmp_img;
                     }

                     imagejpeg($img,  $file_path."top20/top_".$j."_".$is_regist.".jpg", 100);
                     imagedestroy($img);
                }
              }
           }
        }

        echo "<tr>";
        echo "<td>".($j+1).".&nbsp;&nbsp;</td>";
        echo "<td align=center><a href=\"".$chat_url."users/".$reiting_rez[$j]["nick"]."\"><img src=\"".$chat_url."top20/top_".$j."_".$is_regist.".jpg\" vspace=10 hspace=10 border=4 style='border-color=#BFC3D5'></a></td>";
        echo "<td><a href=\"".$chat_url."users/".$reiting_rez[$j]["nick"]."\">";
        if($j < 20) echo "<b>".$reiting_rez[$j]["nick"]."</b>";
        else echo $reiting_rez[$j]["nick"];
        echo "</a>&nbsp;</td><td>".$reiting_rez[$j]["points"]."</td></tr>";
}
?>
<tr><td colspan=3>
</td></tr>
</table>