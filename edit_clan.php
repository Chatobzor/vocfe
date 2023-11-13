<?php
    set_variable("clan_name");
    set_variable("clan_email");
    set_variable("clan_url");
    set_variable("clan_border");
    set_variable("clan_id");
    set_variable("clan_greeting");
    set_variable("clan_goodbye");
    set_variable("delete_avatar");
    set_variable("delete_logo");
    //set_variable("clan_avatar");
    //set_variable("clan_logo");

    function is_email($address) {
      $rc1 = (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
             '@'.
             '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
             '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
             $address));
      $rc2 = (preg_match('/.+\.\w\w+$/',$address));
      return ($rc1 && $rc2);
	}
	function is_url($address) {
		$url = fsockopen($address, 80, $errno, $errstr, 30);
		if(!$url) return false;
	return true;
	}

    $clan_id = intval(trim($clan_id));

    if(strcasecmp(trim($clan_border), "on") == 0) $clan_border = 1;
    else $clan_border = 0;

    if(strcasecmp(trim($delete_avatar), "on") == 0) $delete_avatar = 1;
    else $delete_avatar = 0;

    if(strcasecmp(trim($delete_logo), "on") == 0) $delete_logo = 1;
    else $delete_logo = 0;

    $clan_greeting = htmlspecialchars(trim($clan_greeting));
    $clan_goodbye  = htmlspecialchars(trim($clan_goodbye));

    $clan_err  = "";

    $clan_name = trim(htmlspecialchars($clan_name));
    if(strlen($clan_name) == 0) $clan_err = $w_roz_clan_err_name;

    $clan_name = eregi_replace(" +", " ",$clan_name);

    if (ereg("[^ ".$nick_available_chars."]", $clan_name)) {
        $clan_err = $w_roz_clan_err_name;
	}


    if($base_dir) include("inc_to_canon_nick.php");
    else include("../inc_to_canon_nick.php");

    $canon_new_name = to_canon_nick($clan_name);

    include($data_path."engine/files/clans_get_list.php");
    for($i=0; $i < count($clans_list); $i++) {
		if(strcasecmp(to_canon_nick(trim($clans_list[$i]["name"])), $canon_new_name) == 0 and $clan_id != $clans_list[$i]["id"] and $clan_id > 0) {
          $clan_err = $w_roz_clan_err_name;
          break;
        }
	}

    $clan_email = trim(htmlspecialchars($clan_email));
    if(strlen($clan_email) > 0) {
    	 if	(!is_email($clan_email)) $clan_err = $w_roz_clan_err_email;
    }

    $clan_url = trim(htmlspecialchars($clan_url));

    if(strlen($clan_url) > 0) {
    	 if	(!is_url(htmlentities($clan_url))) $clan_err = $w_roz_clan_err_url;
    }

    if (isset($HTTP_POST_FILES['clan_avatar']['name'])) $clan_avatar = $HTTP_POST_FILES['clan_avatar']['tmp_name'];
	else $clan_avatar = "";

    if(strlen($clan_avatar) > 0) {
	    list($roz_width, $roz_height, $type, $attr) = getimagesize($clan_avatar);
    	if($type != 1 or $roz_height > 14 or $roz_width > 18) $clan_err = $w_roz_clan_err_avatar." ($roz_width x $roz_height, $type, $clan_avatar)";
    }

	if (isset($HTTP_POST_FILES['clan_logo']['name'])) $clan_logo = $HTTP_POST_FILES['clan_logo']['tmp_name'];
	else $clan_logo = "";

    if(strlen($clan_logo) > 0) {
	    list($roz_width, $roz_height, $type, $attr) = getimagesize($clan_logo);
    	if(($type != 1 and $type != 2)  or $roz_height > 210 or $roz_width > 210) $clan_err = $w_roz_clan_err_logo." ($roz_width x $roz_height, $type)";
    }

    if(strlen(trim($clan_err)) == 0) {
     	if(count($clans_list)) $is_regist_clan = $clans_list[count($clans_list)-1]["id"]+1;
        else $is_regist_clan = 1;

        if($clan_id > 0) $is_regist_clan = $clan_id;

        if($mode_add_clan == false)  include($ld_engine_path."clan_get_object.php");

    	$current_clan->name 				= $clan_name;
        $current_clan->registration_time 	= my_time();
        $current_clan->email			 	= $clan_email;
        $current_clan->url				 	= $clan_url;
        $current_clan->border			 	= $clan_border;
        $current_clan->greeting			 	= $clan_greeting;
        $current_clan->goodbye			 	= $clan_goodbye;

       	include($ld_engine_path."clan_update_object.php");

        if($mode_add_clan == true) {
	        $idx = count($clans_list);
            $clans_list[$idx]["id"] 	= $is_regist_clan;
        	$clans_list[$idx]["name"]   = $clan_name;
        }

        else {
        	for($i = 0; $i < count($clans_list); $i++) {
            	if($clans_list[$i]["id"] 	== $clan_id) {
                   $clans_list[$i]["name"] 	= $clan_name;
                   break;
                }
            }
        }

        include($data_path."engine/files/clans_update_list.php");

		if(strlen(trim($clan_avatar)) > 0){
        if(!@is_dir($file_path."clans-avatar/".floor($is_regist_clan/2000)))
		if (ini_get('safe_mode'))
			trigger_error("Your PHP works in SAFE MODE, please create directory clans-avatar/".floor($is_regist_clan/2000),E_USER_ERROR);
		else
			mkdir($file_path."clans-avatar/".floor($is_regist_clan/2000),0777);

        	$newLoc = $file_path."clans-avatar/".floor($is_regist_clan/2000)."/".$is_regist_clan.".gif";
            move_uploaded_file ($clan_avatar, $newLoc);
            chmod($newLoc, 0644);
        }
        if(strlen(trim($clan_logo)) > 0){

        if(!@is_dir($file_path."clans-logos/".floor($is_regist_clan/2000)))
		if (ini_get('safe_mode'))
			trigger_error("Your PHP works in SAFE MODE, please create directory clans-logo/".floor($is_regist_clan/2000),E_USER_ERROR);
		else
			mkdir($file_path."clans-logos/".floor($is_regist_clan/2000),0777);

           	@unlink($file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".gif");
            @unlink($file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".jpg");
            @unlink($file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".jpeg");

        	if($type == 1) $newLoc = $file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".gif";
            else $newLoc = $file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".jpg";
            move_uploaded_file ($clan_logo, $newLoc);
            chmod($newLoc, 0644);
        }
        if($delete_logo) {
        		@unlink($file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".gif");
                @unlink($file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".jpg");
                @unlink($file_path."clans-logos/".floor($is_regist_clan/2000)."/".$is_regist_clan.".jpeg");
        }
       if($delete_avatar) {
        		@unlink($file_path."clans-avatar/".floor($is_regist_clan/2000)."/".$is_regist_clan.".gif");
        }
    }
?>