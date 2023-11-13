<?php
if (!defined("_COMMON_")) {echo "stop";exit;}

function regmail_add($new_user_name, $password, $new_user_mail, $regkey) {
        global $user_data_file, $data_path, $file_path, $max_per_mail,
                $w_already_registered, $session, $w_try_again, $design, $w_title, $current_design, $w_max_per_mail,$w_mail_used,$charset;
        $already_on_mail = 0;
        $canon_view = to_canon_nick($new_user_name);
        $fp = fopen($user_data_file, "a+b");
        flock($fp, LOCK_EX);
        fseek($fp,0);
        while($user = fgets($fp, 4096)) {
                $u_data = explode("\t",trim($user));
                if ($u_data[4] == $canon_view) {
                        $error_text = str_replace("~", $new_user_name, $w_already_registered)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
                        include($file_path."designes/".$design."/error_page.php");
                        flock($fp, LOCK_UN);
                        fclose($fp);
                        exit;
                }
                //error reporting for previous versions? without 5-th field with email...
                if (strcmp(strtolower($u_data[5]), strtolower($new_user_mail)) == 0)
                        $already_on_mail++;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        if ($already_on_mail >= $max_per_mail) {
                $error_text = $w_mail_used."<br>".str_replace("~", $max_per_mail, $w_max_per_mail)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
                include($file_path."designes/".$design."/error_page.php");
                exit;
        }
        $already_registered = 0;
        $new_regmail_users = array();
        $fp = fopen($data_path."regmail.dat", "a+b");
        flock($fp, LOCK_EX);
        fseek($fp,0);
        while($user = fgets($fp, 4096)) {
                $record = str_replace("\r","",str_replace("\n","",($user)));
                $u_data = explode("\t",$record);
                //time -- nick -- password -- email -- regkey -- canon view.
                //code is valid only for 24 hours
                if ($u_data[0] >time() - 86400) {
                        $new_regmail_users[] = $record;
                        if ($u_data[5] == $canon_view)
                                $already_registered = 1;
                }
        }
        $new_regmail_users[] = time()."\t".$new_user_name."\t".$password."\t".$new_user_mail."\t".$regkey."\t".$canon_view."\t".$new_user_mail;
        ftruncate($fp,0);
        fwrite($fp, implode("\n",$new_regmail_users));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        if ($already_registered) {
                $error_text = str_replace("~", $new_user_name, $w_already_registered)."<br><a href=\"registration_form.php?session=$session\">$w_try_again</a>";
                include($file_path."designes/".$design."/error_page.php");
                exit;
        }
}

function regmail_activate($regkey) {
        global $user_data_file, $data_path, $ld_engine_path,
        $file_path, $w_succesfull_reg, $w_already_registered, $w_try_again, $session, $design, $w_title, $current_design,$w_max_per_mail,$w_mail_used,$charset;
        global $vocplus_useguardian, $registration_mailconfirm, $new_user_mail;

        $activated = 0;
        $new_regmail_users = array();
        $fpr = fopen($data_path."regmail.dat", "a+b");
        flock($fpr, LOCK_EX);
        fseek($fpr,0);
        while($r_user = fgets($fpr, 4096)) {
                $r_record = trim($r_user);
                $ru_data = explode("\t",$r_record);
                //time -- nick -- password -- email -- regkey -- canon view.
                //code is valid only for 24 hours
                if ($ru_data[0] >time() - 86400) {
                        if ($ru_data[4] == $regkey) {
                                //register user.
                                $new_user_name = $ru_data[1];
                                $passwd1 = $ru_data[2];
                                $new_user_mail = $ru_data[6];
                                //for reg_add -- user_name is nickname if the user in the chat.
                                $user_name = "";

                                include($ld_engine_path."registration_fake.php");

                                $user->registration_mail = $ru_data[3];
                                $fp = fopen ($data_path."users/".floor($t_id/2000)."/".$t_id.".user", "w");
                                if (!$fp) trigger_error("Cannot open user-data file users/".floor($t_id/2000)."/".$t_id.".msg for writing. Please, check permissions", E_USER_ERROR);
                                if (!flock($fp, LOCK_EX)) trigger_error("Cannot LOCK user-data file. Do you use Win 95/98/Me?", E_USER_WARNING);
                                flock($fp, LOCK_EX);

                                $user->registered    = true;
                                $user->email         = $new_user_mail;
                                $user->registered_at = my_time();

                                fwrite($fp,serialize($user));
                                fflush($fp);
                                flock($fp, LOCK_UN);
                                fclose($fp);
                                $activated = 1;
                        }
                        else
                                $new_regmail_users[] = $r_record;
                }
        }
        ftruncate($fpr,0);
        if (count($new_regmail_users))
                fwrite($fpr, implode("\n",$new_regmail_users));
        fflush($fpr);
        flock($fpr, LOCK_UN);
        fclose($fpr);
        return $activated;
}

?>
