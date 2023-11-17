<?php
if (!defined("_COMMON_")) {
    echo "stop";
    exit;
}
include($engine_path."users_get_list.php");
include($file_path."designes/".$design."/common_title.php");
include($file_path."designes/".$design."/common_body_start.php");

?>
    <style type="text/css">
        .smCat {
            display: block;
            text-decoration: none;
            padding: 2px 3px 3px 3px;
            color: red;
            margin-right: 10px;
        }

        .smCat:hover, .active {
            background: #55c500;
            color: #fff !important;
        }

        .favCat {
            color: red !important;
        }

        .fav {
            color: green;
            text-align: center;
        }

        .fav a {
            color: green;
        }

        .nofav {
            color: red;
            text-align: center;
        }

        .nofav a {
            color: red;
        }

        img {
            border: 0 !important;
        }

        th {
            text-align: left;
            font-size: 12px;
        }

        #smTbl {
        }

        #smTbl td {
            border-bottom: 1px solid #000;
        }

        #smTbl th {
            border-bottom: 1px solid #000;
        }
    </style>

<?
if ($DISPLAY == 'frame'): ?>
    <script type="text/javascript">
        function addSmile(code) {
            if (window.top != null && window.top.frames['voc_sender'] != null) {
                window.top.frames['voc_sender'].addPic(code);
                return false;
            } else {
                return true;
            }
        }
    </script>

    <div style="text-align:center;">
        <?
        if (count($smiles)): ?>
            <?
            foreach ($smiles as $k => $smile): ?>
                <a href="javascript:addPic('<?= str_replace('\'', '\\\'', $smile[1]); ?>');" target="voc_sender"
                        onclick="return addSmile('<?= str_replace('\'', '\\\'', $smile[1]); ?>');"><img
                            src="<?= $sm_config['sm_url']; ?>converts/<?= $smile[2]; ?>" alt="<?= $smile[3]; ?>"
                            title="<?= $smile[3]; ?>"/></a>
            <?
            endforeach; ?>
        <?
        endif; ?>
    </div>
    <?
    exit; ?>
<?
endif; ?>

    <script type="text/javascript">
        function ReloadSmileBar() {
            if (window.opener != null) {
                if (window.opener.top.frames['voc_smileys'] != null) window.opener.top.frames['voc_smileys'].location.reload();
            }
        }

        function addSmile(code) {
            if (window.opener != null && window.opener.top != null && window.opener.top.frames['voc_sender'] != null) {
                window.opener.top.frames['voc_sender'].addPic(code);
                return false;
            } else {
                return true;
            }
        }

        function add_to_fav(id, f) {
            if (window.XMLHttpRequest) {
                req = new XMLHttpRequest();
            } else if (typeof ActiveXObject != undefined) {
                req = new ActiveXObject("Microsoft.XMLHTTP");
            }
            if (req) {
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        if (req.responseText == 'OK') {
                            document.getElementById('sm' + id).className = 'fav';
                            document.getElementById('sm' + id).innerHTML = '<span><?=$w_favor_yes;?></span><br /><a href="#" onclick="remove_from_fav(' + id + ', \'' + f + '\'); return false;"><?=$w_favor_rem;?></a>';
                            ReloadSmileBar();
                        } else if (req.responseText == 'MAX_EXCEEDED') {
                            alert('<?=$w_max_smiles_is;?><?=intval($sm_config['max_total']);?>');
                        } else {
                            alert(req.responseText);
                        }
                    }
                }
                url = '<?=$chat_url;?>pictures.php?session=<?=$session;?>&add=' + f;
                req.open('POST', url, true);
                req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=windows-1251');
                req.send('');
            } else {
                alert('Your browser is not supported!');
                return false;
            }
        }

        function remove_from_fav(id, f) {
            if (window.XMLHttpRequest) {
                req = new XMLHttpRequest();
            } else if (typeof ActiveXObject != undefined) {
                req = new ActiveXObject("Microsoft.XMLHTTP");
            }
            if (req) {
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        if (req.responseText == 'OK') {
                            document.getElementById('sm' + id).className = 'nofav';
                            document.getElementById('sm' + id).innerHTML = '<span><?=$w_favor_no;?></span><br /><a href="#" onclick="add_to_fav(' + id + ', \'' + f + '\'); return false;"><?=$w_favor_add;?></a>';
                            ReloadSmileBar();
                        } else {
                            alert(req.responseText);
                        }
                    }
                }
                url = '<?=$chat_url;?>pictures.php?session=<?=$session;?>&del=' + f;
                req.open('POST', url, true);
                req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=windows-1251');
                req.send('');
            } else {
                alert('Your browser is not supported!');
                return false;
            }
        }
    </script>

    <div style="padding:10px;">
        <table border="0" width="100%">
            <tr>
                <td valign="top" width="180">
                    <?
                    foreach ($categories as $k => $v): ?>
                        <a href="<?= $chat_url; ?>pictures.php?session=<?= $session; ?>&cid=<?= $k; ?>" class="smCat<?
                        if ($k == $CID): ?> active<?
                        endif; ?>"><?= $v; ?></a>
                    <?
                    endforeach; ?>
                    <a href="<?= $chat_url; ?>pictures.php?session=<?= $session; ?>&cid=FAV" class="favCat smCat<?
                    if ('FAV' == $CID): ?> active<?
                    endif; ?>"><?= $w_fav_smiles; ?></a>
                </td>

                <td valign="top">
                    <?
                    if (count($smiles)): ?>
                        <?
                        if ($pages): ?>
                            <?
                            for ($i = 0; $i < $pages; $i++): ?>
                                <a href="<?= $chat_url; ?>pictures.php?session=<?= $session; ?>&cid=<?= $CID; ?>&page=<?= $i; ?>"<?
                                if ($i == $PAGE): ?> style="font-weight:bold;"<?
                                endif; ?>>[<?= $i + 1; ?>]</a>
                            <?
                            endfor; ?>
                        <?
                        endif; ?>
                        <table border="0" cellspacing="1" cellpadding="4" id="smTbl">
                            <tr>
                                <th><?= $w_symbols; ?></th>
                                <th><?= $w_picture; ?></th>
                                <th><?= $w_favor_smile; ?></th>
                            </tr>
                            <?
                            foreach ($smiles as $k => $smile): ?>
                                <tr>
                                    <td><?= $smile[1]; ?></td>
                                    <td align="center"><a href="javascript:addPic('<?= str_replace(
                                            '\'',
                                            '\\\'',
                                            $smile[1]
                                        ); ?>');" target="voc_sender" onclick="return addSmile('<?= str_replace(
                                            '\'',
                                            '\\\'',
                                            $smile[1]
                                        ); ?>');"><img src="<?= $sm_config['sm_url']; ?>converts/<?= $smile[2]; ?>"
                                                    alt="<?= $smile[3]; ?>" title="<?= $smile[3]; ?>"/></a></td>
                                    <td class="<?= (in_array($smile[2], $favorites)) ? 'fav' : 'nofav'; ?>"
                                            id="sm<?= $k; ?>">
                                        <?
                                        if (in_array($smile[2], $favorites)): ?>
                                            <span><?= $w_favor_yes; ?></span><br/>
                                            <a href="#" onclick="remove_from_fav(<?= $k; ?>, '<?= str_replace(
                                                '\'',
                                                '\\\'',
                                                $smile[2]
                                            ); ?>'); return false;"><?= $w_favor_rem; ?></a>
                                        <?
                                        else: ?>
                                            <span><?= $w_favor_no; ?></span><br/>
                                            <a href="#" onclick="add_to_fav(<?= $k; ?>, '<?= str_replace(
                                                '\'',
                                                '\\\'',
                                                $smile[2]
                                            ); ?>'); return false;"><?= $w_favor_add; ?></a>
                                        <?
                                        endif; ?>
                                    </td>
                                </tr>
                            <?
                            endforeach; ?>
                        </table>
                        <?
                        if ($pages): ?>
                            <?
                            for ($i = 0; $i < $pages; $i++): ?>
                                <a href="<?= $chat_url; ?>pictures.php?session=<?= $session; ?>&cid=<?= $CID; ?>&page=<?= $i; ?>"<?
                                if ($i == $PAGE): ?> style="font-weight:bold;"<?
                                endif; ?>>[<?= $i + 1; ?>]</a>
                            <?
                            endfor; ?>
                        <?
                        endif; ?>
                    <?
                    else: ?>
                        <div style="color:red; font-weight:bold;"><?= $w_no_smiles_in_category; ?></div>
                    <?
                    endif; ?>
                </td>
            </tr>
        </table>
    </div>

<?php
include($file_path."designes/".$design."/common_body_end.php"); ?>