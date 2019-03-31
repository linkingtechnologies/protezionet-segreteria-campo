<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2017 Umberto Bresciani

    Camila PHP Framework is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Camila PHP Framework is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Camila PHP Framework. If not, see <http://www.gnu.org/licenses/>. */

setlocale(LC_TIME, "it_IT.utf8");
date_default_timezone_set('Europe/Rome');

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);

define('CAMILA_VAR_ROOTDIR', dirname(__FILE__));
define('CAMILA_APP_PATH', dirname(CAMILA_VAR_ROOTDIR));
define('CAMILA_APP_DIR', basename(dirname(CAMILA_VAR_ROOTDIR)));
define('CAMILA_PRIVATE_SERVER', true);

define('CAMILA_HOMEDIR', dirname(CAMILA_VAR_ROOTDIR));
define('CAMILA_HOMEURL', dirname($_SERVER['SCRIPT_URI']));
define('CAMILA_HELPURL','');

//Heroku
if ($_ENV["DATABASE_URL"] != '') {
	extract(parse_url($_ENV["DATABASE_URL"]));
	$dsn = "postgres9://$user:$pass@$host/".substr($path, 1);
	define('CAMILA_DB_DSN', $dsn);
	define('CAMILA_AUTH_DSN', $dsn);
} else {
	$dbpath = urlencode(CAMILA_VAR_ROOTDIR.'/db/camila.db');
	$dbpath = str_replace ("+", "%20", $dbpath);
	define('CAMILA_DB_DSN', "sqlite3://$dbpath");
	define('CAMILA_AUTH_DSN', "sqlite3://$dbpath");
}
define('CAMILA_AUTH_PASSWORD_HASHING', true);

//define('CAMILA_DB_DSN', "mysql://user:pass@host/dbname");
//define('CAMILA_AUTH_DSN', "mysql://user:pass@host/dbname");


define('CAMILA_APPLICATION_NAME', 'ProtezioNET - Segreteria Campo');
define('CAMILA_APPLICATION_TITLE', 'Segreteria campo');
define('CAMILA_APPLICATION_GROUP', 'ProtezioNET');

/////8
define('CAMILA_LOGIN_MLANG', false);

define('CAMILA_UI_DEFAULT_BTN_SIZE', "btn-sm");

define('CAMILA_LOG_DIR', CAMILA_VAR_ROOTDIR.'/log');
define('CAMILA_TMP_DIR', CAMILA_VAR_ROOTDIR.'/tmp');
define('CAMILA_VAR_DIR', CAMILA_VAR_ROOTDIR.'/kfkfkfkfkf/counters');
//define('CAMILA_TABLES_DIR', CAMILA_VAR_ROOTDIR.'/tables');
define('CAMILA_NEWS_DIR', CAMILA_VAR_ROOTDIR.'/news');
define('CAMILA_TMPL_DIR', CAMILA_VAR_ROOTDIR.'/templates');
define('CAMILA_WORKTABLES_DIR', CAMILA_VAR_ROOTDIR.'/worktables');
define('CAMILA_PLUGINS_DIR', CAMILA_VAR_ROOTDIR.'/plugins');

define('CAMILA_LOG_LEVEL', 0);

define('CAMILA_MAIL_ON_AUTHENTICATION', '0');
define('CAMILA_MAIL_ON_DB_ERROR', '0');
define('CAMILA_MAIL_ON_ERROR', '0');

define('CAMILA_MAIL_HOST', 'mail.example.com');
define('CAMILA_MAIL_IS_SMTP', true);
define('CAMILA_MAIL_SMTP_AUTH', false);

define('CAMILA_ANON_LOGIN', false);
define('CAMILA_ANON_USER', 'anon');
define('CAMILA_ANON_PASS', 'anon');
define('CAMILA_ADMIN_USER', 'admin');
define('CAMILA_ADMIN_PASS', 'admin');

define('CAMILA_LOGIN_URL', 'cf_login.php');
define('CAMILA_LOGOUT_URL', 'cf_logout.php');
define('CAMILA_LOGIN_HOME', 'index.php');
define('CAMILA_HOME', 'index.php');
define('CAMILA_SPLASH_IMG', '');

define('CAMILA_FM_AJAXPLORER_ENABLED', true);
define('CAMILA_FM_EXTFS_ENABLED', false);
define('CAMILA_FM_ROOTDIR', CAMILA_VAR_ROOTDIR.'/files');
define('CAMILA_FM_PUBDIR', CAMILA_FM_ROOTDIR.'/public');
define('CAMILA_FM_PREFIX', 7);
define('CAMILA_FM_DEFAULT_FOLDER', 'Allegati');
define('CAMILA_FM_MAX_UPLOAD_SIZE', 20000000);
define('CAMILA_FM_EXPORT_SAVETO_DISABLED', true);


define('CAMILA_SHOW_ERRORS', false);
define('CAMILA_BAN_IP_SECS', 20);
define('CAMILA_ADMINISTRATOR_EMAIL', 'mail@example.com');

?>