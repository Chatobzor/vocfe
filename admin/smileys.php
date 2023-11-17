<?php

if (!defined("_COMMON_")) {
    /* Direct Request */
    echo 'VOC++ Smile manager v 2.0<br>';
    echo 'Build 20091221<br>';
    echo 'Created by ChatMaster';
    exit();
}

require_once $data_path.'smiles/config.php';
$SmTbl = array();
$numOfImgPhrases = 0;
$lines = file($data_path.'smiles/smiles.dat');

if (count($lines)) {
    foreach($lines as $line) {
        $parts = explode("\t", trim($line));
        if ($sm_config['clickable']) {
            $code = '<a href="javascript:addPic(\''.$parts[1].'\');" target="voc_sender"><img src="'.$sm_config['sm_url'].'converts/'.$parts[2].'" alt="'.$parts[3].'" title="'.$parts[3].'" border="0"></a>';
        } else {
            $code = '<img src="'.$sm_config['sm_url'].'converts/'.$parts[2].'" alt="'.$parts[3].'" title="'.$parts[3].'" border="0">';
        }
        $SmTbl[] = array('name' => $parts[1], 'link' => $code);

        if (trim($parts[4])) {
            $aliases = explode(',', $parts[4]);
            if (count($aliases)) {
                foreach ($aliases as $alias) {
                    if ($sm_config['clickable']) {
                        $code = '<a href="javascript:addPic(\''.$alias.'\');" target="voc_sender"><img src="'.$sm_config['sm_url'].'converts/'.$parts[2].'" alt="'.$parts[3].'" title="'.$parts[3].'" border="0"></a>';
                    } else {
                        $code = '<img src="'.$sm_config['sm_url'].'converts/'.$parts[2].'" alt="'.$parts[3].'" title="'.$parts[3].'" border="0">';
                    }
                    $SmTbl[] = array('name' => $alias, 'link' => $code);
                }
            }
        }
    }
}
$numOfImgPhrases = count($SmTbl);