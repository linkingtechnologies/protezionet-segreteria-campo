<?php

function camila_autoloader($class) {

	switch($class) {
		case 'phpform':
			include(CAMILA_DIR . 'datagrid/form.class.php');
			break;

		case 'dbform':
			include(CAMILA_DIR . 'datagrid/db_form.class.php');
			break;

		case 'report':
			include(CAMILA_DIR . 'datagrid/report.class.php');
			break;

		case 'configurator':
			include(CAMILA_DIR . 'datagrid/configurator.class.php');
			break;

		case 'M2Translator':
			include(CAMILA_LIB_DIR . 'm2translator/M2Translator.class.php');
			break;		

		case 'MiniTemplator':
			include(CAMILA_LIB_DIR . 'minitemplator/MiniTemplator.class.php');
			break;

		case 'CamilaAuth':
			include(CAMILA_DIR . 'auth.class.inc.php');
			break;

		case 'CamilaApp':
			include(CAMILA_DIR . 'app.class.inc.php');
			break;
		
		case 'CamilaLogger':
			include(CAMILA_DIR . 'logger.class.inc.php');
			break;

		case 'CamilaFileManagement':
			include(CAMILA_DIR . 'fm.class.inc.php');
			break;
		
		case 'CamilaUserInterface':
			include(CAMILA_DIR . 'ui.class.inc.php');
			break;
		
		case 'CamilaPlugins':
			include(CAMILA_DIR . 'plugins.class.inc.php');
			break;
		
		case 'CamilaWorkTable':
			include(CAMILA_DIR . 'worktable.class.inc.php');
			break;
		
		case 'CamilaReport':
			include(CAMILA_DIR . 'report.class.inc.php');
			break;
		
		case 'CamilaIntegrity':
			include(CAMILA_DIR . 'integrity.class.inc.php');
			break;

		case 'CamilaTemplate':
			include(CAMILA_DIR . 'template.class.inc.php');
			break;

		default:
			if (substr($class, 0, 5) === 'form_') {
				include(CAMILA_DIR . 'datagrid/elements/form/'.substr($class,5).'.php');
			}
	}
}

spl_autoload_register('camila_autoloader');

?>