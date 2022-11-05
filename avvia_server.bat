@ECHO OFF
REM  Each process handles the maximum number of requests, or is set to Windows Environment variables  
set PHP_FCGI_MAX_REQUESTS=1000
set PHP_FCGI_CHILDREN=8

ECHO Starting PHP FastCGI...
nginx\RunHiddenConsole nginx\php\php-cgi.exe -b 127.0.0.1:9000 -c nginx\php\php.ini
nginx\RunHiddenConsole nginx\php\php-cgi.exe -b 127.0.0.1:9001 -c nginx\php\php.ini
nginx\RunHiddenConsole nginx\php\php-cgi.exe -b 127.0.0.1:9002 -c nginx\php\php.ini
nginx\RunHiddenConsole nginx\php\php-cgi.exe -b 127.0.0.1:9003 -c nginx\php\php.ini


ECHO Starting nginx server...
REM start nginx.exe
:: Start the nginx server
cd nginx
RunHiddenConsole nginx
ECHO Exiting.
@ping 127.0.0.1 -n 2 -w 1000 > nul
@ping 127.0.0.1 -n %1% -w 1000> nul

start http://localhost

EXIT




