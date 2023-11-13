<?php
if (!defined("_USER_")):
define("_USER_", 1);

class User {
	var $nickname = "";
	var $password = "";
	var $surname = "";
	var $firstname = "";
	var $email = "";
	var $url = "";
	var $icquin = "";
	var $photo_url = "";
	var $about = "";
	var $user_class = 0;
	var $last_visit = 0;
	var $b_day = 0;
	var $b_month = 0;
	var $b_year = 0;
	var $show_group_1 = 0;
	var $show_group_2 = 0;
	var $sex = -1;
	var $city = "";
	var $registered_at = 0;
	var $enable_web_indicator = 0;
	var $registration_mail = "";
	var $htmlnick = "";
    //DD addons
    var $married_with = "";
    var $IP = "";
    var $login_phrase = "";
    var $logout_phrase = "";
    var $browser_hash = "";
    var $chat_status = "";
    var $cookie_hash = "";
    var $session = "";
    var $custom_class = 0;
    var $damneds = 0;
    var $rewards = 0;
    var $points = 0;
    var $last_actiontime = 0;
    var $clan_id = 0;
    var $clan_class = 0;
    var $clan_status = "";
    var $style_start = "";
    var $style_end = "";
    var $show_admin = 0;
    var $show_for_moders = 0;
    var $show_ip = 0;
}
class Clan {
    var $name = "";
    var $registration_time = 0;
    var $url = "";
    var $email = "";
    var $border = 0;
    var $members = array();
    var $ustav = "";
    var $greeting = "";
    var $goodbye = "";
}
endif;
?>