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
    <link rel="stylesheet" href="<?php echo $css_path; ?>common.css">
    <link rel="stylesheet" href="<?php  echo $css_path; ?>login.css">
    <title>Admin Panel VOC++ | <?php echo $adm_administration; ?></title>
</head>
<body>
<div class="wrap">
    <div class="main">
        <form method="post" action="index.php" target="_top">
            <input type="hidden" name="operation" value="login">
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
                <button class="btn"><?php
                    echo $adm_login_do; ?></button>
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
<?php include_once($folder_admin_files.'/copy.php'); ?>
</body>
</html>