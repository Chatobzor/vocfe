<?
//auto configure 
$mysql_user = $_POST['db_login'];
$mysql_password = $_POST['db_password'];
$port = $_POST['port'];
$login = $_POST['login'];
$password = $_POST['password'];
$email = $_POST['email'];

$file_path = str_replace('\\', '/', __DIR__);
$this_path = explode('/', $file_path);
array_pop($this_path);
$this_path = implode('/', $this_path);
$data_path = $this_path . '/data/';
$file_path = $file_path . '/';

// html
chmod($file_path . 'admin/sessions.php', 0777);
chmod($file_path . 'clans-avatar', 0777);
chmod($file_path . 'converts', 0777);
chmod($file_path . 'items', 0777);
chmod($file_path . 'photos', 0777);
chmod($file_path . 'plugins', 0777);
chmod($file_path . 'top20', 0777);
chmod($file_path . 'up', 0777);
chmod($file_path . 'users', 0777);

// data
chmoddir($data_path, 0777);
chmoddir($data_path . 'engine/', 0755);
chmod($data_path . 'engine/', 0755);

// config
$conf = file_get_contents($data_path."voc.conf");
$conf = str_replace('[USER]', $login, $conf);
$conf = str_replace('[DB_LOGIN]', $mysql_user, $conf);
$conf = str_replace('[DB_PASSWORD]', $mysql_password, $conf);
$conf = str_replace('[PORT]', $port, $conf);
file_put_contents($data_path."voc.conf", $conf);

// quiz
$conf = file_get_contents($data_path."quiz/config.php");
$conf = str_replace('[USER]', $login, $conf);
$conf = str_replace('[DB_LOGIN]', $mysql_user, $conf);
$conf = str_replace('[DB_PASSWORD]', $mysql_password, $conf);
file_put_contents($data_path."quiz/config.php", $conf);

// admin
chmod($file_path . 'admin/admin_users.php', 0777);
$user_admin = 'admin' . rand(0,99);
$password_admin = rand(10000, 100000);
$data_admin = "<?php\n\n/*\n\nChanged with admin/password.php\n\n*/\n\n" . '$admin_users[0]["nickname"] = \'' . $user_admin . "';\n" . '$admin_users[0]["password"] = \'' . $password_admin . "';\n\n?>";
file_put_contents($file_path . 'admin/admin_users.php', $data_admin);
chmod($file_path . 'admin/admin_users.php', 0644);

// sql
$query = file_get_contents($file_path . 'db.sql');
$mysqli = new mysqli("localhost", $mysql_user, $mysql_password, $mysql_user);
$mysqli->set_charset("cp1251");
$mysqli->multi_query($query);
$mysqli->close();



// delete
$script = str_replace('data/', 'vocbse.zip', $data_path);
unlink($script);
unlink($file_path . 'db.sql');
unlink($file_path . 'install.php');
unlink($file_path . 'index.html');

// email
if ($email) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=windows-1251" . "\r\n";
    $headers .= 'From: <admin@mvoc.ru>' . "\r\n";
    $headers .= "Cc: " . $email . "\r\n";

    $html = '
        Чат <b>' . $login . '</b> установлен.
        <br>
        <b>Админка чата:</b><br>
        Логин: <b>' . $user_admin . '</b><br>
        Пароль: <b>' . $password_admin . '</b><br>
        <br>
        <b>Викторина и сообщения в чате появятся в течении 10 минут<br></b>
        Дополнительные материалы для чата Вы можете найти на сайте <a href="http://mvoc.ru/">Mvoc.ru</a><br>
        <hr>
        Хостинг чатов <a href="http://chat.bz/">Chat.bz</a><br>
        <small>Это письмо сгенерировано автоматически, отвечать на него не нужно!</small>
    ';
    
    $res = mail($email, 'Чат установлен', $html, $headers);
}


function chmoddir($dir = FALSE, $permission = 0777) {
    $scan = scandir($dir);
    foreach ($scan as $file) {
        if ($file == '.' OR $file == '..') continue;
        
        
        @chmod($dir.$file, $permission);
        
        if (is_dir($dir.$file)) {
            chmoddir($dir.$file . '/', $permission);
        }
    }
    
    return TRUE;
}

if ($res) {
    echo 'installed';
} else {
    echo 'no-mail';
}