<?php

if (!defined("_COMMON_")) {
    /* Direct Request */
    echo 'VOC++ Quiz Engine v 3.0<br>';
    exit;
}

require_once $data_path.'quiz/init.php';

if (defined('Q_COMMON')) {
    if ($whisper) {
        include_once "inc_user_class.php";
        include_once $ld_engine_path."users_get_object.php";
    }
    require_once $quiz_config_file;

    if (in_array($room_id, $quiz_config['room_ids'])) {
        require_once $quiz_words_file;
        require_once $quiz_functions_file;
        /* Gradient text fix */
        $mesg = preg_replace('/<div style="display:none">([\s\S]+)<\/div>/iU', '', $mesg);
        $_room_id = $room_id;
        $flood_protection = 0;
        $bot_nick = $quiz_config['bot_nick'];
        $bot_htmlnick = ($quiz_config['bot_htmlnick'] != '') ? $quiz_config['bot_htmlnick'] : $bot_nick;

        /* Add quiz to ignore */
        if (trim(strip_tags($mesg)) == '!-') {
            $messages_to_show = array();
            $add_to_ignor = $quiz_config['bot_nick'];
            include($engine_path."ignor_add.php");
            echo '<script>alert("'.$w_quiz_disabled.'");</script>';
            exit();
        }
        /* End add to ignore */

        /* Remove quiz from ignore */
        if (trim(strip_tags($mesg)) == '!+') {
            $messages_to_show = array();
            $remove_from_ignor = $quiz_config['bot_nick'];
            include($engine_path."ignor_remove.php");
            echo '<script>alert("'.$w_quiz_enabled.'");</script>';
            exit();
        }
        /* End remove from ignore */

        /* Process moderator's commands */
        if ($cu_array[USER_CLASS] > 0) {
        }
        /* End moderators commands */

        /* Output info about user */
        if (trim(strip_tags($mesg)) == $w_quiz_command_me) {
            if ($current_user->registered) {
                if (mysql_connect($mysql_server, $mysql_user, $mysql_password) && mysql_select_db($mysql_db)) {
                    // Select fastest answer and answers count
                    $sql = 'SELECT cnt, fastest FROM '.$quiz_config['db_prefix'].'quiz_full_stat WHERE user_id='.intval(
                            $cu_array[USER_REGID]
                        ).' LIMIT 1';
                    $res = mysql_query($sql);
                    $row = mysql_fetch_array($res);

                    // count
                    $sql = 'SELECT count(*) AS cnt, user_id, user_name 
	                        FROM voc_quiz_top
	                        WHERE answer_date > "'.date('Y-m-01 00:00:00').'"
                            AND user_name="'.$user_name.'"
                            GROUP BY user_id
                            LIMIT 1
                            ';
                    $sql = 'SELECT count(*) FROM voc_quiz_top WHERE user_name="'.$user_name.'" AND answer_date > "'.date(
                            'Y-m-01 00:00:00'
                        ).'"';
                    $res = mysql_query($sql);
                    $res = mysql_fetch_row($res);
                    $cnt = (int)$res[0];

                    $months = array(
                        1 => 'январь',
                        2 => 'февраль',
                        3 => 'март',
                        4 => 'аперль',
                        5 => 'май',
                        6 => 'июнь',
                        7 => 'июль',
                        8 => 'август',
                        9 => 'сентябрь',
                        10 => 'окнябрь',
                        11 => 'ноябрь',
                        12 => 'декабрь',
                    );

                    if ($row and $cnt) {
                        $message_text = str_replace(
                            array('<{USER_NAME}>', '<{POINTS}>', '<{SEC}>', '<{ANSWERS}>', '<{MONTH}>'),
                            array($user_name, $current_user->points, $row['fastest'], $cnt, $months[date('n')]),
                            $w_quiz_me_answer
                        );
                    } else {
                        $message_text = $w_quiz_no_answers;
                    }
                }
            } else {
                $message_text = $w_quiz_register_first;
            }
            if ($quiz_config['private_output']) {
                $messages_to_show = [];
            }
            $room_id = $_room_id;
            $messages_to_show[] = quiz_prepare_message($message_text, $quiz_config['private_output']);
            if ($quiz_config['private_output']) {
                include($engine_path."messages_put.php");
                exit;
            }
        }
        /* End output info about user */

        /* Output TOP */
        if (trim(strip_tags($mesg)) == $w_quiz_command_top && !$whisper && file_exists($quiz_top_file)) {
            $top_data = file($quiz_top_file);
            $i = 0;
            $top = '';
            foreach ($top_data as $top_row) {
                $i++;
                list ($nick, $points) = explode("\t", trim($top_row));
                $top .= $i.'. '.$nick.' ('.$points.')<br>';
            }
            $message_text = str_replace('<{TOP}>', $top, $w_quiz_top_answer);
            if (file_exists($quiz_reset_file)) {
                $date = date('d.m.Y H:i', strtotime(file_get_contents($quiz_reset_file)));
                $message_text .= $w_quiz_last_reset.$date;
            }
            if ($quiz_config['private_output']) {
                $messages_to_show = array();
            }
            $room_id = $_room_id;
            //$messages_to_show[] = quiz_prepare_message($message_text, $quiz_config['private_output']);
            $messages_to_show[] = quiz_prepare_message(
                'Команда <b>!топ</b> временно недоступна. Посмотреть топ пользователей викторины можно <a href="'.$chat_url.'quiz_tops.php?session=&cnt=10&type=COUNT&when=ALWAYS" target="_blank">здесь</a>',
                $quiz_config['private_output']
            );
            if ($quiz_config['private_output']) {
                include($engine_path."messages_put.php");
                exit;
            }
        }
        /* End output TOP */

        if (file_exists($quiz_answer_file)) {
            $f = trim(quiz_strtolower(file_get_contents($quiz_answer_file)));
            list ($answer, $question_time) = explode("\t", $f);
            $tmp_answer = str_replace('ё', 'е', $answer);
            $tmp_answer = str_replace('й', 'и', $tmp_answer);
            $tmp_mesg = quiz_strtolower(strip_tags($mesg));
            $tmp_mesg = str_replace('ё', 'е', $tmp_mesg);
            $tmp_mesg = str_replace('й', 'и', $tmp_mesg);

            $answer_time = time() - $question_time;
            if ($answer_time > $quiz_config['tip_timeout']) {
                $quiz_config['add_points'] = $quiz_config['add_points'] - $quiz_config['tip_price'];
            }
            if ($answer_time > $quiz_config['tip_timeout'] * 2) {
                $quiz_config['add_points'] = $quiz_config['add_points'] - $quiz_config['tip_price'];
            }
            if ($quiz_config['add_points'] <= 0) {
                $quiz_config['add_points'] = 1;
            }

            if (preg_match('/'.preg_quote($tmp_answer).'/iU', $tmp_mesg)) {
                unlink($quiz_answer_file);
                if ($current_user->registered) {
                    if ($current_user->quiz_fastest_answer == 0 || $current_user->quiz_fastest_answer > $answer_time) {
                        $current_user->quiz_fastest_answer = $answer_time;
                    }
                    $current_user->points = $current_user->points + $quiz_config['add_points'];

                    quiz_save_tmp_result($is_regist, $user_name, $answer_time);

                    $cnt = $fastest = 0;
                    if (mysql_connect($mysql_server, $mysql_user, $mysql_password) && mysql_select_db($mysql_db)) {
                        $sql = 'SELECT count(*) AS cnt, user_id, user_name 
	                        FROM voc_quiz_top
	                        WHERE answer_date > "'.date('Y-m-01 00:00:00').'"
                            AND user_name="'.$user_name.'"
                            GROUP BY user_id
                            LIMIT 1
                            ';
                        $sql = 'SELECT count(*) FROM voc_quiz_top WHERE user_name="'.$user_name.'" AND answer_date > "'.date(
                                'Y-m-01 00:00:00'
                            ).'"';
                        $res = mysql_query($sql);
                        $res = mysql_fetch_row($res);
                        $cnt = (int)$res[0];
                    }

                    $cnt++;

                    $message_text = str_replace(
                        array(
                            '<{USER_NAME}>',
                            '<{ANSWER}>',
                            '<{POINTS_TO_ADD}>',
                            '<{QUIZ_TOTAL}>',
                            '<{TIME}>',
                            '<{POINTS_TOTAL}>'
                        ), array(
                        $user_name,
                        $answer,
                        $quiz_config['add_points'],
                        $cnt,
                        $answer_time,
                        $current_user->points
                    ),
                        $w_quiz_ok
                    );
                    $lucky = mt_rand(0, 100);
                    if ($lucky == 10) {
                        $current_user->points = $current_user->points - $quiz_config['add_points'] + $quiz_config['add_points'] * 10;
                        $message_text = str_replace(
                            array(
                                '<{USER_NAME}>',
                                '<{ANSWER}>',
                                '<{POINTS_TO_ADD}>',
                                '<{QUIZ_TOTAL}>',
                                '<{TIME}>',
                                '<{POINTS_TOTAL}>',
                                '<{POINTS_WITH_BONUS}>'
                            ), array(
                            $user_name,
                            $answer,
                            $quiz_config['add_points'],
                            $cnt,
                            $answer_time,
                            $current_user->points,
                            $quiz_config['add_points'] * 10
                        ),
                            $w_quiz_ok_plus
                        );
                    } elseif ($lucky == 20) {
                        $current_user->points = $current_user->points - $quiz_config['add_points'];
                        $message_text = str_replace(
                            array(
                                '<{USER_NAME}>',
                                '<{ANSWER}>',
                                '<{POINTS_TO_ADD}>',
                                '<{QUIZ_TOTAL}>',
                                '<{TIME}>',
                                '<{POINTS_TOTAL}>',
                                '<{POINTS_WITH_BONUS}>'
                            ), array(
                            $user_name,
                            $answer,
                            $quiz_config['add_points'],
                            $cnt,
                            $answer_time,
                            $current_user->points,
                            $quiz_config['add_points'] * 10
                        ),
                            $w_quiz_ok_minus
                        );
                    }
                    foreach ($quiz_config['room_ids'] as $room_id) {
                        $messages_to_show[] = quiz_prepare_message($message_text);
                    }
                    $room_id = $_room_id;
                    include($ld_engine_path."user_info_update.php");
                } else {
                    $message_text = str_replace(
                        array('<{USER_NAME}>', '<{ANSWER}>', '<{POINTS_TO_ADD}>', '<{POINTS_TOTAL}>', '<{TIME}>'),
                        array($user_name, $answer, $quiz_config['add_points'], $current_user->quiz, $answer_time),
                        $w_quiz_ok_no_reg
                    );
                    foreach ($quiz_config['room_ids'] as $room_id) {
                        $messages_to_show[] = quiz_prepare_message($message_text);
                    }
                    $room_id = $_room_id;
                }
            }
        }
    }
}