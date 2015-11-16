/*
 cgi.h

 CGI -- C CGI Library -- Header

 Copyright (c) 2000 Todor Prokopov
 Distributed under GPL, see COPYING for details

 Todor Prokopov <koprok@newmail.net>

 $Id$

 $Log$
*/

#ifndef CGI_H  /* Prevent multiple includes */
#define CGI_H

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif /*HAVE_CONFIG_H*/

#if STDC_HEADERS
#include <ctype.h>
#include <string.h>
#endif /*STDC_HEADERS*/
#if HAVE_STDIO_H
#include <stdio.h>
#endif /*HAVE_STDIO_H*/
#if HAVE_STDLIB_H
#include <stdlib.h>
#endif /*HAVE_STDLIB_H*/
#if HAVE_ERRNO_H
#include <errno.h>
#endif /*HAVE_ERRNO_H*/

enum {
  CGIERR_SUCCESS,
  CGIERR_UNKNOWN_REQUEST_METHOD,
  CGIERR_NULL_QUERY_STRING,
  CGIERR_MEMORY_ALLOCATION,
  CGIERR_REPEATED_INIT_ATTEMPT,
  CGIERR_UNKNOWN_CONTENT_TYPE,
  CGIERR_UNSUPPORTED_CONTENT_TYPE,
  CGIERR_INVALID_CONTENT_LENGTH,
  CGIERR_INPUT_BLOCK_READING,
  CGIERR_INVALID_URLENCODED_DATA,
  CGI_NUM_ERRS
};

#ifndef CGI_C

extern int cgi_errno;

extern int cgi_init(void);
extern void cgi_done(void);
extern const char *cgi_param(const char *name);
extern const char *cgi_strerror(int errnum);

#endif /*CGI_C*/

#endif /* CGI_H */

/* End of cgi.h */
