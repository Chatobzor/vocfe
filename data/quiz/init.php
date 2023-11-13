<?php
/**
 * Quiz initialization. Please don't edit this file
 *
[COPYRIGHTS]
*/

define('QUIZ_SYSDIR', $data_path.'quiz/');
define('Q_COMMON', 1);

$quiz_answer_file    = QUIZ_SYSDIR.'answer.dat';
$quiz_config_file    = QUIZ_SYSDIR.'config.php';
$quiz_top_file       = QUIZ_SYSDIR.'top.dat';
$quiz_words_file     = QUIZ_SYSDIR.'words.php';
$quiz_functions_file = QUIZ_SYSDIR.'functions.php';
$quiz_last_answ_file = QUIZ_SYSDIR.'last_answered.dat';
$quiz_reset_file     = QUIZ_SYSDIR.'reset.dat';
$quiz_pid_file       = QUIZ_SYSDIR.'quiz.pid';
$quiz_last_action	= QUIZ_SYSDIR.'last_action.dat';
$quiz_start_file	= QUIZ_SYSDIR.'started_at.dat';
$quiz_export_cmd	= QUIZ_SYSDIR.'do_export.dat';

?>