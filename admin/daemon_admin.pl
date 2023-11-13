#!/usr/bin/perl -w
my $data_path = "/home/voodoo/www/voc/data/";
 
print "Content-type: text/html\n\n";
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
<head>
	<title>daemon starter</title>
</head>
<body bgcolor=\"white\">";

$buffer=$ENV{'QUERY_STRING'};
@pairs = split(/&/, $buffer);
foreach $pair (@pairs) 
{
	($name, $value) = split(/=/, $pair);
	$value =~ tr/+/ /;
	$value =~ s/%(..)/pack("c",hex($1))/ge; 
	$name=~tr/+/ /; 
	$name=~ s/%(..)/pack("c",hex($1))/ge; 
	$input{$name} = $value;
}
if ($input{'op'} eq "start")
{
	print "<pre>";
	$to_run = 'cd '.$data_path.'daemon/; perl ./daemon.pl 2>&1';
	print `$to_run`;
	sleep(3);
	print "</pre>";
}
if ($input{'op'} eq "stop")
{
	print "<pre>";
	$to_run = 'cd '.$data_path.'daemon/; kill `cat daemon.pid` 2>&1';
	print `$to_run`;
	sleep(3);
	print "</pre>";
}
my $pid_file = $data_path."daemon/daemon.pid";
my $log_file = $data_path."daemon/daemon.log";



open(PIDFILE, "< $pid_file");
@pidfile = <PIDFILE>;
close(PIDFILE);
if (scalar(@pidfile)>0) {$pid = $pidfile[0];} else {$pid = "";}
if ($pid eq "a") {$pid = "";}
print "Status:<br>";
if ($pid eq ""){print "Daemon is not running<br>\n";}
else {print "Daemon is running with pid= $pid<br>\n";}
print "<a href=\"daemon_admin.pl?op=view\">Reload stat page</a> | ";
if ($pid eq "") { print "<a href=\"daemon_admin.pl?op=start\">start daemon</a>";}
else {print "<a href=\"daemon_admin.pl?op=stop\">stop daemon</a>";}


eval('$config = $data_path."voc.conf";require("$config")');
print "<hr><b>Proccesses information:</b><br><pre>";
if ($pid ne "") { $to_run = 'ps -up'.$pid;print `$to_run`;}
print "</pre>\n<b>Netstat information:</b><br><pre>";
eval('$to_run = \'netstat -na |grep \'.$daemon_port');
print `$to_run`;
print "</pre>\n";
print "<hr>";

print "Last 20 lines in the log file:<br>";
open(LOGFILE, "< $log_file");
@logfile = <LOGFILE>;
close(LOGFILE);
my $start_at = (scalar(@logfile)>20)? scalar(@logfile)-20: 0;
for ($i=$start_at;$i<scalar(@logfile);$i++)
{
	print $logfile[$i]."<br>\n";
}
exit;

