<?php
/*[COPYRIGHTS]*/

if (!defined('Q_COMMON')) exit('stop');

$err = (isset($_GET['err'])) ? intval($_GET['err']) : 0;

/*******************************************/
/* Connect to database */
$error = quiz_db_connect();

if ($error) {
	quiz_print_error($error);
	exit;
}

$question = (isset($_GET['question'])) ? trim(strip_tags($_GET['question'])) : '';
if ($question) {
    $answer = (isset($_GET['answer'])) ? trim(strip_tags($_GET['answer'])) : '';
    if (!$answer) {
        header('Location: quiz.php?m=add&session='.$session.'&err=1');
        exit();
    }

    // Add question
    $sql = 'INSERT INTO '.$quiz_config['db_prefix'].'quiz SET question="'.mysql_real_escape_string($question).'", answer="'.mysql_real_escape_string($answer).'"';
    if (!mysql_query($sql)) {
        header('Location: quiz.php?m=add&session='.$session.'&err=3');
        exit();
    }

    header('Location: quiz.php?m=add&session='.$session.'&err=2');
    exit();
}

/**
 * Error processing
 */
switch ($err) {
    case 1: $error = 'Заполните все поля!'; break;
    case 2: $error = 'Вопрос добавлен успешно!'; break;
    case 3: $error = 'Ошибка добавления вопроса!'; break;
    default: $error = '';
}
?>
<script type="text/javascript">
function add_question() {
    if (document.getElementById('questfield').value == '') {
        document.getElementById('errorDiv').innerHTML = 'Введите вопрос!';
        document.getElementById('questfield').focus();
        return false;
    }
    if (document.getElementById('answfield').value == '') {
        document.getElementById('errorDiv').innerHTML = 'Введите ответ!';
        document.getElementById('answfield').focus();
        return false;
    }

    return true;

}
</script>

<div style="position:absolute; left:36px; top:32px; font-weight:bold; color:red;" id="errorDiv"><?=$error;?>&nbsp;</div>

<div style="background:#edeff8; padding:20px;">
  <div style="background:#fff; padding:10px; border:1px solid #bababa; height:227px;">
    <div style="padding-bottom:40px; padding-right:10px; text-align:right;">&nbsp;</div>
    <form action="quiz.php?m=add&session=<?=$session;?>" method="get" onsubmit="return add_question();" style="margin:0px; padding:0px;">
    <input type="hidden" name="session" value="<?=$session;?>">
    <input type="hidden" name="m" value="add">
    <table cellspacing="5" cellpadding="0">
    <tr>
      <td><b>Вопрос</b></td>
      <td><b>Ответ</b></td>
    </tr>
    <tr>
      <td><input type="text" id="questfield" name="question" style="width:250px; border:1px solid #a7a6aa; font-size:14px;"></td>
      <td><input type="text" id="answfield" name="answer" style="width:150px; border:1px solid #a7a6aa; font-size:14px;"></td>
    </tr>
    <tr>
      <td valign="top">
        &nbsp;
      </td>
      <td align="right" valign="top"><input type="submit" value="Добавить" class="button"></td>
    </tr>
    </table>
    </form>
  </div>
</div>
<script type="text/javascript">document.getElementById('questfield').focus();</script>