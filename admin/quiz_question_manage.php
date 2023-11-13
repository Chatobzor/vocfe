<?php

include("check_session.php");
include("../inc_common.php");
include($ld_engine_path."rooms_get_list.php");
include("header.php");

// Initializing
$page = intval($page);
$in_page = 20;
$question_count = 0;
$error = '';
$questions = array();
$QUIZ_CONF_FILE = $data_path.'quiz/config.php';
require_once($QUIZ_CONF_FILE);

// Connecting to database
if ($quiz_config['db_server'] && $quiz_config['db_user'] && $quiz_config['db_pass'] && $quiz_config['db_name'] && $quiz_config['db_prefix']) {
    if (mysql_connect($quiz_config['db_server'], $quiz_config['db_user'], $quiz_config['db_pass'])) {
        // Selecting database
        if (mysql_select_db($quiz_config['db_name'])) {
            // Delete question
            $del = intval($_GET['del']);
            if ($del > 0) {
                $sql = 'DELETE FROM '.$quiz_config['db_prefix'].'quiz WHERE id='.$del;
                mysql_query($sql);
                header('Location: quiz_question_manage.php?session='.$session.'&page='.$page);
                exit;
            }

            // Add question
            set_variable($question);
            set_variable($answer);
            $question = stripslashes($question);
            $answer = stripslashes($answer);
            if ($question && $answer) {
                $sql = 'INSERT INTO '.$quiz_config['db_prefix'].'quiz SET question="'.addslashes(
                        $question
                    ).'", answer="'.addslashes($answer).'"';
                mysql_query($sql);
                header('Location: quiz_question_manage.php?session='.$session.'&page='.$page);
                exit;
            }

            // Selecting questions count
            $sql = 'SELECT count(*) FROM '.$quiz_config['db_prefix'].'quiz LIMIT 1';
            $res = mysql_query($sql);
            list($question_count) = mysql_fetch_row($res);

            // Selecting questions
            $sql = 'SELECT * FROM '.$quiz_config['db_prefix'].'quiz LIMIT '.($page * $in_page).', '.$in_page;
            $res = mysql_query($sql);
            while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
                $questions[] = $row;
            }
        } else {
            $error = 'Cannot select database '.$quiz_config['db_name'];
        }
    } else {
        $error = 'Cannot connect to MySQL server using current settings.';
    }
}
if ($error) {
    $error = '<center><b>'.$error.'</b></center>';
}

// Generate pages list
$pages = array();
$page_count = ceil($question_count / $in_page);
for ($i = 0; $i < $page_count; $i++) {
    if ($i == $page) {
        $pages[] = '<b>'.($i + 1).'</b>';
    } else {
        $pages[] = '<a href="quiz_question_manage.php?session='.$session.'&page='.$i.'">'.($i + 1).'</a>';
    }
}
$page_in_row = 11;
$offset = intval($page - floor($page_in_row / 2));
if ($offset < 0) {
    $offset = 0;
}
$length = $page_in_row;
$page_string = '<a href="quiz_question_manage.php?session='.$session.'&page=0">&laquo; первая страница</a> | '.join(
        ' | ',
        array_slice($pages, $offset, $length)
    ).' | <a href="quiz_question_manage.php?session='.$session.'&page='.($page_count - 1).'">последняя страница &raquo;</a>';

?>
<center>
    <div style="background:#e0dfe3; border-bottom:1px solid #9d9da1; padding:3px; text-align:left; font-face:arial; font-size:11px;">
        <span style="border-right:1px solid #9d9da1; padding-left:3px; padding-right:2px; cursor:pointer;"
                onclick="window.location.assign('quiz.php?session=<?= $session; ?>')">Настройки </span>
        <span style="border-right:1px solid #9d9da1; padding-left:3px; padding-right:2px; cursor:pointer;"
                onclick="window.location.assign('quiz_question_manage.php?session=<?= $session; ?>')">Управление вопросами </span>
    </div>

    <?php
    echo $error; ?>
    <fieldset style="margin:10px;">
        <legend style="color: blue; font-size: 10px; font-weight: bold">Добавить вопрос</legend>
        <form action="quiz_question_manage.php" method="post" style="margin:0px; padding:0px;">
            <input type="hidden" name="page" value="<?php
            echo $page; ?>">
            <input type="hidden" name="session" value="<?php
            echo $session; ?>">
            <table align="center">
                <tr>
                    <td>Вопрос:</td>
                    <td style="padding-right:30px;"><input type="text" name="question" style="width: 300px;"
                                class="input"></td>
                    <td>Ответ:</td>
                    <td style="padding-right:30px;"><input type="text" name="answer" style="width: 120px;"
                                class="input"></td>
                    <td><input type="submit" value="Добавить" class="button"></td>
                </tr>
            </table>
        </form>
    </fieldset>
    <div style="text-align:center; padding-top:5px; padding-bottom:5px;"><?php
        echo $page_string; ?></div>
    <table width="100%" cellspacing="0" cellpadding="3" style="border-collapse:collapse;" border="1"
            bordercolor="#728d94">
        <tr>
            <td width="30" style="text-align:center;"><b>ID</b></td>
            <td><b>Вопрос</b></td>
            <td width="170"><b>Ответ</b></td>
            <td width="30">&nbsp;</td>
        </tr>
        <?php
        foreach ($questions as $question) {
            echo '
    <tr>
      <td width="30" style="text-align:center;">'.$question['id'].'</td>
      <td>'.$question['question'].'</td>
      <td width="170">'.$question['answer'].'</td>
      <td width="30" style="text-align:center;"><a href="quiz_question_manage.php?session='.$session.'&page='.$page.'&del='.$question['id'].'" onclick="if (!confirm(\'Удалить этот вопрос?\')) return false;"><font color="red"><b>[X]</b></font></a></td>
    </tr>
	';
        }
        ?>
        <tr>
            <td width="30"><b>ID</b></td>
            <td><b>Вопрос</b></td>
            <td width="170"><b>Ответ</b></td>
            <td width="30">&nbsp;</td>
        </tr>
    </table>
    <div style="text-align:center; padding-top:5px; padding-bottom:5px;"><?php
        echo $page_string; ?></div>
    <small>&copy; ChatMaster, 2006</small>

    </body>
    </html>