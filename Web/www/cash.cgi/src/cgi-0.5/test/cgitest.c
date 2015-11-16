/*
 cgitest.c

 CGI -- C CGI Library -- Test -- Main

 Copyright (c) 2000 Todor Prokopov
 Distributed under GPL, see COPYING for details

 Todor Prokopov <koprok@newmail.net>

 $Id$

 $Log$
*/

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif /*HAVE_CONFIG_H*/

#if HAVE_STDLIB_H
#include <stdlib.h>
#endif /*HAVE_STDLIB_H*/
#if HAVE_STDIO_H
#include <stdio.h>
#endif /*HAVE_STDIO_H*/
#include <cgi.h>

int main(void)
{
  const char *name;
  const char *age;
  const char *sex;
  const char *os;
  const char *comments;

  printf("Content-type: text/html\n\n");
  printf("<HTML><HEAD><TITLE>CGI Test</TITLE></HEAD>\n");
  printf("<BODY><H1>CGI Test Results</H1><HR>\n");

  if (!cgi_init())
  {
    printf("<P>cgi_init: %s</P></BODY></HTML>\n", cgi_strerror(cgi_errno));
    return EXIT_FAILURE;
  }

  name = cgi_param("name");
  if (name != NULL)
    printf("<P>Name: %s</P>\n", name);

  age = cgi_param("age");
  if (age != NULL)
    printf("<P>Age: %s</P>\n", age);

  sex = cgi_param("sex");
  if (sex != NULL)
    printf("<P>Sex: %s</P>\n", sex);

  printf("<P>Operating Systems:<BR>");
  while ((os = cgi_param("os")) != NULL)
    printf("%s<BR>\n", os);
  printf("</P>\n");

  if (cgi_param("brother") != NULL)
    printf("<P>You have a brother or sister</P>\n");

  comments = cgi_param("comments");
  if (comments != NULL)
    printf("<P>Comments:<BR>%s</P>\n", comments);

  printf("</HTML>\n");
  cgi_done();
  return EXIT_SUCCESS;
}

/* End of cgitest.c */
