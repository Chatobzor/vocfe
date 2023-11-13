<?php
if (!defined("_COMMON_")) exit;

//Patch for not valid user if session was not his =)
//added for Christmas Edition SE
//in inc_common.php browser_hash and c_hash variables have been defined already
//and $current_user already have been defined above :)
include("get_IP.lib.php3");

$IPTest     = trim(str_replace("p", "", $current_user->IP));
$IP         = trim(str_replace("p", "", $IP));
$logIP      = $IP;

$pr_arr_in  = explode(":", $IP);
$pr_arr_out = explode(":", $IPTest);

$pr_arr_in[0]  = trim($pr_arr_in[0]);
$pr_arr_out[0] = trim($pr_arr_out[0]);

$IP         = substr($pr_arr_in[0], 0 , strrpos($pr_arr_in[0],"."));
$IPTest     = substr($pr_arr_out[0], 0 , strrpos($pr_arr_out[0],"."));

$IsProfileHacked = false;

// if($current_user->check_browser) {
   // if(intval($browser_hash) != intval($current_user->browser_hash)) $IsProfileHacked = true;
// }

if($IP != $IPTest and trim($current_user->show_ip) == "") $IsProfileHacked = true;

if($current_user->check_cookie) {
   if(strcasecmp($current_user->cookie_hash, $c_hash) != 0) $IsProfileHacked = true;
}

if($IsProfileHacked) {
      include($file_path."designes/".$design."/common_body_start.php");
      echo "<p align=center>$w_security_error</p>";
      include($file_path."designes/".$design."/common_body_end.php");

      include($engine_path."users_get_list.php");

      $fp = fopen($data_path."users/hacking.log", "a+b");
      if($fp) {
              flock($fp, LOCK_EX);
              fwrite($fp, date("d-m-Y H:i:s")."\tAttempt to hack ($check_type): "."IP: $logIP,"." BID: $browser_hash,"." CID: $c_hash, session = $session, target nickname = ".$cu_array[USER_NICKNAME]."/".$current_user->nickname."\n\t\t\tPossible hackers (online!): ");

              for($i = 0; $i < count($users); $i++) {
                   $user_array          = explode("\t",trim($users[$i]), USER_TOTALFIELDS);
                   $user_array[USER_IP] = str_replace("p", "", $user_array[USER_IP]);
                   if($user_array[USER_IP] == $logIP and $user_array[USER_BROWSERHASH] == $browser_hash) fwrite($fp, $user_array[USER_NICKNAME]. "(ip, bh) ");
                   else if($user_array[USER_IP] == $logIP and $user_array[USER_BROWSERHASH] != $browser_hash) fwrite($fp, $user_array[USER_NICKNAME]. "(ip) ");
                   else if($user_array[USER_IP] != $logIP and $user_array[USER_BROWSERHASH] == $browser_hash) fwrite($fp, $user_array[USER_NICKNAME]. "(bh) ");
              }
              fwrite($fp, "\n");
              fwrite($fp, "\t\t\tProfile info: "."IP: $logIP,"." BID: ".$current_user->browser_hash.","." CID: ".$current_user->cookie_hash."\n");
              fwrite($fp, "\t\t\tIPs test: attacker: [$IP],"." victim: [".$IPTest."]\n");
              flock($fp, LOCK_UN);
              fclose($fp);
              }

      exit;
}
?>