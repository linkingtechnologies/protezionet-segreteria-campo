<?php
require_once(CAMILA_DIR.'db/import.inc.php');
require_once(CAMILA_DIR.'db/schema.inc.php');

class CamilaApp
{
	public $db;
	public $lang;

	public function resetTables($tablesDir) {
		global $_CAMILA;
		if (is_dir($tablesDir)) {
			if ($dh = opendir($tablesDir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..' && substr($file,-3) == 'xml') {
						$result = create_table($tablesDir.'/'.$file, CAMILA_APPLICATION_PREFIX, $this->db);

						if ($result['result'] == 2)
							CamilaLogger::logMe(time(),$file . ' - OK', 10);
						else
							CamilaLogger::logMe(time(),$file . ' - KO - ' . $result['sql'] . ' - ' . $result['statements'], 10);
					}
				}
				closedir($dh);
			}
		}

		if (is_dir($tablesDir)) {
			if ($dh = opendir($tablesDir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..' && substr($file,-3) == 'csv') {
						$result = CSV_import($tablesDir.'/'.$file, CAMILA_APPLICATION_PREFIX . substr($file,0,-4), $this->db);
						if ($result['result'] == 2)
							CamilaLogger::logMe(time(),$file . ' - inserted: ' . $result['processed'], 10);
						else
							CamilaLogger::logMe(time(),$file . ' - error: ' . $result['error'] . ', failed: ' . $result['failed'] . ', inserted: ' . $result['processed'], 10);
					}
				}
				closedir($dh);
			}
		}
		CamilaFileManagement::deleteFiles(CAMILA_TMP_DIR);
	}
	
	public function resetWorkTables($tablesDir) {

		$files = CamilaFileManagement::listDir($tablesDir.'/xls/'.$this->lang); 
		sort($files, SORT_LOCALE_STRING);

		foreach($files as $file) {
			if ($file != '.' && $file != '..' && substr($file,-3) == 'xls') {
				$result = XLS_import($tablesDir.'/xls/'.$this->lang.'/'.$file, CAMILA_APPLICATION_PREFIX . substr($file,0,-4), $this->db);
			}
		}

		if (is_dir($tablesDir)) {

				foreach($files as $file) {
					if ($file != '.' && $file != '..' && substr($file,-3) == 'xls') {
						$result = XLS_import2($tablesDir.'/xls/'.$this->lang.'/'.$file, CAMILA_APPLICATION_PREFIX . substr($file,0,-4), $this->db);
						if ($result['result'] == 2)
							CamilaLogger::logMe(time(), $file . ' - inserted: ' . $result['processed'], 10);
						else
							CamilaLogger::logMe(time(), $file . ' - error: ' . $result['error'] . ', failed: ' . $result['failed'] . ', inserted: ' . $result['processed'], 10);
				}
		}

			$res = $this->db->Execute('update ' . CAMILA_TABLE_PLANG . ' set full_title=short_title where page_url LIKE '.$this->db->qstr('cf_app.php?cat%') . ' and lang='.$this->db->qstr($this->lang));
			if ($res === false)
				CamilaLogger::logMe(time(),camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg(),10);

		}
		CamilaFileManagement::deleteFiles(CAMILA_TMP_DIR);
	}
}

?>