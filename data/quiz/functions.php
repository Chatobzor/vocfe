<?php

const QUIZ_DB_CONFIG_ERROR = 1;
const QUIZ_DB_CONNECT_ERROR = 2;
const QUIZ_DB_DATABASE_ERROR = 3;

function quiz_print_error($err)
{
    global $quiz_config;

    switch ($err) {
        case QUIZ_DB_CONFIG_ERROR:
            $e = 'Ошибка настройки базы данных!';
            break;
        case QUIZ_DB_CONNECT_ERROR:
            $e = 'Не могу соединиться с MySQL-сервером!';
            break;
        case QUIZ_DB_DATABASE_ERROR:
            $e = 'Не могу использовать базу данных <b>'.$quiz_config['db_name'].'</b>';
            break;
        default:
            $e = $err;
    }
    echo '<div style="margin:15px; padding:30px; border:1px solid red; text-align:center; color:red; background:#edeff8;">'.$e.'</div>';
}

function quiz_db_connect()
{
    global $quiz_config, $mysql_server, $mysql_user, $mysql_db, $mysql_password;

    if (!$mysql_server || !$mysql_user || !$mysql_db) {
        return QUIZ_DB_CONFIG_ERROR;
    }

    if (!mysql_connect($mysql_server, $mysql_user, $mysql_password)) {
        return QUIZ_DB_CONNECT_ERROR;
    }

    if (!mysql_select_db($mysql_db)) {
        return QUIZ_DB_DATABASE_ERROR;
    }

    return false;
}

function quiz_db_reconnect()
{
    global $quiz_config;

    mysql_close();
    quiz_db_connect();
}

function quiz_prepare_message($text, $to_private = false)
{
    global $room_id, $bot_htmlnick, $bot_nick, $is_regist, $user_name, $session, $registered_colors, $default_color, $quiz_config;

    $message = [
        MESG_TIME => my_time() + 1,
        MESG_ROOM => $room_id,
        MESG_FROM => $bot_htmlnick,
        MESG_FROMWOTAGS => $bot_nick,
        MESG_FROMSESSION => 0,
        MESG_FROMAVATAR => "",
        MESG_FROMID => $is_regist,
        MESG_TO => $to_private ? $user_name : '',
        MESG_TOSESSION => $to_private ? $session : '',
        MESG_TOID => $to_private ? $is_regist : 0,
        MESG_BODY => '<font color="'.$registered_colors[$default_color][1].'">'.$text.'</font><!-- [:BUILD:] -->',
    ];

    return $message;
}

function quiz_suggest_config($quiz_value = '', $global_value = '')
{
    if ($quiz_value) {
        return '&nbsp;';
    }

    return 'Предполагаемое значение: <b>'.$global_value.'</b>';
}

function quiz_strtolower($str)
{
    $str = strtolower($str);
    $str = strtr(
        $str,
        'QWERTYUIOPASDFGHJKLZXCVBNMЁЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ',
        'qwertyuiopasdfghjklzxcvbnmёйцукенгшщзхъфывапролджэячсмитьбю'
    );

    return $str;
}

function cmp_quiz_top($a, $b)
{
    if (intval($a["points"]) > intval($b["points"])) {
        return -1;
    }
    return 1;
}

function quiz_save_tmp_result($is_regist, $user_name, $answer_time)
{
    global $quiz_last_answ_file;

    $full_top_data = $is_regist."\t".$user_name."\t".$answer_time."\t".date('Y-m-d H:i:s');
    $f = fopen($quiz_last_answ_file, 'w');
    fwrite($f, $full_top_data);
    fclose($f);
}