/************************************************************
*                                                           *
*                Voodoo chat daemon                         *
*               Apache 2.0.x Module                         *
*                    v. 1.0 RC 1                            *
*                                                           *
*                 file: mod_voc2.c                          *
*             (c) 2005 by Vlad Vostrykh                     *
*                 voodoo@vochat.com                         *
*                http://vochat.com/                         *
*                                                           *
*                 QPL ver1 License                          *
*           See voc/LICENSE file for details                *
*                                                           *
*                                                           *
************************************************************/

#define CORE_PRIVATE

#include "ap_config.h"
#include "ap_mmn.h"
#include "httpd.h"
#include "http_config.h"
#include "http_connection.h"
#include "http_core.h"

#include "apr_portable.h"

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


module AP_MODULE_DECLARE_DATA voc2_module;

typedef struct {
	char *serv_sock;
} voc_dir_t;


static void * voc_dirconf (apr_pool_t * _pool, char *notused){
	voc_dir_t *voc_conf = (voc_dir_t *) apr_pcalloc (_pool, sizeof (*voc_conf));
	voc_conf->serv_sock = NULL;
}

static const char *set_sock (cmd_parms * cmd, void *offset, const char *conf_sock) {
	voc_dir_t *voc_conf = ap_get_module_config(cmd->server->module_config, &voc2_module);
	//filename:
	return ap_set_file_slot(cmd, offset, conf_sock);
}

static const command_rec voc_cmds[] = {
	AP_INIT_TAKE1("VocSocket", set_sock, (void *)APR_OFFSETOF(voc_dir_t, serv_sock), OR_OPTIONS, "Unix Socket to connect to Voc-daemon"),
	{NULL}
};

static void send_error(request_rec *req, char *errtext, int error) {
	req->content_type = "text/html";
	req->status = error;
	//ap_send_http_header(req);
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
}

static struct cmsghdr *cmptr = NULL;	/* buffer is malloc'ed first time */
#define CONTROLLEN (sizeof(struct cmsghdr) + sizeof(int))

int voc2_handler(request_rec *r) {
	int sockfd, servlen, error = 0, mysent = 0;
	char myargs[256];
	apr_socket_t *client_socket = ap_get_module_config(r->connection->conn_config, &core_module);
	//In our case (linux/freebsd) -- just int for 'fd'
	apr_os_sock_t fd;
	apr_os_sock_get(&fd, client_socket);

	r->allowed |= (1 << M_GET);
	if (r->method_number != M_GET)
		return DECLINED;
	if (strcmp(r->handler, "voc2-handler")) {
		return DECLINED;
	}
	r->content_type = "text/html";
	if (r->header_only)
		return OK;
	
	if (r->args == NULL )
		send_error(r, "no request", 400);
	else {
		voc_dir_t *voc_conf = (voc_dir_t *) ap_get_module_config(r->per_dir_config, &voc2_module);
		struct sockaddr_un serv_uaddr;
		bzero (&(serv_uaddr), sizeof (serv_uaddr));
		serv_uaddr.sun_family = AF_UNIX;
		strcpy (serv_uaddr.sun_path, voc_conf->serv_sock);
		if ((sockfd = socket (AF_UNIX, SOCK_STREAM, 0)) == -1)
			send_error(r, strerror(errno), 503);
		else {
#if defined(__FreeBSD__)
			serv_uaddr.sun_len = sizeof(serv_uaddr.sun_len)+ sizeof(serv_uaddr.sun_family)+strlen(serv_uaddr.sun_path);
			if (connect (sockfd, (struct sockaddr *) &serv_uaddr, serv_uaddr.sun_len) == -1)
#else
		//linux, but probably some else
			servlen = strlen (serv_uaddr.sun_path) + sizeof (serv_uaddr.sun_family);
			if (connect (sockfd, (struct sockaddr *) &serv_uaddr, servlen) == -1)
#endif
				send_error(r, strerror(errno), 503);
			else {
				//sending socket to Voodoo Chat daemon
				struct iovec iov[2];
				struct msghdr msg;
				char buf[2];
				iov[0].iov_base = buf;
				iov[0].iov_len = 2;
				strncpy(myargs, r->args, 255);
				myargs[255] = 0;
				iov[1].iov_base = myargs;
				iov[1].iov_len = strlen(myargs);

				msg.msg_iov = iov;
				msg.msg_iovlen = 2;

				msg.msg_name = NULL;
				msg.msg_namelen = 0;

				if (fd < 0) {
					msg.msg_control = NULL;
					msg.msg_controllen = 0;
					buf[1] = -fd;	/* nonzero status means error */
					if (buf[1] == 0)
						buf[1] = 1;	/* -256, etc. would screw up protocol */
				} else {
					if (cmptr == NULL && (cmptr = malloc (CONTROLLEN)) == NULL) {
						send_error(r, "cannot malloc memory", 503);
						error = 1;
					} else {
						//(cmptr = cmsghdr *)malloc(CONTROLLEN)) == NULL)
						cmptr->cmsg_level = SOL_SOCKET;
						cmptr->cmsg_type = SCM_RIGHTS;
						cmptr->cmsg_len = CONTROLLEN;
						msg.msg_control = (caddr_t) cmptr;
						msg.msg_controllen = CONTROLLEN;
						*(int *) CMSG_DATA (cmptr) = fd;	/* the fd to pass */
						buf[1] = 0;	/* zero status means OK */
					}
				}
				if (!error) {
					buf[0] = 0;		/* null byte flag to recv_fd() */
					mysent = sendmsg (sockfd, &msg, 0);
					if (mysent == -1)
						send_error(r, strerror(errno), 503);
					else {
						//if ok, and now the socket in the voc-daemon, let's tell apache that we don't have it:
						r->connection->aborted = 1;
						r->eos_sent = 1;
					}

				}
			}//end of if connect
			close(sockfd);
		}//end of if socket()
	}
	return OK;
}


static void register_hooks(apr_pool_t *p) {
	ap_hook_handler(voc2_handler, NULL, NULL, APR_HOOK_MIDDLE);
}

module AP_MODULE_DECLARE_DATA voc2_module = {
	STANDARD20_MODULE_STUFF,
	voc_dirconf,		/* create per-directory config structures */
	NULL,				/* merge per-directory config structures  */
	NULL,				/* create per-server config structures    */
    NULL,				/* merge per-server config structures     */
	voc_cmds,			/* commands */
	register_hooks,		/* register hooks */
};
