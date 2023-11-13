<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

//preminder.dat's fields:
define("PR_UID", 0);
define("PR_NICK", 1);
define("PR_TIME", 2);
define("PR_CODE", 3);
define("PR_TOTALFIELDS", 4);


/**
 * param $look_for - nick in 'canonical' form
 * param $code - code to send
 * return - blank string if everything is ok, error message if something is wrong
**/
function save_code($look_for, $code) {
	global $data_path, $user_data_file, $w_pr_already_sent, $w_search_no_found ;
	$error = "";
	$user_mail = "";
	$already_wait = 0;
	$fp = fopen($user_data_file, "rb");
	$found = 0;
	flock($fp, LOCK_EX);
	fseek($fp,0);
	while($user = fgets($fp, 4096)) {
		if (strlen($user)<7) continue;
		$u_data = explode("\t",trim($user));
		if ($u_data[4] == $look_for) {
			$user_id = $u_data[0];
			$found = 1;
			//echo "found :)".$u_data[5]."!";
			$user_mail = $u_data[5];
			
			$wait_list = array();
			$pfp = fopen($data_path."preminder.dat", "ab+") or die("cannot open file");
			flock($pfp, LOCK_EX) or die("cannot lock");
			while ($line = fgets($pfp, 4096)) {
				$wait_user =explode("\t",trim($line), PR_TOTALFIELDS);
				if ($wait_user[PR_TIME] >time() - 86400) {
					$wait_list[] = $line;
					if ($wait_user[PR_NICK] == $look_for) {
						$already_wait = 1;
					}
				}
			}
			if (!$already_wait) {
				$new_waiter = array();
				$new_waiter[PR_UID] = $user_id;
				$new_waiter[PR_NICK] = $look_for;
				$new_waiter[PR_TIME] = time();
				$new_waiter[PR_CODE] = $code;
				$wait_list[] = implode("\t", $new_waiter)."\n";
			}
			if (!is_array($wait_list)) $wait_list = array();
			ftruncate($pfp, 0);
			fwrite($pfp, implode("", $wait_list));
			fflush($pfp);
			flock($pfp, LOCK_UN);
			fclose($pfp);
			break;
		}
	}
	flock($fp, LOCK_UN);
	fclose($fp);
	if (!$found) $error .=  str_replace("~", $look_for, $w_search_no_found);
	if ($already_wait) $error .= $w_pr_already_sent;
	return array($error, $user_mail);
}

function update_password($new_password, $code) {
	global $data_path, $ld_engine_path, $w_pr_no_code, 
	//for included files
		$w_search_no_found,$user_data_file, $w_succ_updated;
	$info_message = "";
	$error = "";
	$is_valid = 0;
	$wait_list = array();
	$pfp = fopen($data_path."preminder.dat", "ab+") or die("cannot open file");
	flock($pfp, LOCK_EX) or die("cannot lock");
	while ($line = fgets($pfp, 4096)) {
		$wait_user = explode("\t", trim($line), PR_TOTALFIELDS);
		if ($wait_user[PR_TIME] >time() - 86400) {
	 		if ($wait_user[PR_CODE] == $code) {
				$is_valid = 1;
				$user_name = $wait_user[PR_NICK];
				$user_id = $wait_user[PR_UID];
			} else
				$wait_list[] = $line;
		}
	}
	if (!is_array($wait_list)) $wait_list = array();
	ftruncate($pfp, 0);
	fwrite($pfp, implode("", $wait_list));
	fflush($pfp);
	flock($pfp, LOCK_UN);
	fclose($pfp);
	if ($is_valid) {
		//changing password here.
		$is_regist = $user_id;
		include("inc_user_class.php");
		include($ld_engine_path."users_get_object.php");
		$current_user->password = md5($new_password);
		include($ld_engine_path."user_info_update.php");
	} else $error = $w_pr_no_code;
	
	return $error;
}
?>