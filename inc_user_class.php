<?php

if (!defined("_USER_")):
    define("_USER_", 1);

    class User
    {
        public $quiz = 0;
        public $quiz_fastest_answer = 0;
        public $quiz_points = 0;
        public $nickname = "";
        public $password = "";
        public $surname = "";
        public $firstname = "";
        public $email = "";
        public $url = "";
        public $icquin = "";
        public $photo_url = "";
        public $about = "";
        public $user_class = 0;
        public $last_visit = 0;
        public $b_day = 0;
        public $b_month = 0;
        public $b_year = 0;
        public $show_group_1 = 0;
        public $show_group_2 = 0;
        public $sex = -1;
        public $city = "";
        public $registered_at = 0;
        public $enable_web_indicator = 0;
        public $registration_mail = "";
        public $htmlnick = "";
        //DD addons
        public $married_with = "";
        public $IP = "";
        public $login_phrase = "";
        public $logout_phrase = "";
        public $browser_hash = "";
        public $chat_status = "";
        public $cookie_hash = "";
        public $session = "";
        public $custom_class = 0;
        public $damneds = 0;
        public $rewards = 0;
        public $points = 0;
        public $last_actiontime = 0;
        public $clan_id = 0;
        public $clan_class = 0;
        public $clan_status = "";
        public $style_start = "";
        public $style_end = "";
        public $show_admin = 0;
        public $show_for_moders = 0;
        public $reduce_traffic = 0;
        public $plugin_info = array();
        public $registered = false;
        public $is_member = false;
        public $items = array();
        public $smileys = array();
        public $credits = 0;
        public $membered_by = "";
        public $user_agent = "";
        //security
        public $check_browser = 1;
        public $check_cookie = 0;
        public $limit_ips = "";
        // misc
        public $play_sound = 0;
        public $is_dialer = 0;
        //video
        public $allow_webcam = false;
        public $webcam_ip = "";
        public $webcam_port = 8080;
        //referal
        public $reffered_by = 0;
        public $reffered_by_nick = "";
        public $ref_payment_done = false;
        public $ref_arr = array();
        // online time
        public $online_time = 0;
        //photo-reiting
        public $photo_reiting = 0;
        public $photo_voted = array();
        public $photo_voted_mark = array();
        public $photo_take_part = true;
        //pass-check
        public $allow_pass_check = false;
        public $last_pass_check = 0;
    }

    class Clan
    {
        public $name = "";
        public $registration_time = 0;
        public $url = "";
        public $email = "";
        public $border = 0;
        public $members = array();
        public $ustav = "";
        public $greeting = "";
        public $goodbye = "";
        public $credits = 0;
        public $money_log = array();
    }

    class userlist_Cache
    {
        public $timestamp;
        public $u_cache;
    }
endif;