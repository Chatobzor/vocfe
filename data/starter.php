<?
//start?
$startdaemon=1;
$startquiz=1;

//set and check datapath
$scriptname="/starter.php";
$data=str_replace($scriptname,"",$_SERVER["SCRIPT_FILENAME"]);
if(!is_file("$data/daemon/daemon")) die("Daemon not found!");


//запуск daemon
$daemon=@(int)file_get_contents("$data/daemon/daemon.pid");
$isdaemon=`ps aux|grep daemon|grep $daemon|grep -v grep`;
if($startdaemon) 
if(!$daemon || !$isdaemon) {
	`cd $data/daemon && ./daemon`;
	echo "Daemon started!<br>";
}


//запуск quiz
$quiz=@(int)file_get_contents("$data/quiz/quiz.pid");
$isquiz=`ps aux|grep engine.php|grep $quiz|grep -v grep`;
if($startquiz) 
if(!$quiz || !$isquiz) {
	`unlink $data/quiz/quiz.pid`;
	`/usr/bin/php5.6 -q $data/quiz/engine.php > /dev/null 2>&1 &`;
	echo "Quiz started!<br>";
}
?>