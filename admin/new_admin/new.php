<?php

if (!$folder_admin_files) {
    exit;
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta name="robots" content="all"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="<?php
    echo $css_path; ?>common.css">
    <link rel="stylesheet" href="<?php
    echo $css_path; ?>login.css">
    <title>Создаем первого администратора Admin Panel VOC++ | <?php
        echo $adm_administration; ?></title>
</head>
<body>
<div class="wrap">
    <div class="main">
        <form method="post" action="index.php" target="_top">
            <input type="hidden" name="operation" value="login">
            <input type="hidden" name="lang" value="<?php
            echo $lang; ?>">
            <p class="center">
                <img src="<?php
                echo $chat_url.'admin/'.$folder_admin_files; ?>/img/user_add.png"/>
            </p>
            <p>
                <label for="login"><?php
                    echo $adm_login; ?><br/>
                    <input type="text" name="login" value="" id="login" class="input">
                </label>
            </p>

            <p>
                <label for="password"><?php
                    echo $adm_password; ?><br/>
                    <input type="password" name="password" value="" id="password" class="input">
                </label>
            </p>

            <p>
                <label for="password2">Повторите пароль<br/>
                    <input type="password" name="password2" value="" id="password2" class="input">
                </label>
            </p>

            <p>
                <button class="btn">Создать</button>
            </p>
        </form>
        <?php
        if ($status): ?>
            <p class="error"><?php
                echo $status; ?></p>
        <?php
        endif; ?>
    </div>
</div>
<?php
include_once($folder_admin_files.'/copy.php'); ?>
</body>
</html>