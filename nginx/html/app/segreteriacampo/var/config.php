<?php
//Include Host-specific configuration file
$scf = getenv('HTTP_HOST');
$scf = preg_replace('/[^a-z0-9]/', '', $scf);
@include($scf.'.inc.php');

if (!defined('CAMILA_DB_DSN')) {
    @include('1270014001.inc.php');
}

define('CAMILA_ADM_USER_GROUP','admin');

if (!defined('CAMILA_APPLICATION_NAME')) {
    define('CAMILA_APPLICATION_NAME', 'WorkTable');
}

if (!defined('CAMILA_LOGIN_MLANG')) {
    define('CAMILA_LOGIN_MLANG', false);
}

define('CAMILA_APPLICATION_PREFIX', CAMILA_APP_DIR . '_');

define('CAMILA_WORKTABLE_SPECIAL_ICON_ENABLED', true);
define('CAMILA_WORKTABLE_SELECTED_ICON_ENABLED', true);
define('CAMILA_WORKTABLE_IMPORT_ENABLED', true);
define('CAMILA_WORKTABLE_HIDE_POWERED_BY_WHEN_LOGGED_IN', true);


define('CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_ENABLED', false);
define('CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_FROM_NAME', '');
define('CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_FROM', '');
define('CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_REPLY_TO', '');
define('CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_TO', '');
define('CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_CC', '');
define('CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_SUBJECT', CAMILA_APPLICATION_NAME);

define('CAMILA_WORKTABLE_EXT_TABLE_PERSONAL_VISIBILITY_FIELD', 'created_by');

define('CAMILA_TABLE_USERS', CAMILA_APPLICATION_PREFIX . 'camila_users');
if (!defined('CAMILA_AUTH_TABLE_USERS'))
	define('CAMILA_AUTH_TABLE_USERS', CAMILA_TABLE_USERS);


define('CAMILA_TABLE_FILES', CAMILA_APPLICATION_PREFIX . 'camila_files');
define('CAMILA_TABLE_PAGES', CAMILA_APPLICATION_PREFIX . 'camila_pages');
define('CAMILA_TABLE_PLANG', CAMILA_APPLICATION_PREFIX . 'camila_pages_lang');
define('CAMILA_TABLE_PUBDS', CAMILA_APPLICATION_PREFIX . 'camila_db_datasources');
define('CAMILA_TABLE_WORKT', CAMILA_APPLICATION_PREFIX . 'camila_worktables');
define('CAMILA_TABLE_WORKC', CAMILA_APPLICATION_PREFIX . 'camila_worktables_cols');
define('CAMILA_TABLE_WORKP', CAMILA_APPLICATION_PREFIX . 'worktable');

define('CAMILA_TABLE_PLUGINS', CAMILA_APPLICATION_PREFIX . 'camila_plugins');

define('CAMILA_DEFAULT_USER_LEVEL', 20);
define('CAMILA_LINKSET_SEPARATOR', '>');
define('CAMILA_DEFAULT_JS_SKIN', 2);

define('CAMILA_DIR', '../../camila/');
define('CAMILA_CSS_DIR', '../../camila/css/');
define('CAMILA_IMG_DIR', '../../camila/images/');
define('CAMILA_LIB_DIR', '../../lib/');
define('CAMILA_TABLES_DIR', dirname(dirname(dirname(dirname(__FILE__)))).'/camila/tables/');
define('CAMILA_LANG_DIR', 'lang/');
define('CAMILA_FORM_DIR', 'form/');
define('CAMILA_PROV_DIR', 'provider/');


define('CAMILA_LANG', 'it');

define('CAMILA_DB_', 'camila_');
define('CAMILA_SESSION_DURATION', 90*24*60*60);
define('CAMILA_CHALLENGE_LOGIN', false);
define('CAMILA_USERNAME_LOGIN', true);
define('CAMILA_PASSWORD_LENGTH', 14);
define('CAMILA_ENCRYPT_PASSWORD', false);
define('CAMILA_USER_CAN_CHANGE_PWD', true);


define('CAMILA_BORDER', 1);
define('CAMILA_LINK_COLOR', "#006CB4");
define('CAMILA_VLINK_COLOR', "#006CB4");
define('CAMILA_BGCOLOR', "#FFFFFF");
define('CAMILA_DISP_BGCOLOR', "#FFFFFF");
define('CAMILA_WIDTH', 550);
define('CAMILA_HEIGHT', 500);
define('CAMILA_FACE', "Verdana");
define('CAMILA_SIZE', "10pt");
define('CAMILA_TABLE_FACE', "Verdana");
define('CAMILA_TABLE_SIZE', "8pt");

define('CAMILA_REPORT_RPP', 10);

define('CAMILA_RTF_FONT_SIZE', 9);
define('CAMILA_RTF_FONT_BIG', 12);
define('CAMILA_RTF_FONT_SMALL', 7);
define('CAMILA_RTF_FONT_FAMILY', 'Times');

define('CAMILA_SHOW_MICROTIME', false);

define('DEBUG_COUNTER', '0');
?>