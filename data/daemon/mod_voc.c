/************************************************************
*                                                           *
*                Voodoo chat daemon                         *
*               Apache 1.3.x Module                         *
*                    v. 1.0 RC 1                            *
*                                                           *
*                file: mod_voc.cpp                          *
*             (c) 2004 by Vlad Vostrykh                     *
*                 voodoo@vochat.com                         *
*                http://vochat.com/                         *
*                                                           *
*                 QPL ver1 License                          *
*           See voc/LICENSE file for details                *
*                                                           *
*                                                           *
************************************************************/
#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <sys/types.h>
#include <unistd.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <sys/socket.h>
#include <sys/file.h>
#include <fcntl.h>
//unix sockets
#include <sys/un.h>
#include <sys/uio.h>

//to avoid error with timeval structure
#include <sys/time.h>
#include <time.h>
#include <signal.h>

#include "httpd.h"
#include "http_config.h"
#include "http_core.h"
#include "http_log.h"
#include "http_main.h"
#include "http_protocol.h"
#include "util_script.h"

typedef struct {
	char *serv_sock;
} voc_dir_t;

module voc_module;
static void *
voc_dirconf (ap_pool * _pool, char *notused){
	voc_dir_t *voc_conf = (voc_dir_t *) ap_pcalloc (_pool, sizeof (voc_dir_t));
	voc_conf->serv_sock = "/tmp/vochat";
}

static void *
voc_merge_dirconf (pool * _pool, void *parent_conf, void *newloc_conf) {
	voc_dir_t *merged = (voc_dir_t *) ap_pcalloc (_pool, sizeof (voc_dir_t));
	voc_dir_t *pconf = (voc_dir_t *) parent_conf;
	voc_dir_t *nconf = (voc_dir_t *) newloc_conf;
	merged->serv_sock = strcmp (nconf->serv_sock, "/tmp/vochat") ? nconf->serv_sock : pconf->serv_sock;
	return merged;
}

#define CMDCONST static const
CMDCONST char *
set_sock (cmd_parms * cmd, voc_dir_t * voc_conf, char *conf_sock) {
	voc_conf->serv_sock = conf_sock;
	return NULL;
}

static const command_rec voc_cmds[] = {
	{"VocSocket", set_sock, NULL, OR_OPTIONS, TAKE1, "Unix Socket of Voc-daemon"},
	{NULL}
};

static void send_error(request_rec *req, char *errtext, int error) {
	req->content_type = "text/html";
	req->status = error;
	ap_send_http_header(req);
	ap_rputs(DOCTYPE_HTML_3_2, req);
	ap_rputs("<html><head><title>Voodoo chat mod_voc module.</title></head><body>\n", req);
	ap_rprintf(req, "<h2> Cannot connect to Voodoo Chat daemon, <br> error is: %s<br><br></h2>", errtext);
	ap_rputs("<i>Please, contact server administrator</i>\n<!-- this is just a long lines which forces IE to display my text\n"\
			"//                                                       //\n"\
			"//                                                       //\n"\
			"//                                                       //\n"\
			"//                                                       //\n"\
			"//                                                       //\n"\
			"//               btw, http://vochat.com                  //\n"\
			"//                                                       //\n"\
			"//                                                       //\n"\
			"//                                                       //\n"\
			"-->\n</body></html>\n", req);
	ap_kill_timeout(req);
}

static struct cmsghdr *cmptr = NULL;	/* buffer is malloc'ed first time */
#define CONTROLLEN (sizeof(struct cmsghdr) + sizeof(int))

static int voc_handler (request_rec * req) {
	int error = 0, mysent = 0;
	//well, for the daemon i need: 32 for sessID, and 4 for daemon type (&p=1)
	//so set max limit to 256, probably will use something later.
	char myargs[256];
	if (req->args == NULL )
		send_error(req, "no request", 400);
	else {
		voc_dir_t *voc_conf = ap_get_module_config (req->per_dir_config, &voc_module);
		int sockfd, servlen;
		struct sockaddr_un serv_uaddr;
		bzero (&(serv_uaddr), sizeof (serv_uaddr));
		serv_uaddr.sun_family = AF_UNIX;
		strcpy (serv_uaddr.sun_path, voc_conf->serv_sock);
		if ((sockfd = socket (AF_UNIX, SOCK_STREAM, 0)) == -1)
			send_error(req, strerror(errno), 503);
		else {
#if defined(__FreeBSD__)
			serv_uaddr.sun_len = sizeof(serv_uaddr.sun_len)+ sizeof(serv_uaddr.sun_family)+strlen(serv_uaddr.sun_path);
			if (connect (sockfd, (struct sockaddr *) &serv_uaddr, serv_uaddr.sun_len) == -1)
#else
		//linux, but probably some else
			servlen = strlen (serv_uaddr.sun_path) + sizeof (serv_uaddr.sun_family);
			if (connect (sockfd, (struct sockaddr *) &serv_uaddr, servlen) == -1)
#endif
				send_error(req, strerror(errno), 503);
			else {
				//sending socket to Voodoo Chat daemon
				struct iovec iov[2];
				struct msghdr msg;
				char buf[2];
				iov[0].iov_base = buf;
				iov[0].iov_len = 2;

				strncpy(myargs, req->args, 255);
				myargs[255] = 0;
				iov[1].iov_base = myargs;
				iov[1].iov_len = strlen(myargs);

				msg.msg_iov = iov;
				msg.msg_iovlen = 2;

				msg.msg_name = NULL;
				msg.msg_namelen = 0;
				if (req->connection->client->fd < 0) {
					msg.msg_control = NULL;
					msg.msg_controllen = 0;
					buf[1] = -req->connection->client->fd;	/* nonzero status means error */
					if (buf[1] == 0)
						buf[1] = 1;	/* -256, etc. would screw up protocol */
				} else {
					if (cmptr == NULL && (cmptr = malloc (CONTROLLEN)) == NULL) {
						send_error(req, "cannot malloc memory", 503);
						error = 1;
					} else {
						//(cmptr = cmsghdr *)malloc(CONTROLLEN)) == NULL)
						cmptr->cmsg_level = SOL_SOCKET;
						cmptr->cmsg_type = SCM_RIGHTS;
						cmptr->cmsg_len = CONTROLLEN;
						msg.msg_control = (caddr_t) cmptr;
						msg.msg_controllen = CONTROLLEN;
						*(int *) CMSG_DATA (cmptr) = req->connection->client->fd;	/* the fd to pass */
						buf[1] = 0;	/* zero status means OK */
					}
				}
				if (!error) {
					buf[0] = 0;		/* null byte flag to recv_fd() */
					mysent = sendmsg (sockfd, &msg, 0);
					if (mysent == -1)
						send_error(req, strerror(errno), 503);
					else 
						//if ok, and now the socket in the voc-daemon, let's tell apache that we don't have it:
						req->connection->client->fd = -1;
				}
			}//end of if connect
			close(sockfd);
		}//end of if socket()
	}
	return OK;
}

static const handler_rec voc_handlers[] = {
	{"voc-handler", voc_handler},
	{NULL}
};


module voc_module = {
	STANDARD_MODULE_STUFF,
	NULL,			/* module initializer */
	voc_dirconf,		/* per-directory config creator */
	voc_merge_dirconf,	/* dir config merger */
	NULL,			/* server config creator */
	NULL,			/* server config merger */
	voc_cmds,		/* command table */
	voc_handlers,		/* [7] list of handlers */
	NULL,			/* [2] filename-to-URI translation */
	NULL,			/* [5] check/validate user_id */
	NULL,			/* [6] check user_id is valid *here* */
	NULL,			/* [4] check access by host address */
	NULL,			/* [7] MIME type checker/setter */
	NULL,			/* [8] fixups */
	NULL,			/* [10] logger */
#if MODULE_MAGIC_NUMBER >= 19970103
	NULL,			/* [3] header parser */
#endif
#if MODULE_MAGIC_NUMBER >= 19970719
	NULL,			/* process initializer */
#endif
#if MODULE_MAGIC_NUMBER >= 19970728
	NULL,			/* process exit/cleanup */
#endif
#if MODULE_MAGIC_NUMBER >= 19970902
	NULL			/* [1] post read_request handling */
#endif
};
