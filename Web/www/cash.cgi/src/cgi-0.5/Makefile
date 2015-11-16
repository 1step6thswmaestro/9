# Generated automatically from Makefile.in by configure.
#
# Makefile.in
#
# CGI -- C CGI Library -- Makefile source
#
# Copyright (c) 2000 Todor Prokopov
# Copyright (c) 2000 Edward V. POPKOV
# Distributed under GPL, see COPYING for details
#
# Todor Prokopov     <koprok@newmail.net> 
# "Edward V. POPKOV" <evpopkov@carry.neonet.lv>
#
# $Id$
#
# $Log$

.PHONY: all clean distclean install test

prefix      = /usr/local
exec_prefix = ${prefix}

CC       = gcc
LD       = gcc
AR       = ar rs
RM       = rm -f
INSTALL  = /usr/bin/ginstall -c
CFLAGS   = -g -O2
DEFS     = -DHAVE_CONFIG_H
LDFLAGS  = -nostartfiles -shared
MAKE     = make
OBJFILES = cgi.o
STATIC   = libcgi.a
SHARED   = libcgi.so
HEADER   = cgi.h

subdir   = ./test

USER     = root
GROUP    = bin

all: $(STATIC) $(SHARED) test

$(STATIC): $(OBJFILES)
	$(AR) $(STATIC) $(OBJFILES)

$(SHARED): $(OBJFILES)
	$(LD) $(OBJFILES) $(LDFLAGS) -o $(SHARED)

clean:
	$(RM) $(OBJFILES) $(STATIC) $(SHARED)
	$(MAKE) -C $(subdir) clean

distclean: clean
	$(RM) Makefile config.cache config.log config.status config.h
	$(MAKE) -C $(subdir) distclean

install: $(STATIC) $(SHARED)
	$(INSTALL) -o $(USER) -g $(GROUP) -m 0440 $(STATIC) ${exec_prefix}/lib
	$(INSTALL) -o $(USER) -g $(GROUP) -m 0550 $(SHARED) ${exec_prefix}/lib
	$(INSTALL) -o $(USER) -g $(GROUP) -m 0440 $(HEADER) ${prefix}/include

test: $(STATIC) $(SHARED)
	$(MAKE) -C $(subdir) all

%.o: %.c config.h cgi.h Makefile
	$(CC) $< -c -I. $(CFLAGS) $(DEFS) -o $@

# End of Makefile.in
