<?php
/*[COPYRIGHTS]*/

if (!defined('Q_COMMON')) exit('stop');

$page = intval($_GET['page']);
set_variable('kw');
$kw = isset($kw) ? trim(strip_tags($kw)) : '';
$in_page = 20;
$question_count = 0;

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
/* Delete question */
$del = intval($_GET['del']);
if ($del > 0) {
    $sql = 'DELETE FROM '.$quiz_config['db_prefix'].'quiz WHERE id='.$del;
    mysql_query($sql);
    header('Location: quiz.php?m='.$m.'&session='.$session.'&page='.$page.'&kw='.$kw);
    exit;
}

/*******************************************/
/* For search */
$where = '';
if ($kw) $where = ' WHERE question LIKE "%'.mysql_real_escape_string($kw).'%" OR answer LIKE "%'.mysql_real_escape_string($kw).'%" ';

/*******************************************/
/* Select questions count */
$sql = 'SELECT count(*) FROM '.$quiz_config['db_prefix'].'quiz '.$where.' LIMIT 1';
$res = mysql_query($sql);
list($question_count) = mysql_fetch_row($res);
mysql_free_result($res);

/*******************************************/
/* Select questions */
$questions = array();
$sql = 'SELECT * FROM '.$quiz_config['db_prefix'].'quiz '.$where.' ORDER BY id ASC LIMIT '.($page*$in_page).', '.$in_page;
$res = mysql_query($sql);
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
    $questions[] = $row;
}
mysql_free_result($res);

/*******************************************/
/* Generate pages list */
$pages = array();
$page_count = ceil($question_count/$in_page);
for ($i = 0; $i < $page_count; $i++) {
    if ($i == $page) $pages[] = '<span style="border:1px solid #eeeeee;"><b>'.($i+1).'</b></span>';
	else $pages[] = '<a href="quiz.php?m='.$mod.'&session='.$session.'&page='.$i.'&kw='.$kw.'">'.($i+1).'</a>';
}
$page_in_row = 11;
$offset = intval($page-floor($page_in_row/2));
if ($offset < 0) $offset = 0;
$length = $page_in_row;

$prev = $page-1; if ($prev < 0) $prev = 0;
$next = $page+1; if ($next > $page_count-1) $next = $page_count-1;

$page_string = '
<table cellspacing="0" cellpadding="3">
<tr>
  <td><a href="quiz.php?m='.$mod.'&session='.$session.'&page=0" title="Первая страница"><img src="quiz/images/first.png" width="19" height="19" border="0"></a></td>
  <td><a href="quiz.php?m='.$mod.'&session='.$session.'&page='.$prev.'&kw='.$kw.'" title="Предыдущая страница"><img src="quiz/images/prev.png" width="19" height="19" border="0"></a></td>
  <td><a href="quiz.php?m='.$mod.'&session='.$session.'&page='.$next.'&kw='.$kw.'" title="Следующая страница"><img src="quiz/images/next.png" width="19" height="19" border="0"></a></td>
  <td><a href="quiz.php?m='.$mod.'&session='.$session.'&page='.($page_count-1).'&kw='.$kw.'" title="Последняя страница"><img src="quiz/images/last.png" width="19" height="19" border="0"></a></td>
  <td>'.join('</td><td>', array_slice($pages, $offset, $length)).'</td>
  <td><input type="button" value="+ добавить" class="button" title="Добавить вопросы в базу" onclick="wopen(\'quiz.php?m=add&session='.$session.'\', 500, 300);"></td>
  <td><input type="button" value="импорт" class="button" title="Загрузить вопросы в базу данных из файла" onclick="wopen(\'quiz.php?m=import&session='.$session.'\', 500, 300);"></td>
  <td><input type="button" value="экспорт" class="button" title="Сохранить базу данных в файл" onclick="wopen(\'quiz.php?m=export&session='.$session.'\', 500, 300);"></td>
  <td><input type="button" value="перемешать" class="button" title="Изменить порядок следования вопросов" onclick="wopen(\'quiz.php?m=randomize&session='.$session.'\', 500, 300);"></td>
</tr>
</table>
';
?>

<?php echo $error;?>

<div style="text-align:left; padding:5px 10px;"><?php echo $page_string;?></div>
<div style="margin:0px 10px;">
	<table width="98%" style="border:1px solid #bfb8bf;">
	<tr style="background:#eeeeee;">
	  <td width="30" style="text-align:center; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;"><b>ID</b></td>
	  <td style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">Вопрос</td>
	  <td style="border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;"width="30">&nbsp;</td>
	</tr>
	<?php
	foreach ($questions as $question) {
	    echo '
	    <tr class="tr_normal" onmouseover="this.className=\'tr_hover\';" onmouseout="this.className=\'tr_normal\';">
	      <td width="30" height="30" style="text-align:center;">'.$question['id'].'</td>
	      <td height="30">'.str_replace($kw, '<span style="font-weight:bold; color:red;">'.$kw.'</span>', $question['question']).' (<b>'.str_replace($kw, '<span style="font-weight:bold; color:red;">'.$kw.'</span>', $question['answer']).'</b>)</td>
	      <td width="30" height="30" style="text-align:center;"><a href="quiz.php?m='.$mod.'&session='.$session.'&page='.$page.'&del='.$question['id'].'" onclick="if (!confirm(\'Удалить этот вопрос?\')) return false;"><img src="quiz/images/del.gif" width="16" height="16" alt="Удалить" title="Удалить" border="0"></a></td>
	    </tr>
	    ';
	}
	?>
	</table>
</div>
<div style="text-align:left; padding:5px 10px;"><?php echo $page_string;?></div>
