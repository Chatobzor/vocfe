<?php
/**
 * VOC++ Smile management
 * Inactive Smiles module
 *
 * @author ChatMaster <chatmaster@pozitiff.lv>
 * @copyright ChatMaster <chatmaster@pozitiff.lv>
 * @since 07.12.2009
 * @version 2.0
 *
 */

/* Delete file */
$del = $_GET['del'];
if ($del) {
    $del = basename($del);
    $del = str_replace(array('/', '\\'), '', $del); // После basename оно не нужно, но пусть будет, от греха подальше
    if (file_exists($upload_dir.$del)) {
        unlink($upload_dir.$del);
    }

    header('Location: conv.php?&m='.$mod.'&session='.$session);
    exit;
}

/* Add smiles */
$do = $_GET['do'];
if ($do == 'add') {
    $add = isset($_POST['add']) ? $_POST['add'] : array();
    if (count($add)) {
        $codes = $_POST['codes'];
        $cids = $_POST['cids'];
        $emotions = $_POST['emotions'];
        $aliases = $_POST['aliases'];
        $commons = $_POST['commons'];

        foreach ($add as $file => $v) {
            if ($v == 1) {
                $new_smile_data = array(
                    trim(strip_tags($cids[$file])),
                    trim(strip_tags($codes[$file])),
                    trim(strip_tags(urldecode($file))),
                    trim(strip_tags($emotions[$file])),
                    trim(strip_tags($aliases[$file])),
                    ';'
                );
                $f = fopen($SM_SMILES_FILE, 'a+');
                if (!$f) {
                    header('Location: conv.php?m='.$mod.'&session='.$session.'&e=1');
                    exit;
                }
                fwrite($f, join("\t", $new_smile_data)."\n");
                fclose($f);

                if ($commons[$file] == 1) {
                    $f = fopen($SM_DEFAULTS_FILE, "a+");
                    if (!$f) {
                        header('Location: conv.php?m='.$mod.'&session='.$session.'&e=2');
                        exit;
                    }
                    fwrite($f, $file."\n");
                    fclose($f);
                }
            }
        }
    }
    header('Location: conv.php?m='.$mod.'&session='.$session.'&e=3');
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

/* Read acive smiles list */
$smiles = array();
if (file_exists($SM_SMILES_FILE)) {
    $lines = file($SM_SMILES_FILE);
    if (count($lines)) {
        foreach ($lines as $line) {
            $parts = explode("\t", trim($line));
            $smiles[] = trim($parts[2]);
        }
    }
}

/* Get list of inactive smiles */
$inactive = array();
$d = opendir($upload_dir);
if ($d) {
    while ($file = readdir($d)) {
        if ($file != '.' && $file != '..' && !in_array($file, $smiles)) {
            $inactive[] = $file;
        }
    }
    closedir($d);
}

function make_code($name)
{
    $parts = explode('.', $name);
    $ext = array_pop($parts);
    $name = join('.', $parts);
    $name = '*'.$name.'*';
    return $name;
}

/* Error processing */
$e = isset($_GET['e']) ? intval($_GET['e']) : 0;
switch ($e) {
    case 1:
        $err = 'Не могу писать в файл '.$SM_SMILES_FILE.'!';
        break;
    case 2:
        $err = 'Не могу писать в файл '.$SM_DEFAULTS_FILE.'!';
        break;
    case 3:
        $err = 'Изменения успешно сохранены!';
        break;
    default:
        $err = '';
}

if ($err) {
    echo '<div style="text-align:center; color:red; border:1px solid red; padding:20px; margin:10px;">'.$err.'</div>';
}

?>

    <script type="text/javascript">
        function del() {
            if (confirm('Удалить смайл?\nОн будет физически стёрт с сервера и Вы не сможете его восстановить!')) {
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

    <fieldset>
        <legend>Что такое неактивные смайлы?</legend>
        Неактивные смайлы - это смайлы, которые физически существуют на сервере в папке<br/>
        <b><?= $upload_dir; ?></b>,<br/>
        но не используются в чате. <br/>
        На этой странице Вы сможете либо включить их использование, либо полностью удалить с сервера.
    </fieldset>

<?
if (count($inactive)): ?>
    <script type="text/javascript">
        function checkAll() {
            for (i = 0; i <= <?=(count($inactive) - 1);?>; i++) {
                document.getElementById('adder_' + i).checked = true;
            }
        }

        function uncheckAll() {
            for (i = 0; i <= <?=(count($inactive) - 1);?>; i++) {
                document.getElementById('adder_' + i).checked = false;
            }
        }
    </script>
    <form action="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>&do=add" method="post">
        <div style="margin:0px 10px;">
            <a href="#" onclick="checkAll(); return false;">Отметить все</a> |
            <a href="#" onclick="uncheckAll(); return false;">Снять выделение</a>
            <table width="100%" style="border:1px solid #bfb8bf; margin:0;">
                <tr style="background:#eeeeee;">
                    <td align="center"
                            style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;"
                            width="60">Добавить
                    </td>
                    <td width="60"
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
                    <td style="border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;" width="30">&nbsp;</td>
                </tr>
                <?php
                foreach ($inactive as $k => $v) {
                    $v = urlencode($v);
                    ?>
                    <tr class="tr_normal" onmouseover="this.className='tr_hover';"
                            onmouseout="this.className='tr_normal';">
                        <td align="center"><input type="hidden" name="add[<?= $v; ?>]" value="0"/><input type="checkbox"
                                    name="add[<?= $v; ?>]" value="1" id="adder_<?= $k; ?>"/></td>
                        <td style="text-align:center;"><input type="text" name="codes[<?= $v; ?>]"
                                    value="<?= make_code($v); ?>" class="input" style="width:60px;"/></td>
                        <td><select class="input" name="cids[<?= $v; ?>]"><?
                                foreach ($categories as $kk => $vv): ?>
                                    <option value="<?= $kk; ?>"><?= $vv ?></option><?
                                endforeach; ?></select></td>
                        <td align="center"><?
                            if ($sm_config['hide_smiles']): ?><a href="#" id="show<?= $k; ?>"
                                    onclick="show(<?= $k; ?>, '<?= $sm_config['sm_url']; ?>converts/<?= $v; ?>'); return false;"><?= $v; ?> (показать)</a>
                            <div id="img<?= $k; ?>" style="display:none;"><?
                                else: ?>
                                <div><?
                                    endif; ?><img id="pic<?= $k; ?>" src="<?
                                    if ($sm_config['hide_smiles']): ?>smiles/images/next.png<?
                                    else: ?><?= $sm_config['sm_url']; ?>converts/<?= $v; ?><?
                                    endif; ?>" title="" alt=""/></div>
                        </td>
                        <td><input type="text" name="emotions[<?= $v; ?>]" value="" class="input"/></td>
                        <td><input type="text" name="aliases[<?= $v; ?>]" value="" class="input"/></td>
                        <td align="center"><input type="hidden" name="commons[<?= $v; ?>]" value="0"/><input
                                    type="checkbox" name="commons[<?= $v; ?>]" value="1"/></td>
                        <td style="text-align:center;"><a
                                    href="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>&del=<?= urlencode($v); ?>"
                                    onclick="return del();"><img src="smiles/images/del.png" width="16" height="16"
                                        alt="[x]" border="0"></a></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <a href="#" onclick="checkAll(); return false;">Отметить все</a> |
            <a href="#" onclick="uncheckAll(); return false;">Снять выделение</a>

            <div id="btn" style="margin-bottom:10px; margin-left:5px; padding-top:10px;"><input type="submit"
                        value="Добавить" class="button"></div>
        </div>
    </form>
<?
else: ?>
    <div style="padding:20px; text-align:center; color:red;">Не найдено ни одного неактивного смайла.</div>
<?
endif; ?>