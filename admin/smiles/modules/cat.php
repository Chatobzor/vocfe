<?php
/**
 * VOC++ Smile management
 * Category module
 *
 * @author ChatMaster <chatmaster@pozitiff.lv>
 * @copyright ChatMaster <chatmaster@pozitiff.lv>
 * @since 07.12.2009
 * @version 2.0
 *
 */

if (!defined('SM_COMMON')) {
    exit('stop');
}

/*******************************************/
/* Edit categories */
$do = trim(strip_tags($_GET['do']));
if ($do == 'edit') {
    $names = $_POST['names'];
    if (count($names)) {
        $new_file = '';
        if (file_exists($SM_GROUPS_FILE)) {
            $lines = file($SM_GROUPS_FILE);
            if (count($lines)) {
                foreach ($lines as $line) {
                    list($id, $name) = explode("\t", trim($line));
                    if (isset($names[$id])) {
                        $name = trim(strip_tags($names[$id]));
                    }

                    $new_file .= $id."\t".$name."\n";
                }
            }
        }

        $f = fopen($SM_GROUPS_FILE, "w");
        if (!$f) {
            header('Location: conv.php?m='.$mod.'&session='.$session.'&e=1');
            exit;
        }
        fwrite($f, $new_file);
        fclose($f);

        header('Location: conv.php?m='.$mod.'&session='.$session);
        exit;
    }
}

/*******************************************/
/* Delete category */
$del = intval($_GET['del']);
if ($del) {
    $new_file = '';
    $first_id = 0;

    // Process groups
    if (file_exists($SM_GROUPS_FILE)) {
        $lines = file($SM_GROUPS_FILE);
        if (count($lines)) {
            foreach ($lines as $line) {
                list($id, $name) = explode("\t", trim($line));
                if ($id != $del) {
                    if (!$first_id) {
                        $first_id = $id;
                    }
                    $new_file .= $id."\t".$name."\n";
                }
            }
        }
    }

    $f = fopen($SM_GROUPS_FILE, "w");
    if (!$f) {
        header('Location: conv.php?m='.$mod.'&session='.$session.'&e=1');
        exit;
    }
    fwrite($f, $new_file);
    fclose($f);

    // Process smiles
    $new_file = '';
    if (file_exists($SM_SMILES_FILE)) {
        $lines = file($SM_SMILES_FILE);
        if (count($lines)) {
            foreach ($lines as $line) {
                $parts = explode("\t", trim($line));
                if ($parts[0] == $del) {
                    $parts[0] = $first_id;
                }
                $line = join("\t", $parts);
                $new_file .= $line."\n";
            }
        }

        $f = fopen($SM_SMILES_FILE, "w");
        if (!$f) {
            header('Location: conv.php?m='.$mod.'&session='.$session.'&e=2');
            exit;
        }
        fwrite($f, $new_file);
        fclose($f);
    }

    header('Location: conv.php?m='.$mod.'&session='.$session);
    exit;
}

/*******************************************/
/* Add category */
set_variable("new_cat_name");
$new_cat_name = trim(strip_tags(stripslashes($new_cat_name)));
if ($new_cat_name) {
    // Create directory if needed
    $dir = dirname($SM_GROUPS_FILE);
    if (!file_exists($dir)) {
        mkdir($dir);
    }

    // Define category ID
    $cat_id = 0;
    if (file_exists($SM_GROUPS_FILE)) {
        $lines = file($SM_GROUPS_FILE);
        if (count($lines)) {
            $last_line = array_pop($lines);
            list($cat_id, $cat_name) = explode("\t", $last_line);
        }
    }
    $cat_id++;

    $f = fopen($SM_GROUPS_FILE, "a+");
    if (!$f) {
        header('Location: conv.php?m='.$mod.'&session='.$session.'&e=1');
        exit;
    }
    fwrite($f, $cat_id."\t".$new_cat_name."\n");
    fclose($f);

    header('Location: conv.php?m='.$mod.'&session='.$session);
    exit;
}

/* Read category list */
$categories = array();
if (file_exists($SM_GROUPS_FILE)) {
    $lines = file($SM_GROUPS_FILE);
    if (count($lines)) {
        foreach ($lines as $line) {
            list($id, $name) = explode("\t", trim($line));
            $categories[$id] = $name;
        }
    }
}

/* Error processing */
$e = isset($_GET['e']) ? intval($_GET['e']) : 0;
$err = '';

if ($e == 1) {
    $err = 'Не могу открыть файл '.$SM_GROUPS_FILE.' на запись!';
} elseif ($e == 2) {
    $err = 'Не могу открыть файл '.$SM_SMILES_FILE.' на запись!';
}

if ($err) {
    echo '<div style="text-align:center; color:red; border:1px solid red; padding:20px; margin:10px;">'.$err.'</div>';
}

?>

<script type="text/javascript">
    function rename(id, name) {
        document.getElementById('cat' + id).innerHTML = '<input type="text" name="names[' + id + ']" value="' + name + '" class="input" style="width:200px;" id="i' + id + '" />';
        document.getElementById('i' + id).focus();
        document.getElementById('btn').style.display = 'block';
    }
</script>

<!-- AddForm -->
<fieldset>
    <legend>Добавить категорию</legend>
    <form action="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>" method="post" style="margin:0px; padding:0px;">
        <table align="center">
            <tr>
                <td>Название новой категории:</td>
                <td style="padding-right:30px;"><input type="text" name="new_cat_name" style="width: 300px;"
                            class="input"></td>
                <td><input type="submit" value="Добавить" class="button"></td>
            </tr>
        </table>
    </form>
</fieldset>
<!-- End AddForm -->

<?
if (count($categories)): ?>
    <form action="conv.php?m=<?= $mod; ?>&session=<?= $session; ?>&do=edit" method="post">
        <div style="margin:0px 10px;">
            <table width="100%" style="border:1px solid #bfb8bf; margin:0 0 10px 0;">
                <tr style="background:#eeeeee;">
                    <td width="30"
                            style="text-align:center; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">
                        <b>ID</b></td>
                    <td style="padding:0px 2px; font-weight:bold; border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;">Название</td>
                    <td style="border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;" width="30">&nbsp;</td>
                    <td style="border-bottom:1px solid #bdbece; border-right:1px solid #b5b6c8;" width="30">&nbsp;</td>
                </tr>
                <?php
                foreach ($categories as $k => $v) {
                    echo '
			<tr class="tr_normal" onmouseover="this.className=\'tr_hover\';" onmouseout="this.className=\'tr_normal\';">
			  <td width="30" style="text-align:center;">'.$k.'</td>
			  <td><div id="cat'.$k.'">'.$v.'</div></td>
			  <td width="30" style="text-align:center;"><a href="#" onclick="rename('.$k.', \''.$v.'\'); return false;"><img src="smiles/images/edit.gif" alt="Редактировать название" title="Редактировать название" border="0"></a></td>
			  <td width="30" style="text-align:center;"><a href="conv.php?m='.$mod.'&session='.$session.'&del='.$k.'" onclick="if (!confirm(\'Удалить эту категорию? Смайлы из этой категории будут перемещены в первую категорию из оставшихся.\')) return false;"><img src="smiles/images/del.png" width="16" height="16" alt="[x]" border="0"></a></td>
			</tr>
			';
                }
                ?>
            </table>
            <div id="btn" style="display:none; margin-bottom:10px;"><input type="submit" value="Сохранить"
                        class="button"></div>
        </div>
    </form>
<?
else: ?>
    <div style="text-align:center; color:red; border:1px solid red; padding:20px; margin:10px;">Список категорий пуст.</div>
<?
endif; ?>
