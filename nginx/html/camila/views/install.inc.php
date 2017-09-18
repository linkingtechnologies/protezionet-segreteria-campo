<?php
require_once('../../camila/autoloader.inc.php');

require('../../camila/config.inc.php');

require('../../camila/i18n.inc.php');
require('../../camila/camila_hawhaw.php');
require('../../camila/database.inc.php');

global $_CAMILA;

$_CAMILA['lang'] = 'it';

camila_translation_init();

$_CAMILA['page'] = new CHAW_deck('', HAW_ALIGN_LEFT);
 
$_CAMILA['page']->camila_export_enabled = false;
 

$pageTitle = 'Camila Framework';
$pageDesc = 'Init database';

//echo CAMILA_VAR_ROOTDIR;
$camilaAuth = new CamilaAuth();

$db = $camilaAuth->getDatabaseConnection(CAMILA_DB_DSN);

$accessCheck = true;

if (is_object($db)) {
	camila_information_text('Database Connection (' . $db->databaseType . '): OK');
	$camilaAuth->db = $db;
	if ($camilaAuth->checkUserTable() >=0)
		$accessCheck = false;
} else {
	camila_error_text('Database Connection: KO');
}

if ($accessCheck) {
	if (version_compare(PHP_VERSION, "5.3.2", "<")) {
		camila_error_text("PHP 5.3.2 or greater required");
	} else {
		camila_information_text('PHP Version Check: OK');
	}

	$php_modules = get_loaded_extensions();

	if (in_array('SimpleXML', $php_modules)) {
		camila_information_text('PHP Extensions Check (SimpleXML): OK');
	} else {
		camila_error_text("PHP SimpleXML extension required");
	}



	$dir_array = array(CAMILA_VAR_ROOTDIR,CAMILA_LOG_DIR,CAMILA_TMP_DIR,CAMILA_TMPL_DIR,CAMILA_WORKTABLES_DIR,CAMILA_PLUGINS_DIR,CAMILA_FM_ROOTDIR);
	foreach ($dir_array as $dir) {
		if (is_writable($dir.'/')) {
			camila_information_text("Writable Directory Check (".$dir."): OK");
		} else {
			camila_error_text("Writable Directory Check (".$dir."): KO");
		}
	}

}




	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-1 col-md-1">'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-10 col-md-10"><h4>'.$pageTitle.'</h4><h5>'.$pageDesc.'</h5><br/></div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-1 col-md-1">'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));

	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-1 col-md-1">'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-10 col-md-10">'));

if ($accessCheck) {

	$form = new phpform('cf');
	$form->submitbutton = 'Install';
	$form->drawrules = false;
	$form->preservecontext = true;

	new form_textbox($form, 'CAMILA_APP_DIR', 'CAMILA_APP_DIR', true, 30, 200);
	new form_textbox($form, 'CAMILA_APPLICATION_NAME', 'CAMILA_APPLICATION_NAME', true, 30, 200);
	new form_textbox($form, 'CAMILA_APPLICATION_TITLE', 'CAMILA_APPLICATION_TITLE', true, 30, 200);
	new form_textbox($form, 'CAMILA_APPLICATION_GROUP', 'CAMILA_APPLICATION_GROUP', true, 30, 200);



	new form_textbox($form, 'CAMILA_DB_DSN', 'CAMILA_DB_DSN', true, 30, 200);

	new form_textbox($form, 'CAMILA_LOGIN_MLANG', 'CAMILA_LOGIN_MLANG', true, 30, 200);

	$form->fields['CAMILA_APP_DIR']->set_css_class('form-control');
	$form->fields['CAMILA_APP_DIR']->defaultvalue=CAMILA_APP_DIR;
	$form->fields['CAMILA_APP_DIR']->updatable = false;

	$form->fields['CAMILA_DB_DSN']->set_css_class('form-control');
	$form->fields['CAMILA_DB_DSN']->defaultvalue=CAMILA_DB_DSN;
	$form->fields['CAMILA_DB_DSN']->updatable = false;

	$form->fields['CAMILA_APPLICATION_NAME']->set_css_class('form-control');
	$form->fields['CAMILA_APPLICATION_NAME']->defaultvalue=CAMILA_APPLICATION_NAME;
	$form->fields['CAMILA_APPLICATION_NAME']->updatable = false;

	$form->fields['CAMILA_APPLICATION_TITLE']->set_css_class('form-control');
	$form->fields['CAMILA_APPLICATION_TITLE']->defaultvalue=CAMILA_APPLICATION_TITLE;
	$form->fields['CAMILA_APPLICATION_TITLE']->updatable = false;

	$form->fields['CAMILA_APPLICATION_GROUP']->set_css_class('form-control');
	$form->fields['CAMILA_APPLICATION_GROUP']->defaultvalue=CAMILA_APPLICATION_GROUP;
	$form->fields['CAMILA_APPLICATION_GROUP']->updatable = false;

	$form->fields['CAMILA_LOGIN_MLANG']->set_css_class('form-control');
	$form->fields['CAMILA_LOGIN_MLANG']->defaultvalue=CAMILA_LOGIN_MLANG ? 'true' : 'false';
	$form->fields['CAMILA_LOGIN_MLANG']->updatable = false;

	$form->clear();





	if ($form->process())
	{
		if (1)
		{
			$camilaApp = new CamilaApp();
			$db = NewADOConnection(CAMILA_DB_DSN);
			$camilaApp->db = $db;
			$camilaApp->lang = $_REQUEST['lang'];		
			$camilaApp->resetTables(CAMILA_TABLES_DIR);
		}
		else {

		}

	}
		else
			$form->draw();

} else {
	camila_error_text('Access Denied!!!');
}

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-1 col-md-1">'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->use_simulator(CAMILA_CSS_DIR . 'skin0.css');

$_CAMILA['page']->create_page(); 
?>