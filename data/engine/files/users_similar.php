<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
define("STEP_POINT", 50);
$ttt = str_replace("\\*","([".$nick_available_chars."]+)",quotemeta($user_to_search));
$fp = fopen($data_path."similar_nicks.tmp", "rb");
flock($fp, LOCK_EX);
fseek($fp,0);
$ii=0;

if($check_sim_ip and strlen(trim($similar_IP)) == 0) $check_sim_ip = 0;
if($check_sim_hash and strlen(trim($similar_browser_hash)) == 0) $check_sim_hash = 0;
if($check_sim_email and strlen(trim($similar_email)) == 0) $check_sim_email = 0;
if($check_sim_pass_hash and strlen(trim($similar_pass_hash)) == 0) $check_sim_pass_hash = 0;
if($check_sim_cookie and strlen(trim($similar_cookie)) == 0) $check_sim_cookie = 0;

/*
echo "IP:".$check_sim_ip."<br>";
echo "ID:".$check_sim_hash."<br>";
echo "EM:".$check_sim_email."<br>";
echo "PW:".$check_sim_pass_hash."<br>";
echo "CC:".$check_sim_cookie."<br>";
echo "SIP:".$similar_IP."<br>";
echo "SID:".$similar_browser_hash."<br>";
echo "SEM:".$similar_email."<br>";
echo "SPW:".$similar_pass_hash."<br>";
echo "SPC:".$similar_cookie."<br>";
*/
if(isset($similar_rez)) unset($similar_rez);

$user_count = 0;
$view_count = 0;

$blank_dump = "";
for($i = 0; $i < 257; $i++) $blank_dump .= " ";

echo "[";
while ($data = fgets($fp, 4096)) {
        echo " ";
        $user = str_replace("\r","",str_replace("\n","",$data));
        list($t_id, $t_nickname, $t_password, $t_email,$t_IP, $t_browser_hash, $t_cookie_hash, $t_points) = explode("\t",$user);

    $user_count++;
    if(($user_count - $view_count) > STEP_POINT) { echo $blank_dump; echo "."; ob_flush(); $view_count = $user_count; }
    
    $addHim = false;
    if($check_sim_ip and $t_IP == $similar_IP) $addHim = true;

    if($check_sim_hash) {
         if($t_browser_hash == $similar_browser_hash) {
             if(!$check_sim_ip) $addHim = true;
         }
        else $addHim = false;
    }

    if($check_sim_email) {
             if($t_email == $similar_email) {
       // echo "here ".$current_user->nickname.",id=$t_id<br>";
          if(!$addHim) {
            if(!$check_sim_ip and !$check_sim_hash) $addHim = true;
          }
        }
        else $addHim = false;
    }

   if($check_sim_pass_hash) {
             if($t_password == $similar_pass_hash) {
          if(!$addHim) {
            if(!$check_sim_ip and !$check_sim_hash and !$check_sim_email) $addHim = true;
          }
        }
        else $addHim = false;
    }

    if($check_sim_cookie) {
             if($t_cookie_hash == $similar_cookie) {
          if(!$addHim) {
            if(!$check_sim_ip and !$check_sim_hash and !$check_sim_email and !$check_sim_pass_hash) $addHim = true;
          }
        }
        else $addHim = false;
    }

    if($addHim and $t_id > 1) $similar_rez[] = $t_nickname." (".$t_IP.", browser_id=".$t_browser_hash.",reg_id=$t_id)";

}
echo "]";
flock($fp, LOCK_UN);
fclose($fp);

sort($similar_rez, SORT_STRING);
?>