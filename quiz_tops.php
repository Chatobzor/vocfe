<?php
/**
 * Quiz tops builder
 * @version: 3.0
 * @author: ChatMaster <chatmaster@one.lv>
 * @copyright ChatMaster, 2006-2008
 */

require_once('inc_common.php');
set_variable('session');

$type = (isset($_GET['type'])) ? trim(strip_tags($_GET['type'])) : '';
$when = (isset($_GET['when'])) ? trim(strip_tags($_GET['when'])) : '';
$cnt  = (isset($_GET['cnt'])) ? intval($_GET['cnt']) : '';
if (!$cnt) $cnt = 10;

$top = array();

if ($when && $type) {
    require_once($data_path.'quiz/config.php');
    if (mysql_connect($quiz_config['db_server'], $quiz_config['db_user'], $quiz_config['db_pass'], $quiz_config['mysql_encoding']) && mysql_select_db($quiz_config['db_name'])) {

		mysql_query('SET NAMES '.$quiz_config['mysql_encoding']);

		if ($when == 'ALWAYS') {
			if ('SPEED' == $type) {
				$top_type_header = '�������� ������';
				$sql = 'SELECT user_id, user_name, fastest FROM '.$quiz_config['db_prefix'].'quiz_full_stat ORDER BY fastest ASC LIMIT '.$cnt;
			} else {
				$top_type_header = '���-�� �������';
				$sql = 'SELECT user_id, user_name, cnt FROM '.$quiz_config['db_prefix'].'quiz_full_stat ORDER BY cnt DESC LIMIT '.$cnt;
			}
			$res = mysql_query($sql);
			while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	            if ('SPEED' == $type) {
					$row['cnt'] = $row['fastest'].' ���.';
					$top[] = $row;
	            } else {
	                $top[] = $row;
	            }
			}
			
		} else {
	        if ('SPEED' == $type) {
	            $sql = 'SELECT user_id, user_name, answer_time
	                    FROM '.$quiz_config['db_prefix'].'quiz_top ';
	            $top_type_header = '�������� ������';
	        } else {
	            $sql = 'SELECT count(*) AS cnt, user_id, user_name 
	                    FROM '.$quiz_config['db_prefix'].'quiz_top ';
	            $top_type_header = '���-�� �������';
	        }
	        switch ($when) {
	            case 'TODAY':
	                $date = mysql_real_escape_string(date('Y-m-d'));
	                $sql .= 'WHERE answer_date LIKE "'.$date.'%" ';
	                break;
	            case 'YESTERDAY':
	                $date = mysql_real_escape_string(date('Y-m-d', my_time()-86400));
	                $sql .= 'WHERE answer_date LIKE "'.$date.'%" ';
	                break;
	            case 'WEEK':
	                $date = mysql_real_escape_string(date('Y-m-d', my_time()-86400*7).' 00:00:00');
	                $sql .= 'WHERE answer_date > "'.$date.'" ';
	                break;
	            case 'MONTH':
	                $date = mysql_real_escape_string(date('Y-m-d', my_time()-86400*30).' 00:00:00');
	                $sql .= 'WHERE answer_date > "'.$date.'" ';
	                break;
	        }
	        if ('SPEED' == $type) {
	            $sql .= 'ORDER BY answer_time ASC';
	        } else {
	            $sql .= 'GROUP BY user_id
	                     ORDER BY cnt DESC
	                     LIMIT '.$cnt;
	        }
	        $res = mysql_query($sql);
	        $already_in_top = array();
	        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	            if ('SPEED' == $type) {
	                if (count($top) < $cnt && !in_array($row['user_id'], $already_in_top)) {
	                    $already_in_top[] = $row['user_id'];
	                    $row['cnt'] = $row['answer_time'].' ���.';
	                    $top[] = $row;
	                }
	            } else {
	                $top[] = $row;
	            }
	        }
		}
    }
}

$reset = '';
if (file_exists($data_path.'quiz/reset.dat')) {
	$reset = date(' (d.m.Y, H:i)', strtotime(file_get_contents($data_path.'quiz/reset.dat')));
}

?>
<? include($file_path."designes/".$design."/common_title.php"); ?>
<? include($file_path."designes/".$design."/common_body_start.php");?>

<div style="text-align:center; margin-top:10px; margin-bottom:10px;">
<form action="quiz_tops.php" method="get" style="margin:0px; padding:0px;">
<input type="hidden" name="session" value="<?=$session;?>">
<table align="center">
<tr>
  <td><input type="text" name="cnt" value="<?=$cnt;?>" style="width:25px;"></td>
  <td>
  <select name="type">
    <option value="COUNT">����� �����</option>
    <option value="SPEED"<? if ($type=="SPEED") {echo ' selected="selected"';}?>>����� �������</option>
  </select>
  </td>
  <td>
  <select name="when">
    <option value="TODAY"<? if ($when=="TODAY") {echo ' selected="selected"';}?>>�������</option>
    <option value="YESTERDAY"<? if ($when=="YESTERDAY") {echo ' selected="selected"';}?>>�����</option>
    <option value="WEEK"<? if ($when=="WEEK") {echo ' selected="selected"';}?>>�� ������</option>
    <option value="MONTH"<? if ($when=="MONTH") {echo ' selected="selected"';}?>>�� �����</option>
    <option value="ALWAYS"<? if ($when=="ALWAYS") {echo ' selected="selected"';}?>>� ���������� ���������<?=$reset;?></option>
  </select>
  </td>
  <td><input type="submit" value="��������"></td>
</tr>
</table>
</form>
</div>

<? if (count($top)) : ?>
<table align="center" cellspacing="0" cellpadding="4">
<tr>
  <td style="font-weight:bold;">�����</td>
  <td style="font-weight:bold;">������������</td>
  <td style="font-weight:bold;"><?=$top_type_header;?></td>
</tr>
<? for ($i = 0; $i < count($top); $i++) { ?>
<tr>
  <td style="border-top:#000 1px dotted; text-align:center;"><?=($i+1);?></td>
  <td style="border-top:#000 1px dotted;"><a href="<?=$chat_url;?>fullinfo.php?session=<?=$session;?>&user_id=<?=$top[$i]['user_id'];?>"><?=$top[$i]['user_name'];?></a></td>
  <td style="border-top:#000 1px dotted;"><?=$top[$i]['cnt'];?></td>
</tr>
<? } ?>
</table>
<? endif; ?>

<? include($file_path."designes/".$design."/common_body_end.php");?>