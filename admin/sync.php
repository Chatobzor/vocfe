<?php include("check_session.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<?php
include("header.php");

?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<?php
include("../inc_common.php");
include("../inc_user_class.php");
include "../test/Snoopy.class.php";

if ($long_life_data_engine == "files") {
	$users = array();
	$users = file($user_data_file);

    for ($i=0; $i<count($users);$i++)
	{
    	if(isset($snoopy)) unset($snoopy);
    	$snoopy = new Snoopy;

		$user = str_replace("\n","",$users[$i]);
		list($t_id, $t_nickname, $t_password, $t_class, $t_canon, $t_mail) = explode("\t",$user);
        if(intval($t_id) > 1) {

	    	$is_regist = intval($t_id);

            if ($is_regist) {
				if (file_exists($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")) {
						$current_user = unserialize(implode("",file($data_path."users/".floor($is_regist/2000)."/".$is_regist.".user")));
				}
            }else {
	            echo "[ERROR]<br>";
                unset($current_user);
                $current_user = new User();
                continue;
            }

       	    echo ($i+1).". [".$current_user->nickname." = $is_regist]....";
            $admin_str = md5("daredevil03041980libido".rand(0, 9));
			$params    = "";

            if(strlen($current_user->nickname) == 0) $current_user->nickname = $t_nickname;
            if(strlen($current_user->nickname) == 0) continue;
            if(strlen($current_user->password) == 0) $current_user->nickname = $t_password;
            if(strlen($current_user->url) > 60) $current_user->url = "";
            if(strlen($current_user->email) > 60) $current_user->email = "";

		    $params     = "&username=".urlencode($current_user->nickname);

		    $params    .= "&latin1=0";

            if(strlen($current_user->password) == 32)   $params    .= "&password=".$current_user->password;
            else $params    .= "&password=".md5("123");

		    $params    .= "&firstname=".urlencode($current_user->firstname);
		    $params    .= "&lastname=".urlencode($current_user->surname);
		    $params    .= "&country=ua";
		    $params    .= "&website=".urlencode($current_user->url);
		    $params    .= "&email=".urlencode($current_user->email);
		    $params    .= "&showemail=0";
		    $params    .= "&reg_time=".$current_user->registered_at;
		    $params    .= "&gender=".$current_user->sex;
		    $params    .= "&icq=".intval($current_user->icquin);

			$snoopy->fetchtext("http://www.rozmova.if.ua/updater.php?sid=$admin_str".$params);
			echo "[".$snoopy->results."]";

            if($snoopy->results != "OK") {
                unset($current_user);
                $current_user = new User();
            }

            echo "<br>";
        }
	}
}
?>
<center><span class=head>Generated</center>
</td></tr></table></center>
</body>
</html>