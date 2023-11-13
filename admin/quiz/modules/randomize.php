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

/************************************************/
/* Processing */

$res = mysql_query('SELECT count(*) AS cnt FROM '.MYSQL_TABLE_PREFIX.'quiz LIMIT 1');
list ($count) = mysql_fetch_array($res);
mysql_free_result($res);

$ind = $count*10;

$res = mysql_query('SELECT id, last_use FROM '.MYSQL_TABLE_PREFIX.'quiz');

echo 'Перемешиваю...';

while ($row = mysql_fetch_array($res)) {
    $new_date = date("Y-m-d H:i:s", time()-rand(100, $ind));
    $sql = 'UPDATE '.MYSQL_TABLE_PREFIX.'quiz SET last_use="'.$new_date.'" WHERE id='.$row['id'];
    if(!mysql_query($sql)) {
        echo '<br>'.mysql_error();
        exit();
    }
}
?>
<br />Перемешал.