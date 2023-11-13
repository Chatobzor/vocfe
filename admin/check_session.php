<?php

@session_start();

if ($_GET) {
    foreach ($_GET as $key => $value) {
        $$key = $value;
    }
}
if ($_POST) {
    foreach ($_POST as $key => $value) {
        $$key = $value;
    }
}

include('../inc_common.php');

$version = '1.0';
$lang = 'ru';
$logs_limit = 500;
$admin_users = array();
$folder_admin_files = 'new_admin';

$css_path = $chat_url.'admin/'.$folder_admin_files.'/css/';
$js_path = $chat_url.'admin/'.$folder_admin_files.'/js/';

$admin_path = $data_path.'admin/';

$is_auth = false;
$admin_url = $chat_url.'admin/';

$general_included_files = array(
    'index',
    'home',
    'search',
    'info',
    'exit',
    'plugin_info',
    'plugin_configure',
    'plugin_view',
    'progress',
);

include_once($folder_admin_files.'/functions.php');

include_once($folder_admin_files.'/hash.php');

include_once(FILE_PATH.'admin/languages/admin-'.$lang.'.php');

/* create configs */
if (!@file_exists(DATA_PATH.'admin')) {
    @mkdir(DATA_PATH.'admin');
    @chmod(DATA_PATH.'admin', 0777);
}

/* Create first user */
if (!file_exists($admin_path.'admins.dat')) {
    if ($password2 && ($password == $password2) && $login) {
        $admin_users = array();
        $admin_users[0]['login'] = trim($login);
        $admin_users[0]['password'] = do_hash(trim($password), $md5_salt);
        $admin_users[0]['permission'] = true;
        $admin_users[0]['last_online'] = time();

        save_file($admin_path.'admins.dat', serialize($admin_users));

        $_SESSION['login'] = $login;
        $_SESSION['permission'] = true;
        header('Location: index.php');
        exit;
    } else {
        $status = false;
        if ($password != $password2) {
            $status = 'Пароли не совпадают';
        }
        include($folder_admin_files.'/new.php');
        exit;
    }
}

/* block by security */
if (file_exists($admin_path.'security.dat')) {
    $security_key = file_get_contents($admin_path.'security.dat');
    if ($security_key) {
        if (!isset($security_key) && !isset($_SESSION['security'])) {
            exit;
        }
        if (isset($_SESSION['security']) && $_SESSION['security'] != $security_key) {
            exit;
        }
        $_SESSION['security'] = $security_key;
    }
}

/* block by ip */
if (file_exists($admin_path.'ips.dat')) {
    $deny_ips = unserialize(file_get_contents($admin_path.'ips.dat'));
    if (count($deny_ips)) {
        $is_ip = false;
        foreach ($deny_ips as $ip) {
            if ($ip == $_SERVER['REMOTE_ADDR']) {
                $is_ip = true;
            }
        }
        if (!$is_ip) {
            exit;
        }
    }
}

/* Authorization */
if (!$_SESSION['login'] && !$login && !empty($_COOKIE['a_login']) && !empty($_COOKIE['a_hash'])) {
    $login = $_COOKIE['a_login'];
    $hash = $_COOKIE['a_hash'];
    $password = false;
}
if ($login && ($password || $hash)) {
    $admins = unserialize(file_get_contents($admin_path.'admins.dat'));
    if ($password) {
        $password = do_hash($password, $md5_salt);
    }

    foreach ($admins as $key => $admin) {
        if ($admin['login'] === $login && ($admin['password'] === $password || $admin['password'] === $hash)) {
            if ($admin['permission']) {
                $_SESSION['permission'] = true;
            } elseif ($admin['data_permissions']) {
                $_SESSION['data_permissions'] = $admin['data_permissions'];
            }
            $_SESSION['login'] = $login;

            /* Update online */
            $admins[$key]['last_online'] = time();
            save_file($admin_path.'admins.dat', serialize($admins));

            /* Save logs */
            writeLog($admin['login'], 'login');

            /* cookies login */
            if ($password) {
                SetCookie("a_login", $login, time() + 10800);
                SetCookie("a_hash", $password, time() + 10800);
            }

            header('Location: index.php');
            exit;
        }
    }
    $status = 'Неверный логин или пароль';
} elseif (isset($_SESSION['login'])) {
    $is_auth = true;
}

if (!$is_auth) {
    include($folder_admin_files.'/login.php');
    exit;
}

/* Block file / permissions */
check_permissions($general_included_files, $admin_path, $file_path);