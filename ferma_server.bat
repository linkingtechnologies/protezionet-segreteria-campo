@ECHO OFF
ECHO Stopping nginx...
taskkill /im nginx.exe /f
ECHO Stopping PHP FastCGI...
taskkill /im php-cgi.exe /f
ECHO Exiting.
@ping 127.0.0.1 -n 2 -w 1000 > nul
@ping 127.0.0.1 -n %1% -w 1000> nul
EXIT