<?php
/*

  Официальный сайт http://mvoc.ru
  Автор: Skriptoff
  skype: Skriptoff
  icq: 3503584

 */

include("check_session.php");
$menu = array();
$category = array();

// Create menu file
if (!file_exists($admin_path.'menu.dat')) {
    if (file_exists($file_path."admin/quiz.php")) {
        $menu[0]['quiz.php']['name'] = 'Викторина';
    }
    if (file_exists($file_path."admin/attachments.php")) {
        $menu[0]['attachments.php']['name'] = 'Вложения';
    }
    if (file_exists($file_path."admin/antimat.php")) {
        $menu[0]['antimat.php']['name'] = 'Антимат';
    }
    if (file_exists($file_path."admin/antilink.php")) {
        $menu[0]['antilink.php']['name'] = 'Антиреклама';
    }

    $menu[0]['mod_list.php']['name'] = $adm_moder_list;
    $menu[0]['mod_list.php']['files'][] = 'moderators.php';
    $menu[0]['mod_list.php']['files'][] = 'user_delete.php';

    $menu[0]['progress_frameset.php?operation=shaman']['name'] = $adm_shamans_list;
    $menu[0]['progress_frameset.php?operation=shaman']['files'][] = 'generate_shamans_list.php';
    $menu[0]['progress_frameset.php?operation=shaman']['files'][] = 'shamans_list.php';

    $menu[0]['clan_list.php']['name'] = $adm_clans_list;
    $menu[0]['rooms.php']['name'] = $adm_rooms_admin;
    $menu[0]['progress_frameset.php?operation=canon']['name'] = $adm_canon_nicks;
    $menu[0]['progress_frameset.php?operation=canon']['files'][] = 'generate_canon_nicks.php';

    $menu[1]['shop.php']['name'] = $adm_shop_manager_itmes;
    $menu[1]['shop_cats.php']['name'] = $adm_shop_manager_cats;
    $menu[1]['transaction_log.php']['name'] = $adm_shop_manager_log;

    if (file_exists($file_path."admin/items_backup.php")) {
        $menu[1]['items_backup.php']['name'] = 'Бекап магазина';
    }

    $menu[2]['conv.php']['name'] = $adm_sm_convert;

    if (file_exists($file_path."admin/logs/index.php")) {
        $menu[2]['logs/index.php']['name'] = 'Логи';
    }

    if (is_file($data_path."engine/files/guardian.php")) {
        $menu[2]['progress_frameset.php?operation=guardian']['name'] = 'VOC++ Guardian';
        $menu[2]['progress_frameset.php?operation=guardian']['files'][] = 'calibrate_guardian.php';
        $menu[2]['progress_frameset.php?operation=guardian']['files'][] = 'generate_similar_indexes.php';
    }

    $menu[2]['progress_frameset.php?operation=index']['name'] = $adm_reconstruct_idx;
    $menu[2]['progress_frameset.php?operation=index']['files'][] = 'generate_indexes.php';

    $menu[2]['progress_frameset.php?operation=index_similar']['name'] = $adm_gen_similar_table;
    $menu[2]['progress_frameset.php?operation=index_register']['name'] = $adm_register_all;
    $menu[2]['progress_frameset.php?operation=index_register']['files'][] = 'register_users.php';

    $menu[3]['admin_conf.php?step=2']['name'] = $adm_engines;
    $menu[3]['admin_conf.php?step=3']['name'] = $adm_options_and_lim;
    $menu[3]['admin_conf.php?step=4']['name'] = $adm_user_access_lim;
    $menu[3]['admin_conf.php?step=5']['name'] = $adm_add_features;
    $menu[3]['admin_conf.php?step=6']['name'] = $adm_look_and_feel;

    save_file($admin_path.'menu.dat', serialize($menu));
}

if (!file_exists($admin_path.'category.dat')) {
    $category[0] = $adm_admin_tools;
    $category[1] = $adm_shop_manager;
    $category[2] = $adm_other_tools;
    $category[3] = $adm_configuration;

    save_file($admin_path.'category.dat', serialize($category));
}

// Get menu
$menu = unserialize(file_get_contents($admin_path.'menu.dat'));
$category = unserialize(file_get_contents($admin_path.'category.dat'));
$hide_categories = isset($_COOKIE['_hidecats']) && strlen($_COOKIE['_hidecats']) > 0 ? explode(
    '|',
    $_COOKIE['_hidecats']
) : [];
ksort($menu);

$last_key = -1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>New Admin Panel VOC++</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php
    echo $charset; ?>"/>
    <link rel="stylesheet" href="<?php
    echo $css_path; ?>common.css">
    <link rel="stylesheet" href="<?php
    echo $css_path; ?>index.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
</head>
<body>
<div id="header">
    <a href="index.php" class="logo">Admin Panel</a>
    <a href="exit.php" class="button">Выход</a>
    <?php
    if ($_SESSION['permission']): ?>
        <a href="settings.php" class="button to_frame">Настройки</a>
        <a href="admins.php" class="button to_frame">Администраторы</a>
        <a href="info.php" class="button to_frame">INFO</a>
    <?php
    endif; ?>
    <div class="search-wrap">
        <input type="text" size="20" id="tstInfoUser" class="input"/>
        <button class="search-btn"></button>
    </div>
</div>
<table width="100%" height="100%">
    <tr>
        <td width="230px;" valign="top">
            <ul class="left-menu">
                <ul class="category">
                    <li>
                        <a href="home.php" target="_blank" class="new_win" title="В новом окне"></a>
                        <a href="home.php" class="menu" target="admin_main">Главная</a>
                    </li>
                </ul>
                <?php
                foreach ($category as $category_id => $category_title) {
                    $is_category = false;
                    $markUp = '';
                    foreach ($menu[$category_id] as $k => $v) {
                        $file = explode('.php', $k);
                        $file = $file[0];
                        if ($_SESSION['permission'] || (in_array($file, $general_included_files) || (in_array(
                                    $file,
                                    $_SESSION['data_permissions']
                                )))) {
                            $is_category = true;
                            $markUp .= '<li><a href="'.$k.'" target="_blank" class="new_win" title="В новом окне"></a><a href="'.$k.'" class="menu" target="admin_main">'.$v['name'].'</a></li>';
                        }
                    }
                    if ($is_category) {
                        echo '<li class="title-category">'.$category_title.'</li>';
                        $last_key = $category_id;
                    }

                    echo '<ul class="category" data-category="'.$category_id.'" '.(in_array(
                            $category_id,
                            $hide_categories
                        ) ? 'style="display:none;"' : '').'>';
                    echo $markUp;
                    echo '</ul>';
                }
                if (is_dir($file_path.'plugins')) {
                    if ($dh = opendir($file_path.'plugins')) {
                        echo '<li class="title-category">Plugins</li>';
                        echo '<ul class="category" data-category="'. 100 .'" '.(in_array(
                                100,
                                $hide_categories
                            ) ? 'style="display:none;"' : '').'>';
                        while (($file = readdir($dh)) !== false) {
                            if ($file != '.' && $file != '..') {
                                if (is_dir($file_path.'plugins/'.$file)) {
                                    if (is_file($file_path.'plugins/'.$file.'/config.php')) {
                                        include($file_path.'plugins/'.$file.'/config.php');

                                        echo '<li><a href="plugin_info.php?plugin='.$file.'" target="_blank" class="new_win" title="В новом окне"></a><a href="plugin_info.php?plugin='.$file.'" class="menu" target="admin_main">'.$VOCPlugin_Name.'</a></li>';
                                    }
                                }
                            }
                        }
                        echo '</ul>';
                        closedir($dh);
                    }
                }
                ?>
            </ul>
        </td>
        <td>
            <iframe src="home.php" id="home" width="100%" height="100%" align="left"></iframe>
        </td>
    </tr>
</table>
<script>
    $(document).ready(function () {
        if (location.hash) {
            urlHash = location.hash.split('#')[1];
            $("#home").attr("src", decodeURIComponent(urlHash));
        }

        $(".left-menu li a").on("click", function () {
            if ($(this).hasClass('menu')) {
                var url = $(this).attr("href");
                $("#home").attr("src", url);
                $(".left-menu li .active").removeClass("active");
                $(this).addClass("active");
                location.hash = encodeURIComponent(url);
                return false;
            }
        });

        $(".to_frame").on("click", function () {
            var url = $(this).attr("href");
            $("#home").attr("src", url);
            location.hash = encodeURIComponent(url);

            return false;
        });

        $(document).on("click", ".title-category", function () {
            $(this).next().toggle('blind', function () {
                var list = [];
                $('.category:hidden').each(function () {
                    if ($(this).data('category') != undefined) {
                        list.push($(this).data('category'));
                    }
                });
                createCookie('_hidecats', list.join('|'))
            });
        });

        $(document).on("click", ".search-btn", function () {
            searchUsers();
            $("#tstInfoUser").val("");
        });
        $("#tstInfoUser").on("keyup", searchUsers);
    });

    function searchUsers(clear) {
        var search = $("#tstInfoUser").val();
        if (!search) {
            return false;
        }
        $("#home").attr("src", 'search.php?tstInfoUser=' + search);
    }

    function createCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    }
</script>
</body>
</html>