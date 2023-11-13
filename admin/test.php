<?php

if(!isset($DbLink)) {
define("C_DB_NAME", "voodoo");
define("C_DB_USER", "root");
define("C_DB_PASS", "smartpad");
include_once("mysql.lib.php3");
$DbLink = new DB;
}

$DbLink->query("SELECT s_name, url FROM smileys LIMIT 5 OFFSET 10;");

for($i=0; $i < $DbLink->num_rows(); $i++) {
        list($nm, $url) = $DbLink->next_record();
        echo $nm." =&gt ".$url."<br>";
}

?>