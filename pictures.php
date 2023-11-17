<?php
/**
 * VOC++ Smile manager
 *
 * @author ChatMaster <chatmaster@pozitiff.lv>
 * @copyright ChatMaster <chatmaster@pozitiff.lv>
 * @since 07.12.2009
 * @version 2.0
 *
 */

require_once "inc_common.php";
include $engine_path."users_get_list.php";

if (!$exists) {
    $error_text = $w_no_user;
    include $file_path."designes/".$design."/error_page.php";
    exit;
}

include "inc_user_class.php";
include $ld_engine_path."users_get_object.php";
include "user_validate.php";

$CID = isset($_GET['cid']) ? trim(strip_tags($_GET['cid'])) : 0;
$DISPLAY = isset($_GET['display']) ? trim(strip_tags($_GET['display'])) : '';
$PAGE = isset($_GET['page']) ? intval($_GET['page']) : 0;

// for compatibility with smiles 1.0
$SHOW = isset($_GET['show']) ? trim(strip_tags($_GET['show'])) : '';
if ($SHOW == 'my') {
    $CID = 'FAV';
    $DISPLAY = 'frame';
}

if ($CID != 'FAV') {
    $CID = intval($CID);
}

/*******************************************/
/* Configuration */
$SM_CONF_FILE = $data_path.'smiles/config.php';
$SM_GROUPS_FILE = $data_path.'smiles/groups.dat';
$SM_DEFAULTS_FILE = $data_path.'smiles/defaults.dat';
$SM_SMILES_FILE = $data_path.'smiles/smiles.dat';
$upload_dir = $file_path.'converts/';

if (!file_exists($SM_CONF_FILE)) {
    echo 'Module installation is not complete or something bad is happened.';
    exit;
}
require_once $SM_CONF_FILE;
$smiles_on_page = 10;
$user_file = $data_path.'smiles/data/'.floor($is_regist / 1000).'/'.$is_regist.'.dat';

/* Add or delete smile to favorites */
$add = isset($_GET['add']) ? trim(strip_tags($_GET['add'])) : '';
$del = isset($_GET['del']) ? trim(strip_tags($_GET['del'])) : '';
if ($add || $del) {
    if ($add) {
        $do = 'add';
        $file = $add;
    } else {
        $do = 'del';
        $file = $del;
    }

    $file = basename($file);
    $file = str_replace(array('/', '\\'), '', $file); // После basename оно не нужно, но пусть будет, от греха подальше

    // Create file for user if it does not exists and fill with default smiles
    if (file_exists($upload_dir.$file)) {
        if (!file_exists($user_file)) {
            if (!file_exists(dirname(dirname($user_file)))) {
                @mkdir(dirname(dirname($user_file)));
            }
            if (!file_exists(dirname($user_file))) {
                @mkdir(dirname($user_file));
            }

            $f = fopen($user_file, 'w');
            if (!$f) {
                echo 'Error opening file '.$user_file;
                exit;
            }
            fwrite($f, file_get_contents($SM_DEFAULTS_FILE));
            fclose($f);
        }

        $new_fav = array();
        $fav = file_exists($user_file) && filesize($user_file) > 5 ? explode(
            "\n",
            trim(file_get_contents($user_file))
        ) : array();
        $cnt = count($fav);
        if ($cnt) {
            if ($do == 'add' && $sm_config['max_total'] && $cnt >= $sm_config['max_total']) {
                echo 'MAX_EXCEEDED';
                exit;
            }

            foreach ($fav as $f) {
                $f = trim($f);
                if (file_exists($upload_dir.$f) && $f != $file) {
                    $new_fav[] = $f;
                }
            }
        }

        if ($do == 'add') {
            $new_fav[] = $file;
        }

        $f = fopen($user_file, 'w');
        if (!$f) {
            echo 'Error opening file '.$user_file;
            exit;
        }
        fwrite($f, join("\n", $new_fav)."\n");
        fclose($f);
        echo 'OK';
    }

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
            if (!$CID) {
                $CID = $id;
            }
        }
    }
}

/* Read fav smiles list */
if (file_exists($user_file)) {
    $favorites = file($user_file);
} else {
    $favorites = file_exists($SM_DEFAULTS_FILE) ? file($SM_DEFAULTS_FILE) : array();
}
if (count($favorites)) {
    foreach ($favorites as $k => $v) {
        $favorites[$k] = trim($v);
    }
}

/* Read smiles list for current category */
$smiles = array();
$lines = file_exists($SM_SMILES_FILE) ? file($SM_SMILES_FILE) : array();
if (count($lines)) {
    foreach ($lines as $line) {
        $parts = explode("\t", $line);
        if ($CID == 'FAV') {
            if (in_array($parts[2], $favorites)) {
                $smiles[] = $parts;
            }
        } else {
            if ($parts[0] == $CID) {
                $smiles[] = $parts;
            }
        }
    }
}

/* Prepare for paged output */
$cnt = count($smiles);
$pages = 0;
if ($cnt > $smiles_on_page) {
    $pages = ceil($cnt / $smiles_on_page);
}

if ($DISPLAY == 'frame') {
    if ($sm_config['max_to_frame']) {
        $smiles = array_slice($smiles, 0, $sm_config['max_to_frame']);
    }
} else {
    $smiles = array_slice($smiles, $smiles_on_page * $PAGE, $smiles_on_page);
}

include($file_path."designes/".$design."/pictures.php");
?>
