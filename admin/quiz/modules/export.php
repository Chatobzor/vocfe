<?php

if (!defined('Q_COMMON')) {
    exit('stop');
}

/*******************************************/
/* Connect to database */
$error = quiz_db_connect();

if ($error) {
    quiz_print_error($error);
    exit;
}

$dumps_dir = dirname(dirname(__FILE__)).'/dumps';
if (!file_exists($dumps_dir)) {
    mkdir($dumps_dir, 0777);
}

$do_export = (isset($_GET['do_export'])) ? intval($_GET['do_export']) : 0;
if ($do_export) {
    $f = fopen($quiz_export_cmd, 'w');
    fwrite($f, '0');
    fclose($f);

    header('Location: quiz.php?session='.$session.'&m='.$module);
}

$export = file_exists($quiz_export_cmd);
if ($export) {
    $export_status = file_get_contents($quiz_export_cmd);
}

?>

<div style="padding:20px;">
    <?
    if ($export): ?>
        <?
    if ($export_status == 0): ?>
        Экспорт вопросов начнётся через несколько мгновений...<br/>
    <br/>
        Подождите, это может занять несколько минут...
    <?
    else: ?>
        Выполняется экспорт вопросов...<br/>
    <br/>
        Подождите, это может занять несколько минут...
    <?
    endif; ?>
        <script type="text/javascript">
            window.setTimeout('window.location.assign("quiz.php?session=<?=$session;?>&m=<?=$module;?>&r=<?=time(
            );?>")', 5000);
        </script>
    <?
    else: ?>
        <a href="quiz.php?m=<?= $m; ?>&session=<?= $session; ?>&do_export=1">Сделать экспорт текущей базы данных</a>
    <?
    endif; ?>
</div>