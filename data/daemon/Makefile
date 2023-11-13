
CC=g++

MYSQL_CONFIG=mysql_config
CFLAGS=

ifdef MYSQL
	CFLAGS +=  -DSUPPORT_MYSQL `$(MYSQL_CONFIG) --libs` `$(MYSQL_CONFIG) --include`
endif

ifdef SHM
	CFLAGS += -DSUPPORT_SHARED_MEMORY
endif

ifdef MOD_VOC
	CFLAGS += -DSUPPORT_MOD_VOC
endif

all:
	$(CC) $(CFLAGS) daemon.cpp -o daemon
