<?php

include("check_session.php");

/* Null */
if (isset($null)) {
    unlink($admin_path.'menu.dat');
    unlink($admin_path.'category.dat');

    echo 'Обновлено! Обновите страницу';
    exit;
}

/* ips */
$s_saved = false;
if (isset($is_ips)) {
    $ips = explode("\n", $ips);
    $s_ips = array();
    $is_my_ip = false;
    $cantainer = 'security';

    foreach ($ips as $ip) {
        $ip = trim($ip);
        if ($ip) {
            $s_ips[] = $ip;
            if ($ip == $_SERVER['REMOTE_ADDR']) {
                $is_my_ip = true;
            }
        }
    }

    if (count($s_ips) && !$is_my_ip) {
        $error = 'Данные не сохранены! Среди перечисленных ip нету Вашего '.$_SERVER['REMOTE_ADDR'];
    } else {
        if (count($s_ips)) {
            save_file($admin_path.'ips.dat', serialize($s_ips));
            $s_saved = true;
        }
    }
}

/* Get menu */
$menu = unserialize(file_get_contents($admin_path.'menu.dat'));
$category = unserialize(file_get_contents($admin_path.'category.dat'));
$deny_ips = unserialize(file_get_contents($admin_path.'ips.dat'));
$security_key = file_get_contents($admin_path.'security.dat');

set_variable('do');
switch ($do) {
    case 'sortcat':
        set_variable('sorting');
        foreach ($sorting as $value) {
            $new_category[$value] = $category[$value];
        }
        $category = $new_category;

        save_file($admin_path.'category.dat', serialize($category));
        exit();
    case 'sortmenu':
        set_variable('sorting');
        set_variable('cat_id');
        foreach ($sorting as $value) {
            $new_menu[$value] = $menu[$cat_id][$value];
        }
        $menu[$cat_id] = $new_menu;
        save_file($admin_path.'menu.dat', serialize($menu));
        exit();
}

/* Security */
if (isset($is_security)) {
    if (preg_match('/^[a-z]{0,20}$/', $security)) {
        $_SESSION['security'] = $security;
        save_file($admin_path.'security.dat', $security);
        $s_saved = true;
    } else {
        $error = 'Не верный формат ключа, используйте только латинские буквы в нижнем регистре';
    }
    $cantainer = 'security';
}

/* Logs */
if (file_exists($admin_path.'logs.dat')) {
    $logs = file_get_contents($admin_path.'logs.dat');
    $logs = str_replace("\n", '<br/>', $logs);
    $logs = str_replace('login', '<b>login</b>', $logs);
    $logs = str_replace('error', '<b style="color:red">error</b>', $logs);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Settings Admin Panel VOC++</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php
    echo $charset; ?>"/>
    <link rel="stylesheet" href="<?php
    echo $css_path; ?>common.css">
    <link rel="stylesheet" href="<?php
    echo $css_path; ?>settings.css?3">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
            integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="<?php
    echo $js_path; ?>common.js"></script>
</head>
<body>
<?php
/* Edit menu / category */
if (isset($edit) && isset($type)) {
    $edited = 0;
    switch ($type) {
        case 'menu' :
            if (isset($old_key)) {
                $replace_array = array();
                $files_array = array();
                if ($files) {
                    foreach (explode("\n", $files) as $k => $f) {
                        if (strlen($f) > 1) {
                            $files_array[] = $f;
                        }
                    }
                }

                foreach ($menu as $key => $value) {
                    if (isset($menu[$key][$old_key])) {
                        if ($old_key == $file) {
                            $menu[$key][$old_key] = array('name' => $name, 'files' => $files_array);
                        } else {
                            $replace_array[$file] = array('name' => $name, 'files' => $files_array);

                            unset($menu[$key][$old_key]);
                            $menu[$key][$file] = array('name' => $name, 'files' => $files_array);
                        }
                        if ($key != $cat) {
                            $menu[$cat][$file] = $menu[$key][$file];
                            unset($menu[$key][$file]);
                        }
                    }
                }
                $edited = 1;
                save_file($admin_path.'menu.dat', serialize($menu));
            }
            break;
        case 'category':
            if (isset($key) && isset($category[$key])) {
                $category[$key] = $name;
                save_file($admin_path.'category.dat', serialize($category));
                $edited = 1;
            }
            break;
    }
    header("Location: settings.php?edited=".$edited);
    exit;
}
/* Add menu / category */
if (isset($add) && isset($type)) {
    $edited = 0;
    switch ($type) {
        case 'menu' :
            if (isset($cat) && isset($name) && isset($file)) {
                $files_array = array();
                if ($files) {
                    foreach (explode("\n", $files) as $k => $f) {
                        if (strlen($f) > 1) {
                            $files_array[] = $f;
                        }
                    }
                }
                $menu[$cat][$file] = array('name' => $name, 'files' => $files_array);
                $edited = 1;
                save_file($admin_path.'menu.dat', serialize($menu));
            }
            break;
        case 'category' :
            if (isset($cname)) {
                $category[] = $cname;
                save_file($admin_path.'category.dat', serialize($category));
                $edited = 1;
            }
            break;
    }
    header("Location: settings.php?edited=".$edited);
    exit;
}

/* Delete menu */
if (isset($del)) {
    $edited = 0;
    foreach ($menu as $key => $value) {
        if (isset($menu[$key][$del])) {
            unset($menu[$key][$del]);
            $edited = 1;
        }
    }
    save_file($admin_path.'menu.dat', serialize($menu));
    header("Location: settings.php?edited=".$edited);
    exit;
}
/* Delete category */
if (isset($cdel)) {
    $edited = 0;
    foreach ($menu as $key => $value) {
        if (isset($category[$cdel])) {
            unset($category[$cdel]);
            unset($menu[$cdel]);
            $edited = 1;
        }
    }
    save_file($admin_path.'category.dat', serialize($category));
    save_file($admin_path.'menu.dat', serialize($menu));
    header("Location: settings.php?edited=".$edited);
    exit;
}
?>
<div class="wrap">
    <ul class="menu">
        <li <?php
        if (!isset($cantainer) || $cantainer == 'menu') {
            echo 'class="active"';
        } ?> data-type="menu">Категории / Меню
        </li>
        <li <?php
        if (isset($cantainer) && $cantainer == 'security') {
            echo 'class="active"';
        } ?> data-type="security">Безопасность
        </li>
        <li <?php
        if (isset($cantainer) && $cantainer == 'logs') {
            echo 'class="active"';
        } ?> data-type="logs">Логи
        </li>
    </ul>
    <div class="clear"></div>
    <div class="main">
        <div id="menu" class="content" <?php
        if (isset($cantainer) && $cantainer != 'menu') {
            echo 'style="display:none"';
        } ?>>
            <h1>Менеджер меню / категорий</h1>
            <div class="add add-menu">Добавить меню</div>
            <div class="add add-category">Добавить категорию</div>
            <div class="clear"></div>
            <?php
            if (isset($edited) && $edited == 1): ?>
                <p class="messages">Изменения сохранены! Чтобы изменения вступили в силу обновите страницу</p>
            <?php
            endif; ?>
            <ul class="category" id="sortable">
                <?php
                $last_key = -1;
                foreach ($category as $category_id => $category_title) {
                    if (!empty($menu[$category_id])) {
                        echo '<li data-key="'.$category_id.'" data-id="'.$category_id.'">';
                        echo '<div class="title-category">';
                        echo '<div class="mini-btn fas fa-bars"></div>';
                        echo '<div class="mini-btn far fa-edit edit-category" title="Редактировать"></div>';
                        echo '<div class="mini-btn far fa-trash-alt delete-category" title="Удалить"></div>';
                        echo $category_title;
                        echo '</div>';
                        echo '<ul class="category sortable-sub" data-id="'.$category_id.'">';
                        foreach ($menu[$category_id] as $k => $v) {
                            $file = explode('.php', $k);
                            $file = $file[0];
                            echo '<li data-file="'.$k.'" >';
                            echo '<div class="mini-btn fas fa-bars"></div>';
                            echo '<div class="mini-btn far fa-edit edit-menu" title="Редактировать"></div>';
                            echo '<div class="mini-btn far fa-trash-alt delete-menu" title="Удалить"></div>';
                            echo '<div class="file">'.$k.'</div>';
                            echo '<span>'.$v['name'].'</span>';
                            echo '<small>';
                            foreach ($v['files'] as $file) {
                                echo $file."\n";
                            }
                            echo '</small>';
                            echo '</li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
            <p class="center">
                <a href="settings.php?null" class="null">Сбросить по умолчанию</a>
            </p>
        </div>
        <div id="security" class="content" <?php
        if (isset($cantainer) && $cantainer == 'security') {
            echo 'style="display:block"';
        } ?>>
            <h1>Безопасность</h1>
            <h2>Зашифровать url</h2>
            <?php
            if (isset($error)): ?>
                <p class="error"><?php
                    echo $error; ?></p>
            <?php
            endif; ?>
            <?php
            if ($s_saved): ?>
                <p class="messages">Изменения сохранены!</p>
            <?php
            endif; ?>
            <form action="settings.php?is_security" method="post">
                <p>
                    <label for="security">Страница админки будет доступна для авторизации только по адресу:<br/>
                        <u><?php
                            echo $admin_url; ?>index.php?</u><input type="text" name="security" id="security"
                                value="<?php
                                echo $_SESSION['security']; ?>">
                        <small>(Только маленькие английские буквы без пробелов!)</small>
                    </label>
                </p>
                <button class="btn">Сохранить</button>
            </form>
            <hr/>
            <h2>Защита по ip</h2>
            <form action="settings.php?is_ips" method="post">
                <p>
                    <label for="ips">Доступ в админку по ip адресам (каждый с новой строки):<br/>
                        <textarea name="ips" style="width:500px;height:100px;" id="ips"><?php
                            if ($deny_ips) {
                                foreach ($deny_ips as $ip) {
                                    echo $ip."\n";
                                }
                            }
                            ?></textarea>
                    </label>
                </p>
                <p>
                    <small>Ваш ip: <b><?php
                            echo $_SERVER['REMOTE_ADDR']; ?></b></small>
                </p>
                <button class="btn">Сохранить</button>
            </form>
        </div>
        <div id="logs" class="content" <?php
        if (isset($cantainer) && $cantainer == 'logs') {
            echo 'style="display:block"';
        } ?>>
            <h1>Логи</h1>
            <small>
                <?php
                echo $logs; ?>
            </small>
        </div>
    </div>
</div>
<div id="light"></div>
<div id="popup"></div>
<script>
    $(document).ready(function () {
        $(".menu li").on("click", function () {
            var type = $(this).data("type");
            $(".menu .active").removeClass("active");
            $(this).addClass("active");

            if ($("#" + type).css("display") != "block") {
                $(".main .content").hide("blink");
                $("#" + type).show("blink");
            }
        });

        /* Menu */
        $(document).on("click", ".category .edit-menu", function () {
            var parent = $(this).parent();
            var name = parent.find("span").text();
            var file = parent.find(".file").text();
            var files = parent.find("small").text();
            var cat_id = $(this).closest('ul').data('id');
            var markUp = "";

            markUp += '<div class="close" title="Закрыть"></div>';
            markUp += '<h1>Редактировать</h1>';
            markUp += '<form action="settings.php?edit=1" method="post">';
            markUp += '<input type="hidden" name="old_key" value="' + file + '" />';
            markUp += '<input type="hidden" name="type" value="menu" />';
            markUp += '<p>';
            markUp += '<label for="cat">Категория<br/>';
            markUp += '<select name="cat" id="menucat">';
            <?php foreach ($category as $key => $cat): ?>
            markUp += '<option value="<?php echo $key; ?>"><?php echo $cat; ?></option>';
            <?php endforeach; ?>
            markUp += '</select>'
            markUp += '</label>';
            markUp += '</p>';
            markUp += '<p>';
            markUp += '<label for="name">Название<br/>';
            markUp += '<input type="text" name="name" value="' + name + '" id="name" class="input">';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<p>';
            markUp += '<label for="file">Файл<br/>';
            markUp += '<input type="text" name="file" value="' + file + '" id="file" class="input">';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<p>';
            markUp += '<label for="files">Дополнительные файлы<br/>';
            markUp += '<textarea name="files" id="files">' + files + '</textarea>';
            markUp += '<small>- Файлы на которые происходит редиректы, если таких нет, оставте поле пустым.<br/>- Каждый файл с новой строки</small>';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<hr/>';

            markUp += '<p>';
            markUp += '<button class="btn">Сохранить</button>';
            markUp += '</p>';
            markUp += '</form>';
            popup(markUp);
            $('#menucat').val(cat_id);
        });

        $(document).on("click", ".category .delete-menu", function () {
            var parent = $(this).parent();
            var name = parent.find("span").text();
            var file = parent.find(".file").text();

            if (!confirm('Удалить меню "' + name + '"')) {
                return false;
            }

            location.href = "settings.php?del=" + file;
        });

        /* Category */
        $(document).on("click", ".category .edit-category", function () {
            var parent = $(this).parent();
            var name = parent.text();
            var key = parent.data("key");

            var markUp = "";

            markUp += '<div class="close" title="Закрыть"></div>';
            markUp += '<h1>Редактировать</h1>';
            markUp += '<form action="settings.php?edit=1" method="post">';
            markUp += '<input type="hidden" name="key" value="' + key + '" />';
            markUp += '<input type="hidden" name="type" value="category" />';
            markUp += '<p>';
            markUp += '<label for="cname">Название<br/>';
            markUp += '<input type="text" name="name" value="' + name + '" id="cname" class="input">';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<hr/>';

            markUp += '<p>';
            markUp += '<button class="btn">Сохранить</button>';
            markUp += '</p>';
            markUp += '</form>';
            popup(markUp);
            $("#cname").focus();
        });

        $(document).on("click", ".category .delete-category", function () {
            var parent = $(this).parent();
            var key = parent.data("key");
            if (!confirm('Удалить категорию "' + name + '"? Все подменю этой категории будут так же удалены')) {
                return false;
            }

            location.href = "settings.php?cdel=" + key;
        });

        $(document).on("click", ".add-menu", function () {
            var markUp = "";

            markUp += '<div class="close" title="Закрыть"></div>';
            markUp += '<h1>Добавить меню</h1>';
            markUp += '<form action="settings.php?add=1" method="post">';
            markUp += '<input type="hidden" name="type" value="menu" />';
            markUp += '<p>';
            markUp += '<label for="cat">Категория<br/>';
            markUp += '<select name="cat" id="cat">';
            <?php foreach ($category as $key => $cat): ?>
            markUp += '<option value="<?php echo $key; ?>"><?php echo $cat; ?></option>';
            <?php endforeach; ?>
            markUp += '</select>'
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<p>';
            markUp += '<label for="name">Название<br/>';
            markUp += '<input type="text" name="name" id="name" class="input">';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<p>';
            markUp += '<label for="file">Файл<br/>';
            markUp += '<input type="text" name="file" id="file" class="input">';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<p>';
            markUp += '<label for="files">Дополнительные файлы<br/>';
            markUp += '<textarea name="files" id="files"></textarea>';
            markUp += '<small>- Файлы на которые происходит редиректы, если таких нет, оставте поле пустым.<br/>- Каждый файл с новой строки</small>';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<hr/>';

            markUp += '<p>';
            markUp += '<button class="btn">Добавить</button>';
            markUp += '</p>';
            markUp += '</form>';

            popup(markUp);
            $("#name").focus();
        });

        $(document).on("click", ".add-category", function () {
            var markUp = "";

            markUp += '<div class="close" title="Закрыть"></div>';
            markUp += '<h1>Добавить категорию</h1>';
            markUp += '<form action="settings.php?add=1" method="post">';
            markUp += '<input type="hidden" name="type" value="category" />';
            markUp += '<p>';
            markUp += '<label for="cname">Название<br/>';
            markUp += '<input type="text" name="cname" id="cname" class="input">';
            markUp += '</label>';
            markUp += '</p>';

            markUp += '<hr/>';

            markUp += '<p>';
            markUp += '<button class="btn">Добавить</button>';
            markUp += '</p>';
            markUp += '</form>';
            popup(markUp);
            $("#cname").focus();
        });

        $(document).on("click", ".null", function () {
            if (!confirm('Вы уверены что хотите сбросить меню на стандартное?')) {
                return false;
            }

            location.href = $(this).attr("href");
        });

        $("#sortable").sortable({
            update: function (event, ui) {

                var list = [];
                $('#sortable > li').each(function () {
                    if ($(this).data('id') != undefined) {
                        list.push($(this).data('id'));
                    }
                });
                $.ajax({
                    url: '/admin/settings.php',
                    method: "POST",
                    dataType: "json",
                    data: {
                        do: 'sortcat',
                        sorting: list
                    }
                });
            }
        });
        $("#sortable").disableSelection();
        $(".sortable-sub").sortable({
            update: function (event, ui) {
                var list = [];
                var category_id = $(this).data('id');
                $(this).find('li').each(function () {
                    if ($(this).data('file') != undefined) {
                        list.push($(this).data('file'));
                    }
                });

                $.ajax({
                    url: '/admin/settings.php',
                    method: "POST",
                    dataType: "json",
                    data: {
                        do: 'sortmenu',
                        sorting: list,
                        cat_id: category_id
                    }
                });
            }
        });
        $(this).disableSelection();
    });
</script>
</body>
</html>