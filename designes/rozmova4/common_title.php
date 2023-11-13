<?php
if (!defined("_COMMON_")) {echo "stop";exit;}
header("Expires: Mon 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D d M Y H:i:s") . "GMT");
header("Cache-Control: no-cache must-revalidate");
header("Pragma: no-cache");
?>

<html>
<head>
        <title><?=$w_title?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo DEFAULT_CHARSET; ?>>">
        <link rel="STYLESHEET" type="text/css" href="<?php echo $current_design;?>style.css">
        <?php if ($charset!="") echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$charset."\">";?>