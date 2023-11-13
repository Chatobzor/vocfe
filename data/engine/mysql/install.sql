#just me to remember :)
create table [PREFIX]rooms(id int primary key auto_increment, name varchar(100), topic text, design varchar(100), bot_name varchar(100));
create table [PREFIX]banlist(who varchar(30), until int);
create table [PREFIX]users(id int primary key auto_increment, nick varchar(30), passwd varchar(50), user_class smallint, canon_nick varchar(50), user_info text, new_mails smallint, last_visit int);
create table [PREFIX]board(id int primary key auto_increment, user_id int, status tinyint, from_nick varchar(30), from_uid int, subject varchar(255), body text, at_date int);
create table [PREFIX]who (user_name char(30), session char(32) not null, time int, sex tinyint, photo char(30), user_id int, tail_id int, remote_addr char(15), user_status int, last_action int, room int, ignor text, canon_nick char(60), chat_type char(10), user_lang char(10), design char(20), unique (session), index(user_name, canon_nick));
create table [PREFIX]messages (id int primary key auto_increment, room int, time int, from_nick char(100), to_nick char(32), body text);
create table [PREFIX]robotspeak(phrase char(250), answer text, prob int);
create table [PREFIX]impro(time int, id varchar(32), code int);
create table [PREFIX]regmail(time int, nickname varchar(100), password varchar(100), email varchar(200), regkey varchar(32), canon_view varchar(100));



NEW:
#usually it's 32 --md5 + 3 for identificator, like "un|"
#don't think somebody has nicknames longer than 32,
#in fact in mysql engine it has max length 30.
create table [PREFIX]banlist(who varchar(35), until int);

#registration_mail -- to check number of registrations at one mail
create table [PREFIX]users(id int primary key auto_increment, nick varchar(30), passwd varchar(50), user_class smallint, canon_nick varchar(50), user_info text, new_mails smallint, last_visit int, registration_mail varchar(100));


create table [PREFIX]rooms(id int primary key auto_increment, 
name varchar(100), 
topic text, 
design varchar(100), 
bot_name varchar(100));


create table [PREFIX]messages (id int primary key auto_increment, 
room int, 
time int, 
fromnick text, 
fromwotags char(100),
fromsession char(32),
fromid int,
fromavatar char(255),
tonick char(32), 
tosession char(32),
toid int,
body text);

//the same as 'MESSAGES'!
create table [PREFIX]premoder(
id int primary key auto_increment, 
room int, 
time int, 
fromnick text, 
fromwotags char(100),
fromsession char(32),
fromid int,
fromavatar char(255),
tonick char(32), 
tosession char(32),
toid int,
body text);


create table [PREFIX]who (
user_name char(30), 
session char(32) not null, 
time int, 
sex tinyint, 
photo char(30), 
user_id int, 
tail_id int, 
remote_addr char(15), 
user_status int, 
last_action int, 
room int, 
ignor text, 
canon_nick char(60), 
chat_type char(10), 
user_lang char(10), 
htmlnick text,
priv_tailid int,
cookie char(32),
browserhash char(32),
user_class int,
design char(20), 
unique (session), 
index(user_name, canon_nick));

create table [PREFIX]rooms(
id int primary key auto_increment, 
name varchar(100), 
topic text, 
design varchar(100), 
bot_name varchar(100)
creator varchar(30),
allowed_users text,
allow_pics int,
premoder int,
lastaction int

);


create table [PREFIX]password_reminder(
userid int,
nick char(30),
code char(32),
creation_time int);

