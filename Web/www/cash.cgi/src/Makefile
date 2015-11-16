CASH_CLI_ROOT=/home/paadm/catifon/cli
LIB=$(CASH_CLI_ROOT)/cash/cash.cgi/lib
BIN=$(CASH_CLI_ROOT)/cash/cash.cgi/bin

all :
	clear
	rm -rf execway.o cgiway.o core
	gcc -o execway.exec -Wall execway.c $(LIB)/catifonobj.o -lnsl
	mv execway.exec $(BIN)
	gcc -o cgiway.cgi -Wall cgiway.c $(LIB)/catifonobj.o $(LIB)/cgi.o -lnsl
	mv cgiway.cgi $(BIN)
	
	
	
	
	

