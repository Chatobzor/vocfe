<?php

/**
 * $SM_SMILES_FILE format:
 * 0 - cid,
 * 1 - code,
 * 2 - file name,
 * 3 - alt,
 * 4 - aliases
 * 5 - ; (end of line, to avoid errors after trim() )
 *
 */

if (!defined('SM_COMMON')) {
    exit('stop');
}

$cid = intval($_GET['cid']);

/*******************************************/
/* Define action */
$do = trim(strip_tags($_GET['do']));

/*******************************************/
/* Add new smile */
if ($do == 'add') {
    $code = trim(strip_tags($_POST['code']));
    $group = trim(strip_tags($_POST['group']));
    $alt = trim(strip_tags($_POST['alt']));
    $aliases = trim(strip_tags($_POST['aliases']));
    $common = isset($_POST['common']) ? intval($_POST['common']) : 0;
    $file = $_FILES['file'];

    /* Check input data */
    if (!$code) {
        header('Location: conv.php?m='.$mod.'&session='.$session.'&e=1');
        exit;
    }
    if (!$group) {
        header('Location: conv.php?m='.$mod.'&session='.$session.'&e=2');
        exit;
    }
    if (!$file || !count($file) || $file['error']) {
        header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=3');
        exit;
    }

    /* Check codes */
    $used_codes = array();
    if (file_exists($SM_SMILES_FILE)) {
        $lines = file($SM_SMILES_FILE);
        if (count($lines)) {
            foreach ($lines as $line) {
                $parts = explode("\t", trim($line));
                $used_codes[] = trim($parts[1]);
                if ($parts[4]) {
                    $alt_codes = explode(',', $parts[4]);
                    if (count($alt_codes)) {
                        foreach ($alt_codes as $alt_code) {
                            $used_codes[] = trim($alt_code);
                        }
                    }
                }
            }
        }
    }

    if (in_array($code, $used_codes)) {
        header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=4');
        exit;
    }
    $used_codes[] = $code;

    if ($aliases) {
        $alt_codes = explode(',', $aliases);
        if (count($alt_codes)) {
            foreach ($alt_codes as $alt_code) {
                if (in_array($alt_code, $used_codes)) {
                    header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=5');
                    exit;
                }
                $used_codes[] = $alt_code;
            }
        }
    }

    /* Upload file */
    if (!file_exists($upload_dir) || !is_writable($upload_dir)) {
        header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=6');
        exit;
    }

    if (file_exists($upload_dir.$file['name'])) {
        header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=7');
        exit;
    }

    move_uploaded_file($file['tmp_name'], $upload_dir.$file['name']);
    if (!file_exists($upload_dir.$file['name'])) {
        header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=8');
        exit;
    }
    @chmod($upload_dir.$file['name'], 0644);

    /* Register smile */
    $new_smile_data = array($group, $code, $file['name'], $alt, $aliases, ';');

    $f = fopen($SM_SMILES_FILE, 'a+');
    if (!$f) {
        header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=9');
        exit;
    }
    fwrite($f, join("\t", $new_smile_data)."\n");
    fclose($f);

    /* Register smile as common */
    if ($common) {
        $f = fopen($SM_DEFAULTS_FILE, "a+");
        if (!$f) {
            header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session.'&e=10');
            exit;
        }
        fwrite($f, $file['name']."\n");
        fclose($f);
    }

    header('Location: conv.php?cid='.$group.'&m='.$mod.'&session='.$session);
    exit;
}

/*******************************************/
/* Edit smiles */
if ($do == 'edit') {
    $codes = $_POST['codes'];
    if (count($codes)) {
        $cids = $_POST['cids'];
        $emotions = $_POST['emotions'];
        $aliases = $_POST['aliases'];
        $commons = $_POST['commons'];

        /* Edit smiles list */
        $new_file = '';
        $lines = file($SM_SMILES_FILE);
        foreach ($lines as $line) {
            $parts = explode("\t", trim($line));

            if (isset($codes[$parts[2]])) {
                $parts[0] = $cids[$parts[2]];
                $parts[1] = $codes[$parts[2]];
                $parts[3] = $emotions[$parts[2]];
                $parts[4] = $aliases[$parts[2]];
            }


            $new_file .= join("\t", $parts)."\n";
        }

        $f = fopen($SM_SMILES_FILE, 'w');
        if (!$f) {
            header('Location: conv.php?cid='.$cid.'&m='.$mod.'&session='.$session.'&e=9');
            exit;
        }
        fwrite($f, $new_file);
        fclose($f);

        /* Edit common smiles */
        $new_file = '';
        $lines = file($SM_DEFAULTS_FILE);
        if (count($lines)) {
            foreach ($lines as $line) {
                $line = trim($line);
                if (!isset($commons[$line])) {
                    $new_file .= $line."\n";
                }
            }
        }

        foreach ($commons as $k => $v) {
            if ($v == 1) {
                $new_file .= $k."\n";
            }
        }

        $f = fopen($SM_DEFAULTS_FILE, "w");
        if (!$f) {
            header('Location: conv.php?cid='.$cid.'&m='.$mod.'&session='.$session.'&e=10');
            exit;
        }
        fwrite($f, $new_file);
        fclose($f);
    }
}

/*******************************************/
/* Delete smile */
$del = trim(strip_tags($_GET['del']));
if ($del && file_exists($SM_SMILES_FILE)) {
    $new_file = '';
    $lines = file($SM_SMILES_FILE);
    if (count($lines)) {
        foreach ($lines as $line) {
            $parts = explode("\t", trim($line));
            if ($parts[2] != $del) {
                $new_file .= trim($line)."\n";
            }
        }
    }

    $f = fopen($SM_SMILES_FILE, "w");
    if (!$f) {
        header('Location: conv.php?cid='.$cid.'&m='.$mod.'&session='.$session.'&e=9');
        exit;
    }
    fwrite($f, $new_file);
    fclose($f);

    if (file_exists($SM_DEFAULTS_FILE)) {
        $new_file = '';
        $lines = file($SM_DEFAULTS_FILE);
        if (count($lines)) {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line != $del) {
                    $new_file .= $line."\n";
                }
            }
        }
        $f = fopen($SM_DEFAULTS_FILE, "w");
        if (!$f) {
            header('Location: conv.php?cid='.$cid.'&m='.$mod.'&session='.$session.'&e=10');
            exit;
        }
        fwrite($f, $new_file);
        fclose($f);
    }


    header('Location: conv.php?cid='.$cid.'&m='.$mod.'&session='.$session);
    exit;
}

/* Read category list */
$categories = array();
$counts = array();
if (file_exists($SM_GROUPS_FILE)) {
    $lines = file($SM_GROUPS_FILE);
    if (count($lines)) {
        foreach ($lines as $line) {
            list($id, $name) = explode("\t", trim($line));
            $categories[$id] = $name;
            $counts[$id] = 0;
        }
    }
}

/* Read common smiles list */
$defaults = array();
if (file_exists($SM_DEFAULTS_FILE)) {
    $lines = file($SM_DEFAULTS_FILE);
    if (count($lines)) {
        foreach ($lines as $line) {
            $defaults[] = trim($line);
        }
    }
}

/* Read smiles list */
$smiles = array();
if (file_exists($SM_SMILES_FILE)) {
    $lines = file($SM_SMILES_FILE);
    if (count($lines)) {
        foreach ($lines as $line) {
            $parts = explode("\t", trim($line));
            $counts[$parts[0]]++;
            if (in_array($parts[2], $defaults)) {
                $parts['is_default'] = true;
            } else {
                $parts['is_default'] = false;
            }
            if ($parts[0] == $cid) {
                $smiles[] = $parts;
            }
        }
    }
}

/* Error processing */
$e = isset($_GET['e']) ? intval($_GET['e']) : 0;
switch ($e) {
    case 1:
        $err = 'Не указан код вызова для смайла!';
        break;
    case 2:
        $err = 'Не выбрана категория!';
        break;
    case 3:
        $err = 'Ошибка загрузки файла!';
        break;
    case 4:
        $err = 'Основной код вызова уже используется!';
        break;
    case 5:
        $err = 'Один из дополнительных кодов вызова (алиасов) уже используется!';
        break;
    case 6:
        $err = 'Директория '.$upload_dir.' не доступна для записи!';
        break;
    case 7:
        $err = 'Файл с таким именем уже существует!';
        break;
    case 8:
        $err = 'Загрузка файла не удалась!';
        break;
    case 9:
        $err = 'Не могу писать в файл '.$SM_SMILES_FILE.'!';
        break;
    case 10:
        $err = 'Не могу писать в файл '.$SM_DEFAULTS_FILE.'!';
        break;
    default:
        $err = '';
}

if ($err) {
    echo '<div style="text-align:center; color:red; border:1px solid red; padding:20px; margin:10px;">'.$err.'</div>';
}

?>

<script type="text/javascript">
    function edit(id, is_default, fname) {
        document.getElementById('ebtn' + id).innerHTML = '<img src="smiles/images/edit.gif" alt="Редактировать название" title="Редактировать название" border="0">';
        document.getElementById('code' + id).innerHTML = '<input type="text" name="codes[' + fname + ']" value="' + document.getElementById('code' + id).innerHTML + '" class="input" style="width:40px;" id="i_code' + id + '" />';
        document.getElementById('emo' + id).innerHTML = '<input type="text" name="emotions[' + fname + ']" value="' + document.getElementById('emo' + id).innerHTML + '" class="input" id="i_emo' + id + '" />';
        document.getElementById('alias' + id).innerHTML = '<input type="text" name="aliases[' + fname + ']" value="' + document.getElementById('alias' + id).innerHTML + '" class="input" id="i_alias' + id + '" />';
        document.getElementById('cat' + id).innerHTML = '<select class="input" name="cids[' + fname + ']"><? foreach($categories as $k => $v):?><option value="<?=$k;?>"<? if($k == $cid):?> selected="selected"<? endif;?>><?=$v?></option><? endforeach;?></select>';
        document.getElementById('common' + id).innerHTML = '<input type="hidden" value="0" name="commons[' + fname + ']" /><input type="checkbox" value="1" name="commons[' + fname + ']" id="cm' + id + '" />';
        if (is_default == 1) {
            document.getElementById('cm' + id).checked = true;
        }

        document.getElementById('btn').style.display = 'block';
    }

    function check(obj) {
        if (!obj.file.value) {
            alert('Не выбран файл для загрузки!');
            obj.file.focus();
            return false;
        }
        if (!obj.code.value) {
            alert('Не введён код вызова для смайла!');
            obj.code.focus();
            return false;
        }
        if (obj.group.value == 'NONE') {
            alert('Выберите группу!');
            obj.group.focus();
            return false;
        }
    }

    function del() {
        if (confirm('Удалить смайл?\nБудет удалена только запись об этом смайле, сам файл останется на сервере!')) {
            return true;
        } else {
            return false;
        }
    }

    function show(id, url) {
        document.getElementById('show' + id).style.display = 'none';
        document.getElementById('img' + id).style.display = 'block';
        document.getElementById('pic' + id).src = url;
    }
</script>


<!-- AddForm -->
<fieldset>
    <legend>Добавить смайл</legend>
    <form action="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>&do=add" method="post" enctype="multipart/form-data"
            onsubmit="return check(this);" style="margin:0px; padding:0px;">
        <table align="center">
            <tr>
                <td align="right">Файл:</td>
                <td style="padding-right:30px;"><input type="file" name="file" class="input" size="7"></td>
                <td align="right">Код вызова:</td>
                <td style="padding-right:30px;"><input type="text" name="code" class="input"></td>
                <td align="right">Категория:</td>
                <td>
                    <select name="group" class="input">
                        <option value="NONE">--</option>
                        <?php
                        foreach ($categories as $k => $v) {
                            ?>
                            <option value="<?= $k; ?>"<?
                            if ($k == $cid): ?> selected="selected"<?
                            endif; ?>><?= $v; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td title="Какую эмоцию выражает этот смайл? Показывается как alt к картинке."
                        align="right">Эмоция (alt):
                </td>
                <td title="Какую эмоцию выражает этот смайл? Показывается как alt к картинке."
                        style="padding-right:30px;"><input type="text" name="alt" class="input"></td>
                <td title="Альтернативные коды вызова, через запятую." align="right">Алиасы:</td>
                <td title="Альтернативные коды вызова, через запятую." style="padding-right:30px;"><input type="text"
                            name="aliases" class="input"></td>
                <td title="Общие смайлы являются любимыми по-умолчанию."><input type="checkbox" name="common"
                            value="1"> Общий
                </td>
                <td align="right"><input type="submit" value="Добавить" class="button"></td>
            </tr>
        </table>
    </form>
</fieldset>
<!-- End AddForm -->

<?
if (count($categories)): ?>
    <div style="margin:0px 6px; padding-bottom:10px">
        <table width="100%" cellspacing="4" cellpadding="0">
            <tr>
                <td valign="top" width="200" style="border:1px solid #bfb8bf;">
                    <table width="100%">
                        <tr style="background:#eeeeee;">
                            <td style="font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">Категория</td>
                        </tr>
                        <?php
                        foreach ($categories as $k => $v) {
                            ?>
                            <tr <?
                            if ($k == $cid): ?>class="tr_hover" <?
                            else: ?>class="tr_normal" onmouseover="this.className='tr_hover';"
                                    onmouseout="this.className='tr_normal';"<?
                            endif; ?>>
                                <td><a style="display:block; padding-top:2px; padding-bottom:2px;"
                                            href="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>&cid=<?= $k; ?>"><?= $v; ?> (<?= $counts[$k]; ?>)</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </td>
                <td valign="top" style="border:1px solid #bfb8bf;">
                    <?
                    if (!$cid): ?>
                        <div style="padding:20px; text-align:center; color:red;">Выберите категорию смайлов для просмотра.</div>
                    <?
                    else: ?>
                        <?
                        if (count($smiles)): ?>
                            <form action="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>&do=edit&cid=<?= $cid; ?>"
                                    method="post">
                                <table width="100%">
                                    <tr style="background:#eeeeee;">
                                        <td width="40"
                                                style="text-align:center; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">
                                            <b>Код</b></td>
                                        <td style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">Категория</td>
                                        <td align="center"
                                                style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">Смайл
                                        </td>
                                        <td style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">Эмоция (alt)</td>
                                        <td style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">Алиасы</td>
                                        <td align="center"
                                                style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;"
                                                width="50">Общий
                                        </td>
                                        <td style="border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;"
                                                width="30">&nbsp;
                                        </td>
                                        <td style="border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;"
                                                width="30">&nbsp;
                                        </td>
                                    </tr>
                                    <?php
                                    foreach ($smiles as $k => $v) {
                                        ?>
                                        <tr class="tr_normal" onmouseover="this.className='tr_hover';"
                                                onmouseout="this.className='tr_normal';">
                                            <td style="text-align:center;">
                                                <div id="code<?= $k; ?>"><?= $v[1]; ?></div>
                                            </td>
                                            <td>
                                                <div id="cat<?= $k; ?>"><?= $categories[$v[0]]; ?></div>
                                            </td>
                                            <td align="center"><?
                                                if ($sm_config['hide_smiles']): ?><a href="#" id="show<?= $k; ?>"
                                                        onclick="show(<?= $k; ?>, '<?= $sm_config['sm_url']; ?>converts/<?= $v[2]; ?>'); return false;">Показать</a>
                                                <div id="img<?= $k; ?>" style="display:none;"><?
                                                    else: ?>
                                                    <div><?
                                                        endif; ?><img id="pic<?= $k; ?>" src="<?
                                                        if ($sm_config['hide_smiles']): ?>smiles/images/next.png<?
                                                        else: ?><?= $sm_config['sm_url']; ?>converts/<?= $v[2]; ?><?
                                                        endif; ?>" title="<?= $v[3]; ?>" alt="<?= $v[3]; ?>"/></div>
                                            </td>
                                            <td>
                                                <div id="emo<?= $k; ?>"><?= $v[3]; ?></div>
                                            </td>
                                            <td>
                                                <div id="alias<?= $k; ?>"><?= $v[4]; ?></div>
                                            </td>
                                            <td align="center">
                                                <div id="common<?= $k; ?>"><?= ($v['is_default']) ? '<span style="color:green;">Да</span>' : '<span style="color:red;">Нет</span>'; ?></div>
                                            </td>
                                            <td style="text-align:center;">
                                                <div id="ebtn<?= $k; ?>"><a href="#"
                                                            onclick="edit(<?= $k; ?>, <?= intval(
                                                                $v['is_default']
                                                            ); ?>, '<?= $v[2]; ?>'); return false;"><img
                                                                src="smiles/images/edit.gif"
                                                                alt="Редактировать название"
                                                                title="Редактировать название" border="0"></a></div>
                                            </td>
                                            <td style="text-align:center;"><a
                                                        href="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>&del=<?= urlencode(
                                                            $v[2]
                                                        ); ?>&cid=<?= $cid; ?>" onclick="return del();"><img
                                                            src="smiles/images/del.png" width="16" height="16" alt="[x]"
                                                            border="0"></a></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <div id="btn" style="display:none; margin-bottom:10px; margin-left:5px"><input
                                            type="submit" value="Сохранить" class="button"></div>
                            </form>
                        <?
                        else: ?>
                            <div style="padding:20px; text-align:center; color:red;">В этой категории нет ни одного смайла.</div>
                        <?
                        endif; ?>
                    <?
                    endif; ?>
                </td>
            </tr>
        </table>
    </div>
<?
else: ?>
    <div style="text-align:center; color:red; border:1px solid red; padding:20px; margin:10px;">Список категорий пуст.</div>
<?
endif; ?>
