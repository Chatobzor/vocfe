<?
/*[COPYRIGHTS]*/

if (!defined('Q_COMMON')) exit('stop');

/*******************************************/
/* Connect to database */
$error = quiz_db_connect();

if ($error) {
	quiz_print_error($error);
	exit;
}

$act = (isset($_GET['act'])) ? trim(strip_tags($_GET['act'])) : '';

if ($act == 'user') {
	$user = (isset($_GET['user'])) ? intval($_GET['user']) : 0;
	if ($user) {
		$sql = 'DELETE FROM '.$quiz_config['db_prefix'].'quiz_full_stat WHERE user_id='.$user;
		mysql_query($sql);
		if (mysql_error()) {
			quiz_print_error('������ MySQL: '.mysql_error());
			exit;
		}
		$sql = 'DELETE FROM '.$quiz_config['db_prefix'].'quiz_top WHERE user_id='.$user;
		mysql_query($sql);
		if (mysql_error()) {
			quiz_print_error('������ MySQL: '.mysql_error());
			exit;
		}

		header('Location: quiz.php?session='.$session.'&m=zerolize&act=user');
		exit;
	}

	$users = array();
	$sql = 'SELECT user_id, user_name FROM '.$quiz_config['db_prefix'].'quiz_full_stat ORDER BY user_name ASC';
	$res = mysql_query($sql);
	if (mysql_error()) {
		quiz_print_error('������ MySQL: '.mysql_error());
		exit;
	}
	while ($row = mysql_fetch_assoc($res)) {
		$users[$row['user_id']] = $row['user_name'];
	}
	//print_r($users);
}

?>
<style>
.user { display:block; width:180px; float:left; margin-bottom:5px; }
</style>
<fieldset>
	<legend>���������� �������� ��������</legend>
	<ul>
		<li><a href="quiz.php?m=zerolize&session=<?=$session;?>&act=static">�������� ����������� ������ *</a></li>
		<li><a href="quiz.php?m=zerolize&session=<?=$session;?>&act=all">�������� ����������� � ������������ ������ **</a></li>
		<li><a href="quiz.php?m=zerolize&session=<?=$session;?>&act=user">�������� ���������� ��������� ��� ����������� ������������</a></li>
	</ul>
	<div><b>* ����������� ������</b> - ������ �� �Ѩ ����� ������ ��������� (��� � ������� ���������� �������� ����������� ������)</div>
	<div><b>** ������������ ������</b> - ������ �� ��������� 30 ���� ������ ���������. ������ ������ 30 ���� ������������� ��������� �� ����� ������� ������. �� ���� ������ ������������� ���� �� �������/�����/������/�����.</div>
</fieldset>

<? if ($act == 'user'):?>
<fieldset>
	<legend>�������� ������������, ���������� �������� �� ������ ��������</legend>
	<? if (count($users)) :?>
		<div style="padding-top:10px; padding-bottom:20px; ">� ������ ������������ ������ �� ������������, ������� ���� ������ ���� ����� � ������� ���������� ��������� ���������.</div>
		<? foreach($users as $k => $v):?>
			<a href="quiz.php?m=zerolize&session=<?=$session;?>&act=user&user=<?=$k;?>" class="user" onclick="if(!confirm('�������� ��� ���������� ��������� �� ����� ������������?')) return false;"><?=$v;?></a>
		<? endforeach;?>
		<div style="clear:both;"></div>
	<? else: ?>
		����� �� ������������� ��� �� ������� �� �������
	<? endif;?>
</fieldset>
<? endif;?>