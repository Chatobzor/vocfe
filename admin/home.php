<?php

ini_set('memory_limit', '2056M');
include("check_session.php");
include($file_path."inc_statistic.php");
$host = parse_url($chat_url);
$last_users = engine_last_registered();

$host = parse_url($chat_url);
$doc = new DOMDocument;
$dom = [];
$doc->load('http://mvoc.ru/xml/new_admin/'.$version.'/'.$host['host']);
$dom['type'] = $doc->getElementsByTagName("type")->item(0)->nodeValue;
$dom['messages'] = $doc->getElementsByTagName("messages")->item(0)->nodeValue;

if (!$dom['messages']) {
    $dom['type'] = 'error';
    $dom['messages'] = 'Ошибка проверки обновления! Сервер не отвечает, возможно чат находится в черном списке';
}

$statistic = getMsgStatistic();
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
    echo $css_path; ?>home.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="<?php
    echo $js_path; ?>common.js"></script>
</head>
<body>
<h1>Краткие сведения о чате</h1>
<?php if ($dom['messages']): ?>
    <p class="<?php
    echo $dom['type']; ?>"><?php
        echo $dom['messages']; ?></p>
<?php endif; ?>
<div class="clear"></div>
<hr/>

<div class="box">
    <h1>Последние регистрации в чате</h1>
    <div class="wrap">
        <?php
        foreach ($last_users as $user): ?>
            <div class="last-user"><?php
                echo $user['nickname']; ?></div>
        <?php
        endforeach; ?>
    </div>
</div>

<div class="box frame">
    <h1>Последние новинки для чата</h1>
    <iframe src="http://mvoc.ru/iframe/last_post/<?php
    echo $host['host']; ?>" width="100%" height="300" align="left"></iframe>
</div>

<div class="box frame">
    <h1>Самое популярное для чата</h1>
    <iframe src="http://mvoc.ru/iframe/best_post/<?php
    echo $host['host']; ?>" width="100%" height="300" align="left"></iframe>
</div>

<div class="clear"></div>

<div id="chart_div"></div>


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
    google.load("visualization", "1", {packages: ["corechart"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            [
                'Дни',
                'Общие',
                'Приват',
            ],
            <?php
            foreach ($statistic as $key => $value) {
                $date = explode('-', $key);

                echo '["'.$date[2].'.'.$date[1].'", '.(int)$value['public'].', '.(int)$value['private'].'],';
            }
            ?>
        ]);

        var options = {
            title: 'Статистика сообщений',
            hAxis: {
                title: 'Дни',
                titleTextStyle: {
                    color: '#4E7A16',
                },
            },
            vAxis: {
                title: 'Сообщения',
                titleTextStyle: {
                    color: '#4E7A16',
                },
            },
            height: 350,
            width: 980,
            backgroundColor: '#f9f9f9',
            fontSize: 13
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>