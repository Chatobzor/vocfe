<?php

if (!defined("_COMMON_")) {
    echo "stop";
    exit;
}
include_once($engine_path."users_get_list.php");
include_once($ld_engine_path."stat.php");

function stat_total_users()
{
    return engine_total_users();
}

function stat_users_online()
{
    global $users;
    return count($users);
}

function stat_last_registered($num)
{
    return engine_last_registered($num);
}