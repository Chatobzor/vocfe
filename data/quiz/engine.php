<?php

/*[COPYRIGHTS]*/
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);
$path = preg_replace('|([^/]+)$|i', '', __FILE__);
chdir($path);

if (file_exists('runtime.log')) {
    unlink('runtime.log');
}

/************************************************/
/* Configuration */
require_once('config.php');
$path_to_common = FILE_PATH;
define('MYSQL_SERVER', $mysql_server);
define('MYSQL_USER', $mysql_user);
define('MYSQL_PASSWORD', $mysql_password);
define('MYSQL_DB', $mysql_db);
define('MYSQL_TABLE_PREFIX', $quiz_config['db_prefix']);

require_once($path_to_common.'inc_common.php');
define('QUIZ_SYSDIR', $data_path.'quiz/');

/************************************************/
/* Disable messages logging */
$logging_messages = 0;
$logging_ban = 0;

/************************************************/
/* Load required files */
require_once QUIZ_SYSDIR.'words.php';
require_once QUIZ_SYSDIR.'functions.php';

/************************************************/
/* Initialize system filenames */
$pid_file = QUIZ_SYSDIR.'quiz.pid';
$answer = QUIZ_SYSDIR.'answer.dat';
$last_answ = QUIZ_SYSDIR.'last_answered.dat';
$events_log = QUIZ_SYSDIR.'events.log';
$bot_online = QUIZ_SYSDIR.'bot_online.dat';
$last_action = QUIZ_SYSDIR.'last_action.dat';

/************************************************/
/* Get restart flag */
if (function_exists('getopt')) {
    $input = getopt('r');
    if (isset($input['r'])) {
        if (file_exists($pid_file)) {
            unlink($pid_file);
        }
    }
}

/************************************************/
/* Check PID-file */
if (file_exists($pid_file)) {
    $last_action_time = (file_exists($last_action)) ? intval(file_get_contents($last_action)) : 0;
    $one_iteration_time = $quiz_config['tip_timeout'] * 2 + $quiz_config['smoke_timeout'];

    if (time() - $one_iteration_time < $last_action_time) {
        quiz_log_event("В запуске отказано. Другая копия скрипта ещё работает.");
        exit("Process is already runing. Delete ".$pid_file." to stop it.\n");
    }
}

/************************************************/
/* Create PID-file */
$pid = getmypid();
$f = fopen($pid_file, "w");
flock($f, LOCK_EX);
fwrite($f, $pid);
flock($f, LOCK_UN);
fclose($f);

/************************************************/
/* Initializing */
/* MySQL Connect */
define('Q_COMMON', 1);
if (!mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD)) {
    quiz_log_event("Не могу подключиться к серверу MySQL. Проверьте настройки. ".mysql_error());
    exit();
}
if (!mysql_select_db(MYSQL_DB)) {
    quiz_log_event("Не могу подключиться к нужной базе данных. Проверьте настройки. ".mysql_error());
    exit();
}
define("_CONNECT_", 1);

$room_id = $quiz_config['room_id'];
$bot_nick = $quiz_config['bot_nick'];
$bot_htmlnick = ($quiz_config['bot_htmlnick'] != '') ? $quiz_config['bot_htmlnick'] : $bot_nick;

/*************************************************/
/* Save startup time */
$f = fopen(QUIZ_SYSDIR.'started_at.dat', 'w');
fwrite($f, time());
fclose($f);

/*************************************************/
/* Engine */
$iteration = 0;
$unanswered = 0;
$words_count = 0;
$answer_length = 0;
$flood_protection = 0;

$QUIZ_ROOM_IDS = $quiz_config['room_ids'];

while (1) {
    /* For support Bort222's Chat Client */
    $f = fopen($bot_online, 'w');
    fwrite($f, $bot_nick.'|0|4;');
    fclose($f);

    /* Check PID-file */
    if (!file_exists($pid_file) || file_get_contents($pid_file) != $pid) {
        quiz_log_event("Не могу найти свой PID-файл... ОМГ! Меня по ходу убили! :(");
        exit();
    }

    /* Update last action time */
    $f = fopen($last_action, 'w');
    fwrite($f, time());
    fclose($f);

    /* Reconnect if needed */
    if ($quiz_config['need_db_reconnect']) {
        /* Destroy old connection */
        mysql_close();
        /* Make new connection */
        if (!mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD)) {
            quiz_log_event("Не могу переподключиться с MySQL-серверу. ".mysql_error());
            exit();
        }
        if (!mysql_select_db(MYSQL_DB)) {
            quiz_log_event("Не могу подключиться к нужной базе данных. ".mysql_error());
            exit();
        }
    }

    $iteration++;

    /* Update full top data */
    if (file_exists($last_answ)) {
        $data = file_get_contents($last_answ);
        if (strlen($data)) {
            $new_top_str = explode("\t", $data);
            if (count($new_top_str) == 4) {
                /* Save current result */
                $sql = 'INSERT INTO '.MYSQL_TABLE_PREFIX.'quiz_top VALUES ('.intval(
                        $new_top_str[0]
                    ).', "'.mysql_real_escape_string($new_top_str[1]).'", '.intval(
                        $new_top_str[2]
                    ).', "'.$new_top_str[3].'")';
                mysql_query($sql);

                /* Automatically clear results, that are older then 30 days */
                $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 30);
                $sql = 'DELETE FROM '.MYSQL_TABLE_PREFIX.'quiz_top WHERE answer_date < "'.$date.'"';
                mysql_query($sql);

                /* Update full top info */
                $sql = 'SELECT * FROM '.MYSQL_TABLE_PREFIX.'quiz_full_stat WHERE user_id='.intval(
                        $new_top_str[0]
                    ).' LIMIT 1';
                $res = mysql_query($sql);
                $row = mysql_fetch_array($res);
                if (!$row) {
                    $sql = 'INSERT INTO '.MYSQL_TABLE_PREFIX.'quiz_full_stat SET user_id='.intval(
                            $new_top_str[0]
                        ).', user_name="'.mysql_real_escape_string($new_top_str[1]).'", cnt=1, fastest='.intval(
                            $new_top_str[2]
                        );
                    mysql_query($sql);
                } else {
                    $fastest = $row['fastest'];
                    if ($new_top_str[2] < $fastest) {
                        $fastest = $new_top_str[2];
                    }
                    $sql = 'UPDATE '.MYSQL_TABLE_PREFIX.'quiz_full_stat SET cnt=cnt+1, fastest='.intval(
                            $fastest
                        ).' WHERE user_id='.intval($new_top_str[0]);
                    mysql_query($sql);
                }
            }
        }
        unlink($last_answ);
    }
    /* End Update full top data */

    /* Multiroom support: rebuild rooms list according to settings */
    $rooms_data = array();
    include($engine_path."users_get_list.php");
    foreach ($QUIZ_ROOM_IDS as $room_id) {
        $rooms_data[$room_id] = 0;
        foreach ($users as $user) {
            $data = explode("\t", $user);
            if ($data[10] == $room_id) {
                $rooms_data[$room_id]++;
            }
        }
    }
    $quiz_config['room_ids'] = array();
    foreach ($rooms_data as $room_id => $cnt) {
        $add = false;
        if ($cnt > 0) {
            $add = true;
        }
        if ($quiz_config['max_users'] > 0 && $cnt > $quiz_config['max_users']) {
            $add = false;
        }
        if ($add == true) {
            $quiz_config['room_ids'][] = $room_id;
        }
    }

    /* Wait if all rooms are empty or overloaded */
    if (!count($quiz_config['room_ids'])) {
        sleep(20);
        continue;
    }

    $question = get_question();
    $q_text = str_replace('<{QUESTION}>', $question, $w_quiz_question_text).str_replace(
            '<{COUNT}>',
            intval($answer_length),
            $w_quiz_letters_count
        );
    if ($words_count > 1) {
        $q_text .= str_replace('<{COUNT}>', intval($words_count), $w_quiz_words_count);
    }
    $messages_to_show = array(); /* Clear variable */
    foreach ($quiz_config['room_ids'] as $room_id) {
        $messages_to_show[] = quiz_prepare_message($q_text);
    }
    include($engine_path."messages_put.php");
    sleep($quiz_config['tip_timeout']);

    /* Sleep before next question if previous answered */
    if (!file_exists($answer)) {
        sleep($quiz_config['answered_pause']);
        continue;
    }

    /* TIP 1 */
    if (file_exists($answer) && $answer_length > 1) {
        $answer_text = trim(file_get_contents($answer));
        $tip = substr($answer_text, 0, 1);
        $t_text = str_replace(array('<{TIP_NUM}>', '<{TIP_TEXT}>'), array(1, $tip), $w_quiz_tip_text);
        $messages_to_show = array(); /* Clear variable */
        foreach ($quiz_config['room_ids'] as $room_id) {
            $messages_to_show[] = quiz_prepare_message($t_text);
        }
        include($engine_path."messages_put.php");
        sleep($quiz_config['tip_timeout']);
    }

    /* Sleep before next question if previous answered */
    if (!file_exists($answer)) {
        sleep($quiz_config['answered_pause']);
        continue;
    }

    /* TIP 2 */
    if (file_exists($answer) && $answer_length > 3) {
        $answer_text = trim(file_get_contents($answer));
        if ($answer_length == 4) {
            $tip_length = 2;
        } else {
            $tip_length = 3;
        }
        $tip = substr($answer_text, 0, $tip_length);
        $t_text = str_replace(array('<{TIP_NUM}>', '<{TIP_TEXT}>'), array(2, $tip), $w_quiz_tip_text);
        $messages_to_show = array(); /* Clear variable */
        foreach ($quiz_config['room_ids'] as $room_id) {
            $messages_to_show[] = quiz_prepare_message($t_text);
        }
        include($engine_path."messages_put.php");
        sleep($quiz_config['tip_timeout']);
    }
    if (file_exists($answer)) {
        /* Prepare correct answer for output */
        if ($quiz_config['show_correct_answer']) {
            list($correct_answer, $answer_timeout) = explode("\t", file_get_contents($answer));
            $correct_answer = str_replace('<{ANSWER}>', $correct_answer, $w_quiz_correct_answer_was);
        } else {
            $correct_answer = '';
        }
        unlink($answer);
        $unanswered++;
        if ($unanswered == $quiz_config['max_unanswered']) {
            /* Smoking */
            $unanswered = 0;
            $messages_to_show = array(); /* Clear variable */
            foreach ($quiz_config['room_ids'] as $room_id) {
                $messages_to_show[] = quiz_prepare_message($w_quiz_smoke_text.' '.$correct_answer);
            }
            include($engine_path."messages_put.php");
            if (file_exists($bot_online)) {
                unlink($bot_online);
            }
            sleep($quiz_config['smoke_timeout']);
            /* Back:) */
            $messages_to_show = array(); /* Clear variable */
            foreach ($quiz_config['room_ids'] as $room_id) {
                $messages_to_show[] = quiz_prepare_message($w_quiz_smoke_back);
            }
            include($engine_path."messages_put.php");
            sleep(5);
        } else {
            /* Nobody answered. */
            $messages_to_show = array(); /* Clear variable */
            foreach ($quiz_config['room_ids'] as $room_id) {
                $messages_to_show[] = quiz_prepare_message(
                    str_replace('<{SEC}>', $quiz_config['unanswered_pause'], $w_quiz_unanswered).' '.$correct_answer
                );
            }
            include($engine_path."messages_put.php");
            sleep($quiz_config['unanswered_pause']);
        }
    } else {
        if (intval($quiz_config['unanswered_type']) == 2) {
            $unanswered = 0;
        }
        /* Sleep before next question if previous answered */
        sleep($quiz_config['answered_pause']);
        continue;
    }
}

/*************************************************/
/* Functions */
function get_question()
{
    if (!defined('Q_COMMON')) {
        exit('Installation error');
    }
    global $answer, $words_count, $answer_length, $file_path;

    if (!file_exists($file_path.'quiz.php')) {
        exit('Some system files was not found. Please reinstall quiz!');
    }

    $str = file_get_contents($file_path.'quiz.php');
    //if (!preg_match('/Build 20070928092230<br>/iU', $str)) exit('I think, You have too low IQ to install quiz correctly.');

    /* Select question */
    $sql = 'SELECT * FROM '.MYSQL_TABLE_PREFIX.'quiz ORDER BY last_use ASC LIMIT 1';
    $res = mysql_query($sql) or die ('SQL ERROR! '.mysql_error());

    list ($id, $question, $answer_text, $last_use) = mysql_fetch_array($res, MYSQL_NUM);
    mysql_free_result($res);

    $answer_text = trim($answer_text);
    $answer_length = strlen($answer_text);
    $words_count = count(explode(' ', $answer_text));

    /* Update last use time */
    $sql = 'UPDATE '.MYSQL_TABLE_PREFIX.'quiz SET last_use="'.date('Y-m-d H:i:s').'" WHERE id='.intval($id);
    mysql_query($sql);

    /* Write Answer */
    $f = fopen($answer, "w");
    flock($f, LOCK_EX);
    fwrite($f, $answer_text."\t".time());
    flock($f, LOCK_UN);
    fclose($f);

    /* Anti-google */
    $strlen = mt_rand(1, 4);
    $anti_google_string = '';
    for ($i = 0; $i < $strlen; $i++) {
        $anti_google_string .= chr(mt_rand(33, 125));
    }
    $strlen = mt_rand(1, 4);
    $anti_google_string_2 = '';
    for ($i = 0; $i < $strlen; $i++) {
        $anti_google_string_2 .= chr(mt_rand(33, 125));
    }

    $anti_google_string = str_replace(array('<', '>', '"', "'"), '', $anti_google_string);
    $anti_google_string_2 = str_replace(array('<', '>', '"', "'"), '', $anti_google_string_2);
    $question = str_replace(
        ' ',
        '<span style="color:#ffffff; font-size:1px; width:1px; overflow:hidden;">'.$anti_google_string.'</span> <span style="color:#ffffff; font-size:1px; width:1px; overflow:hidden;">'.$anti_google_string_2.' </span>',
        $question
    );
    $replaces = array();
    $replaces[] = array('from' => 'А', 'to' => '&#'.ord('A').';');
    $replaces[] = array('from' => 'В', 'to' => '&#'.ord('B').';');
    $replaces[] = array('from' => 'Е', 'to' => '&#'.ord('E').';');
    $replaces[] = array('from' => 'К', 'to' => '&#'.ord('K').';');
    $replaces[] = array('from' => 'М', 'to' => '&#'.ord('M').';');
    $replaces[] = array('from' => 'О', 'to' => '&#'.ord('0').';');
    $replaces[] = array('from' => 'Р', 'to' => '&#'.ord('P').';');
    $replaces[] = array('from' => 'С', 'to' => '&#'.ord('C').';');
    $replaces[] = array('from' => 'Т', 'to' => '&#'.ord('T').';');
    $replaces[] = array('from' => 'Х', 'to' => '&#'.ord('X').';');
    $replaces[] = array('from' => 'а', 'to' => '&#'.ord('a').';');
    $replaces[] = array('from' => 'е', 'to' => '&#'.ord('e').';');
    $replaces[] = array('from' => 'о', 'to' => '&#'.ord('o').';');
    $replaces[] = array('from' => 'р', 'to' => '&#'.ord('p').';');
    $replaces[] = array('from' => 'с', 'to' => '&#'.ord('c').';');
    $replaces[] = array('from' => 'у', 'to' => '&#'.ord('y').';');
    $replaces[] = array('from' => 'х', 'to' => '&#'.ord('x').';');
    //$replaces[] = array('from' => 'a', 'to' => '@');

    foreach ($replaces as $replace) {
        $question = str_replace($replace['from'], $replace['to'], $question);
    }
    /* End Anti-google */

    return $question;
}

/**
 * Last events logging
 * Always turned on
 */
function quiz_log_event($text)
{
    global $events_log;
    $log = array();

    $log[] = getmypid()."\t".date('Y-m-d H:i:s')."\t".$text;

    if (file_exists($events_log)) {
        $current_log = explode("\n", file_get_contents($events_log));
        $log = array_merge($log, $current_log);
        $log = array_slice($log, 0, 100);
    }

    $f = fopen($events_log, 'w');
    fwrite($f, join("\n", $log));
    fclose($f);
}

?>
