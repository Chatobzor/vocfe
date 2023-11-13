<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
$converts = file($converts_file);
for ($i=0;$i<count($converts);$i++)
	list ($pic_phrases[$i], $pic_urls[$i]) = explode("\t",str_replace("\r","",str_replace("\n","",$converts[$i])));
unset($converts);
?>