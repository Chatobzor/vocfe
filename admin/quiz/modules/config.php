<?php
/*[COPYRIGHTS]*/

if (!defined('Q_COMMON')) exit('stop');

/* Update settings */
if ($ff == "settings") {
    /* Updating Settings */
    set_variable('quiz_points');
    set_variable('tip_price');
    set_variable('tip_timeout');
    set_variable('smoke_timeout');
    set_variable('max_unanswered');
    set_variable('unanswered_pause');
    set_variable('answered_pause');
    set_variable('unanswered_type');
    set_variable('quiz_bot_nick');
    set_variable('quiz_bot_htmlnick');
    set_variable('quiz_max_users');
    set_variable('db_server');
    set_variable('db_user');
    set_variable('db_pass');
    set_variable('db_name');
    set_variable('db_prefix');
    set_variable('show_correct_answer');
    set_variable('need_db_reconnect');
    set_variable('mysql_encoding');
    set_variable('quiz_short_top_cnt');
    set_variable('quiz_short_top_type');
    set_variable('quiz_short_top_when');
    set_variable('private_output');
    set_variable('lic');

    $quiz_room_ids = array();
    if (count($_POST['quiz_room_ids'])) {
        foreach ($_POST['quiz_room_ids'] as $rid) {
            $quiz_room_ids[] = intval($rid);
        }
    }

    $quiz_bot_nick = str_replace("\\", "/", $quiz_bot_nick);
    $quiz_bot_htmlnick = str_replace("\\", "/", $quiz_bot_htmlnick);
    $db_server = str_replace("\\", "/", $db_server);
    $db_user   = str_replace("\\", "/", $db_user);
    $db_pass   = str_replace("\\", "/", $db_pass);
    $db_name   = str_replace("\\", "/", $db_name);
    $db_prefix = str_replace("\\", "/", $db_prefix);

    $configs = array();
    $configs[] = '<?php';

    $configs[] = '$quiz_config = array();';
    $configs[] = '$quiz_config[\'room_ids\'] = array();';
    foreach ($quiz_room_ids as $rid) {
        $configs[] = '$quiz_config[\'room_ids\'][] = '.$rid.';';
    }
    $configs[] = '$quiz_config[\'path_to_common\'] = "'.$file_path.'";';
    $configs[] = '$quiz_config[\'add_points\'] = '.intval($quiz_points).';';
    $configs[] = '$quiz_config[\'tip_price\'] = '.intval($tip_price).';';
    $configs[] = '$quiz_config[\'tip_timeout\'] = '.intval($tip_timeout).';';
    $configs[] = '$quiz_config[\'smoke_timeout\'] = '.intval($smoke_timeout).';';
    $configs[] = '$quiz_config[\'max_unanswered\'] = '.intval($max_unanswered).';';
    $configs[] = '$quiz_config[\'unanswered_pause\'] = '.intval($unanswered_pause).';';
    $configs[] = '$quiz_config[\'answered_pause\'] = '.intval($answered_pause).';';
    $configs[] = '$quiz_config[\'unanswered_type\'] = '.intval($unanswered_type).';';
    $configs[] = '$quiz_config[\'bot_nick\'] = "'.trim(str_replace("\"", "\\\"", $quiz_bot_nick)).'";';
    $configs[] = '$quiz_config[\'bot_htmlnick\'] = "'.trim(str_replace("\"", "\\\"", $quiz_bot_htmlnick)).'";';
    $configs[] = '$quiz_config[\'db_server\'] = "'.trim(str_replace("\"", "\\\"", $db_server)).'";';
    $configs[] = '$quiz_config[\'db_user\'] = "'.trim(str_replace("\"", "\\\"", $db_user)).'";';
    $configs[] = '$quiz_config[\'db_pass\'] = "'.trim(str_replace("\"", "\\\"", $db_pass)).'";';
    $configs[] = '$quiz_config[\'db_name\'] = "'.trim(str_replace("\"", "\\\"", $db_name)).'";';
    $configs[] = '$quiz_config[\'db_prefix\'] = "'.trim(str_replace("\"", "\\\"", $db_prefix)).'";';
    $configs[] = '$quiz_config[\'mysql_encoding\'] = "'.trim(str_replace("\"", "\\\"", $mysql_encoding)).'";';
    $configs[] = '$quiz_config[\'show_correct_answer\'] = '.intval($show_correct_answer).';';
    $configs[] = '$quiz_config[\'need_db_reconnect\'] = '.intval($need_db_reconnect).';';
    $configs[] = '$quiz_config[\'max_users\'] = '.intval($quiz_max_users).';';
    $configs[] = '$quiz_config[\'short_top_cnt\'] = '.intval($quiz_short_top_cnt).';';
    $configs[] = '$quiz_config[\'short_top_type\'] = "'.trim(strip_tags(str_replace("\"", "\\\"", $quiz_short_top_type))).'";';
    $configs[] = '$quiz_config[\'short_top_when\'] = "'.trim(strip_tags(str_replace("\"", "\\\"", $quiz_short_top_when))).'";';
    $configs[] = '$quiz_config[\'private_output\'] = '.intval($private_output).';';
    $configs[] = '$quiz_config[\'lic3_accepted\'] = '.intval($lic).';';

    $configs[] = '?>';
    $CONFIG = join("\n", $configs);

    $f = fopen($quiz_config_file, "a+");
    flock($f, LOCK_EX);
    ftruncate($f, 0);
    fwrite($f, $CONFIG);
    flock($f, LOCK_UN);
    fclose($f);

    header('Location: quiz.php?session='.$session);
    exit;

}

/* Error processing */
set_variable('error');
$error = intval($error);

/*******************************************/
/* Connect to database */
if (!$error) {
	$error = quiz_db_connect();

	if ($error) {
		quiz_print_error($error);
	}
}

if (!$error) {
	set_variable('do');
	if ($do == 'create_tables') {
		$sql = "CREATE TABLE IF NOT EXISTS `".$quiz_config['db_prefix']."quiz` ( `id` int(11) unsigned NOT NULL auto_increment, `question` text NOT NULL, `answer` varchar(255) NOT NULL default '', `last_use` datetime NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY (`id`) ) TYPE=MyISAM ";
		if ($quiz_config['mysql_encoding']) $sql .= ' DEFAULT CHARSET '.$quiz_config['mysql_encoding'];
		mysql_query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".$quiz_config['db_prefix']."quiz_top` ( `user_id` int(11) unsigned NOT NULL default '0', `user_name` varchar(255) NOT NULL default '', `answer_time` smallint(6) NOT NULL default '0', `answer_date` datetime NOT NULL default '0000-00-00 00:00:00', KEY `user_id` (`user_id`), KEY `answer_date` (`answer_date`) ) TYPE=MyISAM  ";
		if ($quiz_config['mysql_encoding']) $sql .= ' DEFAULT CHARSET '.$quiz_config['mysql_encoding'];
		mysql_query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `".$quiz_config['db_prefix']."quiz_full_stat` (`user_id` int(11) unsigned NOT NULL default '0', `user_name` varchar(255) default NULL, `cnt` int(11) unsigned NOT NULL default '0', `fastest` int(11) unsigned NOT NULL default '0', PRIMARY KEY  (`user_id`)) TYPE=MyISAM ";
		if ($quiz_config['mysql_encoding']) $sql .= ' DEFAULT CHARSET '.$quiz_config['mysql_encoding'];
		mysql_query($sql);

		header('Location: quiz.php?session='.$session);
		exit;
	}
	
	/* Check table exists */
	define('QUESTION_TABLE_EXISTS', (bool)mysql_query('SELECT 1 FROM '.$quiz_config['db_prefix'].'quiz WHERE 1 LIMIT 1'));
	define('TOP_TABLE_EXISTS', (bool)mysql_query('SELECT 1 FROM '.$quiz_config['db_prefix'].'quiz_top WHERE 1 LIMIT 1'));
	define('STAT_TABLE_EXISTS', (bool)mysql_query('SELECT 1 FROM '.$quiz_config['db_prefix'].'quiz_full_stat WHERE 1 LIMIT 1'));
} else {
	/* It's fake */
	define('QUESTION_TABLE_EXISTS', true);
	define('TOP_TABLE_EXISTS', true);
	define('STAT_TABLE_EXISTS', true);
}

if (!is_writable($data_path.'quiz/')) { $error = 3; }

switch ($error) {
    //case 1: $err = 'Не могу соединиться с MySQL-сервером, используя текущие настройки'; break;
    //case 2: $err = 'Не могу выбрать базу данных '.$quiz_config['db_name']; break;
    case 3: $err = 'Не могу писать в папку '.$data_path.'quiz/'; break;
    default: $err = '';
}

/* Creating rooms list */
$rooms_list = '';
foreach ($ar_rooms as $k=>$v) {
	$rooms_list .= '<option value="'.intval($k).'"';
	if (in_array(intval($k), $quiz_config['room_ids'])) $rooms_list .= ' selected="selected"';
	$rooms_list .= '>'.$v[ROOM_TITLE].'</option>';
}

/* Unanswered action types */
$unanswered_types = '';

$unanswered_types .= '<option value="1"';
if ($quiz_config['unanswered_type'] != 2) $unanswered_types .= ' selected="selected"';
$unanswered_types .= '>вообще</option>';

$unanswered_types .= '<option value="2"';
if ($quiz_config['unanswered_type'] == 2) $unanswered_types .= ' selected="selected"';
$unanswered_types .= '>подряд</option>';

/* Get Y/N enum */
function yes_no ($yes = 0) {
	$ret = '';
	$ret .= '<option value="0"';
	if (!$yes) $ret .= ' selected="selected"';
	$ret .= '>Нет</option>';
	$ret .= '<option value="1"';
	if ($yes) $ret .= ' selected="selected"';
	$ret .= '>Да</option>';
	return $ret;
}

?>
<? if ($err) { ?><div style="margin:10px; padding:15px; text-align:center; color:red; font-weight:bold; border:1px solid red; background:#edeff8;"><?=$err;?></div><? } ?>
<? if (!TOP_TABLE_EXISTS || !QUESTION_TABLE_EXISTS || !STAT_TABLE_EXISTS) { ?>
  <div style="margin:10px; padding:15px; text-align:center; color:red; font-weight:bold; border:1px solid red; background:#edeff8;">Обнаружены не все необходимые таблицы MySQL! <input type="button" class="button" value="создать" onclick="window.location.assign('quiz.php?session=<?=$session?>&do=create_tables');"></div>
<? } ?>
<!-- Main Configuration -->

<script type="text/javascript">
tip_timeout = <?=intval($quiz_config['tip_timeout']);?>;
smoke_timeout = <?=intval($quiz_config['smoke_timeout']);?>;
</script>

<form action="quiz.php" method="post"<? if(!$quiz_config['lic3_accepted']) { ?> onsubmit="return license_accepted();<? } ?>">
  <input type="hidden" name="ff" value="settings">
  <input type="hidden" name="lic" value="<?=intval($quiz_config['lic3_accepted']);?>">
  <input type="hidden" name="session" value="<?=$session;?>">
  <div style="background:#edeff8; border:0px solid #bababa; position:absolute; top:100px; left:30px; width:500px;">
    <div id="lic_text" style="color:#000; display:none; background:#ffffff; border:0px solid #bababa; margin:30px; height:340px; padding:10px; overflow:auto;">
<pre>
Лицензионное соглашение по использованию мода "Викторина 2008"
Пожалуйста, прочтите её, чтобы потом не было мучительно больно.

-  Данный мод является коммерческой разработкой.

-  Данный мод не продаётся, а лицензируется для установки на конкретный
   чат.

-  Продать лицензию официально Вам могуть только ChatMaster или DareDevil.
   Если Вы купили викторину у кого-то другого, значит Вы украли её.
   
-  Запрещается удалять любые копирайты разработчика (всё равно никто из
   простых пользователей их не видит, так что вряд ли они Вам мешают)

-  Данный мод разрешается устанавливать только на один чат - на тот,
   который был указан при оплате скрипта.
   В случае переезда/переноса чата на другой домен необходимо уведомить 
   об этом автора мода.

-  Запрещается вносить какие-либо изменения в код без согласования
   с автором. Помните, баги устраняются БЕСПЛАТНО до выхода следующей
   версии скрипта!
   
-  Запрещается распространять данный мод или какие-то его части.

-  Запрещается давать доступ к файлам данного мода третьим лицам.
   В случае "утечки" мода полную ответственность за это несёт тот, у
   кого украли мод. Тот, у кого мод был украден будет считаться 
   нелегальным распространителем мода и будет снят с техподдержки и
   лишён права на обновление до следующих версий.

-  Лицо, купившее лицензию на данный мод получает так же:
   * бесплатную техподдержку
   * бесплатные обновления в пределах ветки 3.0.х.
   * скидку в 50% для обновления до ветки 4.0 (от стоимости
     версии 4.0)
   
-  Инсталляция данного мода является подтверждением того, что клиент
   целиком и полностью согласен с данной лицензией, даже если он не
   читал её.

-  Непрочтение данной лицензии не освобождает от ответственности.
</pre>
<input type="checkbox" name="lic" id="lic_" value="1"> Я согласен с данной лицензией.<br />
    </div>
  </div>

<script type="text/javascript" src="<?=QUIZ_SERVER;?>/data/time_settings.php"></script>

  <fieldset>
    <legend>Настройки времени</legend>
    <table width="100%">

     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Cколько секунд ждать между подсказками.">Время между подсказками:</td>
      <td><input type="text" name="tip_timeout" value="<?=intval($quiz_config['tip_timeout']);?>" style="width: 50;" class="input"> сек. <?=quiz_suggest_config($quiz_config['tip_timeout'], 15);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="На сколько секунд выходить курить :).">Выходить курить на:</td>
      <td><input type="text" name="smoke_timeout" value="<?=intval($quiz_config['smoke_timeout']);?>" style="width: 50;" class="input"> сек. <?=quiz_suggest_config($quiz_config['smoke_timeout'], 300);?></td>
     </tr>


     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Через сколько секунд после сообщения о неотвеченном задавать следующий вопрос.">Пауза после неотвеченного вопроса:</td>
      <td><input type="text" name="unanswered_pause" value="<?=intval($quiz_config['unanswered_pause']);?>" style="width: 50;" class="input"> сек.  <?=quiz_suggest_config($quiz_config['unanswered_pause'], 60);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Через сколько секунд после сообщения об отвеченном задавать следующий вопрос.">Пауза после отвеченного вопроса:</td>
      <td><input type="text" name="answered_pause" value="<?=intval($quiz_config['answered_pause']);?>" style="width: 50;" class="input"> сек. <?=quiz_suggest_config($quiz_config['answered_pause'], 60);?></td>
     </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>Общие настройки</legend>
    <table width="100%">
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Cколько пойнтов начислять пользователю в случае правильного ответа.">Начисляемые пойнты:</td>
      <td><input type="text" name="quiz_points" value="<?=$quiz_config['add_points'];?>" style="width: 50;" class="input"> пойнтов. <?=quiz_suggest_config($quiz_config['add_points'], 300);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Cколько пойнтов отнимать от максимально возможного количества за каждую подсказку.">Цена подсказки:</td>
      <td><input type="text" name="tip_price" value="<?=$quiz_config['tip_price'];?>" style="width: 50;" class="input"> пойнтов. <?=quiz_suggest_config($quiz_config['tip_price'], 100);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Через сколько неотвеченых вопросов бот выходит на перекур.">Выходить на перекур каждые </td>
      <td><input type="text" name="max_unanswered" value="<?=intval($quiz_config['max_unanswered']);?>" style="width: 50;" class="input"> <select name="unanswered_type" class="input"><?=$unanswered_types;?></select> неотвеченных вопросов</td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Автоматически отключаться когда в комнате слишком много людей. 0 - не отключаться">Отключаться когда в комнате больше</td>
      <td><input type="text" name="quiz_max_users" value="<?=$quiz_config['max_users'];?>" style="width: 50;" class="input"> человек</td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Показывать правильный ответ когда на вопрос не был дан ответ?">Показывать правильный ответ?</td>
      <td><select name="show_correct_answer" class="input"><?=yes_no($quiz_config['show_correct_answer']);?></select></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Комната в которых будут задаваться вопросы и приниматься ответы на вопросы.">Комнаты:<br><small>можно указывать несколько комнат сразу</small></td>
      <td><select name="quiz_room_ids[]" style="width:150;" class="input" multiple="multiple" size="<?=count($ar_rooms);?>"><?php echo $rooms_list;?></select></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Ник бота, который задаёт вопросы. Можно указывать ник отличный от ника бота текущей комнаты.">Ник бота:</td>
      <td><input type="text" name="quiz_bot_nick" value="<?php echo htmlspecialchars(stripslashes($quiz_config['bot_nick']));?>" style="width: 150;" class="input"></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="А чем бот хуже человека? :)">HTML-Ник бота:</td>
      <td>
        <div id="nick_preview" style="float:right; font-weight:bold; margin-top:3px;"><?php echo $quiz_config['bot_htmlnick'];?></div>
        <input id="htmlnick" type="text" name="quiz_bot_htmlnick" value="<?php echo htmlspecialchars($quiz_config['bot_htmlnick']);?>" style="width: 150;" class="input">
        <input type="button" class="button" value="Preview" title="Предварительный просмотр HTML-ника бота" style="margin-top:1px;" onclick="document.getElementById('nick_preview').innerHTML = document.getElementById('htmlnick').value;">
      </td>
    </table>
  </fieldset>

  <fieldset>
    <legend>Настройки команд</legend>
    <table width="100%">
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left">По команде !топ показывать:</td>
      <td>
	<table cellspacing="0" cellpadding="0">
	<tr>
	  <td><input type="text" name="quiz_short_top_cnt" value="<?php echo intval($quiz_config['short_top_cnt']);?>" style="width:25px;" class="input"></td>
	  <td>
	    <select name="quiz_short_top_type" class="input">
	      <option value="COUNT">самых умных</option>
	      <option value="SPEED"<? if ($quiz_config['short_top_type']=="SPEED") {echo ' selected="selected"';}?>>самых быстрых</option>
	    </select>
	  </td>
	  <td>
	    <select name="quiz_short_top_when" class="input">
	      <option value="TODAY"<? if ($quiz_config['short_top_when']=="TODAY") {echo ' selected="selected"';}?>>сегодня</option>
	      <option value="YESTERDAY"<? if ($quiz_config['short_top_when']=="YESTERDAY") {echo ' selected="selected"';}?>>вчера</option>
	      <option value="WEEK"<? if ($quiz_config['short_top_when']=="WEEK") {echo ' selected="selected"';}?>>за неделю</option>
	      <option value="MONTH"<? if ($quiz_config['short_top_when']=="MONTH") {echo ' selected="selected"';}?>>за месяц</option>
	      <option value="ALWAYS"<? if ($quiz_config['short_top_when']=="ALWAYS" || !$quiz_config['short_top_when']) {echo ' selected="selected"';}?>>с последнего обнуления</option>
	    </select>
	  </td>
	</tr>
	</table>
      </td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Выводить !команды в приват вызывающему.">Выводить <i>!команды</i> в приват:</td>
      <td><select name="private_output" class="input"><?=yes_no($quiz_config['private_output']);?></select></td>
     </tr>
    </table>
  </fieldset>

  <fieldset>
    <legend>Настройки базы данных</legend>
    <table width="100%">
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Сервер MySQL">Сервер MySQL:</td>
      <td><input type="text" name="db_server" value="<?php echo htmlspecialchars(stripslashes($quiz_config['db_server']));?>" style="width: 150;" class="input"> <?=quiz_suggest_config($quiz_config['db_server'], $mysql_server);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Имя пользователя MySQL">Пользователь MySQL:</td>
      <td><input type="text" name="db_user" value="<?php echo htmlspecialchars(stripslashes($quiz_config['db_user']));?>" style="width: 150;" class="input"> <?=quiz_suggest_config($quiz_config['db_user'], $mysql_user);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Пароль к базе данных MySQL">Пароль пользователя MySQL:</td>
      <td><input type="text" name="db_pass" value="<?php echo htmlspecialchars(stripslashes($quiz_config['db_pass']));?>" style="width: 150;" class="input"> <?=quiz_suggest_config($quiz_config['db_pass'], $mysql_password);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="База данных MySQL">Название базы данных:</td>
      <td><input type="text" name="db_name" value="<?php echo htmlspecialchars(stripslashes($quiz_config['db_name']));?>" style="width: 150;" class="input"> <?=quiz_suggest_config($quiz_config['db_name'], $mysql_db);?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Префикс таблиц MySQL">Префикс таблиц:</td>
      <td><input type="text" name="db_prefix" value="<?php echo htmlspecialchars(stripslashes($quiz_config['db_prefix']));?>" style="width: 150;" class="input"> <?=quiz_suggest_config($quiz_config['db_prefix'], $mysql_table_prefix);?></td>
     </tr>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Кодировка MySQL">Кодировка:</td>
      <td><input type="text" name="mysql_encoding" value="<?php echo htmlspecialchars(stripslashes($quiz_config['mysql_encoding']));?>" style="width: 150;" class="input"> <?=quiz_suggest_config($quiz_config['mysql_encoding'], 'cp1251');?></td>
     </tr>
     <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
      <td class="settings_left" title="Переподключаться к базе задавая каждый вопрос?">Переподключаться к базе?</td>
      <td><select name="need_db_reconnect" class="input"><?=yes_no($quiz_config['need_db_reconnect']);?></select> <small>Ставить "ДА", только если демон викторины периодически падает. Возможно это поможет.</small></td>
     </tr>
    </table>
  </fieldset>
  <div style="margin:0px 10px; text-align:right;">
    <input type="submit" class="button" value="Сохранить" style="margin-left:3px;">
    <input type="reset" class="button" value="Отмена" style="margin-left:3px;">
  </div>
</form>
<!-- End Main Settings-->
