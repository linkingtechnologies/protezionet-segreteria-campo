<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2022 Umberto Bresciani

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


/*require_once('../../camila/autoloader.inc.php');

require('../../camila/config.inc.php');

require('../../camila/i18n.inc.php');
require('../../camila/camila_hawhaw.php');
require('../../camila/database.inc.php');

defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

global $_CAMILA;

($_REQUEST['lang'] != '') or die('Lang is not set.');

camila_translation_init();

$_CAMILA['page'] = new CHAW_deck('', HAW_ALIGN_LEFT); 
$_CAMILA['page']->camila_export_enabled = false;

$camilaAuth = new CamilaAuth();
$db = $camilaAuth->getDatabaseConnection(CAMILA_DB_DSN);*/

defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

$fCheckName = CAMILA_WORKTABLES_DIR . '/ephemeral.txt';

if (!file_exists($fCheckName)){

$db = $_CAMILA['db'];
$configurator = new configurator();
$configurator->db = $db;	
//$configurator->translation_init();
//$configurator->interactive = false;

if (is_object($db)) {
	//$camilaAuth->db = $db;
	//if ($camilaAuth->checkUserTable() >=0) {	
		$resultTemp = $db->Execute('select id from ' . CAMILA_TABLE_WORKT);
        if ($resultTemp === false) {
            //camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
		} else {
			while (!$resultTemp->EOF) {
				$successTemp = $configurator->create_script_from_template($resultTemp->fields['id']);
				//camila_information_text('WorkTable rebuild ['.$resultTemp->fields['id'].']: OK');
				$resultTemp->MoveNext();
			}

			$fh = fopen($fCheckName, 'wb');
			if (!$fh) {
				echo 'File open failed.';
			}
			fwrite($fh, "OK");
			fclose($fh);
			
			if (defined('CAMILA_TABLE_TEMPL')) {
				$lang = $_CAMILA['lang'];
				$camilaTemplate = new CamilaTemplate($lang);
	
				$old = $db->SetFetchMode(ADODB_FETCH_ASSOC);

				$query = 'SELECT name, lang, value FROM ' . CAMILA_TABLE_TEMPL . ' WHERE lang = ' . $db->qstr($lang);
				
				$rs = $db->Execute($query);
				if ($rs) {
					$cnt = $rs->recordCount();
					if ($cnt > 0) {
						while ($arr = $rs->FetchRow()) {
							//echo $arr;
							$camilaTemplate->setParameter($arr['name'], $arr['value']);
							//echo $arr['name'] .'-->'.$arr['value'];
						}
					} else {						
						$params = $camilaTemplate->getParameters();

						$record = array();

						foreach ($params as $key => $val) {
							$record = Array();
							$record['name'] = $key;
							$record['lang'] = $lang;
							$record['value'] = $val;
							$updateSQL = $db->AutoExecute(CAMILA_TABLE_TEMPL, $record, 'INSERT', 'name=' . $db->qstr($key) . ' and lang=' . $db->qstr($lang));

							if (!$updateSQL) {
								//camila_information_text(camila_get_translation('camila.worktable.db.error'));
								//$success = false;
								echo "Error rebuild!";
							}
						}
					}
				}
				$db->SetFetchMode($old);

				/*$camilaTemplate = new CamilaTemplate($lang);
				echo $lang;
				$params = $camilaTemplate->getParameters();
				
				$record = array(); # Initialize an array to hold the record data to insert*/
				
				foreach ($params as $key => $val) {
					$record = Array();
					$record['name'] = $key;
					$record['lang'] = $lang;
					$record['value'] = $val;
					/*$updateSQL = $db->AutoExecute(CAMILA_TABLE_TEMPL, $record, 'UPDATE', 'name=' . $db->qstr($key) . ' and lang=' . $db->qstr($lang));
					
					if (!$updateSQL) {
						camila_information_text(camila_get_translation('camila.worktable.db.error'));
						$success4 = false;
					}*/

				}
			}
			
		}
	//}
}
//} else {
//	camila_error_text('Database Connection: KO');
//}
}
//$_CAMILA['page']->use_simulator(CAMILA_CSS_DIR . 'skin0.css');
//$_CAMILA['page']->create_page();
?>
