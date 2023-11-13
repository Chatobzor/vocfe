/************************************************************
*                 VOC++ chat daemon                         *
*               version 0.9 revision 1.6.4                  *
*          (c)2004-2005 by CREATIFF Design                  *
*              support@creatiff.com.ua                      *
*            http://vocplus.creatiff.com.ua                 *
*                                                           *
*                     Original                              *
*                 Voodoo chat daemon                        *
*                 file: daemon.cpp                          *
*            (c) 2003-04 by Vlad Vostrykh                   *
*                voodoo@vochat.com                          *
*                http://vochat.com/                         *
*                                                           *
*                 QPL ver1 License                          *
*           See voc/LICENSE file for details                *
*                                                           *
*                                                           *
************************************************************/
#define _VOC_VERSION_ "VOC++ 0.9 rev 1.6.4 Business Special Edition / HighSpeed Edition (1.0 RC1 original)"
//#define SUPPORT_MYSQL 1
#define SUPPORT_SHARED_MEMORY 1

//#define SUPPORT_MOD_VOC 1

#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <errno.h>
#include <sys/types.h>
#include <unistd.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <sys/socket.h>
#include <sys/file.h>
#include <fcntl.h>
#include <cstring>
#include <math.h>
#include <string.h>
//to avoid error with timeval structure
#include <sys/time.h>
#include <time.h>
#include <signal.h>

//sockaddr_un
#include <sys/un.h>
#include <sys/uio.h>

//chmod on FreeBSD
#include <sys/stat.h>

#if defined(__CYGWIN__)
        #include <sys/msg.h>
        #include <sys/ipc.h>
        #include <sys/shm.h>
        #include <sys/sem.h>
                #include <c-client/flockcyg.h>
                //fake to strike back against flock's, without native cygwin support
                int flocksim (int fd,int operation) { return 0; }
#endif
//statistic:
#include <sys/resource.h>

#ifdef SUPPORT_SHARED_MEMORY
        #include <sys/ipc.h>
        #include <sys/shm.h>
        #include <sys/sem.h>

#define SHM_MAX_USAGE 3         
                int php_sem_get(long key);
                int php_sem_aquire(int semid);
                int php_sem_release(int semid);
                int php_sem_status(int semid);
                int php_sysvsem_semop(int semid, int acquire);
                int regular_sem_get(long key);
                int php_get_usage(int semid);
                int php_unlock_sem(int semid);
                int php_get_val(int semid);
                int php_check_sem(int semid);
#endif

//VOC++ specific
#include <locale.h>
int clearFlag;
#define CLEAR_TIME                0
#define CLEAR_NICKNAME            1
#define CLEAR_AUTHOR              2
#define CLEAR_TYPE                3
#define CLEAR_TOTAL               4

#define GENDER_BOY                1
#define GENDER_GIRL               2
#define GENDER_THEY               3
#define CST_SHAMAN                1
char        ADMINZ_PRIVATE[50];
char        GIRLS_PRIVATE[50];
char        BOYS_PRIVATE[50];
char        THEY_PRIVATE[50];
char        ALL_PRIVATE[50];
char        SHAMAN_PRIVATE[50];
char        CLAN_PRIVATE[50];
char        w_roz_clear_pub_adm[255];
char        zeroBlk[2];
char        daemon_port_str[255];
char        daemon_host[255];
char        locale_str[255];
//end VOC++ specific

#define ENGINE_FILES 0
#define ENGINE_SHM 1
#define ENGINE_MYSQL 2
#define ENGINE_HIGHSPEED 3

#define SYSVSEM_SEM             0
#define SYSVSEM_USAGE   1
#define SYSVSEM_SETVAL  2

//show types, all messages:private:normal
#define ST_ALL 0
#define ST_NORMAL 1
#define ST_PRIVATE 2

#define USER_NICKNAME 0
#define USER_SESSION 1
#define USER_TIME 2
#define USER_GENDER 3
#define USER_AVATAR 4
#define USER_REGID 5
#define USER_TAILID 6
#define USER_IP 7
#define USER_STATUS 8
#define USER_LASTSAYTIME 9
#define USER_ROOM 10
#define USER_IGNORLIST 11
#define USER_CANONNICK 12
#define USER_CHATTYPE 13
#define USER_LANG 14
#define USER_HTMLNICK 15
#define USER_PRIVTAILID 16
#define USER_COOKIE 17
#define USER_BROWSERHASH 18
#define USER_CLASS 19
#define USER_SKIN 20
//VOC++ specific
#define USER_INVISIBLE 21
#define USER_SILENCE 22
#define USER_SILENCE_START 23
#define USER_FILTER 24
#define USER_CUSTOMCLASS 25
#define USER_CLANID 26
#define USER_REDUCETRAFFIC 27
#define USER_REGISTERED 28 
#define USER_MEMBER 29
#define USER_VOCLOVE_LOGGED 31
#define USER_CHAT_LOGGED 32
//end VOC++ specific
#define USER_TOTALFIELDS 33

#define MESG_ID 0
#define MESG_ROOM 1
#define MESG_TIME 2
#define MESG_FROM 3
#define MESG_FROMWOTAGS 4
#define MESG_FROMSESSION 5
#define MESG_FROMID 6
#define MESG_FROMAVATAR 7
#define MESG_TO 8
#define MESG_TOSESSION 9
#define MESG_TOID 10
#define MESG_BODY 11
//VOC++ specific
#define MESG_CLANID 12
//end VOC++ specific
#define MESG_TOTALFIELDS 13

#define ROOM_ID 0
#define ROOM_TITLE 1
#define ROOM_TOPIC 2
#define ROOM_DESIGN 3
#define ROOM_BOT 4
#define ROOM_CREATOR 5
#define ROOM_ALLOWEDUSERS 6
#define ROOM_ALLOWPICS 7
#define ROOM_PREMODER 8
#define ROOM_LASTACTION 9; 
//VOC++ specific
#define ROOM_CLUBONLY 10
#define ROOM_PASSWORD 11
#define ROOM_JAIL  12
#define ROOM_POINTS 13

//end VOC++ specific
#define ROOM_TOTALFIELDS 14

#define ONMODVOC 2

#if !defined(__CYGWIN__)
void strlwr(char *s) {
    while(*s) {
        *s = tolower(*s);
        s++;
    }
}
#endif


void sigger(int sig);
/* global data from config.voc */
int engine = ENGINE_FILES;
int ld_engine = ENGINE_FILES;
char *shm_users;
char *shm_mess;
//shared memory keys
int shm_users_id = -1;
int shm_mess_id = -1;
int shm_usize = 500*1024;
int shm_msize = 500*1024;
long sem_users;
long sem_mess;

int max_users, max_messages,history_size, daemon_port, daemon_type=0;
//dinamically with new?
char data_path[255];
char message_format[1024];
char message_fromme[1024];
char private_message[1024];
char private_message_fromme[1024];
char private_hidden[1024];
char daemon_listen[50];
char file_path[255];
char nick_highlight_before[128];
char nick_highlight_after[128];
char str_w_n_before[128];
char str_w_n_after[128];
char charset[32];
char chat_url[1024];
char language[15];
char w_no_user[255];
char w_server_restarting[1024];
char w_whisper_to[255];
char w_only_one_tail[1024];
char modvoc_socket[256];

#ifdef SUPPORT_MYSQL
        #include <mysql.h>
        MYSQL mysql;
        MYSQL_RES *mysql_result;
        MYSQL_ROW mysql_row;
        unsigned int mysql_num_f;
        char mysql_server[255];
        char mysql_user[255];
        char mysql_password[255];
        char mysql_db[255];
        char mysql_table_prefix[255];
        char *get_rooms_query;
        int get_rooms_query_length = 0;
        char *get_messages_query;
        int get_messages_query_length = 0;
        char *get_users_query;
        int get_users_query_length = 0;
#endif

/* end of data from config.voc*/

//to use everywhere as temporary buffer.
char tb[16384];
char log_time[24];
/*file for logging */
FILE *log_file;
char log_string[1024];
void my_log(char* message) {
        if(log_file == NULL) return;
        //char time_to_out[24];
        time_t mtm;
        mtm = time(NULL);
        strftime(log_time,(size_t)23,"%Y-%m-%d %T> ",localtime(&mtm));
        fputs(log_time,log_file);
        fputs(message, log_file);
        //fputc('\n', log_file);
        fflush(log_file);
}

//statistic:
int users_in_chat = 0, active_connections = 0;
char start_time[24];
/* some functions*/

//must be improved!
//i need some checkers that I've got correct data!

//added by DareDEVIL from CREATIFF -- VOC++
#ifdef SUPPORT_MOD_VOC
//end of DD addon

#define MAXLINE 259
static struct cmsghdr *cmptr = NULL;        /* malloc'ed first time */
#define CONTROLLEN (sizeof(struct cmsghdr) + sizeof(int))
int recv_fd (int servfd, char *query)
{
        int newfd, nread, status;
        char buf[MAXLINE];
        struct iovec iov[1];
        struct msghdr msg;

        status = -1;
        for (;;)
        {
                iov[0].iov_base = buf;
                iov[0].iov_len = sizeof (buf);

                iov[1].iov_base = query;
                iov[1].iov_len = sizeof(query);

                msg.msg_iov = iov;
                msg.msg_iovlen = 2;

                msg.msg_name = NULL;
                msg.msg_namelen = 0;
                if (cmptr == NULL && (cmptr = (cmsghdr *) malloc (CONTROLLEN)) == NULL)
                        return (-1);
                msg.msg_control = (caddr_t) cmptr;
                msg.msg_controllen = CONTROLLEN;
                if ((nread = recvmsg (servfd, &msg, 0)) < 0)
                        my_log ("recvmsg error\n");
                else if (nread == 0) {
                        my_log ("connection closed by server\n");
                        return (-1);
                }
                strcpy(query, "");
                strncpy(query, &buf[2], (nread-2 > 255)? 255: nread-2);
                query[(nread-2 > 255)? 255: nread-2] = 0;
                newfd = *(int *) CMSG_DATA (cmptr);
                return (newfd);
        }
}
#endif

bool string2int(char* digit, int& result) {
        if (digit == NULL) result = 0;
        else {
                result = 0;
                int minus = 0;
                while ((*digit >= '0' && *digit <='9') || *digit == '-') {
                        if (*digit == '-') minus = 1;
                        else result = (result * 10) + (*digit - '0');
                        digit++;
                }
                if (minus == 1) result = result*(-1);
                   //--- Check that there were no non-digits at end.
                if (*digit != 0) {
                        return false;
                }
        }
        return true;
}

int string2int(char* digit) {
        int result = 0;
        if (digit == NULL) result = 0;
        else{
                int minus = 0;
                while ((*digit >= '0' && *digit <='9') || *digit == '-') {
                        if (*digit == '-') minus = 1;
                        else result = (result * 10) + (*digit - '0');
                        digit++;
                }
                if (minus == 1) result = result*(-1);
        //--- Check that there were no non-digits at end.
                if (*digit != 0) {
                        return 0;
                }
        }
        return result;
}

char *strrepl(char *Str, size_t BufSiz, char *OldStr, char *NewStr) {
        int OldLen, NewLen;
        char *p, *q;
        //if(NULL == (p = strstr(Str, OldStr)))
        //return Str;
        p = strstr(Str, OldStr);
        q = p;
        //just additional checker, to avoid unfin. loop.
        int cycles = 0;
        while (p!=NULL && (cycles <1500)) {
                OldLen = strlen(OldStr);
                NewLen = strlen(NewStr);
                if ((strlen(Str) + NewLen - OldLen + 1) > BufSiz)
                        return NULL;
                memmove(q = p+NewLen, p+OldLen, strlen(p+OldLen)+1);
                memcpy(p, NewStr, NewLen);
                p = p+NewLen;
                p = strstr(p, OldStr);
                cycles++;
        }
        return q;
}

//added for VOC++ 1.2
//this function kindly cuts off all img-like tags
void strip_images(char *str) {
        int                oldLen, i, IsLTFound;
        char        *p;

        while((p = strstr(str, "<img"))) {
                oldLen                = strlen(str);
                IsLTFound        = 0;

                for(i = p - str; i < oldLen; i++) {
                        if(str[i] == '>') { IsLTFound = 1; break; }
                }
                //if > found
                if(IsLTFound) {
                        if(oldLen - i -1 > 0) {
                                //some shift?
                                memmove(p, &str[i + 1], oldLen - i);
                                str[oldLen - (i - (p - str))] = 0;
                        }
                        else { p[0] = 0; break;}
                }
                else {
                        //incomplete tag, must reduce it
                        p[0] = ' ';
                }
        }
}

char tba[16384];

void addslashes(char *cvt_buf) {
int i, len, nLen = 0;

len = strlen(cvt_buf);

memset(tba, 0, 16383);

for(i=0; i < len; i++) {
        if(cvt_buf[i] == '\'') {
           tba[nLen] ='\\';
           tba[nLen+1] ='\'';
           nLen += 2;
         }
         else if(cvt_buf[i] == '\\') {
           tba[nLen] ='\\';
           tba[nLen+1] ='\\';
           nLen += 2; 
         }
         else if(cvt_buf[i] == '/') {
           tba[nLen] ='\\';
           tba[nLen+1] ='/';
           nLen += 2; 
         }
         else {
          tba[nLen] = cvt_buf[i];
          nLen++;
         }
         if(nLen > 16380) break;
}
  tba[nLen] = 0;
  strcpy(cvt_buf, tba);
}
//end of VOC++ specific


char *get_next_token2(int token, char *str) {
        if (strlen(str) == 0) return NULL;
        char *next_pos = strchr(str, token);
        /*
                snprintf(log_string,sizeof(log_string),"found token: >%s<\n",next_pos);
                my_log(log_string);
        */
        if (next_pos == NULL) next_pos = str+strlen(str);
        if ((next_pos - str) == 0)
                str++;
        else
                if (next_pos>=str+strlen(str)) return NULL;
                else
                        str = next_pos + 1;

        *next_pos = 0;
        /*
                snprintf(log_string,sizeof(log_string),"inc string now: >%s<\n",str);
                my_log(log_string);
        */
        return str;
}

char *get_next_token(char *str, int token, char *result, size_t max_l) {
        strcpy(result,"");
        if (strlen(str) == 0) return NULL;
        char *next_pos = strchr(str, token);
        if (next_pos == NULL) next_pos = str+strlen(str);
        if ((next_pos - str) == 0) {
                str++;
        }else {
                //size_t to_copy = ((next_pos - str) > (int)(sizeof(result) - (size_t)1))?
                //        (result - (size_t)1):
                //        (next_pos - str);
                size_t to_copy = next_pos - str;
                if (to_copy > max_l - 1) to_copy = max_l-1;
                strncpy(result,str, to_copy);
                result[to_copy] = '\0';
                str = next_pos+1;
        }
        return str;
}

char *get_next_token(char *str, int token, int &result) {
        result = 0;
        if (strlen(str) == 0) return NULL;
        char *next_pos = strchr(str, token);
        if (next_pos == NULL) next_pos = str+strlen(str);
        if ((next_pos - str) == 0) {
                str++;
        }else {
                size_t to_copy = next_pos - str;
                char *pch = new char[to_copy+1];
                strncpy(pch,str, to_copy);
                pch[to_copy] = '\0';
                string2int(pch, result);
                delete(pch);
                str = next_pos+1;
        }
        return str;
}

/* end of 'some functions */
// unsigned long long= 18446744073709551616 (min 0 - max 18446744073709551615 )
// = 1 073 741 824 GBytes, should be enough :)
class Statistic {
        private:
                unsigned long long inc_bytes;
                unsigned long long out_bytes;
                time_t t_start_time;
                char start_time[23];
                long pid;
                unsigned long total_requests;
                int active_connections;

                long double get_bytes (long double val, char* type) {
                        if (val > 1024) {
                                val = val/1024;
                                strcpy(type, "KBytes");
                                if (val > 1024) {
                                        val = val/1024;
                                        strcpy(type, "MBytes");
                                        if (val > 1024) {
                                                val = val/1024;
                                                strcpy(type, "GBytes");
                                        }
                                }
                        }
                        return val;
                }


        public:
                Statistic() {
                        t_start_time = time(NULL);
                        strftime(start_time,(size_t)23,"%Y-%m-%d %T  ",localtime(&t_start_time));
                        this->pid = getpid();
                        this->inc_bytes = 0;
                        this->out_bytes = 0;
                        this->total_requests = 0;
                        this->active_connections = 0;
                }

                void add_inc(long bytes) {
                        inc_bytes += bytes;
                }
                unsigned long long get_inc_bytes() { return inc_bytes; }

                void add_out(long bytes) {
                        out_bytes += bytes;
                }

                unsigned long long get_out_bytes() { return out_bytes; }

                void add_req() {
                        this->total_requests++;
                }

                void add_con() {
                        this->active_connections++;
                }

                void remove_con() {
                        this->active_connections--;
                }

                void stat(char *out, int maxlength) {
                        struct rusage usage;
                        getrusage (RUSAGE_SELF, &usage);
                        time_t t_current_time = time(NULL);

                        long days =0, left = 0;
                        int hours = 0, minutes = 0, seconds = 0;
                        long work_time = (t_current_time - t_start_time);
                        days = (long)floor(work_time/86400);
                        left = work_time - days*86400;
                        hours = (int)floor(left/3600);
                        left = left - hours*3600;
                        minutes = (int)floor(left/60);
                        seconds = left - minutes * 60;

                        double cpuusage = (double)usage.ru_utime.tv_sec+usage.ru_stime.tv_sec+(usage.ru_utime.tv_usec+usage.ru_stime.tv_usec)/1000000.0;

                        double avr_cpu = 0.0, avr_requests = 0.0;
                        long double avr_inb = 0.0, avr_outb = 0.0, avr_inb_perreq = 0.0, avr_outb_perreq = 0.0;


                        char avr_inb_t[7],avr_outb_t[7], ib_t[7], ob_t[7], avr_inb_perreq_t[7], avr_outb_perreq_t[7];
                        strcpy(avr_inb_t, "bytes");
                        strcpy(avr_outb_t, "bytes");
                        strcpy(ib_t, "bytes");
                        strcpy(ob_t, "bytes");
                        strcpy(avr_inb_perreq_t, "bytes");
                        strcpy(avr_outb_perreq_t, "bytes");

                        long double ib = get_bytes((long double) inc_bytes, ib_t);
                        long double ob = get_bytes((long double) out_bytes, ob_t);

                        if (work_time >0) {
                                //100 -- for percents :)
                                avr_cpu = cpuusage/work_time * 100;
                                avr_requests = (double)this->total_requests/work_time;
                                avr_inb = get_bytes(this->inc_bytes / (double)work_time, avr_inb_t);
                                avr_outb = get_bytes(this->out_bytes / (double)work_time, avr_outb_t);
                        }

                        if (this->total_requests > 0)
                                avr_inb_perreq = get_bytes(this->inc_bytes /(double) this->total_requests, avr_inb_perreq_t);

                        //currenet request is not yet counted in out_bytes because we didn't send anything at this point
                        if (this->total_requests > 1)
                                avr_outb_perreq = get_bytes(this->out_bytes /(double) (this->total_requests-1), avr_outb_perreq_t);

                        snprintf(out, maxlength,
                                        "<html><head><style>body, td, a, a.visited, a.hover {color:black; font-size: 12px; font-family: Verdana; text-decoration: none;}</style><title>VOC++ daemon statistic (Voodoo Chat original)</title></head>\n<body bgcolor=\"white\"><b><a href=\"http://vocplus.creatiff.com.ua\">VOC++</a>. daemon statistic</b><table><tr><td>Daemon is running with pid:</td><td>%ld</td><td rowspan=\"9\">&nbsp;&nbsp;&nbsp;</td><td colspan=\"2\"></td></tr><tr><td>Started at:</td><td>%s</td><td>Uptime:</td><td>%ld days, %d hours, %d minutes, %d seconds</td></tr><tr><td>Uses memory:</td><td>%ld / %ld / %ld</td><td colspan=\"2\"></td></tr><tr><td valign=\"top\">Has used CPU time:</td><td>%ld.%06ld sec user,<br>%ld.%06ld sec system</td><td valign=\"top\">Average:</td><td valign=\"top\">%.3f%% CPU</td></tr><tr><td>total requests:</td><td>%lu</td><td>Average:</td><td>%.3f requests per second</td></tr><tr><td>Bytes recieved (IN):</td><td>%.2Lf %s</td><td>Average:</td><td>%.2Lf  %s / sec, %.2Lf %s / request</td></tr><tr><td>Bytes sent (OUT):</td><td>%.2Lf %s</td><td>Average:</td><td>%.2Lf %s / sec, %.2Lf %s / request</td></tr><tr><td>Now in the chat:</td><td>%d</td><td colspan=\"2\"></td></tr><tr><td>Number of active connections to the daemon:</td><td>%d</td><td colspan=\"2\"></td></tr></table><hr><small>C++ voc-daemon version %s, &copy; 2003-04 by Vlad Vostrykh, http://vochat.com</small></body></html>",
                                        this->pid, start_time,
                                        days, hours, minutes, seconds,
                                        usage.ru_ixrss, usage.ru_idrss, usage.ru_isrss,
                                        usage.ru_utime.tv_sec, usage.ru_utime.tv_usec,
                                        usage.ru_stime.tv_sec, usage.ru_stime.tv_usec,
                                        avr_cpu,
                                        this->total_requests, avr_requests,
                                        ib, ib_t, avr_inb, avr_inb_t, avr_inb_perreq, avr_inb_perreq_t,
                                        ob, ob_t, avr_outb, avr_outb_t, avr_outb_perreq, avr_outb_perreq_t,
                                        users_in_chat,
                                        this->active_connections,
                                        _VOC_VERSION_);
                }
};

Statistic *mystat;

class DesignHeader{
        public:
        char* design_name;
        char* design_header;
        DesignHeader* prev;
        DesignHeader* next;

        DesignHeader(char* name, char* header) {
                int l = strlen(name);
                design_name = new char[l+1];
                strcpy(design_name, name);
                design_name[l] = 0;
                l = strlen(header);
                design_header = new char[l+1];
                strcpy(design_header, header);
                design_header[l] = 0;
                prev = NULL;
                next = NULL;
        }

        ~DesignHeader() {
                delete[] design_name;
                delete[] design_header;
        }
};

class Room {
        public:
        int room_id;
        char* name;
        char* topic;
        char* design;
        //bot?
        Room* next;
        Room* prev;

        Room(int id, char* n, char* t, char* d) {
                room_id = id;
                name = new char[strlen(n)+1];
                strcpy(name,n);
                topic = new char[strlen(t)+1];
                strcpy(topic,t);
                design = new char[strlen(d)+1];
                strcpy(design,d);
        }

        ~Room() {
                delete[] name;
                delete[] topic;
                delete[] design;
        }
};

class User {
        public:
        //for the daemon I don't need all fields from the list, so I now left only used by daemon
        char* nickname;
        char* session;
        //avatar
        char* avatar;
        int tail_id;
        int priv_tail_id;
        char* browser_hash;
        int room_id;
        char* ignor;
        char* user_lang;
        char* chat_design;
        User* next;
        User* prev;

        //VOC++ specific
        int nClass;
        int nGender;
        int nFilter;
        int nCustomClass;
        int nClanID;
        int nReduceTraffic;

        User (        char* nickname,
                        char* session,
                        char* avatar,
                        int tail_id,
                        int priv_tail_id,
                        char* browser_hash,
                        int room_id,
                        char* ignor,
                        char* user_lang,
                        char* chat_design,
                        char *pNClass,
                        char *pNGender,
                        char *pNFilter,
                        char *pCClass,
                        char *pnClanID,
                        char *pnReduceTraffic) {
        //end of VOC++ specific
                if (nickname!=NULL) {
                        this->nickname = new char[strlen(nickname)+1];
                        strcpy(this->nickname, nickname);
                }else {this->nickname = new char[1]; this->nickname[0] = 0;}
                if (session!=NULL) {
                        this->session = new char[strlen(session)+1];
                        strcpy(this->session, session);
                }else {this->session = new char[1]; this->session[0] = 0;}
                if (avatar!=NULL) {
                        this->avatar = new char[strlen(avatar)+1];
                        strcpy(this->avatar, avatar);
                }else {this->avatar = new char[1]; this->avatar[0] = 0;}
                this->tail_id = tail_id;
                this->priv_tail_id = priv_tail_id;
                if (browser_hash!=NULL) {
                        this->browser_hash = new char[strlen(browser_hash)+1];
                        strcpy(this->browser_hash, browser_hash);
                }else {this->browser_hash = new char[1]; this->browser_hash[0] = 0;}
                this->room_id = room_id;
                if (ignor!=NULL) {
                        this->ignor = new char[strlen(ignor)+1];
                        strcpy(this->ignor, ignor);
                }else {this->ignor = new char[1]; this->ignor[0] = 0;}
                if (user_lang!=NULL) {
                        this->user_lang = new char[strlen(user_lang)+1];
                        strcpy(this->user_lang, user_lang);
                }else {this->user_lang = new char[1]; this->user_lang[0] = 0;}
                if (chat_design!=NULL) {
                        this->chat_design = new char[strlen(chat_design)+1];
                        strcpy(this->chat_design, chat_design);
                }else {this->chat_design = new char[1]; this->chat_design[0] = 0;}

                //VOC++ specific
                                if(strlen(pNClass)) {
                                        if(pNClass[0] != '0') this->nClass = 1;
                                        else this->nClass = 0;
                                }
                                else this->nClass = 0;
                                        if(strlen(pNGender)) {
                                        this->nGender = atoi(pNGender);
                                        if(this->nGender < 1 || this->nGender > 3) this->nGender = 3;
                                }
                                else this->nGender = 3;

                                if(strlen(pNFilter)) {
                                        this->nFilter = atoi(pNFilter);
                                        if(this->nFilter < 0 || this->nFilter > 1) this->nFilter = 0;
                                }
                                else this->nFilter = 0;

                                if(strlen(pCClass)) {
                                        this->nCustomClass = atoi(pCClass);
                                        if(this->nCustomClass < 0) this->nCustomClass = 0;
                                }
                                else this->nCustomClass = 0;

                                if(strlen(pnClanID)) {
                                        this->nClanID = atoi(pnClanID);
                                        if(this->nClanID < 0) this->nClanID = 0;
                                }
                                else this->nClanID = 0;

                                if(strlen(pnReduceTraffic)) {
                                        this->nReduceTraffic = atoi(pnReduceTraffic);
                                        if(this->nReduceTraffic < 0 || this->nReduceTraffic > 1) this->nReduceTraffic  = 0;
                                }
                                else this->nReduceTraffic = 0;

                // end of VOC++ specific
        }

        ~User() {
                delete[] nickname;
                delete[] session;
                delete[] avatar;
                delete[] browser_hash;
                delete[] ignor;
                delete[] user_lang;
                delete[] chat_design;
        }
};

typedef struct {
        int id;
        int room_id;
        time_t time;
        /* hope it will enough even for colorized nicks.
                another way is to allocate memory dinamically? (slower i think)
        */
        char from[2048];
        char from_wotags[200];
        char from_session[33];
        char from_avatar[255];
        int from_id;
        char to[100];
        char to_session[33];
        int to_id;
        int clan_id;
        char body[16384];
} Message;

#define CONNECTING 0
#define ONLINE 1
class Client {
        int client, status, connecting_loop, room_id, exists_in_list, count_wo_mesg, active;
        //char out_buffer[16384];
        //char inc_buffer[16384];
        //char session[33];
        char remote_ip[16];

        char* out_buffer;
        char* session;
        char* nickname;
        char* ignor_list;
        char* highlighted_nick;
        //show_type -- all messages, normal messages of just private messages;


        public:
        char* inc_buffer;
        int tail_id, priv_tail_id, show_type;
        Client *prev,*next;/* for list*/

        Client(int socket_id, char *r_ip) {
                active = 1;
                client = socket_id;
                strcpy(remote_ip, r_ip);
                status = CONNECTING;
                connecting_loop = 0;
                exists_in_list = 0;
                count_wo_mesg = 0;
                out_buffer = new char[16384];
                inc_buffer = new char[16384];
                session = new char[33];
                nickname = new char[100];
                highlighted_nick = new char[100+strlen(nick_highlight_before)+strlen(nick_highlight_after)];
                ignor_list = new char[1];
                ignor_list[0] = 0;
                strcpy(nickname,"...");
                strcpy(highlighted_nick,"...");
                strcpy(session,"");

                sprintf(out_buffer,"HTTP/1.0 200 Ok Welcome to VOC\015\012Server: VOC++ Voodoo chat Extension daemon ver cpp%s\015\012Content-type: text/html\015\012Expires: Mon, 08 Apr 1976 19:30:00 GMT+3\015\012Connection: close\015\012Keep-Alive: max=0\015\012Cache-Control: no-store, no-cache, must-revalidate\015\012Cache-Control: post-check=0, pre-check=0\015\012Pragma: no-cache\015\012\015\012", _VOC_VERSION_);
                strcpy(inc_buffer,"");
        }
        ~Client() {
                if (inc_buffer) delete[] inc_buffer;
                delete[] out_buffer;
                delete[] session;
                delete[] nickname;
                delete[] highlighted_nick;
                delete[] ignor_list;
                //delete prev;
                //delete next;
        }

        int get_socket_id() {
                return client;
        }

        int get_status() {
                return status;
        }

        int get_tail_id() {
                return tail_id;
        }

        char *get_nickname() {
                return nickname;
        }

        char *get_ip() {
                return remote_ip;
        }

        void set_exists_in_list(int e) {
                exists_in_list = e;
        }

        int get_exists_in_list() {
                return exists_in_list;
        }

        int is_active() {
                return active;
        }

        void set_active(int a) {
                active = a;
        }

        void set_ignor_list(char* to_ignor) {
                delete[] this->ignor_list;
                if (to_ignor!=NULL) {
                        this->ignor_list = new char[strlen(to_ignor)+1];
                        strcpy(ignor_list, to_ignor);
                        ignor_list[strlen(to_ignor)] = 0;
                } else {ignor_list = new char[1]; ignor_list[0] = 0;}
        }

        int check_connection_loop() {
                if (this->status == ONLINE) {return 1;this->count_wo_mesg++;}
                this->connecting_loop++;
                if (this->connecting_loop > 30) {
                        return (-1);
                }
                return 1;
        }

        int form_message(Message& mesg, char* to_out,User* &firstU, User* &lastU) {
                //VOC++ specific
                int i;
                User *currentU;
                int user_class = 0, user_gender = 3, user_filter = 0, user_customclass = 0, user_clanid = 0, user_reducetraffic = 0;
                //End VOC++ specific

                strcpy(to_out,"");
                char avatar[1000];
                int showed = 0;
                if (mesg.room_id == room_id || 
                                    mesg.room_id == -1 || 
                                        strcmp(mesg.to_session, session)==0 || (strcmp(mesg.to, nickname)==0 && mesg.to_id >0)) {
                        tm *mesg_time;
                        char time_to_out[3];
                        char private_to[100];
                        strcpy(private_to, "");

                        //char to_out[16384];
                        /* check for ignor */
                        int ignored = 0;
                        char *ignor;
                        ignor = ignor_list;

                        //VOC++ specific whoami?
                        currentU = firstU;
                        while(currentU != NULL) {
                           if (strcmp(currentU->session,session) == 0) {
                                        user_class                        = currentU->nClass;
                                        user_filter                        = currentU->nFilter;
                                        user_gender                        = currentU->nGender;
                                        user_customclass        = currentU->nCustomClass;
                                        user_clanid                        = currentU->nClanID;
                                        user_reducetraffic        = currentU->nReduceTraffic;
                                        break;
                           }
                                currentU = currentU->next;
                        }

                        //End VOC++ specific

                        if (strlen(mesg.from_wotags)>0) do {
                                ignor = strstr(ignor, mesg.from_wotags);
                                if (ignor) {
                                        ignor = ignor+strlen(mesg.from_wotags);
                                        if (*ignor == ',' || *ignor == '\0') {
                                                ignored = 1;
                                                break;
                                        }
                                }
                        } while (ignor!=NULL);
                        if (ignored == 0) {

                                if (strlen(mesg.to)>0) {
                                        //private message
                                        //if (show_type == ST_ALL || show_type == ST_PRIVATE) {
                                        if ( strcmp(mesg.from_session, session) == 0 || (strcmp(mesg.from_wotags, nickname) == 0 && mesg.from_id > 0)) {
                                                if (show_type == ST_ALL || show_type == ST_PRIVATE)
                                                        strcpy(to_out, private_message_fromme);
                                        } else if (strcmp(mesg.to_session, session)==0 || (strcmp(mesg.to, nickname)==0 && mesg.to_id >0)){
                                                if (show_type == ST_ALL || show_type == ST_PRIVATE)
                                                        strcpy (to_out, private_message);
                                        }
                                        //VOC++ specific
                                        //DD patch
                                        else if (strcmp(mesg.to, ADMINZ_PRIVATE)==0){
                                                if(user_class > 0) {
                                                        if (show_type == ST_ALL || show_type == ST_PRIVATE)  {
                                                                        strcpy (to_out, private_message);
                                                        }
                                                }
                            }
                                        else if (strcmp(mesg.to, ALL_PRIVATE)==0){
                                                if (show_type == ST_ALL || show_type == ST_PRIVATE)  {
                                                                  strcpy (to_out, private_message);
                                                }
                                }
                                        else if (strcmp(mesg.to, BOYS_PRIVATE)==0){
                                                if(user_gender == GENDER_BOY) {
                                                        if (show_type == ST_ALL || show_type == ST_PRIVATE)  {
                                                                                        strcpy (to_out, private_message);
                                                        }
                                                }
                                 }
                                         else if (strcmp(mesg.to, GIRLS_PRIVATE)==0){
                                                if(user_gender == GENDER_GIRL) {
                                                        if (show_type == ST_ALL || show_type == ST_PRIVATE)  {
                                                                                strcpy (to_out, private_message);
                                                        }
                                                }
                                 }
                                        else if (strcmp(mesg.to, THEY_PRIVATE)==0){
                                                if(user_gender == GENDER_THEY) {
                                                        if (show_type == ST_ALL || show_type == ST_PRIVATE)  {
                                                                                strcpy (to_out, private_message);
                                                        }
                                                }
                                }
                                        else if (strcmp(mesg.to, CLAN_PRIVATE)==0){
                                                if(user_clanid == mesg.clan_id && user_clanid > 0) {
                                                        if (show_type == ST_ALL || show_type == ST_PRIVATE)  {
                                                                                strcpy (to_out, private_message);
                                                        }
                                                }
                                }
                                        else if (strcmp(mesg.to, SHAMAN_PRIVATE)==0){
                                                if(user_customclass == CST_SHAMAN || user_class > 0) {
                                                        if (show_type == ST_ALL || show_type == ST_PRIVATE)  {
                                                                                strcpy (to_out, private_message);
                                                        }
                                                }
                                }
                                        //end VOC++ specific
                                        else {
                                                //'whisper to somebody'
                                                if (show_type == ST_ALL || show_type == ST_NORMAL)
                                                        strcpy(to_out,private_hidden);
                                        }
                                } else {
                                        //normal message
                                        if (show_type == ST_ALL || show_type == ST_NORMAL) {
                                                if (strcmp(mesg.from_wotags, nickname) == 0)
                                                        strcpy(to_out, message_fromme);
                                                else
                                                        strcpy(to_out, message_format);
                                        }
                                }
                                /*check for max length?*/

                                if (strlen(to_out)>0){
                                        //for gcc 3.2.1
                                        //mesg_time = localtime(&(time_t)mesg.time);
                                        mesg_time = localtime((const time_t *) &mesg.time);
                                        strftime(time_to_out,(size_t)sizeof(time_to_out),"%H",mesg_time);
                                        strrepl(to_out,16384,"[HOURS]",time_to_out);
                                        strftime(time_to_out,sizeof(time_to_out),"%M",mesg_time);
                                        strrepl(to_out,16384,"[MIN]",time_to_out);
                                        strftime(time_to_out,sizeof(time_to_out),"%S",mesg_time);
                                        strrepl(to_out,16384,"[SEC]",time_to_out);

                                        strrepl(to_out,16384,"[NICK]",mesg.from);
                                        strrepl(to_out,16384,"[NICK_WO_TAGS]", mesg.from_wotags);

                                        strrepl(to_out,16384,"[TO]", mesg.to);
                                        strrepl(to_out,16384,"[PRIVATE]",w_whisper_to);
                                        if (strlen(mesg.from_avatar)>3) {
                                                strcpy(avatar, "<img src=\"");
                                                strcat(avatar, chat_url);
                                                strcat(avatar, "photos/");
                                                strcat(avatar, mesg.from_avatar);
                                                strcat(avatar, "\">");
                                                strrepl(to_out, 16384, "[AVATAR]", avatar);
                                        } else {
                                                strrepl(to_out, 16384, "[AVATAR]", "");
                                        }


                                        char body[16384];

                                        //VOC++ specific
                                        char high_body[16384];
                                        char lwr_nick[16384];

                                        strcpy(high_body, mesg.body);
                                        strcpy(lwr_nick, nickname);

                                        strlwr(high_body);
                                        strlwr(lwr_nick);
                                        //end VOC++ specific

                                        strcpy(body, mesg.body);
                                        char* highlight;
                                        char* end_of_h;
                                        int found_nick = 0;
                                        int nick_length = strlen(nickname);
                                        int high_length = strlen(highlighted_nick);
                                        highlight = body;
                                        do {
                                                highlight = strstr(highlight, nickname);
                                                if (highlight) {
                                                        /* check for symbol after */
                                                        end_of_h  = highlight + strlen(nickname);
                                                        if (*end_of_h == '?' || *end_of_h == '&' || *end_of_h == ':' ||
                                                                *end_of_h == ',' || *end_of_h == ' ' || *end_of_h == '!' ||
                                                                *end_of_h == '\0') {
                                                                if ((strlen(body) + high_length - nick_length) < 16383) {
                                                                        memmove(highlight + high_length, highlight+nick_length, strlen(highlight)-nick_length+1);
                                                                        memcpy(highlight, highlighted_nick, high_length);
                                                                } else {
                                                                        break;
                                                                }
                                                                found_nick = 1;
                                                        }
                                                        highlight = highlight +high_length+1;
                                                }
                                        } while(highlight!=NULL);
                                        
                                        //VOC++ traffic reducer
                                        if(user_reducetraffic) {
                                           strip_images(body);
                                        }
                                        
                                        strrepl(to_out,16384,"[MESSAGE]",body);
                                        
                                        //highlight string if 1.found nick in the string;2.there ie enough space in buffer;3.it's not a private message

                                        // VOC++ specific filtering and group message checking
                                        if(user_filter && (show_type == ST_ALL || show_type == ST_NORMAL) && strlen(mesg.to) == 0) {
                                                if(strstr(high_body, lwr_nick) ||
                                                   strstr(mesg.from_wotags, nickname) ||
                                                   (user_gender == GENDER_BOY && strstr(body, BOYS_PRIVATE)) ||
                                                    (strstr(body, ALL_PRIVATE)) ||
                                                    (user_class > 0 && strstr(body, ADMINZ_PRIVATE)) ||
                                                    (user_customclass == CST_SHAMAN && strstr(body, SHAMAN_PRIVATE)) ||
                                                    (user_clanid == mesg.clan_id && user_clanid > 0 && strstr(body, CLAN_PRIVATE)) ||
                                                    (user_gender == GENDER_GIRL && strstr(body, GIRLS_PRIVATE)) ||
                                                    (user_gender == GENDER_THEY && strstr(body, THEY_PRIVATE)) ||
                                                    (strstr(mesg.to, nickname))
                                                   )
                                                   {
                                                                   showed = 1;
                                                   }
                                                   else {
                                                            strcpy(to_out, "<script>up();</script>\n");
                                                            return 1;
                                                   }
                                                                                        }
                                        else {
                                                if ((found_nick == 1 || strstr(high_body, lwr_nick)) && strlen(to_out)+strlen(str_w_n_before)+strlen(str_w_n_after) < 16383 && strlen(mesg.to)==0) {
                                                memmove(to_out + strlen(str_w_n_before), to_out, strlen(to_out)+1);
                                                memcpy(to_out, str_w_n_before, strlen(str_w_n_before));
                                                strcat(to_out, str_w_n_after);
                                                                                        } else {
                                                                                                if( ((user_gender == GENDER_BOY && strstr(body, BOYS_PRIVATE)) ||
                                                                                                        (strstr(body, ALL_PRIVATE)) ||
                                                    (user_class > 0 && strstr(body, ADMINZ_PRIVATE)) ||
                                                                                                        (user_gender == GENDER_GIRL && strstr(body, GIRLS_PRIVATE)) ||
                                                                                                        (user_customclass == CST_SHAMAN && strstr(body, SHAMAN_PRIVATE)) ||
                                                                                                        (user_clanid == mesg.clan_id && user_clanid > 0 && strstr(body, CLAN_PRIVATE)) ||
                                                                                                        (user_gender == GENDER_THEY && strstr(body, THEY_PRIVATE)))
                                                                                                        && strlen(to_out)+strlen(str_w_n_before)+strlen(str_w_n_after) < 16383 && strlen(mesg.to)==0) {
                                                                                                                        memmove(to_out + strlen(str_w_n_before), to_out, strlen(to_out)+1);
                                                                                                                                memcpy(to_out, str_w_n_before, strlen(str_w_n_before));
                                                                                                                                strcat(to_out, str_w_n_after);
                                                                                                        }
                                                                                                }

                                        }

            
                                       if(strcmp(mesg.from, "&CMD") == 0) {
                                          if(strlen(to_out) > 0) {
                                                   strcpy(to_out, "<script>");
                                                   strcat(to_out, mesg.body);
                                                   strcat(to_out, "</script>");
                                                  }
                                               }
                                        else {

                                                 if(show_type == ST_ALL) {

                                                 addslashes(to_out);
                                                 strcpy(high_body, to_out);
                                                 strcpy(to_out, "");

                                           if(strlen(mesg.to) > 0) {
                                              strcat(to_out,"<script>parent.AddMsgToPriv('");
                                                     }   else {
                                              strcat(to_out,"<script>parent.AddMsgToPublic('");
                                            }
                                            strcat(to_out, high_body);
                                            strcat(to_out, "','");
                                            strcat(to_out, mesg.from_wotags);
                                            
                                            if(strlen(mesg.to) > 0) {
                                              strcat(to_out, "','");
                                              strcat(to_out,mesg.to);
                                            }
                                            
                                            strcat(to_out, "');</script>");
                                            }
                                        }
                                        //end VOC++ specific

                                        strcat(to_out,"<script>up();</script><br>\015\012");
                                        showed = 1;
                                }
                        }//end of if ignored
                }
                return showed;
        }

        int process_messages(Message* messages, int& total_messages, int& new_messages, User* &firstU, User* &lastU, Room* &firstR, Room* &lastR) {
        //khm... actually... do I need a list of rooms here? :)
                char *to_out = tb;
                strcpy(to_out, "");
                int showed = 0;

                for (int i=new_messages;i<total_messages;i++) {
                        showed += form_message(messages[i], to_out, firstU, lastU);
                        add_to_out(to_out);
                }
                if (!showed) {
                        this->count_wo_mesg++;
                        if (this->count_wo_mesg > 9) {
                                this->count_wo_mesg = 0;
                                add_to_out("<script>up();</script>\n");
                        }
                } else this->count_wo_mesg = 0;
                return 1;
        }

        int show_header(Message* messages, int& total_messages, DesignHeader* &firstDH, DesignHeader* &lastDH, char* design,User* &firstU, User* &lastU, char* topic) {
                DesignHeader *current;
                current = firstDH;
                int design_loaded = 0, showed = 0;;
                char full_out[16384], buff[16384];
                char* to_out;
                to_out = tb;
                strcpy(to_out, "");
                strcpy(full_out,"");
                while(current!=NULL) {
                        if (strcmp(current->design_name,design) == 0) {
                                strcpy(full_out, current->design_header);
                                strrepl(full_out, 16384, "[TOPIC]", topic);
                                if (strlen(charset)>0) {
                                        //<meta http-equiv="Content-Type" content="text/html; charset=..">
                                        char meta[63+strlen(charset)];
                                        snprintf(meta, 63+strlen(charset), "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">",charset);
                                        strrepl(full_out, 16384, "[CHARSET]", meta);
                                } else strrepl(full_out, 16384, "[CHARSET]", "");
                                strrepl(full_out, 16384, "[CHAT_URL]", chat_url);
                                strrepl(full_out, 16384, "[SKIN]", current->design_name);
                                add_to_out(full_out);
                                strcpy(full_out,"");
                                design_loaded = 1;
                                break;
                        }
                        current = current->next;
                }
                if (design_loaded == 0) {
                        char header_file[255];
                        strcpy(header_file,file_path);
                        strcat(header_file,"designes/");
                        strcat(header_file,design);
                        strcat(header_file,"/daemon_html_header.html");
                        /*khm... is there an easy way to find out the filesize?*/
                        char header[100000];
                        char string[16384];
                        strcpy(header,"");
                        FILE* pFile = fopen (header_file, "r");
                        if (pFile == NULL) perror ("Error opening design file ");
                        else {
                                while(!feof(pFile)){
                                        /*i need this fake, otherwise in case of empty string in the file
                                                fgets returns the previous one */
                                        strcpy(string,"");
                                        fgets (string , 16383 , pFile);
                                        strcat(header,string);
                                }
                                fclose (pFile);
                        }
                        current = new DesignHeader(design,header);
                        if (lastDH != NULL) {
                                lastDH->next = current;
                                current->prev = lastDH;
                                current->next = NULL;
                                lastDH = current;
                        } else {
                                current->next = NULL;
                                current->prev = NULL;
                                lastDH = current;
                                firstDH = current;
                        }
                        strcpy(full_out, current->design_header);
                        strrepl(full_out, 16384, "[TOPIC]", topic);
                        if (strlen(charset)>0) {
                                //<meta http-equiv="Content-Type" content="text/html; charset=..">
                                char meta[63+strlen(charset)];
                                snprintf(meta, 63+strlen(charset), "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">",charset);
                                strrepl(full_out, 16384, "[CHARSET]", meta);
                        } else strrepl(full_out, 16384, "[CHARSET]", "");
                        strrepl(full_out, 16384, "[CHAT_URL]", chat_url);
                        strrepl(full_out, 16384, "[SKIN]", current->design_name);
                        add_to_out(full_out);
                        strcpy(full_out,"");
                        //to clear the buffer
                        send();
                }
                for (int i=total_messages-1;i>=0 && showed<history_size;i--) {
                        showed += form_message(messages[i], to_out, firstU, lastU);
                        if(strlen(full_out)+strlen(to_out)<16383) {
                                strcpy(buff,"");
                                strcat(buff,full_out);
                                strcpy(full_out,to_out);
                                strcat(full_out,buff);
                        }
                }
                add_to_out(full_out);
                return 1;
        }

        int send() {
                int len = strlen(this->out_buffer);
                int result = ::send(this->client, this->out_buffer, len, 0);
                if (result >0) mystat->add_out(result);
                /*and now remove chars from buffer*/
                memmove(out_buffer, out_buffer+result, strlen(out_buffer)-result +1);
                return result;
        }

        void stat() {
                mystat->stat(tb, sizeof(tb));
                add_to_out(tb);
                send();
        }

        int search_user(User* &firstU, User* &lastU, Message* messages, int& total_messages, DesignHeader* &firstDH, DesignHeader* &lastDH, Room* &firstR, Room* &lastR) {
                int exists = -1;
                char design[30];
                Room *currentR;
                User *currentU;
                char topic[4096];
                strcpy(topic,"");
                if (strcmp(session, "stat") == 0) {
                        snprintf(log_string,sizeof(log_string),"\"stat\" request from ip >%s<\n",this->remote_ip);
                        my_log(log_string);
                        stat();
                        return -1;
                }
                snprintf(log_string,sizeof(log_string),"search user with session >%s<, ip >%s<\n",session, this->remote_ip);
                my_log(log_string);
                currentU = firstU;
                while(currentU != NULL) {
                //for (int i=0; i<users_online;i++) {

                        if (strcmp(currentU->session,session) == 0) {
                                status = ONLINE;
                                //the inc buffer will never be used anymore
                                //delete[] this->inc_buffer;
                                exists_in_list = 1;
                                //nickname = new char[strlen(users[i].nickname)+1];
                                strcpy(nickname, currentU->nickname);
                                strcpy(highlighted_nick, nick_highlight_before);
                                strcat(highlighted_nick, nickname);
                                strcat(highlighted_nick, nick_highlight_after);
                                room_id = currentU->room_id;
                                tail_id = currentU->tail_id;
                                priv_tail_id = currentU->priv_tail_id;
                                if (show_type == ST_PRIVATE) priv_tail_id++;
                                if (show_type == ST_ALL || show_type == ST_NORMAL) tail_id++;
                                strcpy(design, currentU->chat_design);
                                //checking for room , topic of the room, and predefined design
                                currentR = firstR;
                                while (currentR!=NULL) {
                                        if (currentR->room_id == room_id) {
                                                if (strlen(currentR->design)>1) strcpy(design, currentR->design);
                                                strcpy(topic, currentR->topic);
                                                break;
                                        }
                                        currentR = currentR->next;
                                }
                                delete[] this->ignor_list;
                                this->ignor_list = new char[strlen(currentU->ignor)+1];
                                strcpy(ignor_list, currentU->ignor);
                                //check for room design here!
                                exists = 1;
                                snprintf(log_string,sizeof(log_string),"found >%s<, ip >%s<\n",nickname, this->remote_ip);
                                my_log(log_string);
                                show_header(messages, total_messages, firstDH, lastDH, design, firstU, lastU, topic);
                                break;
                        }
                        currentU = currentU->next;
                }
                if (exists == -1) {
                        snprintf(log_string,sizeof(log_string),"session >%s< was not found\n",session);
                        my_log(log_string);
                        add_to_out(w_no_user);
                        send();
                }
                return exists;
        }
        int recv(User* &firstU, User* &lastU, Message* messages, int& total_messages, DesignHeader* &firstDH, DesignHeader* &lastDH, Room* &firstR, Room* &lastR) {
                return recv(firstU, lastU, messages, total_messages, firstDH, lastDH, firstR, lastR, 1);
        }
        int recv(User* &firstU, User* &lastU, Message* messages, int& total_messages, DesignHeader* &firstDH, DesignHeader* &lastDH, Room* &firstR, Room* &lastR, int needrecv) {
                char  *search_http, *search_start;
                //char tb[16384];
                //char *temp_buff = tb;
                //strcpy(temp_buff, "");
                char *temp_buff;
                temp_buff = tb;
                strcpy(temp_buff, "");
                int result = 0;
                result = ::recv(this->client, temp_buff, 16383, 0);
                if (result > -1) mystat->add_inc(result);
                if (needrecv) {
                        if (result < 0) return (-1);
                }
                if (this->status == ONLINE) return 1;
                //if (!inc_buffer) return (-1);

                if (strlen(this->inc_buffer)+strlen(temp_buff) > 16383 ) {
                        //delete[] temp_buff;
                        return (-1);
                }

                strcat(this->inc_buffer,temp_buff);
                //delete[] temp_buff;
                search_start = strstr(this->inc_buffer, "GET ");
                if (search_start == NULL) return result;
                //in case we don't have ? in request
                search_http = strstr(search_start, "HTTP/1.");
                search_start = strstr(search_start, "?");
                if (search_http != NULL && search_start != NULL) {
                        if (search_http - search_start-2 > 0) {
                                size_t to_copy = (search_http - search_start-2 > 32) ? 32:search_http - search_start-2;
                                char *st = strstr(search_start, "&t=");
                                show_type = ST_ALL;
                                if (st) {
                                        if (st[3] == 'p') show_type = ST_PRIVATE;
                                        else if (st[3] == 'n') show_type = ST_NORMAL;
                                }
                                search_start[1+to_copy] = '\0';
                                if (to_copy>0) {
                                        strcpy(this->session, search_start+1);
                                        return this->search_user(firstU, lastU, messages, total_messages, firstDH, lastDH, firstR, lastR);
                                }
                                else {
                                        /*cannot find session in the request*/
                                        this->add_to_out(w_no_user);
                                        (void)this->send();
                                        return (-1);
                                }
                        }
                        my_log("cannot find session id in request string\n");
                        this->add_to_out(w_no_user);
                        (void)this->send();
                        return (-1);
                } else if (search_http !=NULL && search_start == NULL) {
                        my_log("no get-query in request string\n");
                        this->add_to_out(w_no_user);
                        (void)this->send();
                        return (-1);
                }
                return result;
        }

        void add_to_out(char* addition) {
                if (strlen(this->out_buffer) + strlen(addition) <16383) {
                        strcat(this->out_buffer, addition);
                }
        }
};

/*i need it here 'cause sigger could be called from anywhere
and i want to send messages to all visitors
probably it's better to use firstC  at this level
*/

Client *startC;

void remove_client(Client*& client, Client*& firstC, Client*& lastC, char* comment) {
        Client *next, *prev;
        client->send();
        (void) close(client->get_socket_id());
        snprintf(log_string,sizeof(log_string),">%s< disconnected. %s\n",client->get_nickname(), comment);
        my_log(log_string);
        mystat->remove_con();

        if (client == firstC) firstC = client->next;
        if (client == lastC) lastC = client->prev;
        next = client->next;
        prev = client->prev;
        if (prev!=NULL) prev->next = next;
        if (next!=NULL) next->prev = prev;
        delete (client);
        startC = firstC;
        client = next;
}

#ifdef SUPPORT_MYSQL
int mysql_get_rooms_list(Room* &firstR, Room* &lastR) {
        Room *currentR, *prevR;
        prevR = NULL;
        int current = 0;

        if (mysql_ping(&mysql)!=0) {
                snprintf(log_string,sizeof(log_string),"lost connection to the database, Error:>%s<\n", mysql_error(&mysql));
                my_log(log_string);
                sigger(-1);
         }
        if (mysql_real_query(&mysql, get_rooms_query, get_rooms_query_length) != 0) {
                snprintf(log_string,sizeof(log_string),"error while retrieving rooms, Error:>%s<\n", mysql_error(&mysql));
                my_log(log_string);
                sigger(-1);
        } else {
                mysql_result = mysql_use_result(&mysql);
                mysql_num_f = mysql_num_fields(mysql_result);
                while ((mysql_row = mysql_fetch_row(mysql_result))) {
                        //process row
                        if (mysql_fetch_lengths(mysql_result)) {
                                //string2int(mysql_row[0], r_id);
                                //currentR = new Room(r_id, mysql_row[1], mysql_row[2], mysql_row[3]);
                                currentR = new Room(string2int(mysql_row[ROOM_ID]),
                                                                                                mysql_row[ROOM_TITLE],
                                                                                                mysql_row[ROOM_TOPIC],
                                                                                                mysql_row[ROOM_DESIGN]);
                                currentR->prev = prevR;
                                if (prevR!=NULL) prevR->next = currentR;
                                currentR->next = NULL;
                                prevR = currentR;
                                lastR = currentR;
                                if (current == 0) firstR = currentR;
                                current++;
                        }
                }
                mysql_free_result(mysql_result);
        }
        return 0;
}

int mysql_get_messages_list(Message* messages, int& new_messages, int& last_id) {
        int current = 0;
        new_messages = -1;
        if (mysql_ping(&mysql)!=0) {
                snprintf(log_string,sizeof(log_string),"lost connection to the database, Error:>%s<\n", mysql_error(&mysql));
                my_log(log_string);
                sigger(-1);
         }
        if (mysql_real_query(&mysql, get_messages_query, get_messages_query_length) != 0) {
                snprintf(log_string,sizeof(log_string),"error while retrieving messages, Error:>%s<\n", mysql_error(&mysql));
                my_log(log_string);
                sigger(-1);
        } else {
                mysql_result = mysql_use_result(&mysql);
                mysql_num_f = mysql_num_fields(mysql_result);
                while ((mysql_row = mysql_fetch_row(mysql_result))) {
                        //process row
                        if (current>=max_messages) continue;
                        if (mysql_fetch_lengths(mysql_result)) {

                                string2int(mysql_row[MESG_ID], messages[current].id);
                                if (messages[current].id > last_id) {
                                        last_id = messages[current].id;
                                        if (new_messages == -1) new_messages = current;
                                }
                                string2int(mysql_row[MESG_ROOM], messages[current].room_id);
                                string2int(mysql_row[MESG_TIME], messages[current].time);
                                strncpy(messages[current].from, mysql_row[MESG_FROM], sizeof(messages[current].from)-1);
                                messages[current].from[sizeof(messages[current].from)] = 0;
                                strncpy(messages[current].from_wotags, mysql_row[MESG_FROMWOTAGS], sizeof(messages[current].from_wotags)-1);
                                messages[current].from_wotags[sizeof(messages[current].from_wotags)] = 0;
                                strncpy(messages[current].from_session, mysql_row[MESG_FROMSESSION], sizeof(messages[current].from_session)-1);
                                strncpy(messages[current].from_avatar, mysql_row[MESG_FROMAVATAR], sizeof(messages[current].from_avatar)-1);
                                messages[current].from_session[sizeof(messages[current].from_session)] = 0;
                                messages[current].from_id = string2int(mysql_row[MESG_FROMID]);
                                strncpy(messages[current].to, mysql_row[MESG_TO], sizeof(messages[current].to)-1);
                                messages[current].to[sizeof(messages[current].to)] = 0;
                                strncpy(messages[current].to_session, mysql_row[MESG_TOSESSION], sizeof(messages[current].to_session)-1);
                                messages[current].to_session[sizeof(messages[current].to_session)] = 0;
                                messages[current].to_id = string2int(mysql_row[MESG_TOID]);
                                strncpy(messages[current].body, mysql_row[MESG_BODY], sizeof(messages[current].body)-1);
                                messages[current].body[sizeof(messages[current].body)] = 0;
                                if (current>=max_messages) break;
                                current++;
                        }
                }
                mysql_free_result(mysql_result);
        }
        if (new_messages == -1) new_messages = current;
        return current;
}

int mysql_get_users_list(User* &firstU, User* &lastU, Client* &firstC, Client* &lastC) {
        int current = 0;
//        int _time, _sex, _user_id, _tail_id, _user_status, _last_action, _room_id;
        int tail_id, priv_tail_id;
        Client* currentC;
        User *currentU, *prevU;
        currentU = NULL;
        prevU = NULL;
        //used for update set time=NOW where session in(...,...,...,...)
        int users_to_update = 0;
        char up_query[16384];
        strcpy(up_query,"");
        snprintf(up_query, sizeof(up_query)-1, "update %swho set time=%d where session in (",mysql_table_prefix,(int)time(NULL));

        if (mysql_ping(&mysql)!=0) {
                snprintf(log_string,sizeof(log_string),"lost connection to the database, Error:>%s<\n", mysql_error(&mysql));
                my_log(log_string);
                sigger(-1);
         }
        if (mysql_real_query(&mysql, get_users_query, get_users_query_length) != 0) {
                snprintf(log_string,sizeof(log_string),"error while retrieving users\nquery is >%s<,\n Error:>%s<\n", get_users_query, mysql_error(&mysql));
                my_log(log_string);
                sigger(-1);
        } else {
                //i need to store it here, because i need to call 'update'queries before fetch all rows.
                mysql_result = mysql_store_result(&mysql);
                mysql_num_f = mysql_num_fields(mysql_result);
                while ((mysql_row = mysql_fetch_row(mysql_result))) {
                        //process row
                        if (mysql_fetch_lengths(mysql_result)) {
                                string2int(mysql_row[USER_TAILID], tail_id);
                                string2int(mysql_row[USER_PRIVTAILID], priv_tail_id);
        //int tail_id = string2int(user_fields[USER_TAILID]);
        //int priv_tail_id = string2int(user_fields[USER_PRIVTAILID]);
        //int user_time = string2int(user_fields[USER_TIME]);
                                currentC = firstC;
                                int tail_update = 0;
                                while (currentC!=NULL) {
                                        tail_update = 0;
                                        if(strcmp(currentC->get_nickname(),mysql_row[USER_NICKNAME]) == 0) {
                                                if (currentC->show_type == ST_PRIVATE) {
                                                        if (priv_tail_id > currentC->priv_tail_id) {
                                                                //disconnect user, incorrect tail_id
                                                                currentC->add_to_out(w_only_one_tail);
                                                                Client *tmpC;
                                                                tmpC = currentC->next;
                                                                remove_client(currentC,firstC,lastC, "wrong PRIV_tail_id, disconnecting one connection");
                                                                currentC = tmpC;
                                                                continue;
                                                        }
                                                        else if (priv_tail_id < currentC->priv_tail_id) {
                                                                priv_tail_id = currentC->priv_tail_id;
                                                                tail_update = 1;
                                                        }
                                                } else {
                                                        if(tail_id > currentC->get_tail_id()) {
                                                                //disconnect user, incorrect tail_id
                                                                currentC->add_to_out(w_only_one_tail);
                                                                Client *tmpC;
                                                                tmpC = currentC->next;
                                                                remove_client(currentC,firstC,lastC, "wrong tail_id, disconnecting one connection");
                                                                currentC = tmpC;
                                                                continue;
                                                        }
                                                        else if (tail_id < currentC->get_tail_id()) {
                                                                tail_id = currentC->get_tail_id();
                                                                tail_update = 1;
                                                        }
                                                }
                                                if (currentC->is_active()){
                                                        if (tail_update == 1) {
                                                                snprintf(tb, sizeof(tb)-1,"update %swho set time=%d, tail_id=%d, priv_tailid=%d where session=\"%s\"",mysql_table_prefix,(int)time(NULL),tail_id,priv_tail_id, mysql_row[USER_SESSION]);
                                                                //my_log(tb);
                                                                if (mysql_real_query(&mysql, tb, strlen(tb)) != 0) {
                                                                        snprintf(log_string,sizeof(log_string),"error while updating tail-id for user, Error:>%s<\n", mysql_error(&mysql));
                                                                        my_log(log_string);
                                                                        //sigger(-1);
                                                                }
                                                        } else {
                                                                //just update
                                                                //,"") +4 symbols
                                                                if (strlen(up_query)+strlen(mysql_row[1])+4 < 16383) {
                                                                        if (users_to_update > 0) strcat(up_query, ",");
                                                                        strcat(up_query, "\"");
                                                                        strcat(up_query, mysql_row[1]);
                                                                        strcat(up_query, "\"");
                                                                        users_to_update++;
                                                                } else {
                                                                        strcat(up_query,")");
                                                                        if (mysql_real_query(&mysql, up_query, strlen(up_query)) != 0) {
                                                                                snprintf(log_string,sizeof(log_string),"error while updating time for users, Error:>%s<\n", mysql_error(&mysql));
                                                                                my_log(log_string);
                                                                                //sigger(-1);
                                                                        }
                                                                        strcpy(up_query, "");
                                                                        snprintf(up_query, sizeof(up_query)-1, "update %swho set time=%d where session in (",mysql_table_prefix,(int)time(NULL));
                                                                        users_to_update = 0;
                                                                }
                                                        }
                                                }
                                                currentC->set_exists_in_list(1);
                                                //user_time = (int)time(NULL);
                                                currentC->set_ignor_list(mysql_row[USER_IGNORLIST]);
                                                /*cannot break here, probably we have another client with same nickname
                                                but different tail_id*/
                                                //break;
                                        }
                                        currentC = currentC->next;
                                }
                                currentU = new User(mysql_row[USER_NICKNAME],
                                                                        mysql_row[USER_SESSION],
                                                                        mysql_row[USER_AVATAR],
                                                                        tail_id,
                                                                        priv_tail_id,
                                                                        mysql_row[USER_BROWSERHASH],
                                                                        string2int(mysql_row[USER_ROOM]),
                                                                        mysql_row[USER_IGNORLIST],
                                                                        mysql_row[USER_LANG],
                                                                        mysql_row[USER_SKIN]);
                                currentU->prev = prevU;
                                if (prevU!=NULL) prevU->next = currentU;
                                currentU->next = NULL;
                                prevU = currentU;
                                lastU = currentU;
                                if (current == 0) firstU = currentU;

                                current++;
                        }
                }
                mysql_free_result(mysql_result);
                if (users_to_update > 0) {
                        strcat(up_query,")");
                        if (mysql_real_query(&mysql, up_query, strlen(up_query)) != 0) {
                                snprintf(log_string,sizeof(log_string),"error while updating time for users, Error:>%s<\n", mysql_error(&mysql));
                                my_log(log_string);
                                //sigger(-1);
                        }
                }
        }
        return current;
}
#endif

int files_get_rooms_list(Room* &firstR, Room* &lastR) {
        FILE * pFile;
        //char string [16384];
        char* string;
        string = tb;
        strcpy(string,"");
        int current = 0;
        char rooms_file[255];
        Room *currentR, *prevR;
        prevR = NULL;
        char *pch;


        strcpy(rooms_file,data_path);
        strcat(rooms_file, "rooms.dat");
        pFile = fopen (rooms_file, "rb");
        if (pFile == NULL) perror ("Error opening rooms file");
        else {
                flock(fileno(pFile), LOCK_EX);
                bool parsed = true;
                //checking for feof is slow -- first check, second read
                //i can just read
                //while(!feof(pFile)){
                while (fgets (string , 16383 , pFile)) {
                        //strcpy(string, "");
                        //fgets (string , 16383 , pFile);
                        pch = strchr(string,'\n');
                        if (pch) *pch = '\0';
                        pch = strchr(string,'\r');
                        if (pch) *pch = '\0';
                        if (strlen(string)<7) continue;
                        char *room_fields[ROOM_TOTALFIELDS];
                        room_fields[0] = string;
                        parsed = true;
                        for (int i=1;i<ROOM_TOTALFIELDS;i++) {
                                room_fields[i] = get_next_token2('\t', room_fields[i-1]);
                                if (room_fields[i] == NULL) {
                                        parsed = false;
                                        break;
                                }
                        }
                        if (!parsed) continue;
                        currentR = new Room(string2int(room_fields[ROOM_ID]),
                                                                                        room_fields[ROOM_TITLE],
                                                                                        room_fields[ROOM_TOPIC],
                                                                                        room_fields[ROOM_DESIGN]);
                        currentR->prev = prevR;
                        if (prevR!=NULL) prevR->next = currentR;
                        currentR->next = NULL;
                        prevR = currentR;
                        lastR = currentR;
                        if (current == 0) firstR = currentR;
                        current++;
                }
                flock(fileno(pFile),LOCK_UN);
                fclose (pFile);
        }
        return 0;
}

int files_get_messages_list(Message* messages, int& new_messages, int& last_id) {
        FILE * pFile;
        //char string [16384];
        char* string;
        string = tb;
        strcpy(string,"");
        char *mesg_fields[MESG_TOTALFIELDS];
        //VOC++ specific
        char *clear_fields[CLEAR_TOTAL];
        char clear_file[255];
        int i;
        //End VOC++ specific
        int current = 0;
        new_messages = -1;
        char *pch;
        char messages_file[255];
        strcpy(messages_file,data_path);
        strcat(messages_file, "messages.dat");


        pFile = fopen (messages_file, "rb");
        if (pFile == NULL) perror ("Error opening messages file");
        else {
                (void) lseek(fileno(pFile), 0, 0L);
                flock(fileno(pFile), LOCK_EX);
                //while(!feof(pFile)){
                //        strcpy(string, "");
                //        fgets (string , 16383 , pFile);
                while(fgets (string , 16383 , pFile)) {
                        pch = strchr(string,'\n');
                        if (pch) *pch = '\0';
                        pch = strchr(string,'\r');
                        if (pch) *pch = '\0';
                        mesg_fields[0] = string;
                        int error = 0;
                        for (int i=1;i<MESG_TOTALFIELDS;i++) {
                                mesg_fields[i] = get_next_token2('\t', mesg_fields[i-1]);
                                if (mesg_fields[i] == NULL){
                                        error = 1;
                                        break;
                                }
                        }
                        if (error == 1) continue;

                        //VOC++ patch - untouchable private if clear flag is set
                        if(strlen(mesg_fields[MESG_TO]) > 0 && clearFlag) continue;
                        // end of VOC++ patch

                        messages[current].id = string2int(mesg_fields[MESG_ID]);
                        if (messages[current].id > last_id) {
                                last_id = messages[current].id;
                                if (new_messages == -1) new_messages = current;
                        }

                        //VOC++ specific
                        messages[current].clan_id = string2int(mesg_fields[MESG_CLANID]);
                        //End VOC++ specific

                        messages[current].room_id = string2int(mesg_fields[MESG_ROOM]);
                        messages[current].time = string2int(mesg_fields[MESG_TIME]);
                        strncpy(messages[current].from, mesg_fields[MESG_FROM], sizeof(messages[current].from)-1);
                        strncpy(messages[current].from_wotags, mesg_fields[MESG_FROMWOTAGS], sizeof(messages[current].from_wotags)-1);
                        strncpy(messages[current].from_session, mesg_fields[MESG_FROMSESSION], sizeof(messages[current].from_session)-1);
                        strncpy(messages[current].from_avatar, mesg_fields[MESG_FROMAVATAR], sizeof(messages[current].from_avatar)-1);
                        messages[current].from_id = string2int(mesg_fields[MESG_FROMID]);
                        strncpy(messages[current].to, mesg_fields[MESG_TO], sizeof(messages[current].to)-1);
                        strncpy(messages[current].to_session, mesg_fields[MESG_TOSESSION], sizeof(messages[current].to_session)-1);
                        messages[current].to_id = string2int(mesg_fields[MESG_TOID]);
                        strncpy(messages[current].body, mesg_fields[MESG_BODY], sizeof(messages[current].body)-1);
                        current++;
                        if (current>=max_messages) break;
                }

                                //VOC++ specific patch - adding a "who cleared" phrase to adminz private
                                // fake to display a message about clearing
                                if(clearFlag) {
                                        if(current < max_messages) {
                                                //my_log("Before!\n");
                                                //my_log(w_roz_clear_pub_adm);
                                                //my_log("After!\n");
                                                if(current > 0)        {
                                                        messages[current].id = messages[current-1].id;
                                                        for(i = 0; i < current; i++) messages[i].id--;

                                                }
                                                else messages[current].id = 0;

                                                new_messages++;
                                                last_id = current;

                                                messages[current].room_id = 0;
                        messages[current].time = time(NULL);
                        strncpy(messages[current].from, ADMINZ_PRIVATE, sizeof(ADMINZ_PRIVATE)-1);
                        strncpy(messages[current].from_wotags, ADMINZ_PRIVATE, sizeof(ADMINZ_PRIVATE)-1);
                        strncpy(messages[current].from_session, zeroBlk, sizeof(zeroBlk)-1);
                        //strncpy(messages[current].from_avatar, mesg_fields[MESG_FROMAVATAR], sizeof(messages[current].from_avatar)-1);
                        messages[current].from_id = 0;
                        strncpy(messages[current].to, ADMINZ_PRIVATE, sizeof(ADMINZ_PRIVATE)-1);
                        strncpy(messages[current].to_session, zeroBlk, sizeof(zeroBlk)-1);
                        messages[current].to_id = 0;

                        strncpy(messages[current].body, w_roz_clear_pub_adm , sizeof(w_roz_clear_pub_adm)-1);
                                        }
                                }
                                //end VOC++ specific

                flock(fileno(pFile),LOCK_UN);
                fflush(pFile);
                fclose (pFile);
        }
        if (new_messages == -1) new_messages = current;
        return current;
}

int parse_user_string(char* user_string, User* &currentU, Client* &firstC, Client* &lastC, char* to_write) {
        Client* currentC;
        //for int, so now it's 2^32, with opteron probably 2^64
        char tmp[50];
        if (strlen(user_string)<7) return 0;
        char *user_fields[USER_TOTALFIELDS];
        user_fields[0] = user_string;
        for (int i=1;i<USER_TOTALFIELDS;i++) {
                user_fields[i] = get_next_token2('\t', user_fields[i-1]);
                if (user_fields[i] == NULL) {
                        //VOC++ patch
                        user_fields[i] = zeroBlk;
                        //return 0;
                }
        }
        int tail_id = string2int(user_fields[USER_TAILID]);
        int priv_tail_id = string2int(user_fields[USER_PRIVTAILID]);
        int user_time = string2int(user_fields[USER_TIME]);
        currentC = firstC;
        while (currentC!=NULL) {
                if(strcmp(currentC->get_nickname(),user_fields[USER_NICKNAME]) == 0) {
                        if (currentC->show_type == ST_PRIVATE) {
                                if (priv_tail_id > currentC->priv_tail_id) {
                                        //disconnect user, incorrect tail_id
                                        currentC->add_to_out(w_only_one_tail);
                                        Client *tmpC;
                                        tmpC = currentC->next;
                                        remove_client(currentC,firstC,lastC, "wrong PRIV_tail_id, disconnecting one connection");
                                        currentC = tmpC;
                                        continue;
                                }
                                else if (priv_tail_id < currentC->priv_tail_id)
                                        priv_tail_id = currentC->priv_tail_id;
                        } else {
                                if(tail_id > currentC->get_tail_id()) {
                                        //disconnect user, incorrect tail_id
                                        currentC->add_to_out(w_only_one_tail);
                                        Client *tmpC;
                                        tmpC = currentC->next;
                                        remove_client(currentC,firstC,lastC, "wrong tail_id, disconnecting one connection");
                                        currentC = tmpC;
                                        continue;
                                }
                                else if (tail_id < currentC->get_tail_id())
                                        tail_id = currentC->get_tail_id();
                        }
                        currentC->set_exists_in_list(1);
                        if (currentC->is_active()) user_time = (int)time(NULL);
                        currentC->set_ignor_list(user_fields[USER_IGNORLIST]);
                        /*cannot break here, probably we have another client with same nickname
                          but different tail_id*/
                        //break;
                }
                currentC = currentC->next;
        }
        //VOC++ specific
        currentU = new User(user_fields[USER_NICKNAME],
                                                user_fields[USER_SESSION],
                                                user_fields[USER_AVATAR],
                                                tail_id,
                                                priv_tail_id,
                                                user_fields[USER_BROWSERHASH],
                                                string2int(user_fields[USER_ROOM]),
                                                user_fields[USER_IGNORLIST],
                                                user_fields[USER_LANG],
                                                user_fields[USER_SKIN],
                                                                                                user_fields[USER_CLASS],
                                                                                                user_fields[USER_GENDER],
                                                                                                user_fields[USER_FILTER],
                                                                                                user_fields[USER_CUSTOMCLASS],
                                                                                                user_fields[USER_CLANID],
                                                                                                user_fields[USER_REDUCETRAFFIC]);
        //VOC++ specific

        strcpy(to_write, "");
        for (int i=0;i<USER_TOTALFIELDS;i++) {
                if (i>0) strcat(to_write, "\t");
                if (i==USER_TAILID) {
                        snprintf(tmp, 49, "%d", tail_id);
                        strcat(to_write, tmp);
                        continue;
                }
                if (i==USER_PRIVTAILID) {
                        snprintf(tmp, 49, "%d", priv_tail_id);
                        strcat(to_write, tmp);
                        continue;
                }
                if (i == USER_TIME) {
                        snprintf(tmp, 49, "%d", user_time);
                        strcat(to_write, tmp);
                        continue;
                }
                strcat(to_write, user_fields[i]);
        }
        return 1;
}


int files_get_users_list(User* &firstU, User* &lastU, Client* &firstC, Client* &lastC) {
        FILE * pFile;
        char string [16384];
        strcpy(string,"");
        char to_write[16384];
        int current = 0;
        char *pch;
        char who_file[255];
        User *currentU, *prevU;
        currentU = NULL;
        prevU = NULL;

        strcpy(who_file,data_path);
        strcat(who_file, "who.dat");
        strcpy(to_write,"");
        pFile = fopen (who_file, "r+b");


        if (pFile == NULL){
                printf("Error opening who.dat file: %s\n", who_file);
                strcpy(to_write, "Error opening who.dat file: ");
                strcat(to_write, who_file);
                strcat(to_write, " (trying to SIGTERM, process is dead)\n");
                my_log (to_write);
                sigger(SIGTERM);
                }
        else {

                (void) flock(fileno(pFile), LOCK_EX);
                (void) fseek (pFile, 0, SEEK_END);
            unsigned long size=ftell (pFile);
                //for some reasons i have (from time to time) size+1 bytes to read from file
                // on a linux server with heavelly loaded ext3 partition...
                //ftell returns wrong data? or fseek doesn't point to the end?
                //looks like it fails when  there is a \n at the end

                //will try to strncpy
                //size ++;
                char* read_buff;
                read_buff = new char[size+1];
                (void)strcpy(read_buff, "");

                (void) fseek(pFile, 0, SEEK_SET);
                //while(!feof(pFile)){
                //        strcpy(string, "");
                //        fgets (string , 16383 , pFile);
                while(fgets (string , 16383 , pFile)) {
                        if(strlen(read_buff)+strlen(string)<=size) strcat(read_buff, string);
                        else {
                                my_log("oops... reading more than allocated\n");
                                snprintf(log_string,sizeof(log_string),"strlen(read_buff) >%d< +strlen(string)>%d< < size>%d<\n",strlen(read_buff),strlen(string),(int)size);
                                my_log(log_string);
                                //in case we have something strange at the end.
                                strncat(read_buff, string, size-strlen(read_buff));
                        }

                }
                read_buff[size] = 0;
                (void) fseek(pFile, 0, SEEK_SET);
                //truncating file and then rewriting it from scratch is slow.
                //so i will rewrite it and truncate at the last fwrite.
                //(void) ftruncate(fileno(pFile),0);
                char* substr;
                char* next;
                substr = read_buff;
                do {
                        next = strchr(substr,'\n');
                        strcpy(string,"");
                        if (substr != NULL) {
                                if(next != NULL) {
                                        strncpy(string, substr, next-substr);
                                        string[next-substr] = 0;
                                        substr = next+1;
                                }
                                else {
                                        strncpy(string, substr, (size_t)size-(substr-read_buff));
                                        string[(size_t)size-(substr-read_buff)] = 0;
                                        substr = read_buff+size;
                                }
                        }

                        /*replacing \n and \r. khm, do i really need \r here?*/
                        pch = strchr(string,'\n');
                        if (pch) *pch = '\0';
                        pch = strchr(string,'\r');
                        if (pch) *pch = '\0';
                        if (strlen(string)<7) continue;
                        if (parse_user_string(string, currentU, firstC, lastC, to_write) == 0) {
                                //my_log("parse returned 0\n");
                                continue;
                        }
                        if (currentU == NULL) {
                                //my_log("curentU continue\n");
                                continue;
                        }
                        if (prevU!=NULL) prevU->next = currentU;
                        currentU->next = NULL;
                        prevU = currentU;
                        lastU = currentU;
                        if (current == 0) firstU = currentU;

                        if(current>0) fputc('\n', pFile);
                        current++;
                        fputs(to_write,pFile);
                } while (strlen(string)>0);
                fflush(pFile);
                //finally truncate, if the new file is less then previous
                (void) ftruncate(fileno(pFile),ftell(pFile));
                flock(fileno(pFile),LOCK_UN);
                fflush(pFile);
                fclose (pFile);
                delete[] read_buff;
        }
        return current;
}
#ifdef SUPPORT_SHARED_MEMORY
int shm_get_messages_list(Message* messages, int& new_messages, int& last_id) {

        //char string [16384];
        char* string;
        string = tb;
        strcpy(string,"");
        int current = 0;
        new_messages = -1;
        char *pch;
        char *mesg_fields[MESG_TOTALFIELDS];
                
                if(!php_sem_aquire(sem_mess)) {
                        snprintf(log_string,sizeof(log_string),"Acquiring failed for messages list semaphore\n");
                        my_log(log_string);
                        return 0;
                }
                
        char* substr;
        char* next;
        char* read_buff;
        unsigned long size=strlen(shm_mess);
        read_buff = new char[size+1];
        read_buff[size] = 0;
        strcpy(read_buff, shm_mess);
        substr = read_buff;
        do {
                next = strchr(substr,'\n');
                strcpy(string,"");
                if (substr != NULL) {
                        if(next != NULL) {
                                strncpy(string, substr, next-substr);
                                string[next-substr] = 0;
                                substr = next+1;
                        }
                        else {
                                strncpy(string, substr, (size_t)size-(substr-read_buff));
                                string[(size_t)size-(substr-read_buff)] = 0;
                                substr = read_buff+size;
                        }
                }
                pch = strchr(string,'\n');
                if (pch) *pch = '\0';
                pch = strchr(string,'\r');
                if (pch) *pch = '\0';
                mesg_fields[0] = string;
                int error = 0;
                for (int i=1;i<MESG_TOTALFIELDS;i++) {
                        mesg_fields[i] = get_next_token2('\t', mesg_fields[i-1]);
                        if (mesg_fields[i] == NULL){
                                error = 1;
                                break;
                        }
                }
                if (error == 1) continue;
                messages[current].id = string2int(mesg_fields[MESG_ID]);
                if (messages[current].id > last_id) {
                        last_id = messages[current].id;
                        if (new_messages == -1) new_messages = current;
                }
                messages[current].room_id = string2int(mesg_fields[MESG_ROOM]);
                messages[current].time = string2int(mesg_fields[MESG_TIME]);
                strncpy(messages[current].from, mesg_fields[MESG_FROM], sizeof(messages[current].from)-1);
                strncpy(messages[current].from_wotags, mesg_fields[MESG_FROMWOTAGS], sizeof(messages[current].from_wotags)-1);
                strncpy(messages[current].from_session, mesg_fields[MESG_FROMSESSION], sizeof(messages[current].from_session)-1);
                strncpy(messages[current].from_avatar, mesg_fields[MESG_FROMAVATAR], sizeof(messages[current].from_avatar)-1);
                messages[current].from_id = string2int(mesg_fields[MESG_FROMID]);
                strncpy(messages[current].to, mesg_fields[MESG_TO], sizeof(messages[current].to)-1);
                strncpy(messages[current].to_session, mesg_fields[MESG_TOSESSION], sizeof(messages[current].to_session)-1);
                messages[current].to_id = string2int(mesg_fields[MESG_TOID]);
                strncpy(messages[current].body, mesg_fields[MESG_BODY], sizeof(messages[current].body)-1);
                current++;
                if (current>=max_messages) break;

        } while (strlen(string)>0);
        delete[] read_buff;
        
                if(!php_sem_release(sem_mess)) {
                        snprintf(log_string,sizeof(log_string),"Releasing failed for messages list semaphore\n");
                        my_log(log_string);
                        return 0;
                }
                
        if (new_messages == -1) new_messages = current;
        return current;
}

char to_shm[500*1024];

int shm_to_file(char *ptr, char *sword) {
 /*
  FILE *dbg;
  time_t mtm;
  mtm = time(NULL);
  
  strftime(log_time,(size_t)23,"%Y-%m-%d-%H-%M-%S",localtime(&mtm));
  sprintf(log_string, "%s-log-%s.log", sword, log_time);
  
  dbg = fopen(log_string, "a+b");
  if(dbg) {
   fprintf(dbg, "%s", ptr);
   fclose(dbg); 
  }
 */  
}

int shm_get_users_list(User* &firstU, User* &lastU, Client* &firstC, Client* &lastC) {
        char string [16384];
        char dbg_string [16384];
            char to_write[16384];
        int current = 0;
        char *pch;
        User *currentU, *prevU;
        
                strcpy(string,"");
                strcpy(to_shm,"");
                                
                currentU = NULL;
        prevU = NULL;
                        
                if(!php_sem_aquire(sem_users)) {
                        snprintf(log_string,sizeof(log_string),"Acquiring failed for userlist semaphore\n");
                        my_log(log_string);
                        return 0;
                }

        //process line here.
        char* read_buff;
                        
        unsigned long size = strlen(shm_users);
        
        shm_to_file(shm_users, "open"); 
                
        read_buff = new char[size+1];
        read_buff[size] = 0;
                
        //to_shm = new char[size+1025];
        to_shm[0] = 0;
                
        strcpy(read_buff, shm_users);
                        
        char* substr;
        char* next;
        substr = read_buff;
        do {
                next = strchr(substr,'\n');
                strcpy(string,"");
                if (substr != NULL) {
                        if(next != NULL) {
                                strncpy(string, substr, next-substr);
                                string[next-substr] = 0;
                                substr = next+1;
                        }
                        else {
                                strncpy(string, substr, (size_t)size-(substr-read_buff));
                                string[(size_t)size-(substr-read_buff)] = 0;
                                substr = read_buff+size;
                        }
                }

                /*replacing \n and \r. khm, do i really need \r here?*/
                pch = strchr(string,'\n');
                if (pch) *pch = '\0';
                pch = strchr(string,'\r');
                if (pch) *pch = '\0';

                strcpy(dbg_string, string);

               if(!strlen(string)) {
                 to_write[0] = 0;
                 continue;
               }
                             
        if (!parse_user_string(string, currentU, firstC, lastC, to_write)) {
                    to_write[0] = 0;
                   continue;
                }
                //my_log("to_write\n");
                //my_log(to_write);
                
                if (currentU == NULL) continue;
                if (prevU!=NULL) prevU->next = currentU;
                currentU->next = NULL;
                prevU = currentU;
                lastU = currentU;
                if (current == 0) firstU = currentU;
                if(current>0) strcat(to_shm, "\n");
                current++;
                strcat(to_shm,to_write);
        } while (strlen(string)>0);
                
        if(abs(strlen(to_shm) - size) < 100) strcpy(shm_users, to_shm);

     shm_to_file(shm_users, "close");   
        
        delete[] read_buff;
        //delete[] to_shm;
                                
                if(!php_sem_release(sem_users)) {
                        snprintf(log_string,sizeof(log_string),"Releasing failed for userlist semaphore\n");
                        my_log(log_string);
                        return 0;
                }
                
        return current;
}
#endif
int get_users_list(User* &firstU, User* &lastU, Client* &firstC, Client* &lastC) {
        User *currentU, *nextU;
        /*clearing the list first:*/
        currentU = firstU;
        nextU = NULL;
        while(currentU!=NULL) {
                nextU = currentU->next;
                delete(currentU);
                currentU = nextU;
        }
        firstU = NULL;
        lastU = NULL;
        switch (engine) {
                case ENGINE_FILES:
                        return files_get_users_list(firstU, lastU, firstC, lastC);
                break;
#ifdef SUPPORT_SHARED_MEMORY
                case ENGINE_SHM:
                case ENGINE_HIGHSPEED:
                        return shm_get_users_list(firstU, lastU, firstC, lastC);
                break;
#endif
#ifdef SUPPORT_MYSQL
                case ENGINE_MYSQL:
                        return mysql_get_users_list(firstU, lastU, firstC, lastC);
                break;
#endif

        }
        return 0;
}

int get_messages_list(Message* messages, int& new_messages, int& last_id) {
        switch (engine) {
                case ENGINE_FILES:
                        return files_get_messages_list(messages, new_messages, last_id);
                break;
#ifdef SUPPORT_SHARED_MEMORY
                case ENGINE_SHM:
                case ENGINE_HIGHSPEED:
                        return shm_get_messages_list(messages, new_messages, last_id);
                break;
#endif
#ifdef SUPPORT_MYSQL
                case ENGINE_MYSQL:
                        return mysql_get_messages_list(messages, new_messages, last_id);
                break;
#endif
        }
        return 0;
}

int get_rooms_list(Room* &firstR, Room* &lastR) {
        Room *currentR, *nextR;
        /*clearing the list first:*/
        currentR = firstR;
        nextR = NULL;
        while(currentR!=NULL) {
                nextR = currentR->next;
                delete(currentR);
                currentR = nextR;
        }
        firstR = NULL;
        lastR = NULL;
        switch (ld_engine) {
                case ENGINE_FILES:
                case ENGINE_HIGHSPEED:
                        return files_get_rooms_list(firstR, lastR);
                break;
#ifdef SUPPORT_MYSQL
                case ENGINE_MYSQL:
                        return mysql_get_rooms_list(firstR, lastR);
                break;
#endif
        }
        return 0;
}

void look_for_parameter(char* string, char* param_name, char *variable, int length) {
        char look_for[strlen(param_name)+4];
        strcpy(look_for,"$");
        strcat(look_for,param_name);
        //Commented for VOC++ and compatibility
        //strcat(look_for, " = ");

        char* pch;

        pch = strstr(string, look_for);
        if (pch) {
                pch = strchr(string,'"');
                pch++;
                int to_copy = (strrchr(pch,'"')-pch)>(length-1)?length-1:(strrchr(pch,'"')-pch);
                strncpy(variable, pch, to_copy);
                variable[to_copy] = 0;
                strrepl(variable,length,"\\\"","\"");
        }

}

void look_for_parameter(char* string, char* param_name, int& variable) {
        char look_for[strlen(param_name)+4];
        strcpy(look_for,"$");
        strcat(look_for,param_name);
        strcat(look_for, " = ");
        char* pch = strstr(string, look_for);
        if (pch) {
                pch = pch+strlen(param_name)+4;
                string2int(pch, variable);
        }
}

void initialize_parameters() {
        FILE * pFile;
        char string [16384];
        printf("Initalizing engine ...\n");
        //fake for a moment!
        char who_file[255];
        //added by DD
#ifdef SUPPORT_SHARED_MEMORY                    
        char who_file_real[255];        
        char mess_file_real[255];
        
        strcpy(who_file_real,data_path);
        strcat(who_file_real, "who.dat");
        
        strcpy(mess_file_real,data_path);
        strcat(mess_file_real, "messages.dat");
#endif                          
        //end added by DD
        
        strcpy(who_file,data_path);
        strcat(who_file, "voc.conf");
        pFile = fopen (who_file, "r");

        //to be sure that everything is here
        strcpy(message_format,"");
        strcpy(message_fromme,"");
        strcpy(private_message,"");
        strcpy(private_message_fromme,"");
        strcpy(private_hidden,"");
        strcpy(file_path,"");
        strcpy(nick_highlight_before,"");
        strcpy(nick_highlight_after,"");
        //for tests only, remove!
        strcpy(str_w_n_before, "");
        strcpy(str_w_n_after, "");
        strcpy(language,"en");
        max_users = 0;
        history_size = 10;
        char eng[15], ld_eng[15];

        if (pFile == NULL) {
                printf("Cannot open voc.conf: %s\n", who_file);
                my_log ("Error opening config file"); exit (-1);}
        else {
                (void) lseek(fileno(pFile), 0, 0L);
                do{
                        fgets (string , 16383 , pFile);
                        look_for_parameter(string,"message_format", message_format, sizeof(message_format));
                        look_for_parameter(string,"message_fromme", message_fromme, sizeof(message_fromme));
                        look_for_parameter(string,"private_message ", private_message, sizeof(private_message));
                        look_for_parameter(string,"private_message_fromme", private_message_fromme, sizeof(private_message_fromme));
                        look_for_parameter(string,"private_hidden", private_hidden, sizeof(private_hidden));
                        look_for_parameter(string,"file_path", file_path, sizeof(file_path));
                        look_for_parameter(string,"language", language, sizeof(language));
                        look_for_parameter(string,"daemon_listen", daemon_listen, sizeof(daemon_listen));
                        look_for_parameter(string,"nick_highlight_before", nick_highlight_before, sizeof(nick_highlight_before));
                        look_for_parameter(string,"nick_highlight_after", nick_highlight_after, sizeof(nick_highlight_after));
                        look_for_parameter(string,"str_w_n_before", str_w_n_before, sizeof(str_w_n_before));
                        look_for_parameter(string,"str_w_n_after", str_w_n_after, sizeof(str_w_n_after));
                        look_for_parameter(string,"daemon_port", daemon_port);
                        look_for_parameter(string,"max_connect", max_users);
                        look_for_parameter(string,"history_size", history_size);
                        look_for_parameter(string,"engine", eng, sizeof(eng));
                        look_for_parameter(string,"long_life_data_engine", ld_eng, sizeof(ld_eng));
                        look_for_parameter(string,"shm_mess_id", shm_mess_id);
                        look_for_parameter(string,"shm_users_id", shm_users_id);
                        
//added by DD for VOC++ HSE
#ifdef SUPPORT_SHARED_MEMORY                    
                        shm_mess_id = ftok(mess_file_real,'r'); 
                        shm_users_id = ftok(who_file_real, 'r');
#endif                  
                        
                        look_for_parameter(string,"charset", charset, sizeof(charset));
                        look_for_parameter(string,"chat_url", chat_url, sizeof(chat_url));
                        look_for_parameter(string,"daemon_type", daemon_type);
                        look_for_parameter(string,"modvoc_socket", modvoc_socket, sizeof(modvoc_socket));

                        //VOC++ specific
                        look_for_parameter(string,"daemon_host", daemon_host, sizeof(daemon_host));
                        look_for_parameter(string,"daemon_port", daemon_port);
                        sprintf(daemon_port_str,"%d", daemon_port);
                        look_for_parameter(string,"locale", locale_str, sizeof(locale_str));
                        //end of VOC++ specific
#ifdef SUPPORT_MYSQL
                        look_for_parameter(string,"mysql_server", mysql_server, sizeof(mysql_server));
                        look_for_parameter(string,"mysql_user", mysql_user, sizeof(mysql_user));
                        look_for_parameter(string,"mysql_password", mysql_password, sizeof(mysql_password));
                        look_for_parameter(string,"mysql_db", mysql_db, sizeof(mysql_db));
                        look_for_parameter(string,"mysql_table_prefix", mysql_table_prefix, sizeof(mysql_table_prefix));

                        //select * from PREFIXrooms order by id =32 chars
                        get_rooms_query_length = strlen(mysql_table_prefix)+32;
                        get_rooms_query = new char[get_rooms_query_length];
                        snprintf(get_rooms_query, get_rooms_query_length, "select * from %srooms order by id", mysql_table_prefix);
                        get_rooms_query[get_rooms_query_length-1] = 0;
                        /*strcpy(get_rooms_query, "select * from ");
                        strcat(get_rooms_query, mysql_table_prefix);
                        strcat(get_rooms_query, "rooms order by id");*/
                        //select * from PREFIXmessages order by id =35 chars
                        get_messages_query_length = strlen(mysql_table_prefix)+35;
                        get_messages_query = new char[get_messages_query_length];
                        snprintf(get_messages_query, get_messages_query_length, "select * from %smessages order by id", mysql_table_prefix);
                        get_messages_query[get_messages_query_length-1] = 0;
                        //printf("%s\n", get_messages_query);
                        /*strcpy(get_messages_query, "select * from ");
                        strcat(get_messages_query, mysql_table_prefix);
                        strcat(get_messages_query, "messages order by id");        */
                        //select * from PREFIXwho order by user_name =30 chars
                        get_users_query_length = strlen(mysql_table_prefix)+37;
                        get_users_query = new char[get_users_query_length];
                        snprintf(get_users_query, get_users_query_length, "select * from %swho order by user_name", mysql_table_prefix);
                        get_users_query[get_users_query_length-1] = 0;
                        //printf("%s\n",get_users_query);
                        /*strcpy(get_users_query, "select * from ");
                        strcat(get_users_query, mysql_table_prefix);
                        strcat(get_users_query, "who order by user_name");*/
#endif
                }while(!feof(pFile));
                fclose (pFile);
        }
        if (strcmp(eng,"files") == 0) engine = ENGINE_FILES;
        else if(strcmp(eng,"mysql") == 0) engine = ENGINE_MYSQL;
        else if(strcmp(eng,"shm") == 0) engine = ENGINE_SHM;
        else if(strcmp(eng,"highspeed") == 0) engine = ENGINE_HIGHSPEED;
                        else {printf("Unknown engine. Cannot work\nExiting\n"); exit (-1);}
        if (strcmp(ld_eng,"files") == 0) ld_engine = ENGINE_FILES;
        else if(strcmp(eng,"highspeed") == 0) ld_engine = ENGINE_HIGHSPEED;
        else if(strcmp(ld_eng,"mysql") == 0) ld_engine = ENGINE_MYSQL;

#ifndef SUPPORT_MYSQL
        if (engine == ENGINE_MYSQL || ld_engine == ENGINE_MYSQL) {
                printf("Chat is configured to use mysql-engine but the daemon has been compiled without MySQL-support\nExiting\n");
                exit(-1);
        }
#endif
#ifndef SUPPORT_SHARED_MEMORY
        if (engine == ENGINE_SHM or engine == ENGINE_HIGHSPEED) {
                printf("Chat is configured to use SharedMemory V5-engine but the daemon has been compiled without SHM-support\nExiting\n");
                exit(-1);
        }
#endif

        //and the same story with lang-parameters
        strcpy(who_file,file_path);
        strcat(who_file, "languages/");
        strcat(who_file, language);
        strcat(who_file, ".php");

        pFile = fopen (who_file, "r");
        if (pFile == NULL) {perror ("Error opening config file"); exit (-1);}
        else {
                (void) lseek(fileno(pFile), 0, 0L);
                do{
                        fgets (string , 16383 , pFile);
                        look_for_parameter(string,"w_no_user", w_no_user, sizeof(w_no_user));
                        look_for_parameter(string,"w_server_restarting", w_server_restarting, sizeof(w_server_restarting));
                        look_for_parameter(string,"w_whisper_to", w_whisper_to, sizeof(w_whisper_to));
                        look_for_parameter(string,"w_only_one_tail", w_only_one_tail, sizeof(w_only_one_tail));

                        //VOC++ specific
                        look_for_parameter(string,"w_usr_adm_link", ADMINZ_PRIVATE, sizeof(ADMINZ_PRIVATE));
                        look_for_parameter(string,"w_usr_boys_link", BOYS_PRIVATE, sizeof(BOYS_PRIVATE));
                        look_for_parameter(string,"w_usr_girls_link", GIRLS_PRIVATE, sizeof(GIRLS_PRIVATE));
                        look_for_parameter(string,"w_usr_they_link", THEY_PRIVATE, sizeof(THEY_PRIVATE));
                        look_for_parameter(string,"w_usr_all_link", ALL_PRIVATE, sizeof(ALL_PRIVATE));
                        look_for_parameter(string,"w_usr_clan_link", CLAN_PRIVATE, sizeof(CLAN_PRIVATE));
                        look_for_parameter(string,"w_usr_shaman_link", SHAMAN_PRIVATE, sizeof(SHAMAN_PRIVATE));
                        look_for_parameter(string,"w_roz_clear_pub_adm", w_roz_clear_pub_adm, sizeof(w_roz_clear_pub_adm));
                        //end of VOC++ specific

                }while(!feof(pFile));
                fclose (pFile);
        }
        printf("OK\n");
}

//need it here for sigger.

DesignHeader *firstDH, *lastDH;
void sigger(int sig) {
#ifdef SUPPORT_MYSQL
        int recon = 0;
#endif
        switch (sig) {
                case SIGPIPE:
                        signal(sig, SIG_IGN);
                break;

                case SIGHUP:
                        snprintf(log_string,sizeof(log_string),"got signal SIGHUP, reloading config\n");
                        my_log(log_string);
                        initialize_parameters();
                        //VOC++ specific
                        setlocale(LC_ALL, locale_str);
                        //end VOC++ specific

                        //flush headers
                        DesignHeader *currentDH, *nextDH;
                        currentDH = firstDH;
                        nextDH = NULL;
                        while(currentDH!=NULL) {
                                nextDH = currentDH->next;
                                delete(currentDH);
                                currentDH = nextDH;
                        }
                        firstDH = NULL;
                        lastDH = NULL;
                        //signal(sig, SIG_IGN);
                break;
                case -1:
                // mysql_reconnect:)
#ifdef SUPPORT_MYSQL

                        for (int i=1;i<10;i++) {
                                snprintf(log_string,sizeof(log_string),"attempt n%d: waiting for %d second and then will try to reconnect\n",i,i*3);
                                my_log(log_string);
                                //my_log("waiting 5 seconds and then will try to reconnect to the database (or will die)\n");
                                sleep(i*3);
                                if (engine == ENGINE_MYSQL || ld_engine == ENGINE_MYSQL) {
                                        if (!mysql_real_connect(&mysql, mysql_server, mysql_user, mysql_password, mysql_db, 0, NULL, 0)) {
                                                snprintf(log_string,sizeof(log_string),"Failed to connect to database: Error: %s\n", mysql_error(&mysql));
                                                my_log(log_string);
                                                //exit(-1);
                                        }
                                        else {recon = 1; break;}
                                }
                        }
                        if (recon == 1)
                                my_log("reconnected!\n");
                        else { my_log("gave up... run mysql please\n\nExiting"); exit(-1);}
#endif
                        sig = SIGHUP;
                        signal(sig, SIG_IGN);
                break;
                default:
                        snprintf(log_string,sizeof(log_string),"got signal %d\n",sig);
                        my_log(log_string);
                        snprintf(log_string,sizeof(log_string),"statistic: %llu bytes IN, %llu bytes OUT \n",mystat->get_inc_bytes(), mystat->get_out_bytes());
                        my_log(log_string);
                        Client *currentC;
                        currentC = startC;
                        while (currentC != NULL) {
                                currentC->add_to_out(w_server_restarting);
                                currentC->add_to_out("<script>window.setTimeout('location.reload()',6000);</script>");
                                (void) currentC->send();
                                currentC = currentC->next;
                        }
                        fclose(log_file);
                        char pid_f[255];
                        FILE *pid_file;
                        strcpy(pid_f, data_path);
                        strcat(pid_f,"daemon/daemon.pid");
                        pid_file = fopen (pid_f, "w");
                        fputs("",pid_file);
                        fclose(pid_file);
                        signal(sig, SIG_DFL);
                        raise(sig);
                break;
        }
}

void daemonize() {
        int i=0;
        if(getppid()==1) return; /* already a daemon */
        if (getpid()!=1){
                i=fork();
                if (i<0) {perror("cannont fork"); exit (-1);} /* fork error */
                if (i>0) exit (0);/* parent exits */
                /* child (daemon) continues */
                setsid(); /* obtain a new process group */
                for (i=getdtablesize();i>=0;--i) close(i); /* close all descriptors */
                i=open("/dev/null",O_RDWR); dup(i); dup(i); /* handle standart I/O */

                char log_f[255];
                strcpy(log_f, data_path);
                strcat(log_f,"daemon/daemon.log");
                log_file = fopen (log_f, "a");
                if (log_file == NULL){ perror ("Error opening log file");exit (-1);}

                snprintf(log_string,sizeof(log_string),"daemon started with pid >%d<\n", getpid());
                my_log(log_string);
        }
        for(int i=0;i<16;i++)
                signal(i,sigger);
}

int main(int argc, char *argv[]) {

        int voc_server, client_sock, retval, sock_opt, last_in_list = 0, max_sock, total_messages =0, last_message_id = 0, new_messages = 0;
        struct sockaddr_in my_addr;
        struct sockaddr_in client_addr;
        int    iSem = 0, IsConsoleMode = 0;

        socklen_t sin_size;
        struct timeval listen_timeout;
        fd_set vocList, read_list, write_list, problem_list;/*list of sockets*/
        Client *firstC, *lastC, *currentC;
        Room *firstR, *lastR;
        User *firstU, *lastU;
        int i;
#ifdef SUPPORT_MOD_VOC
        int mv_serv, mv_mesgsock;
        struct sockaddr_un mv_uaddr;
        struct sockaddr_un mv_client_addr;
        fd_set  mv_list;
        char query[256];
#endif
#ifdef SUPPORT_SHARED_MEMORY                    
        char who_file_real[255];        
        char mess_file_real[255];
#endif  
                long su = -1;
                long sm = -1;
                                
        //end added by DD

        firstDH = NULL;
        lastDH = NULL;
        firstC = NULL;
        lastC = NULL;
        startC = firstC;
        currentC = NULL;
        firstR = NULL;
        lastR = NULL;
        firstU = NULL;
        lastU = NULL;

        //VOC++ added '--console' paramater for anti-daemonization
        printf("----------------------------------------------------\n");
        printf("VOC++ daemon: %s\n",_VOC_VERSION_);
        printf("Available parameters: daemon [data-dir] [--console]\n");
        printf("Recognized %d parameters.\n", argc - 1);

        strcpy(data_path, "../");

        if(argc == 2) { // 1 parameter only;
                if(strcmp(argv[1], "--console") != 0) strcpy(data_path, argv[1]);
                else { IsConsoleMode = 1; strcat(data_path,"../");}
        }
        else if(argc == 3) {
                strcpy(data_path, argv[1]);
                if(strcmp(argv[2], "--console") == 0) IsConsoleMode = 1;
        }

        printf("Recognizing: console mode is ");

        if(!IsConsoleMode) printf("OFF");
        else printf("ON");

        printf(", data_path = %s\n", data_path);
        fflush(stdout);
        //get it from config file:!
        initialize_parameters();

      //VOC++ specific
        zeroBlk[0] = '0';
        zeroBlk[1] = 0;
        printf("Locale: [%s]\n", setlocale(LC_ALL, locale_str));

#ifdef SUPPORT_SHARED_MEMORY        
        strcpy(who_file_real,data_path);
        strcat(who_file_real, "who.dat");
        
        strcpy(mess_file_real,data_path);
        strcat(mess_file_real, "messages.dat");
#endif
        
        //end VOC++ specific

        char log_f[255];
        strcpy(log_f, data_path);
        strcat(log_f,"daemon/daemon.log");
        log_file = fopen (log_f, "a");
        if (log_file == NULL) {perror ("Error opening log file");exit(-1);}

        //VOC++ specific
        if(!IsConsoleMode) { daemonize(); }
        else {
                        printf("Console mode enabled (no daemonization)\n");
                        for(int i=0;i<16;i++)
                                        signal(i,sigger);
        }
        //End VOC++ specific

        time_t mtm = time(NULL);
        strftime(start_time,(size_t)23,"%Y-%m-%d %T  ",localtime(&mtm));
        mystat = new Statistic();


//daemonize closes all out/inc connections :)
#ifdef SUPPORT_MYSQL
        if (engine == ENGINE_MYSQL || ld_engine == ENGINE_MYSQL) {
                mysql_init(&mysql);
                if (&mysql == NULL) {
                        my_log("sorry, no enough memory to allocate mysql-connection\nExiting\n");
                        exit(-1);
                }
                if (!mysql_real_connect(&mysql, mysql_server, mysql_user, mysql_password, mysql_db, 0, NULL, 0)) {
                        snprintf(log_string,sizeof(log_string),"Failed to connect to database: Error: %s\nExiting\n", mysql_error(&mysql));
                        my_log(log_string);
                        exit(-1);
                }
        }
#endif
#ifdef SUPPORT_SHARED_MEMORY
        if(engine == ENGINE_SHM || engine == ENGINE_HIGHSPEED) {
                //connecting to the shared memory block and creating semaphores
                if ((su = shmget (shm_users_id, shm_usize, IPC_CREAT | 0777)) == -1) {
                        snprintf(log_string,sizeof(log_string),"shmget: shmget for users_id %d failed. error %s\nExiting\n",shm_users_id, strerror(errno));
                        my_log(log_string);
                        exit(-1);
                }
                if ((sm = shmget (shm_mess_id, shm_msize, IPC_CREAT | 0777)) == -1) {
                        snprintf(log_string,sizeof(log_string),"shmget: shmget for mess_id %d failed. error %s\nExiting\n",shm_mess_id, strerror(errno));
                        my_log(log_string);
                        exit(-1);
                }
            if ((shm_users = (char *)shmat(su, NULL, 0)) == (char *) -1) {
                my_log("shmat failed for userlist segment\n");
                       exit(-1);
            }
            if ((shm_mess = (char *)shmat(sm, NULL, 0)) == (char *) -1) {
                my_log("shmat failed for messages segment\n");
                       exit(-1);
            }

                                snprintf(log_string,sizeof(log_string),"userlist semaphore key: 0x%X\n", (unsigned)ftok(who_file_real, 'v'));
                                my_log(log_string);
                
                                snprintf(log_string,sizeof(log_string),"messages list semaphore key: 0x%X\n", (unsigned)ftok(mess_file_real, 'v'));
                                my_log(log_string);
                        
                            sem_users = php_sem_get(ftok(who_file_real, 'v'));
                                
                if (!sem_users) {
                        snprintf(log_string,sizeof(log_string),"semget: semget for users_id %d failed. error %s\nExiting\n",sem_users, strerror(errno));
                        my_log(log_string);
                        exit(-1);
                }
                                
                                sem_mess = php_sem_get(ftok(mess_file_real, 'v'));
                if (!sem_mess) {
                        snprintf(log_string,sizeof(log_string),"semget: semget for mess_id %d failed. error %s\nExiting\n",sem_mess, strerror(errno));
                        my_log(log_string);
                        exit(-1);
                }

                                snprintf(log_string,sizeof(log_string),"userlist semaphore key: 0x%X, id = %d\n", (unsigned)ftok(who_file_real, 'v'), sem_users);
                                my_log(log_string);
                
                                snprintf(log_string,sizeof(log_string),"messages list semaphore key: 0x%X, id = %d\n", (unsigned)ftok(mess_file_real, 'v'), sem_mess);
                                my_log(log_string);
                                
                                if(!php_sem_aquire(sem_users)) {
                                        snprintf(log_string,sizeof(log_string),"Acquiring test failed for userlist semaphore\n");
                                        my_log(log_string);
                                        exit(-1);
                                }
                                else {
                                        if(!php_sem_release(sem_users)) {
                                        snprintf(log_string,sizeof(log_string),"Releasing test failed for userlist semaphore\n");
                                        my_log(log_string);
                                        exit(-1);
                                        }
                                }
                                
                                if(!php_sem_aquire(sem_mess)) {
                                        snprintf(log_string,sizeof(log_string),"Acquiring test failed for messages list semaphore\n");
                                        my_log(log_string);
                                        exit(-1);
                                }
                                else {
                                        if(!php_sem_release(sem_mess)) {
                                        snprintf(log_string,sizeof(log_string),"Releasing test failed for messages list semaphore\n");
                                        my_log(log_string);
                                        exit(-1);
                                        }
                                }
                }               
#endif
        max_messages = 40;
        Message* messages;
        messages = new Message[max_messages];

        my_addr.sin_family = AF_INET;         /* host byte order */
        my_addr.sin_port = htons(daemon_port);     /* short, network byte order */
        my_addr.sin_addr.s_addr = inet_addr(daemon_listen); /* auto-fill with my IP */
        bzero(&(my_addr.sin_zero), 8);        /* zero the rest of the struct */

        if ((voc_server = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
                my_log("problem: cannot create socket, EXITING. Other copy of daemon is started?\n");
                return(-1);
        }
                sock_opt = 1;
        if (setsockopt(voc_server, SOL_SOCKET, SO_REUSEADDR, (void *)&sock_opt, sizeof (sock_opt)) == -1) {
                my_log("problem with  setsockopt(SO_REUSEADDR). EXITING. Other copy of daemon is started?\n");
                (void) close(voc_server);
                return (-1);
        }


        fcntl(voc_server, F_SETFL, O_NONBLOCK);
        if (bind(voc_server, (struct sockaddr *)&my_addr, sizeof(struct sockaddr)) == -1) {
                my_log("problem: Cannot bind to a given ip&port. EXITING. Other copy of daemon is started?\n");
                (void) close(voc_server);
                return (-1);
        }
        if (listen(voc_server, max_users) == -1) {
                my_log("Problem: cannot listen. EXITING. Other copy of daemon is started?\n");
                (void) close(voc_server);
                return (-1);
        }
#ifdef SUPPORT_MOD_VOC
        bzero (&(mv_uaddr), sizeof (mv_uaddr));
        mv_uaddr.sun_family = AF_UNIX;
        strcpy (mv_uaddr.sun_path, modvoc_socket);
        unlink(mv_uaddr.sun_path);

        if ((mv_serv = socket (AF_UNIX, SOCK_STREAM, 0)) == -1) {
                my_log ("problem: cannot create socket, EXITING. Other copy of daemon is started?\n");
                (void) close(voc_server);
                return (-1);
        }
        if (bind(mv_serv, (struct sockaddr *) &mv_uaddr, sizeof (struct sockaddr)) == -1) {
           printf("problem: Cannot bind to a given unix sock.\n");        
           my_log ("problem: Cannot bind to a given unix sock. EXITING. Other copy of daemon is started?\n");
                (void) close(voc_server);
                (void) close (mv_serv);
                return (-1);
        }
        chmod(mv_uaddr.sun_path, 0777);
        if (listen(mv_serv, max_users) == -1) {
                my_log("Problem: cannot listen. EXITING. Other copy of daemon is started?\n");
                (void) close(voc_server);
                (void) close(mv_serv);
                return (-1);
        }
#endif
        char c_pid[8];
        char pid_f[255];
        FILE *pid_file;
        sprintf(c_pid,"%d",getpid());
        //writing pid
        strcpy(pid_f, data_path);
        strcat(pid_f,"daemon/daemon.pid");
        pid_file = fopen (pid_f, "w");
        fputs(c_pid,pid_file);
        fclose(pid_file);

        /*starting main loop. never ends :)*/
        while(1) {
                /*sleeping for a second */

                                if(engine == ENGINE_SHM || engine == ENGINE_HIGHSPEED) {
                                        for(iSem = 0; iSem < 10; iSem++) {
                                                php_check_sem(sem_mess);
                                                php_check_sem(sem_users);
                                                usleep(90000);
                                        }
                                } else sleep(1);
                                                                

                //VOC++ specific
                clearFlag = 0;
                //End VOC++ specific

                users_in_chat = get_users_list(firstU, lastU, firstC, lastC);
                if (users_in_chat == 0) {
                        continue;
                }
                total_messages = get_messages_list(messages, new_messages, last_message_id);
                get_rooms_list(firstR, lastR);
                if (daemon_type != ONMODVOC) {
                        /* checking for new connections max 30 times */
                        for (int i=0;i<30;i++) {
                                listen_timeout.tv_sec = 0;
                                listen_timeout.tv_usec = 0;
                                FD_ZERO(&vocList);
                                FD_SET(voc_server, &vocList);
                                retval = select(voc_server+1, &vocList, NULL, NULL, &listen_timeout);
                                if(retval> 0) {
                                        //actually, i guess i don't need this additional checker
                                        if (FD_ISSET(voc_server, &vocList)) {
                                                sin_size = sizeof(struct sockaddr_in);
                                                if ((client_sock = accept(voc_server, (struct sockaddr *)&client_addr, &sin_size)) == -1) {
                                                        snprintf(log_string,sizeof(log_string),"server: cannot accept connection! %s\n", strerror(errno));
                                                        my_log(log_string);
                                                        continue;
                                                }
                                                snprintf(log_string,sizeof(log_string),"server: got connection from %s\n", inet_ntoa(client_addr.sin_addr));
                                                my_log(log_string);
                                                //total_requests++;
                                                mystat->add_req();
                                                mystat->add_con();

                                                fcntl(client_sock, F_SETFL, O_NONBLOCK);
                                                currentC = new Client(client_sock, inet_ntoa(client_addr.sin_addr));
                                                if (lastC == NULL) { firstC = currentC; lastC = currentC; currentC->next = NULL; currentC->prev = NULL;}
                                                else { lastC->next = currentC; currentC->next = NULL; currentC->prev = lastC; lastC = currentC;}
                                                startC = firstC;
                                                last_in_list++;
                                        }
                                } else {break;}
                        }
                }
                FD_ZERO(&read_list);
                FD_ZERO(&write_list);
                FD_ZERO(&problem_list);
                max_sock = 0;
                currentC = firstC;
                active_connections = 0;
                while (currentC!=NULL) {
                        if (currentC->get_status() == ONLINE && currentC->get_exists_in_list() == 0) {
                                //unclosed connection, 'cause there are no such user in chat.
                                Client *tmpC;
                                tmpC = currentC->next;
                                remove_client(currentC, firstC, lastC,"no such user in the online-list");
                                currentC = tmpC;
                                continue;
                        }
                        if (currentC->get_status() == ONLINE) active_connections++;
                        int tmp_sock = currentC->get_socket_id();
                        FD_SET(tmp_sock, &read_list);
                        FD_SET(tmp_sock, &write_list);
                        FD_SET(tmp_sock, &problem_list);
                        if (tmp_sock > max_sock) max_sock = tmp_sock;
                        //on the next loop we'll update 'exists in list' while updating users-list
                        currentC->set_exists_in_list(0);
                        currentC = currentC->next;
                }
                listen_timeout.tv_sec = 0;
                listen_timeout.tv_usec = 10000;
                retval = select(max_sock+1, &read_list, &write_list, &problem_list, &listen_timeout);
                if (retval >0 ) {
                        currentC = firstC;
                        while (currentC!=NULL) {
                                if (currentC->check_connection_loop() == -1) {
                                        /* disconnect client */
                                        currentC->add_to_out("sorry, too long connecton attempt\n");
                                        currentC->send();
                                        remove_client(currentC, firstC, lastC,"too long connection attempt");
                                        continue;
                                }
                                if (FD_ISSET(currentC->get_socket_id(), &problem_list)) {
                                        remove_client(currentC, firstC, lastC, "exception in socket");
                                        continue;
                                }
                                if (FD_ISSET(currentC->get_socket_id(), &write_list) ) {
                                        /* writing content to a client*/
                                        currentC->set_active(1);
                                        if (currentC->get_status() == ONLINE)
                                                currentC->process_messages(messages, total_messages, new_messages, firstU, lastU, firstR, lastR);
                                        if (currentC->send() == -1) {
                                                /*remove client then*/
                                                remove_client(currentC, firstC, lastC,"cannot send data to the socket");
                                                continue;
                                        }
                                } else
                                        currentC->set_active(0);
                                /*new users don't need proccess_messages, which is in previous block*/
                                if (FD_ISSET(currentC->get_socket_id(), &read_list)) {
                                        /* reading content from a client*/
                                        if (currentC->recv(firstU, lastU, messages, total_messages, firstDH, lastDH, firstR, lastR) == -1) {
                                                /*remove client*/
                                                remove_client(currentC, firstC, lastC,"cannot retrieve data from the socket");
                                                continue;
                                        }
                                }
                                currentC = currentC->next;
                        }
                }

#ifdef SUPPORT_MOD_VOC
                /* checking for new connections from MOD_VOC max 30 times */
                /* I've moved it after client->process_messages, because this code sets status = ONLINE */
                /* so I can get some messages twice*/
                if (daemon_type != ONMODVOC) continue;
                for (int i=0;i<30;i++) {
                        //snprintf(log_string,sizeof(log_string),"cycle %d\n",i);
                        //my_log(log_string);
                        listen_timeout.tv_sec = 0;
                        listen_timeout.tv_usec = 0;
                        FD_ZERO(&mv_list);
                        FD_SET(mv_serv, &mv_list);
                        retval = select(mv_serv+1, &mv_list, NULL, NULL, &listen_timeout);
                        //snprintf(log_string,sizeof(log_string),"retval is %d\n",retval);
                        //my_log(log_string);
                        if(retval> 0) {
                                //actually, i guess i don't need this additional checker
                                if (FD_ISSET(mv_serv, &mv_list)) {
                                        sin_size = sizeof(struct sockaddr_un);
                                        if ((mv_mesgsock = accept(mv_serv, (struct sockaddr *)&mv_client_addr, &sin_size)) == -1) {
                                                snprintf(log_string,sizeof(log_string),"server: cannot accept connection2. %s\n", strerror(errno));
                                                my_log(log_string);
                                                continue;
                                        }
                                        client_sock = recv_fd(mv_mesgsock, query);
                                        close(mv_mesgsock);

                                        snprintf(log_string,sizeof(log_string),"server: got connection from mod_voc, query is >%s<\n", query);
                                        my_log(log_string);
                                        //total_requests++;
                                        mystat->add_req();
                                        mystat->add_con();

                                        if (client_sock >0) {
                                                fcntl(client_sock, F_SETFL, O_NONBLOCK);
                                                sin_size = sizeof(struct sockaddr_in);
                                                if (getpeername(client_sock, (struct sockaddr *)&client_addr, &sin_size) == -1)
                                                        currentC = new Client(client_sock, "apache");
                                                else
                                                        currentC = new Client(client_sock, inet_ntoa(client_addr.sin_addr));
                                                if (lastC == NULL) { firstC = currentC; lastC = currentC; currentC->next = NULL; currentC->prev = NULL;}
                                                else { lastC->next = currentC; currentC->next = NULL; currentC->prev = lastC; lastC = currentC;}
                                                startC = firstC;
                                                last_in_list++;

                                                sprintf(currentC->inc_buffer, "GET /?%s HTTP/1.0\012\015", query);
                                                if (currentC->recv(firstU, lastU, messages, total_messages, firstDH, lastDH, firstR, lastR, 0) == -1) {
                                                        remove_client(currentC, firstC, lastC,"no ses?");
                                                }
                                        }
                                }
                        } else {break;}
                }//end of operation with unix-sock
#endif
        }

        return 0;
}


int regular_sem_get(long key) {
        return(semget(key, 3, 0777|IPC_CREAT));
}

int php_sem_get(long key)
{
        int max_acquire = 1, perm = 0777;
        int semid;
        struct sembuf sop[3];
        int count;
                
        /* Get/create the semaphore.  Note that we rely on the semaphores
         * being zeroed when they are created.  Despite the fact that
         * the(?)  Linux semget() man page says they are not initialized,
         * the kernel versions 2.0.x and 2.1.z do in fact zero them.
         */
        semid = semget(key, 3, perm|IPC_CREAT);
        if (semid == -1) {
                snprintf(log_string,sizeof(log_string),"SEM_GET failed for key 0x%lx: %s", key, strerror(errno));
                my_log(log_string);                                     
                return 0;
        }

        /* Find out how many processes are using this semaphore.  Note
         * that on Linux (at least) there is a race condition here because
         * semaphore undo on process exit is not atomic, so we could
         * acquire SYSVSEM_SETVAL before a crashed process has decremented
         * SYSVSEM_USAGE in which case count will be greater than it
         * should be and we won't set max_acquire.  Fortunately this
         * doesn't actually matter in practice.
         */

        /* Wait for sem 1 to be zero . . . */
        sop[0].sem_num = SYSVSEM_SETVAL;
        sop[0].sem_op  = 0;
        sop[0].sem_flg = 0;

        /* . . . and increment it so it becomes non-zero . . . */
        sop[1].sem_num = SYSVSEM_SETVAL;
        sop[1].sem_op  = 1;
        sop[1].sem_flg = SEM_UNDO;

        /* . . . and increment the usage count. */
        sop[2].sem_num = SYSVSEM_USAGE;
        sop[2].sem_op  = 1;
        sop[2].sem_flg = SEM_UNDO;
        
        while (semop(semid, sop, 3) == -1) {
                if (errno != EINTR) {
                        snprintf(log_string,sizeof(log_string),"SEMOP (init) failed acquiring SYSVSEM_SETVAL for key 0x%lx: %s", key, strerror(errno));
                        my_log(log_string);                                     
                        return 0;
                }
        }

        /* Get the usage count. */
        count = semctl(semid, SYSVSEM_USAGE, GETVAL, NULL);
        if (count == -1) {
                snprintf(log_string,sizeof(log_string),"SEMCTL (usage count) failed for key 0x%lx: %s", key, strerror(errno));
                my_log(log_string);                                     
                return 0;               
        }

        /* If we are the only user, then take this opportunity to set the max. */
        if (count == 1) {
                /* This is correct for Linux which has union semun. */
                union semun semarg;
                semarg.val = max_acquire;
                if (semctl(semid, SYSVSEM_SEM, SETVAL, semarg) == -1) {
                        snprintf(log_string,sizeof(log_string),"SEMCTL/Linux (max_aquire) failed for key 0x%lx: %s", key, strerror(errno));
                        my_log(log_string);                     
                        return 0;
                }
        }

        /* Set semaphore 1 back to zero. */
        sop[0].sem_num = SYSVSEM_SETVAL;
        sop[0].sem_op  = -1;
        sop[0].sem_flg = SEM_UNDO;
        
        while (semop(semid, sop, 1) == -1) {
                if (errno != EINTR) {
                        snprintf(log_string,sizeof(log_string),"SEMOP (sem 1 sets to zero) failed releasing SYSVSEM_SETVAL for key 0x%lx: %s", key, strerror(errno));
                        my_log(log_string);                     
                        return 0;
                }
        }
        //here sem is fully initialized but not aquired!
        //aquiring
        //if(php_sysvsem_semop(semid, 1)) return semid;
        return semid;
}

int php_sysvsem_semop(int semid, int acquire)
{
        struct sembuf sop;
        int nAbort = 0; 
        if (!acquire && semid == 0) {
                snprintf(log_string,sizeof(log_string),"SysV semaphore %ld is not currently acquired", semid);
        my_log(log_string);
                return 0;
        }

        sop.sem_num = SYSVSEM_SEM;
        sop.sem_op  = acquire ? -1 : 1;
        sop.sem_flg = SEM_UNDO | IPC_NOWAIT;
        //sop.sem_flg = SEM_UNDO;
        /*
                if (errno != EINTR or nAbort > 500) {
                        snprintf(log_string,sizeof(log_string),"SEMOP failed to %s semid %ld: %s\n", acquire ? "acquire" : "release", semid, strerror(errno));
                        my_log(log_string);
                        return 0;
        } */
                
        while (semop(semid, &sop, 1) == -1) {
                if(nAbort > 1500) { php_unlock_sem(semid); return 0; }
                usleep(2000); // 1500 iterations x 2000 microseconds each (3 sec) should be enough to aquire sem, ele sem is locked infinitely.
                nAbort++;
        }
        
        return 1;
}

int php_sem_release(int semid) {
    if(semid) {
            if(php_sysvsem_semop(semid, 0)) return 1;
    }   
    else return 0;
}

int php_sem_aquire(int semid) {
    if(semid) {
            if(php_sysvsem_semop(semid, 1)) return 1;
    }   
    else return 0;
}

int php_sem_status(int semid) {
        if(semid) {
                printf("SYSVSEM_SEM    = %d\n", semctl(semid, SYSVSEM_SEM, GETVAL, 0));
                        printf("SYSVSEM_USAGE  = %d\n", semctl(semid, SYSVSEM_USAGE, GETVAL, 0));
                        printf("SYSVSEM_SETVAL = %d\n", semctl(semid, SYSVSEM_SETVAL, GETVAL, 0));
    }   
    else return 0;
}

int php_get_usage(int semid) {
    if(semid) {
           return(semctl(semid, SYSVSEM_USAGE, GETVAL, 0));
        }
}

int php_get_val(int semid) {
    if(semid) {
           return(semctl(semid, SYSVSEM_SEM, GETVAL, 0));
        }
}

int php_check_sem(int semid) {
int iUsage, iVal;
 
 iUsage = php_get_usage(semid);
 iVal   = php_get_val(semid);
 
 if(iUsage >= SHM_MAX_USAGE || iVal < 0 || iVal > 1) php_unlock_sem(semid);
 
}
 
int php_unlock_sem(int semid) {
union semun arg;
int retn, oUsage, oVal;

        if(semid) {
          oUsage = semctl(semid, SYSVSEM_USAGE, GETVAL, 0);
          oVal   = semctl(semid, SYSVSEM_SEM, GETVAL, 0);
          
          snprintf(log_string,sizeof(log_string),"---------------------------------------------------------------\n");
      my_log(log_string);
          
          snprintf(log_string,sizeof(log_string),"Detected overload OR locked sem for %ld (%d processes are in the queue, %d value)\n", semid, oUsage, oVal);
      my_log(log_string);
          
      arg.val = 1;
          retn = semctl(semid, SYSVSEM_SEM, SETVAL, arg);
      if(retn == -1) snprintf(log_string,sizeof(log_string),"SEMCTL failed to unlock semid %ld (SYSVSEM_SEM). Result is: %s\n", semid, strerror(errno));
          else snprintf(log_string,sizeof(log_string),"SEMCTL unlocked semid (SYSVSEM_SEM) %ld\n", semid);
          my_log(log_string);
          
          snprintf(log_string,sizeof(log_string),"---------------------------------------------------------------\n");
      my_log(log_string);
        
          return(retn);
        }
 return 1;      
}
