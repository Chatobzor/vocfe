#!/usr/bin/perl
#************************************************************
#                                                           *
#                Voodoo chat daemon                         *
#                  file: daemon.pl                          *
#            (c) 2003-04 by Vlad Vostrykh                   *
#                voodoo@vochat.com                          *
#                http://vochat.com/                         *
#                                                           *
#                 QPL ver1 License                          *
#           See voc/LICENSE file for details                *
#                                                           *
#                                                           *
#***********************************************************/
use constant VOC_VERSION	=> "1.0 RC1";
#PROBABLY YOU NEED TO CHANGE NEXT LINE!!!!!!!!!!!!
$IPC_CREATE = 0001000;

#close STDIN;
#close STDOUT;
#close STDERR;

#constants
use constant ST_ALL			=>0;
use constant ST_NORMAL		=>1;
use constant ST_PRIVATE		=>2;

use constant USER_NICKNAME	=>0;
use constant USER_SESSION		=>1;
use constant USER_TIME			=>2;
use constant USER_GENDER		=>3;
use constant USER_AVATAR		=>4;
use constant USER_REGID			=>5;
use constant USER_TAILID		=>6;
use constant USER_IP				=>7;
use constant USER_STATUS		=>8;
use constant USER_LASTSAYTIME	=>9;
use constant USER_ROOM			=>10;
use constant USER_IGNORLIST	=>11;
use constant USER_CANONNICK	=>12;
use constant USER_CHATTYPE	=>13;
use constant USER_LANG			=>14;
use constant USER_HTMLNICK	=>15;
use constant USER_PRIVTAILID	=>16;
use constant USER_COOKIE		=>17;
use constant USER_BROWSERHASH	=>18;
use constant USER_CLASS		=>19;
use constant USER_SKIN			=>20;
use constant USER_TOTALFIELDS	=>21;

use constant MESG_ID				=>0;
use constant MESG_ROOM		=>1;
use constant MESG_TIME			=>2;
use constant MESG_FROM			=>3;
use constant MESG_FROMWOTAGS	=>4;
use constant MESG_FROMSESSION	=>5;
use constant MESG_FROMID		=>6;
use constant MESG_FROMAVATAR =>7;
use constant MESG_TO			=>8;
use constant MESG_TOSESSION	=>9;
use constant MESG_TOID			=>10;
use constant MESG_BODY			=>11;
use constant MESG_TOTALFIELDS	=>12;

use constant ROOM_ID				=>0;
use constant ROOM_TITLE		=>1;
use constant ROOM_TOPIC		=>2;
use constant ROOM_DESIGN		=>3;
use constant ROOM_BOT			=>4;
use constant ROOM_CREATOR	=>5;
use constant ROOM_ALLOWEDUSERS	=>6;
use constant ROOM_ALLOWPICS	=>7;
use constant ROOM_PREMODER	=>8;
use constant ROOM_TOTALFIELDS	=>9;

use constant HTTP_HEADER => "HTTP/1.0 200 Ok Welcome to VOC\015\012Server: Voodoo chat daemon ver perl ".VOC_VERSION."\015\012".
																	"Content-type: text/html\015\012Expires: Mon, 26 Jul 1997 05:00:00 GMT\015\012".
																	"Cache-Control: no-store, no-cache, must-revalidate\015\012Cache-Control: post-check=0, pre-check=0\015\012".
																	"Pragma: no-cache\015\012\015\012";

my $my_path = $0;
$my_path =~ s/daemon_win.pl/..\//;

$config = shift;
if (!defined($config)) {$config = $my_path."voc.conf";}

#to avoid warnings:
$language = "";
$message_format = "";
$message_fromme = "";
$daemon_port = 0;
$private_hidden = "";
$default_design = "";
$daemon_listen = "";
$nick_highlight_before = "";
$nick_highlight_after = "";
$private_message = "";
$private_message_fromme = "";
$mysql_server = "";
$mysql_user = "";
$mysql_password = "";
$mysql_db = "";
$mysql_table_prefix = "";
$str_w_n_before = "";
$str_w_n_after = "";
$max_connect  = 10;
$history_size = 10;
$charset = "";
$chat_url = "";
#end "to avoid warnings"

eval('require("$config")') or die "cannot process config file $config";
my $charset_str = "";
if ($charset ne "") {
	$charset_str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$charset."\">";
}

$lang_file = $file_path."languages/".$language.".php";
open(LANG,"< $lang_file");
flock(LANG, 2);
while (<LANG>) {
	($key, $value) = split(" = ",$_,2);
	
	if ($key eq "\$w_no_user") {
		$value =~ s/\"//g;
		$value =~ s/;//g;
		$lang{"no_such_user"}  = $value;
	}
	if ($key eq "\$w_server_restarting") {
		$value =~ s/\"//g;
		$value =~ s/;//g;
		$lang{"server_restarting"} = $value;
	}
	if ($key eq "\$w_whisper_to") {
		$value =~ s/\"//g;
		$value =~ s/;//g;
		$lang{"whisper_to"} = $value;
	}
	if ($key eq "\$w_only_one_tail") {
		$value =~ s/\"//g;
		$value =~ s/;//g;
		$lang{"only_one_tail"} = $value;
	}
	if ($key eq "\$w_rob_name") {
		$value =~ s/\"//g;
		$value =~ s/;//g;
		$lang{"rob_name"} = $value;
	}
}
flock(LANG, 8);
close(LANG);


use POSIX qw(:errno_h :fcntl_h :float_h :limits_h :math_h :pwd_h :signal_h :stdio_h :string_h :sys_wait_h :unistd_h);
use IO::Socket;
use IO::Select;
use Socket;
use Fcntl; 
use Time::localtime;


$LOG = 1;
STDOUT->autoflush;
STDERR->autoflush;
$messages_file = $my_path."messages.dat";
$who_file = $my_path."who.dat";
$rooms_file = $my_path."rooms.dat";
$log_file = $my_path."daemon/daemon.log";
$header_file = "daemon_html_header.html";
$pid_file = $my_path."daemon/daemon.pid";
my $SELF = $0." $config";
my @ARGS = qw();



#if we're using MySQL, connecting:
my $dbh;
my $sth;
if ($engine eq "mysql" || $long_life_data_engine eq "mysql") {
	eval('use DBI;');
	my $data_source = "DBI:mysql:$mysql_db:$mysql_server";
	$dbh = DBI->connect( $data_source, $mysql_user, $mysql_password) or die "Can't connect to $data_source: $dbh->errstr\n"; 
	my_log("successfully connected to db");
}

%userhashes = ();
%usernames = ();
%new_users = ();
%cycles_on_connect = ();
%userbuffers = ();
%user_incbuffers = ();
%usertails = ();
%userprivtails = ();
%wait_counters = ();
%users_during_connections = ();
%headers = ();
%contypes = ();
my $wait_counter = 0;
my $users;
my @users_array;
my $new_messages;
my $messages;
my $rooms;
my @ignored;
%users_to_update =();
%users_to_privupdate =();
my $user_design = "";
my $session = "";
my $send_ok = 0;
my $user_exists = 0;
my $new_counter = 0;
my $last_id = 0;
my $inter = 0;
my $to_write = 0;
my $semnum = 0;
my $vc_daemon;
my $cycles = 0;
foreach $signal(keys %SIG) {
	$SIG{$signal}  = \&sigger;
}
$SIG{__DIE__} = \&sigger;
$SIG{HUP} = \&restarter;
eval
{

my_log("trying to get port");
$vc_daemon = IO::Socket::INET->new(	LocalPort => $daemon_port,
					LocalAddr => $daemon_listen,
					Timeout => 5,
					ReuseAddr => 1,
					Listen => $max_connect) or die "can't create socket";

nonblock($vc_daemon);
$daemon_list = IO::Select->new($vc_daemon);
$list = IO::Select->new();
my_log("Daemon started");

my $prefix = "";
my $my_length = 0;


while(1) {
	$cycles++;
	undef %new_users;
	%new_users=();
	sleep(1);
	undef $rooms;
	if ($long_life_data_engine eq "mysql") {
		$sth = $dbh->prepare("select * from ".$mysql_table_prefix."rooms order by id;") or die "MYSQL"; 
		$res = $sth->execute or die "MYSQL: Unable to execute query: $dbh->errstr\n";
		$rooms = $sth->fetchall_arrayref or die "MYSQL: $sth->errstr\n";
		$sth->finish;
	} else {
		open(ROOMS,"< $rooms_file");
		flock(ROOMS, 2);
		@rooms_in_str = <ROOMS>;
		flock(ROOMS, 8);
		close(ROOMS);
		for ($i = scalar(@rooms_in_str)-1; $i>=0;$i--) {
			$rooms_in_str[$i] =~ s/\n//g;
			$rooms_in_str[$i] =~ s/\r//g;
			@{$rooms->[$i]} = split("\t", $rooms_in_str[$i],5);
		}
	}

	if ($engine eq "mysql") {

		undef $messages;
		undef $new_messages;
		undef $users;
		$sth = $dbh->prepare("select * from ".$mysql_table_prefix."who order by user_name;"); 
		$res = $sth->execute or die "Unable to execute query: $dbh->errstr\n"; 
		$users = $sth->fetchall_arrayref or die "$sth->errstr\n";
		$sth->finish;
		if ($to_write) {
			my @just_time;#array of session IDs which has to be updated
			for ($i=0;$i<=$#{$users};$i++) {
				if ( defined( $users_to_update{$users->[$i][USER_NICKNAME]} ) ) {
					$update_query = "";
					if ( $users_to_update{$users->[$i][USER_NICKNAME]} ) {
						$update_query .= ", tail_id=".$users_to_update{$users->[$i][USER_NICKNAME]};
						$users->[$i][USER_TAILID] = $users_to_update{$users->[$i][USER_NICKNAME]};
					}
					if ( defined( $users_to_privupdate{$users->[$i][USER_NICKNAME]}) && $users_to_privupdate{$users->[$i][USER_NICKNAME]} ) {
						$update_query .=  ", priv_tailid=".$users_to_update{$users->[$i][USER_NICKNAME]};
						$users->[$i][USER_PRIVTAILID] = $users_to_privupdate{$users->[$i][USER_NICKNAME]};
					}
					if ($update_query ne "") {
						$res = $dbh->do("update ".$mysql_table_prefix."who set time=".time.$update_query." where session='".$users->[$i][USER_SESSION]."'") or die "Unable to execute query: $dbh->errstr\n";
					} else {
						$just_time[scalar(@just_time)] = "'".$users->[$i][USER_SESSION]."'";
					}
				}
			}
			if (scalar(@just_time)){
				my $sql_in = join (",", @just_time);
				$res = $dbh->do("update ".$mysql_table_prefix."who set time=".time."  where session in(".$sql_in.")") or die "MYSQL: Unable to execute query: $dbh->errstr\n";
			}
		}
		$sth = $dbh->prepare("select * from ".$mysql_table_prefix."messages order by id;");
		$res = $sth->execute or die "Unable to execute query: $dbh->errstr\n";
		$messages = $sth->fetchall_arrayref or die "$sth->errstr\n";
		$sth->finish;
		$k = 0;
		for ($i = 0; $i<=$#{$messages};$i++) {
			if ($messages->[$i][MESG_ID]>$last_id) {
				$new_messages->[$k] = $messages->[$i];
				$last_id = $messages->[$i][MESG_ID];
				$k++;
			}
		}
	} else {
		undef @messages_in_str;
		open(MESSAGES,"< $messages_file");
		flock(MESSAGES, 2);
		@messages_in_str = <MESSAGES>;
		flock(MESSAGES, 8);
		close(MESSAGES);
		undef @users_in_str;	

		undef $users;
		undef @users_array;
		if ($to_write){
			$to_write_buffer = "";
			open(USERS_FILE,"+>> $who_file");
			binmode(USERS_FILE);
			flock(USERS_FILE, 2);
			seek (USERS_FILE, 0, 0);
			@users_in_str= <USERS_FILE>;
			for ($i=0;$i<(scalar(@users_in_str));$i++) {
				$users_in_str[$i] =~ s/\n//g;
				$users_in_str[$i] =~ s/\r//g;
				@{$users->[$i]} = split("\t", $users_in_str[$i], USER_TOTALFIELDS);
				if (defined $users_to_update{$users->[$i][USER_NICKNAME]}) {
					if ($users_to_update{$users->[$i][USER_NICKNAME]}>0) {
						$users->[$i][USER_TAILID] = $users_to_update{$users->[$i][USER_NICKNAME]};
					}
					$users->[$i][USER_TIME] = time();
				}
				if (defined $users_to_privupdate{$users->[$i][USER_NICKNAME]}) {
					if ($users_to_privupdate{$users->[$i][USER_NICKNAME]}>0) {
						$users->[$i][USER_PRIVTAILID] = $users_to_privupdate{$users->[$i][USER_NICKNAME]};
					}
					$users->[$i][USER_TIME] = time();
				}
				$to_write_buffer .=  join("\t",@{$users->[$i]});
				if ($i<(scalar(@users_in_str)-1)) {
					 $to_write_buffer .= "\n";
				}
			}

			#THIS SEEK is highly important in windows version.
			#just me to don't forget it in next release.
			seek(USERS_FILE,0,0);
			truncate (USERS_FILE, 0);
			print USERS_FILE $to_write_buffer;
			flock(USERS_FILE, 8);
			close(USERS_FILE);
		} else {
			open(USERS_FILE,"+>> $who_file");
			binmode(USERS_FILE);
			flock(USERS_FILE, 2);
			seek (USERS_FILE, 0, 0);
			@users_in_str= <USERS_FILE>;
			flock(USERS_FILE, 8);
			close(USERS_FILE);
			for ($i=0;$i<(scalar(@users_in_str));$i++) {
				$users_in_str[$i] =~ s/\n//g;
				#also win-specific
				$users_in_str[$i] =~ s/\r//g;
				@{$users->[$i]} = split("\t", $users_in_str[$i], USER_TOTALFIELDS);
				if (defined $users_to_update{$users->[$i][USER_NICKNAME]}) {
					if ($users_to_update{$users->[$i][USER_NICKNAME]}>0) {
						$users->[$i][USER_TAILID] = $users_to_update{$users->[$i][USER_NICKNAME]};
					}
					$users->[$i][USER_TIME] = time();
				}
				if (defined $users_to_privupdate{$users->[$i][USER_NICKNAME]}) {
					if ($users_to_privupdate{$users->[$i][USER_NICKNAME]}>0) {
						$users->[$i][USER_PRIVTAILID] = $users_to_privupdate{$users->[$i][USER_NICKNAME]};
					}
					$users->[$i][USER_TIME] = time();
				}
				$to_write_buffer .=  join("\t",@{$users->[$i]});
				if ($i<(scalar(@users_in_str)-1)) {
					 $to_write_buffer .= "\n";
				}
			}
			for ($i = scalar(@users_in_str)-1; $i>=0;$i--) {
				$users_in_str[$i] =~ s/\n//g;
				$users_in_str[$i] =~ s/\r//g;
				@{$users->[$i]} = split("\t", $users_in_str[$i],USER_TOTALFIELDS);
			}
		} 
		undef @users_in_str;
		
		undef $messages;
		undef $new_messages;
		$j=0; $k=0;
		$new_last_id = 0;
		for ($i = 0; $i<scalar(@messages_in_str);$i++) {
			$messages_in_str[$i] =~ s/\n//g;
			$messages_in_str[$i] =~ s/\r//g;
			@{$messages->[$j]} = split("\t", $messages_in_str[$i], MESG_TOTALFIELDS);
			if ($messages->[$j][MESG_ID] >= 0) {
				if ($messages->[$j][MESG_ID]>$new_last_id) {$new_last_id = $messages->[$j][MESG_ID];}
				if ($messages->[$j][MESG_ID]>$last_id) {
					$new_messages->[$k] = $messages->[$j];
					$last_id = $messages->[$j][MESG_ID];
					$k++;
				}
				$j++;
			}
		}
		#in case of some faults 
		#i.e. when all IDs was dropped and counter started from 0
		if ($last_id>$new_last_id) { $last_id = $new_last_id;}
		undef @messages_in_str;

	}
	$cont = 0;
	
	$to_write = 0;
	undef %users_to_update;
	%users_to_update = ();
	foreach $client_key (keys %users_during_connections) {
		$client = $users_during_connections{$client_key};
		if (++$cycles_on_connect{$client}>20) {
			$userbuffers{$client} ="sorry";
			$usernames{$client} = "...";
			client_remove($client, "too long time to connect, disconnected");
		}
	}
	#each hour we clear non-used handles:
	if ($cycles>3600){
		$cycles = 0;
		#my_log("removing by hour check");
		foreach $client ($list->handles){
			if ($client != $vc_daemon) {
				my $exists = 0;
				for ($i=0;$i<=$#{$users};$i++) {
					if ($userhashes{$client} eq $users->[$i][1]) {$exists = 1; last;}
				}
				if (!$exists) {
					client_remove($client, "hour check");
				}
			}
		}
	}
	
	while (@incoming = $daemon_list->can_read(0.01)) {
		foreach $inc_c (@incoming) {
			$new_client = $vc_daemon->accept() or do { undef $new_client; my_log("failed connection attempt");};
			if (defined $new_client) {
				nonblock($new_client);
				$list->add($new_client);
				$user_incbuffers{$new_client} = "";
				$cycles_on_connect{$new_client} = 0;
				$users_during_connections{$new_client}=$new_client;
			}
		}
		if($cont++>30) {last;}
	}

	while (@clients_to_read = $list->can_read(0.01)) {
		foreach $client (@clients_to_read) {
			$readed = $client->sysread($buf,POSIX::BUFSIZ);
			if(!defined $readed) {$readed = 0;}
			if($readed) {
				if(!defined $userhashes{$client}) {
					if ($readed>0) {
						#if request too long, remove user
						if(length($user_incbuffers{$client}) < 16*1024) {
							$user_incbuffers{$client} .= $buf;
							$userhashes{$client} = "";
							$usernames{$client} = "...";
							my $ttt_buf  = $user_incbuffers{$client};
							if ($ttt_buf =~ /^\w+[^\012]+HTTP\/\d+\.\d+\015?\012/) {
								if ($ttt_buf !~ s/^(\S+)[ \t]+\/\?(\S+)(?:[ \t]+(HTTP\/\d+\.\d+))?[^\012]*\012//) {
									#actually, i have to do something with wrong requests
								} else {
									my $method = $1;
									my $request_string = $2;
									my $proto = $3 || "HTTP/0.9";
									my ($session, $tail_type_str) = split("&t=",$request_string, 2);
									$tail_type = ST_ALL;
									if ($tail_type_str eq "n") {$tail_type = ST_NORMAL;}
									elsif ($tail_type_str eq "p") {$tail_type  = ST_PRIVATE};
									my_log("trying to find user with hash: \"".$session."\", con type is ".$tail_type);
									for $i ( 0 .. $#{$users} ) {
										if (($session eq $users->[$i][USER_SESSION]) && (length($session)==32)) {
											$userhashes{$client} = $users->[$i][USER_SESSION];
											$usernames{$client} = $users->[$i][USER_NICKNAME];
											$wait_counters{$client} = 0;
											$contypes{$client} = $tail_type;
											$userprivtails{$client} = -1;
											$usertails{$client} = -1;
											if ($tail_type == ST_PRIVATE) {
												$users->[$i][USER_PRIVTAILID]++;
												$userprivtails{$client} = $users->[$i][USER_PRIVTAILID];
												$users_to_privupdate{$users->[$i][USER_NICKNAME]}=$users->[$i][USER_PRIVTAILID];
											} else {
												$users->[$i][USER_TAILID]++;
												$usertails{$client} = $users->[$i][USER_TAILID];
												$users_to_update{$users->[$i][USER_NICKNAME]}=$users->[$i][USER_TAILID];
											}
											$to_write = 1;
											my_log("".$users->[$i][USER_NICKNAME]." connected (".$users->[$i][USER_IP].")");
											my $ttt_ex = 0;
											for ($des_i=0;$des_i<scalar(@designes);$des_i++) {
												if ($users->[$i][USER_SKIN] eq $designes[$des_i]) {$ttt_ex = 1; last;}
											}
											if (!$ttt_ex) { $user_design = $default_design; }
											$new_users{$client} = $user_design;
											$userbuffers{$client} = HTTP_HEADER;
											show_top($client, $users->[$i]);
											delete $cycles_on_connect{$client};
											delete $users_during_connections{$client};
											last;
										}
									}
								}
								if ($usernames{$client} eq "...") {
									$userbuffers{$client} .= HTTP_HEADER."<html><body bgcolor=\"white\" color=\"black\">".$lang{"no_such_user"}."</body></html>";
									client_remove($client, "can't find user");
								}
							}#//end of buff checking
						}
						else  {
							#else for too long request string
							client_remove($client,"too long request string");
						}
					}#end for reading>0
				}#end of userhash already defined
			}#readed undefined -- client disconnected
			else { client_remove($client,"can't read from socket"); }
		}

	}

		foreach $client ($list->can_write(0.01)) {
			$user_exists = 0;
			if (defined $new_users{$client}) {
				undef $new_users{$client};
			}elsif(defined $userhashes{$client}) {
				my $to_send = "";
				$send_ok = 0;
				for  ( my $user_i = 0;$user_i<=$#{$users}; $user_i++) {
					if ($userhashes{$client} eq $users->[$user_i][USER_SESSION]) {
						if ($usertails{$client} == $users->[$user_i][USER_TAILID] ||
							$userprivtails{$client} == $users->[$user_i][USER_PRIVTAILID]	) {
							$user_exists = $user_i;
							$ttt_buf = "";
							for (my $mes_i=0;$mes_i <= $#{$new_messages};$mes_i++){
								$ttt_buf .= show_message($new_messages->[$mes_i],$users->[$user_i], $client);
							}
							if ($ttt_buf ne "") {
								$userbuffers{$client} .= $ttt_buf;
							} else {
								$wait_counters{$client}++;
							}
							if ($wait_counters{$client}>10) {
								$wait_counters{$client} = 0;
								$userbuffers{$client} .= "<script>up()</script>\015\012";
							}
							if ($usertails{$client} == $users->[$user_i][USER_TAILID]) {
								$users_to_update{$users->[$user_i][USER_NICKNAME]} = 0;
							}
							$to_write = 1;
						}else {
							$user_exists = 0;
							$userbuffers{$client} .= $lang{"only_one_tail"};
							client_remove($client, "wront tail id");
						}
						last;
					}
				}
				if (defined $userbuffers{$client}) {
					my $buffer_length = length($userbuffers{$client});
					#will try to write always, in problematic situations syswrite should fail and return 'undefinded'
					#if ($buffer_length) {
						eval('$bytewriten = syswrite($client, $userbuffers{$client}, $buffer_length,0);');
						if (defined $bytewriten){
							$userbuffers{$client} = substr($userbuffers{$client},$bytewriten, $buffer_length -$bytewriten);
						}
						else {
							client_remove($client,"can't write to a socket");
						}
					#}
				}
			}
		}
	}
};

if ($inter) {
	my_log("Daemon tries to restart");
	exec($SELF);
} else {
	exit;
}

sub client_remove {
	my $client = shift;
	my $reason = shift;
	my $buffer_length = length($userbuffers{$client});
	if ($buffer_length) {	
		eval('$bytewriten = syswrite($client, $userbuffers{$client}, $buffer_length,0);');
	}

	my_log($usernames{$client}." disconnected, reason: ".$reason);
	$list->remove($client);
	delete $userhashes{$client};
	delete $usernames{$client};
	delete $userbuffers{$client};
	delete $usertails{$client};
	delete $userprivtails{$client};
	delete $contypes{$client};
	delete $user_incbuffers{$client};
	delete $wait_counters{$client};
	delete $cycles_on_connect{$client};
	delete $users_during_connections{$client};
	close($client);
}

sub my_log {
	my $log_message = shift;
	if ($LOG) {
		$tm = localtime(); 
		printf("%04d-%02d-%02d %02d:%02d:%02d: ", $tm->year + 1900,$tm->mon + 1,$tm->mday,$tm->hour,$tm->min,$tm->sec);
		print $log_message."\n";
	}
}

sub nonblock {
    my $socket = shift;
    my $flags;
    my $ok = 0;
	my $set_it = "1";
	eval {
		ioctl($socket, 0x80000000 | (4 << 16) | (ord('f') << 8) | 126, $set_it) or die "ioctl: $!";$ok = 1;
	};
    #eval('$flags = fcntl($socket, F_GETFL, 0) or  die "PIPE"; fcntl($socket, F_SETFL, $flags | O_NONBLOCK ) or die "PIPE"; $ok = 1;');
    return $ok;
}

sub show_top {
	my $client = shift;
	my $current_user = shift;
	my $send_ok = 0;
	my $topic = "";
	for ($r_i=0;$r_i<=$#{$rooms};$r_i++) {
		if ($rooms->[$r_i][ROOM_ID] == $current_user->[USER_ROOM]) {
			if ($rooms->[$r_i][ROOM_DESIGN] ne "") {$current_user->[USER_SKIN] = $rooms->[$r_i][ROOM_DESIGN];}
			$topic = $rooms->[$r_i][ROOM_TOPIC];
			last;
		}
	}
	if (!defined($headers{$current_user->[USER_SKIN]})) {
		$full_header_file_name = $file_path."designes/".$current_user->[USER_SKIN]."/".$header_file;
		$headers{$current_user->[USER_SKIN]} = "";
		open(PAGE_HEADER,"< $full_header_file_name");
		flock(PAGE_HEADER, 1);
		while(<PAGE_HEADER>) {
			$headers{$current_user->[USER_SKIN]} .= $_;
		}
		flock(PAGE_HEADER, 8);
		close(PAGE_HEADER);
		$headers{$current_user->[USER_SKIN]} =~s/\[CHARSET\]/$charset_str/g;
		$headers{$current_user->[USER_SKIN]} =~s/\[CHAT_URL\]/$chat_url/g;
	}
	my $header = $headers{$current_user->[USER_SKIN]};

	$header =~s /\[SKIN]/$current_user->[USER_SKIN]/g;
	$header =~ s/\[TOPIC\]/$topic/g;
	$userbuffers{$client} .= $header;
	my $to_send = "";
	my $already_showed = 0;
	for ($i = $#{$messages};$i>=0;$i--) {
		if ($already_showed>=$history_size) {
			last;
		}
		$temp = show_message($messages->[$i], $current_user, $client);
		if ($temp ne ""){
			$already_showed++;
			$to_send = $temp.$to_send;
		}

	}
	$userbuffers{$client} .= $to_send;
}

sub show_message {
	my $message = shift;
	my $current_user = shift;
	my $client = shift;

	my $ret = "";
	my $show_type = $contypes{$client};
	if (($message->[MESG_ROOM] == $current_user->[USER_ROOM]) or ($message->[MESG_ROOM] == -1)) {
		$ignored_user = 0;
		my @ignored = split(",", $current_user->[USER_IGNORLIST]);
		$tm = localtime($message->[MESG_TIME]);
		my $tmp_hour = sprintf ("%02d",$tm->hour);
		my $tmp_min = sprintf ("%02d",$tm->min);
		my $tmp_sec = sprintf ("%02d",$tm->sec);
		for ($k=0;$k<scalar(@ignored);$k++) {
			if ($ignored[$k] eq $message->[MESG_FROMWOTAGS] && $ignored[$k] ne "") {
				$ignored_user = 1;
				last;
			}
		}
		if (!$ignored_user) {
			if ($message->[MESG_TO] ne "") {
				#private message
				if ( $message->[MESG_FROMSESSION] eq $current_user->[USER_SESSION] ||
						($message->[MESG_FROMWOTAGS] eq $current_user->[USER_NICKNAME] && $message->[MESG_FROMID] > 0)) {
					if ($show_type == ST_ALL || $show_type == ST_PRIVATE) {
						$ret = $private_message_fromme;
					}
				} elsif ($message->[MESG_TOSESSION]eq $current_user->[USER_SESSION] ||
								($message->[MESG_TO] eq $current_user->[USER_NICKNAME] && $message->[MESG_TOID] >0)){
					if ($show_type == ST_ALL || $show_type == ST_PRIVATE) {
						$ret = $private_message;
					}
				} else {
					#'whisper to somebody'
					if ($show_type == ST_ALL || $show_type == ST_NORMAL) {
						$ret = $private_hidden;
					}
				}
			} else {
				#normal message
				if ($show_type == ST_ALL || $show_type == ST_NORMAL) {
					if ($message->[MESG_FROMWOTAGS] eq $current_user->[USER_NICKNAME]) {
						$ret = $message_fromme;
					} else{
						$ret = $message_format;
					}
				}
			}
		}
		if ($ret ne "") {
			my $avatar = (length($message->[MESG_FROMAVATAR])<3)? "":
						"<img src=\"".$chat_url."photos/".$message->[MESG_FROMAVATAR]."\">";
			my $h_user_name = $nick_highlight_before.$current_user->[USER_NICKNAME].$nick_highlight_after;
			my $pattern = quotemeta($current_user->[USER_NICKNAME])."([\?\&:, \!])";
			$ret =~ s/\[NICK\]/$message->[MESG_FROM]/g;
			$ret =~ s/\[NICK_WO_TAGS\]/$message->[MESG_FROMWOTAGS]/g;
			$ret =~ s/\[PRIVATE\]/$lang{"whisper_to"}/g;
			$ret =~ s/\[TO\]/$message->[MESG_TO]/g;
			$ret =~ s/\[HOURS\]/$tmp_hour/g;
			$ret =~ s/\[MIN\]/$tmp_min/g;
			$ret =~ s/\[SEC\]/$tmp_sec/g;
			$ret =~ s/\[AVATAR\]/$avatar/g;
			my $msg = $message->[MESG_BODY];
			$nick_in_m = 0;
			$_ = $msg;
			if (/$pattern/) { $nick_in_m = 1; }
			$msg =~ s/$pattern/$h_user_name$1/g;
			$ret =~ s/\[MESSAGE\]/$msg/g;
			if ($nick_in_m) {$ret = $str_w_n_before . $ret . $str_w_n_after;}
			$ret .= "<br><script>up()</script>\015\012";
		}
	}
	return $ret;
}

sub sigger {
	my $sig = shift;
	#hope, PIPE is just somebody disconnected.
	if ($sig ne "PIPE") {
		foreach $client ($list->can_write(0.01)) {
			$userbuffers{$client}.= $lang{"server_restarting"}." <script>window.setTimeout('location.reload()',6000);</script>";
			my $buffer_length = length($userbuffers{$client});
			eval('$bytewriten = syswrite($client, $userbuffers{$client}, $buffer_length,0);');
			$list->remove($client);
			my_log($usernames{$client}." disconnected 'cause of $sig");
			delete $userhashes{$client};
			delete $usernames{$client};
			delete $userbuffers{$client};
			delete $usertails{$client};
			delete $userprivtails{$client};
			delete $contypes{$client};
			delete $user_incbuffers{$client};
			delete $wait_counters{$client};
			delete $cycles_on_connect{$client};
			delete $users_during_connections{$client};
			close($client);
			next; 	
		}
		close($vc_daemon);
		my_log("SigChecker: SIG$sig");
		if ($engine eq "mysql" || $long_life_data_engine eq "mysql") {
			$dbh->disconnect; 
		}
		if (substr($sig,0,5) eq "DBD::") {
			my_log("db problem, restart");
			$inter = 1;
			die "restarter\n";
		}
		die "sigger\n";
	}
}

sub restarter {
	my $sig = shift;

	foreach $client ($list->can_write(0.01)) {
		$userbuffers{$client}.= $lang{"server_restarting"}." <script>window.setTimeout('location.reload()',6000);</script>";
		my $buffer_length = length($userbuffers{$client});
		eval('$bytewriten = syswrite($client, $userbuffers{$client}, $buffer_length,0);');
		if ($charset ne "") {
			$charset_str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$charset."\">";
		}
		$list->remove($client);
		my_log($usernames{$client}." disconnected 'cause of HUP");
		delete $userhashes{$client};
		delete $usernames{$client};
		delete $userbuffers{$client};
		delete $usertails{$client};
		delete $user_incbuffers{$client};
		delete $wait_counters{$client};
		delete $cycles_on_connect{$client};
		delete $users_during_connections{$client};
		close($client2);
		next; 	
	}
	close($vc_daemon);
	$inter =1;
	sleep(4);
	if ($engine eq "mysql" || $long_life_data_engine eq "mysql") {
		$dbh->disconnect; 
	}
	die "restarter\n";
}
