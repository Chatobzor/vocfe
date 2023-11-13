<?php
/*[COPYRIGHTS]*/
set_time_limit(0);
error_reporting(E_ALL);
$path = preg_replace('|([^/]+)$|i', '', __FILE__);
chdir($path);

/************************************************/
/* Configuration */
require_once('config.php');
define('MYSQL_SERVER', $mysql_server);
define('MYSQL_USER', $mysql_user); /* MySQL user */
define('MYSQL_PASSWORD', $mysql_password); /* MySQL Password */
define('MYSQL_DB', $mysql_db); /* database name */
define('MYSQL_TABLE_PREFIX', $quiz_config['db_prefix']); /* common table prefix */

/************************************************/
/* Initializing */
/* MySQL Connect */
echo("Connecting database... \n");
if (!mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD)) {
    echo("Cannot connect to the database. ".mysql_error()."\n");
    exit();
}
if (!mysql_select_db(MYSQL_DB)) {
    echo("Cannot select database. ".mysql_error()."\n");
    exit();
}

$res = mysql_query('SELECT count(*) AS cnt FROM '.MYSQL_TABLE_PREFIX.'quiz LIMIT 1');
list ($count) = mysql_fetch_array($res);
mysql_free_result($res);

$ind = $count*10;

$res = mysql_query('SELECT id, last_use FROM '.MYSQL_TABLE_PREFIX.'quiz');

while ($row = mysql_fetch_array($res)) {
    $new_date = date("Y-m-d H:i:s", time()-rand(100, $ind));
    echo $row['id']."\t".$row['last_use']."\t".$new_date."\n";
    $sql = 'UPDATE '.MYSQL_TABLE_PREFIX.'quiz SET last_use="'.$new_date.'" WHERE id='.$row['id'];
    mysql_query($sql);
}

?>