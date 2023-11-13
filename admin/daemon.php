<?php
include("check_session.php");
include("../inc_common.php");
include("header.php");
?>
<center><table width="90%" cellpadding=4 cellspacing=0><tr><td width="90%" class=head>
<?php
echo "Status:<br>";
$daemon_pid = file($data_path."daemon/daemon.pid");
if (!isset($daemon_pid)) $daemon_pid = 0;
if (is_array($daemon_pid))
	$daemon_pid = intval($daemon_pid[0]);
else $daemon_pid = 0;


if (isset($op))
{
	if ($op == "reset")
	{
		echo "deleting pid-info<br>";
		$fp = fopen($data_path."daemon/daemon.pid","w");
		ftruncate($fp,0);
		fflush($fp);
		fclose($fp);
		$daemon_pid = file($data_path."daemon/daemon.pid");
		if (!isset($daemon_pid)) $daemon_pid = 0;
		if (is_array($daemon_pid))
			$daemon_pid = intval($daemon_pid[0]);
		else $daemon_pid = 0;
	}
	if ($op == "start")
	{
		if ($daemon_pid)
		{
			echo "daemon is already started<br>If you absolutelly shure that daemon is not running, <a href=\"daemon.php?op=reset&session=$session\">click here</a> to clear the pid-file!";
		}
		else
		{
			echo "trying to start daemon:<br><pre>\n";
			#echo "DOESNT WORK!!!!!!!!!!!";
			#`$data_path/daemon/daemon.pl $data_path/voc.conf 2>&1 >> $data_path/daemon/daemon.log & `;
			#exec('./starter.sh  2>&1 >/dev/null &');
			#session_write_close();
			exec("$data_path/daemon/daemon.pl 2>/dev/null >&- <&- >/dev/null &");
			sleep(5);
			$daemon_pid = file($data_path."daemon/daemon.pid");
			if (!isset($daemon_pid)) $daemon_pid = 0;
			if (is_array($daemon_pid))
				$daemon_pid = intval($daemon_pid[0]);
			else $daemon_pid = 0;
			echo "</pre><br>\n";
		}

	}

	if ($op == "stop")
	{
		if (!$daemon_pid)
		{
			echo "cannot find daemon pid<br>";
		}
		else
		{
			echo "trying to stop daemon:<br>\n";
			passthru("kill -CONT $daemon_pid");
			sleep(5);
			$daemon_pid = file($data_path."daemon/daemon.pid");
			if (!isset($daemon_pid)) $daemon_pid = 0;
			if (is_array($daemon_pid))
				$daemon_pid = intval($daemon_pid[0]);
			else $daemon_pid = 0;
		}

	}

}


if ($daemon_pid)
{
	echo "pid file found, pid = $daemon_pid<br>\n";
	echo "information about process (in some cases doesn't work):<br><pre>\n";
	passthru("ps -aux | grep $daemon_pid");
	echo "</pre><br>";
	echo "netstat (in some cases doesn't work):<br><pre>\n";
	passthru("netstat -na | grep $daemon_port");
	echo "</pre><br>";
	echo "<a href=\"daemon.php?op=stop&session=$session\">try to stop</a>";

	echo "<br>If you absolutelly shure that daemon is not running, <a href=\"daemon.php?op=reset&session=$session\">click here</a> to clear the pid-file! Be carefull, because otherwise you wouldn't be able to stop it! ";
}
else
{
	echo "netstat (in some cases doesn't work):<br><pre>\n";
	passthru("netstat -na | grep $daemon_port");
	echo "</pre><br>";
	#echo "daemon is not running<br><a href=\"daemon.php?op=start&session=$session\">try to start</a>";
	#echo "<br>(It is highly <b>NOT</b> recommended to use this tool to start daemon. Because you wouldn't be able to restart Apache while daemon is working. use shell or Perl-starter instead of this script)";
	echo "USE daemon_admin.pl script to START/STOP the daemon!";

}
echo "<hr>last 20 records in the log-file:<br>";
$log = file($data_path."daemon/daemon.log");
if (isset($log))
	if (is_array($log))
	{
		$total = count($log);
		$start_at = ($total>20) ? ($total-20): 0;

		for ($i=$start_at;$i<$total;$i++)
			echo $log[$i]."<br>\n";
	}

?>

</td></tr></table></center>
</body>
</html>
<?php exit();?>