<?php

include("check_session.php");
include("header.php");
include("../inc_common.php");

$error = "";
$conv_file = $data_path."converts.dat";
$converts = file($conv_file);
$dir = $file_path."converts/";
$www_dir = $chat_url."converts/";

if (!is_writeable($conv_file)) {
    $error = "$conv_file is not wtiteable";
}


if ($ff == "upload") {
    if (!$code) {
        $error = "Не указан код вызова смайла!";
    }
    if (is_file($dir.$_FILES['file']['name'])) {
        $error = "Смайл ".$_FILES['file']['name']." уже существует";
    } else {
        if ($_FILES['file']['error']) {
            $error = "Ошибка при закачке смайла";
        } else {
            foreach ($converts as $conv) {
                $a = explode("\t", $conv);
                if ($code == $a[0]) {
                    $error = "Код вызова смайла $code уже используется";
                }
            }
        }
    }

    include('blacklist.php');
    foreach ($blacklist as $item) {
        if (preg_match("/$item\$/i", $_FILES['image']['name'])) {
            echo "Неподдерживаемый формат!\n";
            exit;
        }
    }

    if (!$error) {
        move_uploaded_file($_FILES['file']['tmp_name'], $dir.$_FILES['file']['name']);
        $string = "$code\t";
        $string .= "<img src=\"".$www_dir.$_FILES['file']['name']."\" border=\"0\">\n";
        $f = fopen($conv_file, "a+");
        fwrite($f, $string);
        fclose($f);
        $error = $_FILES['file']['name']." закачан успешно";
    }
}
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="blkimg" width="30"><img src="images/blk_login.gif" width="17" height="16" border="0" alt=""></td>
        <td class="blkhead">Смайлы /<b>ВНИМАНИЕ!!! В коде вызова нужно обязательно вписывать значки ** Пример: *like*</b>
        </td>
    </tr>
</table>
<?php
if ($error) {
    echo "<span class=\"error\">$error</span>";
}
?>
<center>
    <form action="?" method="post" enctype="multipart/form-data">
        <input type="hidden" name="session" value="<?php
        echo $session; ?>">
        <input type="hidden" name="ff" value="upload">
        <table border="1" bordercolor="#728d94" cellspacing="0" cellspacing="0">

            <tr>
                <td>Файл</td>
                <td>
                    <input type="file" name="file" class="input">
                </td>
            </tr>
            <tr>
                <td>Код вызова</td>
                <td>
                    <input type="text" name="code" class="input">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" class="button" value="Закачать">
                </td>
            </tr>
        </table>
    </form>
</center>
</body>
</html>