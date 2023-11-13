<?php

require_once 'inc_common.php';
$quiz_top_file = $data_path.'quiz/top.dat';
if (!file_exists($quiz_top_file)) {
    exit();
}

/*************************************/
/* Configuration. You can edit it. */
$w_answers = 'ответов';
$font_color = '#000000'; // Цвет текста
$font_family = 'Verdana, Tahoma, Arial, Helvetica'; // Гарнитура шрифта
$font_size = '11px'; // Размер шрифта
$cellpadding = '1'; // Внутренний отступ ячеек таблицы

/*************************************/
/* Do not edit something below. */
define('Q_COMMON', 1);

if (defined('Q_COMMON')) {
    $css = ' style="color:'.$font_color.'; font-family:'.$font_family.'; font-size:'.$font_size.';"';
    $lines = file($quiz_top_file);
    if (count($lines)) {
        echo 'document.write(\'<table cellspacing="0" cellpadding="'.$cellpadding.'">';
        $i = 0;
        foreach ($lines as $line) {
            $i++;
            $line = trim($line);
            list($nick, $cnt, $user_id) = explode("\t", $line);
            echo str_replace(array("\\", "'"),
                array("", "\\'"),
                '<tr><td'.$css.'>'.$i.'.</td><td'.$css.'><a'.$css.' href="'.$chat_url.'fullinfo.php?user_id='.$user_id.'" target="_blank">'.$nick.' ('.$cnt.' '.$w_answers.')</a></td></tr>'
            );
        }
        echo '</table>\');';
    }
}