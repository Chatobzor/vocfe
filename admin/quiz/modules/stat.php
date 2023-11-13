<?php
/*[COPYRIGHTS]*/

if (!defined('Q_COMMON')) exit('stop');

/*******************************************/
/* Stop script */
if ($act && $act == 'stop' && file_exists($quiz_pid_file)) {
    unlink($quiz_pid_file);
    header('Location: quiz.php?session='.$session.'&m='.$module);
}

/*******************************************/
/* Check script status */
$is_running = 0;
$question_count = 0;
$error = '';

if (file_exists($quiz_pid_file)) {
    $last_action_time = (file_exists($quiz_last_action)) ? intval(file_get_contents($quiz_last_action)) : 0;
    $one_iteration_time = $quiz_config['tip_timeout']*2+$quiz_config['smoke_timeout'];

    if (time()-$one_iteration_time < $last_action_time) {
        $is_running = 1;
    }
}

/*******************************************/
/* Connect to database */
$error = quiz_db_connect();

if ($error) {
	quiz_print_error($error);
	exit;
}

$sql = 'SELECT count(*) FROM '.$quiz_config['db_prefix'].'quiz LIMIT 1';
$res = mysql_query($sql);
list($question_count) = mysql_fetch_row($res);


/*******************************************/
/* Zerolize tops */
if ($del == 'static' || $del == 'full') {
	// Create timestamp of last reset
	$f = fopen($quiz_reset_file, 'w');
	fwrite($f, date('Y-m-d H:i:s'));
	fclose($f);

	// Delete top.dat
	if (file_exists($quiz_top_file)) unlink($quiz_top_file);

	// Truncate quiz_full_stat
	mysql_query('TRUNCATE TABLE '.$quiz_config['db_prefix'].'quiz_full_stat');
}
if ($del == 'full') {
	// Truncate quiz_top
	mysql_query('TRUNCATE TABLE '.$quiz_config['db_prefix'].'quiz_top');
}
if ($del) {
	header('Location: quiz.php?session='.$session.'&m='.$module);
}

/*******************************************/
/* Get Uptime */
if (file_exists($quiz_start_file) && $is_running) {
	$started_at = file_get_contents($quiz_start_file);
	$current = time();
	$runtime = $current - $started_at;
	$seconds = $runtime;
	$minutes = 0;
	$hours = 0;
	$days = 0;
	if ($runtime > 60) {
		$minutes = floor($runtime / 60);
		$seconds = $seconds - ($minutes * 60);
	}
	if ($minutes > 60) {
		$hours = floor($minutes / 60);
		$minutes = $minutes - ($hours * 60);
	}
	if ($hours > 24) {
		$days = floor($hours / 24);
		$hours = $hours - ($days * 24);
	}
	$uptime = '';
	if ($days) $uptime .= $days.' days, ';
	if ($hours) $uptime .= $hours.' hours, ';
	if ($minutes) $uptime .= $minutes.' minutes, ';
	if ($seconds) $uptime .= $seconds.' seconds';
} else {
	$uptime = '0';
}

?>
<?php echo $error;?>
<!-- Statistics -->
<fieldset>
  <legend>Статистика</legend>
  <table>
   <tr>
    <td width="250">Состояние скрипта:</td>
    <td><?php if (!$is_running) echo 'не запущен'; else echo 'запущен, PID: '.file_get_contents($quiz_pid_file).' <a href="quiz.php?session='.$session.'&m='.$module.'&act=stop" onclick="if (!confirm(\'Эт... а ты хорошо подумал?\')) return false;">[остановить]</a>'; ?></td>
   </tr>
   <? if ($is_running) { ?>
   <tr>
    <td width="250">Uptime:</td>
    <td><?=$uptime;?></td>
   </tr>
   <? } ?>
   <tr>
    <td width="250">Количество вопросов в базе:</td>
    <td><?php echo intval($question_count); ?></td>
   </tr>
   <tr>
    <td width="250" valign="top">Топ пользователей:</td>
    <td><script type="text/javascript" src="<?=$chat_url;?>quiz_top_export.php"></script></td>
   </tr>
   <tr>
    <td width="250" valign="top">Экспорт топа пользователей:</td>
    <td>
      Для вставки топа пользователей в нужное Вам место используйте указанный ниже код.<br> Настроить внешний вид таблицы Вы можете отредактировав файл <b><?=$file_path;?>quiz_top_export.php</b><br />
      <textarea style="width:100%; height:40px; color:#5b645b; font-family:Verdana, Tahoma, Arial; font-size:11px;"><?=htmlspecialchars('<script type="text/javascript" src="'.$chat_url.'quiz_top_export.php"></script>');?></textarea>
    </td>
   </tr>
   <tr>
    <td width="250" valign="top">Динамические топы:</td>
    <td>
      Для показа динамических топов пользователям используйте URL: <a href="<?=$chat_url;?>quiz_tops.php" target="_blank" style="font-weight:bold;"><?=$chat_url;?>quiz_tops.php</a><br>
      Просто вставьте эту ссылку туда, куда Вам удобно.
    </td>
   </tr>
  </table>
<input type="button" onclick="if (confirm('Это удалит статичный топ пользователей. Хотите продолжить?')) { window.location.assign('quiz.php?session=<?=$session;?>&m=<?=$module;?>&del=static'); }" value="Обнулить статичные данные" class="zerolize" /><br />
<input type="button" onclick="if (confirm('Это удалит статичный топ пользователей и все топы за последние 30 дней. Хотите продолжить?')) { window.location.assign('quiz.php?session=<?=$session;?>&m=<?=$module;?>&del=full'); }" value="Обнулить ВСЕ данные" class="zerolize" />
</fieldset>
<!-- End Statistics -->
<?php
$events_log = $data_path.'quiz/events.log';
if (file_exists($events_log)) {
$logs = file($events_log);
?>
<!-- Latest Logs -->
<fieldset>
  <legend>Лог событий (последние 100, новые сверху)</legend>
  <table width="100%">
    <td><b>ID процесса</b></td>
    <td><b>Дата</b></td>
    <td><b>Сообщение</b></td>
  </tr>
<? foreach ($logs as $log_str): ?>
<? $log = explode("\t", trim($log_str)); ?>
  <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
    <td width="90"><?=$log[0]?></td>
    <td width="120"><?=$log[1]?></td>
    <td><?=$log[2]?></td>
  </tr>
<? endforeach; ?>
  </table>
</fieldset>
<!-- End Latest Logs -->
<?php
}
?>
