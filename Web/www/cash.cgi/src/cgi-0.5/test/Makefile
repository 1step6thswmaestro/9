# Generated automatically from Makefile.in by configure.
#
# Makefile.in
#
# CGI -- C CGI Library -- Test -- Makefile source
#
# Copyright (c) 2000 Todor Prokopov
# Copyright (c) 2000 Edward V. POPKOV
# Distributed under GPL, see COPYING for details
#
# Todor Prokopov     <koprok@newmail.net>
# "Edward V. POPKOV" <evpopkov@carry.neonet.lv>

.PHONY: all clean distclean

CC       = gcc
LD       = gcc
RM       = rm -f
CFLAGS   = -g -O2
DEFS     = -DHAVE_CONFIG_H
LDFLAGS  = 
OBJFILES = cgitest.o
TARGET   = cgitest

all: $(TARGET)

$(TARGET): $(OBJFILES)
	$(LD) $(OBJFILES) $(LDFLAGS) -static -L.. -lcgi -o $(TARGET)

clean:
	$(RM) $(OBJFILES) $(TARGET)

distclean: clean
	$(RM) Makefile config.h

%.o: %.c config.h Makefile
	$(CC) $< -c -I. -I.. $(CFLAGS) $(DEFS) -o $@

# End of Makefile.in
