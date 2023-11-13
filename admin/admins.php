<?php

include("check_session.php");
$admins = unserialize(file_get_contents($admin_path.'admins.dat'));
/* edit password */
if (isset($_POST['id'])) {
    if ($admins[$del]['permission'] || $_SESSION['permission'] != 1) {
        header('Location: admins.php');
        exit;
    }
    $admins[$id]['password'] = do_hash(trim($new_password), $md5_salt);
    save_file($admin_path.'admins.dat', serialize($admins));
    header('Location: admins.php');
    exit;
}
/* delete admin */
if (isset($del)) {
    if ($admins[$del]['permission'] || $_SESSION['permission'] != 1) {
        header('Location: admins.php');
        exit;
    }
    unset($admins[$del]);
    save_file($admin_path.'admins.dat', serialize($admins));
    header('Location: admins.php');
    exit;
}
/* create new admin */
if ($create && $new_login && $new_password) {
    if (!$_SESSION['permission']) {
        header('Location: admins.php');
        exit;
    }
    $key = trim($new_login);
    $admins[$key]['login'] = $key;
    $admins[$key]['password'] = do_hash(trim($new_password), $md5_salt);
    $admins[$key]['last_online'] = 0;

    save_file($admin_path.'admins.dat', serialize($admins));

    header('Location: admins.php?edit='.$key);
    exit;
}
?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <title>Admins</title>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php
        echo $charset; ?>"/>
        <link rel="stylesheet" href="<?php
        echo $css_path; ?>common.css">
        <?php
        if ($new): ?>
            <link rel="stylesheet" href="<?php
            echo $css_path; ?>login.css"><?php
        endif; ?>
        <link rel="stylesheet" href="<?php
        echo $css_path; ?>admins.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    </head>
    <body>
    <?php
    /* permissions */
    if (isset($edit)) {
    if (!$_SESSION['permission']) {
        header('Location: admins.php');
        exit;
    }
    $menus = unserialize(file_get_contents($admin_path.'menu.dat'));
    $categories = unserialize(file_get_contents($admin_path.'category.dat'));
    if ($save_permission) {
        $admins[$edit]['data_permissions'] = array();
        foreach ($menus as $menu) {
            foreach ($menu as $key => $value) {
                $key = explode('.php', $key);
                $key = $key[0];
                if (isset($_POST[$key])) {
                    $admins[$edit]['data_permissions'][$key] = $key;
                }
            }
        }

        save_file($admin_path.'admins.dat', serialize($admins));
        header('Location: admins.php');
    }
    ?>
    <h2>Права доступа</h2>
    <?php
    $parse_menu = array();

    foreach ($menus as $category_id => $menu) {
        foreach ($menu as $key => $value) {
            $key = explode('.php', $key);
            $key = $key[0];
            if (!isset($parse_menu[$key])) {
                $parse_menu[$category_id][$key] = $value['name'];
            } else {
                $parse_menu[$category_id][$key] .= '<br/>'.$value['name'];
            }
        }
    }
    ?>
    <table class="permissions">
        <form method="post" action="admins.php?edit=<?php
        echo $edit; ?>">
            <input type="hidden" name="save_permission" value='1'/>
            <?php
            foreach ($categories as $category_id => $category_title): ?>
                <tr style="background: #7bab3d;color: #fff;text-align: center;">
                    <td colspan="2"><?php
                        echo $category_title; ?></td>
                </tr>
                <?php
                foreach ($parse_menu[$category_id] as $k => $v): ?>
                    <tr>
                        <td><?php
                            echo $v; ?></td>
                        <td><input type="checkbox" name="<?php
                            echo $k; ?>" <?php
                            echo (in_array($k, $admins[$edit]['data_permissions'])) ? 'checked="checked"' : ''; ?> />
                        </td>
                    </tr>
                <?php
                endforeach; ?>
            <?php
            endforeach; ?>
    </table>
    <p>
        <a href="#" class="select_all">Выделить все</a> | <a href="#" class="unselect_all">Снять все</a>
    </p>
    <p>
        <button class="btn">Сохранить</button>
    </p>
    <p class="center">
        <a href="admins.php">Отмена</a>
    <p>
        </form>
        <script>
            $(document).ready(function () {
                $(document).on("click", ".select_all", function () {
                    $(".permissions input[type='checkbox']").prop("checked", true);
                    return false;
                });
                $(document).on("click", ".unselect_all", function () {
                    $(".permissions input[type='checkbox']").prop("checked", false);
                    return false;
                });
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}
?>
<?php
if ($new): ?>
    <h2>Создать нового админа</h2>
    <div class="wrap">
        <div class="main">
            <form method="post" action="admins.php?new=1">
                <input type="hidden" name="create" value="login">
                <p>
                    <label for="new_login"><?php
                        echo $adm_login; ?><br/>
                        <input type="text" name="new_login" value="" id="new_login" class="input">
                    </label>
                </p>

                <p>
                    <label for="new_password"><?php
                        echo $adm_password; ?><br/>
                        <input type="password" name="new_password" value="" id="new_password" class="input">
                    </label>
                </p>

                <p>
                    <button class="btn">Создать</button>
                </p>
                <p class="center">
                    <a href="admins.php">Отмена</a>
                <p>
            </form>
        </div>
    </div>
    </body>
    </html>
<?php
else: ?>
    <h2>Администраторы</h2>
    <p class="text">Настройка прав доступа в админ панель. Создание/удаление администраторов и настройка их прав к различным настройкам и модерированию в админке</p>
    <table>
        <tr>
            <th width="200">Логин</th>
            <th width="200">Пароль</th>
            <th width="200">Последняя активность</th>
            <th width="100">Доступ</th>
            <th width="100">Удалить</th>
        </tr>
        <?php
        foreach ($admins as $key => $admin): ?>
            <form method="post" action="admins.php">
                <input type="hidden" name="id" value="<?php
                echo $key; ?>"/>
                <tr>
                    <td>
                        <?php
                        if ($admin['permission']): ?><b><?php
                            endif; ?>
                            <?php
                            echo $admin['login']; ?>
                            <?php
                            if ($admin['permission']): ?></b><?php
                    endif; ?>
                    </td>
                    <td align="center">
                        <a href="#" class="change_password">Изменить</a>
                        <div class="hide">
                            <input type="password" name="new_password" class="mini"/>
                            <button class="btn mini">Ok</button>
                        </div>
                    </td>
                    <td align="center">
                        <?php
                        echo $admin['last_online'] == 0 ? '' : date('d.m.Y в H:i', $admin['last_online']); ?>
                    </td>
                    <td align="center">
                        <?php
                        if ($admin['permission']): ?>
                            <b>Полный</b>
                        <?php
                        else: ?>
                            <a href="admins.php?edit=<?php
                            echo $key; ?>">Изменить</a>
                        <?php
                        endif; ?>
                    </td>
                    <td align="center">
                        <?php
                        if (!$admin['permission']): ?>
                            <a href="admins.php?del=<?php
                            echo $key; ?>">Удалить</a>
                        <?php
                        endif; ?>
                    </td>
                </tr>
            </form>
        <?php
        endforeach; ?>
    </table>
    <p class="buttons">
        <a href="admins.php?new=1" class="btn">Создать нового админа</a>
    </p>
    <script>
        $(document).ready(function () {
            $(".change_password").on("click", function () {
                $(this).hide();
                $(this).next().show();
                $(this).next().find("input[name='password']").trigger("focus");
                return false;
            });
        });
    </script>
    </body>
    </html>
<?php
endif; ?>