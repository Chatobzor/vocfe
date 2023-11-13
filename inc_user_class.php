<?php
if (!defined("_USER_")):
define("_USER_", 1);

class User {
	var $quiz = 0;
    var $quiz_fastest_answer = 0;
    var $quiz_points = 0;
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
    var $reduce_traffic  = 0;
    var $plugin_info = array();
    var $registered  = false;
    var $is_member   = false;
    var $items = array();
    var $smileys = array();
    var $credits = 0;
    var $membered_by = "";
    var $user_agent  = "";
    //security
    var $check_browser = 1;
    var $check_cookie  = 0;
    var $limit_ips     = "";
    // misc
    var $play_sound    = 0;
    var $is_dialer     = 0;
    //video
    var $allow_webcam  = false;
    var $webcam_ip     = "";
    var $webcam_port   = 8080;
    //referal
    var $reffered_by      = 0;
    var $reffered_by_nick = "";
    var $ref_payment_done = false;
    var $ref_arr          = array();
    // online time
    var $online_time      = 0;
    //photo-reiting
    var $photo_reiting    = 0;
    var $photo_voted      = array();
    var $photo_voted_mark = array();
    var $photo_take_part  = true;
    //pass-check
    var $allow_pass_check = false;
    var $last_pass_check  = 0;
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
    var $credits = 0;
    var $money_log = array();
}
class userlist_Cache {
    var $timestamp;
    var $u_cache;
}
endif;
?>