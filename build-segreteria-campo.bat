php build-scripts/download-container-segreteria-campo.php
cd segreteria-campo
cd nginx
cd html

php cli.php create-app segreteriacampo worktable-sqlite-it it

cd app
cd segreteriacampo

php cli.php install-plugin segreteria-campo it

php cli.php init-app it

php cli.php init-plugin segreteria-campo it
php cli.php generate-plugin-docs segreteria-campo it

php cli.php set-config-var CAMILA_APPLICATION_NAME "ProtezioNET - Segreteria Campo"
php cli.php set-config-var CAMILA_APPLICATION_TITLE "Segreteria campo"
php cli.php set-config-var CAMILA_APPLICATION_GROUP "ProtezioNET"

cd..
cd..
cd..
cd..

rename view_IP_addresses.bat mostra_indirizzi_IP.bat
rename start_server.bat avvia_server.bat
rename start_server_8080.bat avvia_server_8080.bat
rename stop_server.bat ferma_server.bat

cd..

set zipFilename=%DATE:~7,2%-%DATE:~4,2%-%DATE:~10,4%

tar.exe -a -cf segreteria-campo-%zipFilename%.zip segreteria-campo