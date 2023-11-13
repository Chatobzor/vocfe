<?php

if (!defined('SM_COMMON')) {
    exit('stop');
}

$critical_error = 0;
if (!file_exists(dirname($SM_CONF_FILE))) {
    @mkdir(dirname($SM_CONF_FILE));
    @chmod(dirname($SM_CONF_FILE), 0777);
}

if (!file_exists(dirname($SM_CONF_FILE))) {
    $critical_error = 1;
}

// Backup sender.php and try to patch it.
$sender_backup_file = $data_path.'smiles/sender.php';
if (!$critical_error && !file_exists(
        $sender_backup_file
    ) && (!isset($sm_config['installed']) || !$sm_config['installed'])) {
    $sender = file_get_contents($file_path.'sender.php');
    if (strpos($sender, 'require_once "smiles.php";') === false) {
        $f = fopen($sender_backup_file, 'w');
        if ($f) {
            fwrite($f, $sender);
            fclose($f);

            // Check backup file
            if (file_exists($sender_backup_file) && filesize($sender_backup_file) != filesize(
                    $file_path.'sender.php'
                )) {
                // Delete backup file. It's incorrect.
                unlink($sender_backup_file);
            }

            // Patch sender.php
            if (file_exists($sender_backup_file)) {
                $patched_sender = str_replace(
                    'usort($SmTbl, "cmpLen");',
                    'require_once "smiles.php";'.PHP_EOL.'       usort($SmTbl, "cmpLen");',
                    $sender
                );
                $f = fopen($file_path.'sender2.php', 'w');
                if ($f) {
                    fwrite($f, $patched_sender);
                    fclose($f);

                    if (file_exists($file_path.'sender2.php') && filesize($file_path.'sender2.php') > filesize(
                            $file_path.'sender.php'
                        )) {
                        unlink($file_path.'sender.php');
                        rename($file_path.'sender2.php', $file_path.'sender.php');
                    }
                }
            }
        }
    }
}

if (!file_exists(dirname($SM_CONF_FILE).'/data/')) {
    @mkdir(dirname($SM_CONF_FILE).'/data/');
    @chmod(dirname($SM_CONF_FILE).'/data/', 0777);
}
if (!file_exists(dirname($SM_CONF_FILE).'/data/0/')) {
    @mkdir(dirname($SM_CONF_FILE).'/data/0/');
    @chmod(dirname($SM_CONF_FILE).'/data/0/', 0777);
}

/* Update settings */
if ($ff == "settings") {
    // Create groups file
    if (isset($_POST['cat_name']) && trim(strip_tags($_POST['cat_name']))) {
        $f = fopen($SM_GROUPS_FILE, 'w');
        if (!$f) {
            header('Location: conv.php?m='.$mod.'&session='.$session.'&e=5');
            exit;
        }
        fwrite($f, '1'."\t".trim(strip_tags($_POST['cat_name']))."\n");
        fclose($f);
    }

    // Create defaults file
    if (!file_exists($SM_DEFAULTS_FILE)) {
        $f = fopen($SM_DEFAULTS_FILE, 'w');
        fclose($f);
    }

    // Importing converts.dat
    if (isset($_POST['import_converts_dat']) && $_POST['import_converts_dat']) {
        if (file_exists($data_path.'converts.dat')) {
            $lines = file($data_path.'converts.dat');
            if (count($lines)) {
                $new_file = '';
                foreach ($lines as $line) {
                    $parts = explode("\t", trim($line));
                    $code = trim($parts[0]);
                    $tag = strip_tags($parts[1], '<img>');
                    preg_match('#src="([\s\S]+)"#iU', $tag, $match);
                    $tag = trim($match[1]);
                    $tag_parts = explode('/', $tag);
                    $tag = array_pop($tag_parts);
                    if (file_exists($upload_dir.$tag)) {
                        $new_smile_data = array(1, $code, $tag, '', '', ';');
                        $new_file .= join("\t", $new_smile_data)."\n";
                    }
                }

                if ($new_file) {
                    $f = fopen($SM_SMILES_FILE, 'w');
                    if (!$f) {
                        header('Location: conv.php?m='.$mod.'&session='.$session.'&e=4');
                        exit;
                    }
                    fwrite($f, $new_file);
                    fclose($f);
                }
            }
        }
    }

    // Updating Settings
    set_variable('max_to_frame');
    set_variable('max_total');
    set_variable('sm_url');
    set_variable('hide_smiles');
    set_variable('clickable');

    $sm_url = str_replace("\\", "/", $sm_url);

    $configs = array();
    $configs[] = '<?php';
    $configs[] = '$sm_config[\'installed\'] = true;';
    $configs[] = '$sm_config[\'max_to_frame\'] = '.intval($max_to_frame).';';
    $configs[] = '$sm_config[\'max_total\'] = '.intval($max_total).';';
    $configs[] = '$sm_config[\'hide_smiles\'] = '.intval($hide_smiles).';';
    $configs[] = '$sm_config[\'clickable\'] = '.intval($clickable).';';
    $configs[] = '$sm_config[\'sm_url\'] = "'.trim(str_replace("\"", "\\\"", $sm_url)).'";';

    $configs[] = '?>';
    $CONFIG = join("\n", $configs);

    $dir = dirname($SM_CONF_FILE);
    if (!file_exists($dir)) {
        mkdir($dir);
    }

    $f = fopen($SM_CONF_FILE, "a+");
    if (!$f) {
        header('Location: conv.php?e=1&session='.$session);
        exit;
    }
    flock($f, LOCK_EX);
    ftruncate($f, 0);
    fwrite($f, $CONFIG);
    flock($f, LOCK_UN);
    fclose($f);

    header('Location: conv.php?e=2&session='.$session);
    exit;
}

/* Get Y/N enum */
function yes_no($yes = 0)
{
    $ret = '';
    $ret .= '<option value="0"';
    if (!$yes) {
        $ret .= ' selected="selected"';
    }
    $ret .= '>Нет</option>';
    $ret .= '<option value="1"';
    if ($yes) {
        $ret .= ' selected="selected"';
    }
    $ret .= '>Да</option>';
    return $ret;
}

$e = isset($_GET['e']) ? intval($_GET['e']) : 0;
$err = '';

if (!file_exists($SM_CONF_FILE)) {
    $e = 3;
}

if ($e == 1) {
    $err = 'Не могу открыть файл '.$SM_CONF_FILE.' на запись!';
} elseif ($e == 2) {
    $err = 'Настройки успешно сохранены!';
} elseif ($e == 3) {
    $err = 'Конфигурационный файл ещё не существует. Прежде чем приступить к дальнейшей работе, настройте скрипт (см. форму ниже)';
} elseif ($e == 4) {
    $err = 'Не могу писать в файл '.$SM_SMILES_FILE.'!';
} elseif ($e == 5) {
    $err = 'Не могу писать в файл '.$SM_GROUPS_FILE.'!';
}

if ($err) {
    echo '<div style="text-align:center; color:red; border:1px solid red; padding:20px; margin:10px;">'.$err.'</div>';
}

?>
<!-- Main Configuration -->
<form action="conv.php" method="post">
    <input type="hidden" name="ff" value="settings">
    <input type="hidden" name="session" value="<?= $session; ?>">

    <?
    if (!isset($sm_config['installed']) || !$sm_config['installed']): ?>
        <fieldset>
            <legend>Проверка системы:</legend>
            <table width="100%">
                <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                    <td class="settings_left">Проверка директории <br/><b><?= dirname($SM_CONF_FILE); ?></b></td>
                    <td>
                        <?
                        if (!file_exists(dirname($SM_CONF_FILE))): $critical_error = 1; ?>
                            <span style="color:red;">Не существует. Автоматическое создание не удалось.</span>
                        <?
                        else: ?>
                            <?
                            if (!is_writable(dirname($SM_CONF_FILE))): ?>
                                <span style="color:red;">Существует, но не доступна для записи. Автоматическое выставление прав не удалось.</span>
                            <?
                            else: ?>
                                <span style="color:green;">Всё в порядке.</span>
                            <?
                            endif; ?>
                        <?
                        endif; ?>
                    </td>
                </tr>
                <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                    <td class="settings_left">Проверка директории <br/><b><?= dirname($SM_CONF_FILE); ?>/data/</b></td>
                    <td>
                        <?
                        if (!file_exists(dirname($SM_CONF_FILE).'/data/')): $critical_error = 1; ?>
                            <span style="color:red;">Не существует. Автоматическое создание не удалось.</span>
                        <?
                        else: ?>
                            <?
                            if (!is_writable(dirname($SM_CONF_FILE).'/data/')): ?>
                                <span style="color:red;">Существует, но не доступна для записи. Автоматическое выставление прав не удалось.</span>
                            <?
                            else: ?>
                                <span style="color:green;">Всё в порядке.</span>
                            <?
                            endif; ?>
                        <?
                        endif; ?>
                    </td>
                </tr>
                <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                    <td class="settings_left">Проверка директории <br/><b><?= dirname($SM_CONF_FILE); ?>/data/0/</b>
                    </td>
                    <td>
                        <?
                        if (!file_exists(dirname($SM_CONF_FILE).'/data/0/')): $critical_error = 1; ?>
                            <span style="color:red;">Не существует. Автоматическое создание не удалось.</span>
                        <?
                        else: ?>
                            <?
                            if (!is_writable(dirname($SM_CONF_FILE).'/data/0/')): ?>
                                <span style="color:red;">Существует, но не доступна для записи. Автоматическое выставление прав не удалось.</span>
                            <?
                            else: ?>
                                <span style="color:green;">Всё в порядке.</span>
                            <?
                            endif; ?>
                        <?
                        endif; ?>
                    </td>
                </tr>
                <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                    <td class="settings_left">Проверка <b>sender.php</b></td>
                    <td>
                        <?
                        if (strpos(
                                file_get_contents($file_path.'sender.php'),
                                'require_once "smiles.php";'
                            ) === false): $critical_error = 1; ?>
                            <span style="color:red;">Не удалось пропатчить файл автоматически. Сделайте это вручную:</span>
                            <ol>
                                <li>Откройте файл <b><?= $file_path; ?>sender.php</b></li>
                                <li>Найдите в нём строку
                                    <div style="border:1px solid #dbdbdb; padding:3px; font-weight:bold; background:#fff;">usort($SmTbl, "cmpLen");</div>
                                </li>
                                <li><span style="color:blue;">ПЕРЕД</span> ней вставьте строку
                                    <div style="border:1px solid #dbdbdb; padding:3px; font-weight:bold; background:#fff;">require_once "smiles.php";</div>
                                </li>
                                <li>Сохраните файл и перезагрузите эту страницу.</li>
                            </ol>
                        <?
                        else: ?>
                            <span style="color:green;">Всё в порядке.</span>
                        <?
                        endif; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <fieldset>
            <legend>Импорт:</legend>
            <table width="100%">
                <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                    <td class="settings_left">Импортировать смайлы из <b>converts.dat</b>:</td>
                    <td><input type="checkbox" name="import_converts_dat" value="1" checked="checked"/></td>
                </tr>
                <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                    <td class="settings_left">Импортировать в категорию:</td>
                    <td><input type="text" name="cat_name" value="Без категории" style="width:200px;"
                                class="input"> Введите сюда название основной категории для смайлов. В будущем Вы сможете его изменить.
                    </td>
                </tr>
            </table>
        </fieldset>
    <?
    endif; ?>

    <fieldset>
        <legend>Настройки</legend>
        <table width="100%">
            <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                <td class="settings_left"
                        title="Сколько максимум смайлов разрешается загружать во фрейм. 0 - не ограничено.">Максимум смайлов во фрейме:
                </td>
                <td><input type="text" name="max_to_frame"
                            value="<?= isset($sm_config['max_to_frame']) ? intval($sm_config['max_to_frame']) : 0; ?>"
                            style="width:50px;" class="input"></td>
            </tr>
            <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                <td class="settings_left"
                        title="Сколько всего смайлов можно добавить в любимые. 0 - не ограничено.">Максимум любимых смайлов всего:
                </td>
                <td><input type="text" name="max_total"
                            value="<?= isset($sm_config['max_total']) ? intval($sm_config['max_total']) : 0; ?>"
                            style="width:50px;" class="input"></td>
            </tr>
            <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                <td class="settings_left" title="URL сервера, с которого нужно загружать смайлы">URL для смайлов:</td>
                <td><input type="text" name="sm_url"
                            value="<?= isset($sm_config['sm_url']) ? $sm_config['sm_url'] : $chat_url; ?>"
                            style="width:200px;" class="input"> должно иметь вид как
                    <b><?= $chat_url; ?></b>, т.е. начинаться с http:// и заканчиваться слэшем!
                </td>
            </tr>
            <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                <td class="settings_left"
                        title="Не показывать смайлы в админке чата. Если да, то их можно будет посмотреть по клику.">Скрывать смайлы в админке:
                </td>
                <td><input type="hidden" name="hide_smiles" value="0"/><input type="checkbox" name="hide_smiles"
                            value="1" <?
                    if (!isset($sm_config['hide_smiles']) || $sm_config['hide_smiles'] == 1): ?>checked="checked"<?
                    endif; ?> /></td>
            </tr>
            <tr class="tr_normal" onmouseover="this.className='tr_hover';" onmouseout="this.className='tr_normal';">
                <td class="settings_left"
                        title="Вкл/выкл кликабельности смайлов в окне сообщений (приват, общак).">Кликабельность смайлов в окне сообщений:
                </td>
                <td><input type="hidden" name="clickable" value="0"/><input type="checkbox" name="clickable" value="1"
                        <?
                        if (!isset($sm_config['clickable']) || $sm_config['clickable'] == 1): ?>checked="checked"<?
                    endif; ?> /></td>
            </tr>
        </table>
    </fieldset>


    <?
    if ($critical_error): ?>
        <div style="text-align:center; color:red; border:1px solid red; padding:20px; margin:10px;">
            Сохранение настроек и дальнейшая работа со скриптом невозможны. Пожалуйста исправьте вручную указанные выше ошибки и перезагрузите данную страницу.
        </div>
    <?
    else: ?>
        <div style="margin:0px 10px; text-align:right;">
            <input type="submit" class="button" value="Сохранить" style="margin-left:3px;">
            <input type="reset" class="button" value="Отмена" style="margin-left:3px;">
        </div>
    <?
    endif; ?>
</form>
<!-- End Main Settings-->
