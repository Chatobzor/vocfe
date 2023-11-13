<?php
/*[COPYRIGHTS]*/

if (!defined('Q_COMMON')) exit('stop');

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

/*
	$error = 0;
	$dump_file = $dumps_dir.'/'.date('Y-m-d').'.dat';

	$in_page = 100;
	$page = 0;
	
	$f = fopen($dump_file, 'w');

	do {
		$cnt = 0;
		$sql = 'SELECT * FROM '.$quiz_config['db_prefix'].'quiz '.$where.' ORDER BY id ASC LIMIT '.($page*$in_page).', '.$in_page;
		echo $sql.'<br />'.PHP_EOL;
		$page++;
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
			fwrite($f, $row['question']."\t".$row['answer']."\n");
			$cnt++;
		}
		mysql_free_result($res);
	} while ($cnt >= $in_page);

	fclose($f);
*/

}

$export = file_exists($quiz_export_cmd);
if ($export) $export_status = file_get_contents($quiz_export_cmd);

?>

<div style="padding:20px;">
	<? if ($export): ?>
		<? if ($export_status == 0): ?>
			Экспорт вопросов начнётся через несколько мгновений...<br />
			<br />
			Подождите, это может занять несколько минут...
		<? else: ?>
			Выполняется экспорт вопросов...<br />
			<br />
			Подождите, это может занять несколько минут...
		<? endif; ?>
		<script type="text/javascript">
		window.setTimeout('window.location.assign("quiz.php?session=<?=$session;?>&m=<?=$module;?>&r=<?=time();?>")', 5000);
		</script>
	<? else: ?>
		<a href="quiz.php?m=<?=$m;?>&session=<?=$session;?>&do_export=1">Сделать экспорт текущей базы данных</a>
	<? endif; ?>
</div>