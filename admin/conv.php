<?php

include("check_session.php");
include("../inc_common.php");

/*******************************************/
/* Configuration */
$SM_CONF_FILE = DATA_PATH.'smiles/config.php';
$SM_GROUPS_FILE = DATA_PATH.'smiles/groups.dat';
$SM_DEFAULTS_FILE = DATA_PATH.'smiles/defaults.dat';
$SM_SMILES_FILE = DATA_PATH.'smiles/smiles.dat';
$upload_dir = FILE_PATH.'converts/';

define('SM_COMMON', true);

/*******************************************/
/* Select module */
$module = trim(strip_tags($_GET['m']));
switch ($module) {
    case 'cat':
        $mod = 'cat';
        break;
    case 'smiles':
        $mod = 'smiles';
        break;
    case 'inactive':
        $mod = 'inactive';
        break;
    default:
        $mod = 'config';
}


/* Reading settings */
if (file_exists($SM_CONF_FILE)) {
    require_once($SM_CONF_FILE);
}
if (!defined('SM_COMMON')) {
    exit('Installation error');
}
?>
<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
    <link href="smiles/style.css" type="text/css" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
    function about() {
        document.getElementById('about_div').className = 'info';
    }

    function about_close() {
        document.getElementById('about_div').className = 'hidden';
    }
</script>

<!-- Menu Tabs -->
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="head">VOC++ Smile manager</td>
        <td class="head" style="text-align:right;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td class="tab_bar_bg" colspan="2">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <?
                    if (isset($sm_config['installed']) && $sm_config['installed']) : ?>
                        <td <?
                        if ($mod == 'config') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"
                                onclick="window.location.assign('conv.php?session=<?= $session; ?>&m=config');"<?
                        } ?>>Настройки
                        </td>
                        <td <?
                        if ($mod == 'cat') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"
                                onclick="window.location.assign('conv.php?session=<?= $session; ?>&m=cat');"<?
                        } ?>>Категории
                        </td>
                        <td <?
                        if ($mod == 'smiles') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"
                                onclick="window.location.assign('conv.php?session=<?= $session; ?>&m=smiles');"<?
                        } ?>>Активные смайлы
                        </td>
                        <td <?
                        if ($mod == 'inactive') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"
                                onclick="window.location.assign('conv.php?session=<?= $session; ?>&m=inactive');"<?
                        } ?>>Неактивные смайлы
                        </td>
                    <?
                    else: ?>
                        <td class="menu_btn_active">Инсталляция</td>
                    <?
                    endif; ?>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- End Menu Tabs -->


<?php
/*******************************************/

/* Load module */
$mod_file = 'smiles/modules/'.$mod.'.php';
if (file_exists($mod_file)) {
    include $mod_file;
} else {
    echo '<div>Attempt to load unknown module</div>';
}
?>

</body>
</html>