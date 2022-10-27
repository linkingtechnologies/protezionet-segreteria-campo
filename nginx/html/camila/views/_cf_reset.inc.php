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

defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

require_once('var/config.php');

require_once(CAMILA_LIB_DIR.'minitemplator/MiniTemplator.class.php');
require_once(CAMILA_LIB_DIR.'m2translator/M2Translator.class.php');
require_once(CAMILA_VENDOR_DIR . '/adodb/adodb-php/adodb.inc.php');
//require_once(CAMILA_LIB_DIR.'adodb5/adodb.inc.php');
require_once(CAMILA_DIR.'hawhaw/hawhaw.inc');
require_once(CAMILA_DIR.'db/import.inc.php');
require_once(CAMILA_DIR.'db/schema.inc.php');
require_once(CAMILA_DIR.'fm.class.inc.php');


function camila_delete_files($directory) {

    if(!$dirhandle = @opendir($directory) )
        return;

        while( false !== ($filename = readdir($dirhandle)) ) {
            if( $filename != '.' && $filename != '..' ) {
                $filename = $directory. '/'. $filename;

                if (!unlink($filename))
                    echo 'Error deleting ' . $filename;
        }
    }
}

function listdir($dir='.') { 
    if (!is_dir($dir)) { 
        return false; 
    }

    $files = array(); 
    listdiraux($dir, $files); 

    return $files; 
} 

function listdiraux($dir, &$files) { 
    $handle = opendir($dir); 
    while (($file = readdir($handle)) !== false) { 
        if ($file == '.' || $file == '..') { 
            continue; 
        } 
        $filepath = $dir == '.' ? $file : $dir . '/' . $file; 
        if (is_link($filepath)) 
            continue; 
        if (is_file($filepath)) 
            $files[] = $file; 
        else if (is_dir($filepath)) 
            listdiraux($filepath, $files); 
    } 
    closedir($handle); 
} 

class LongRunningTaskLogger {
	public function logMe($id, $value, $progress)  {
		$myfile = fopen(CAMILA_TMP_DIR."/newfile.log", "w") or die("Unable to open file!");
		fwrite($myfile, $value);
		fclose($myfile);

		$d = array('message' => $value , 'progress' => $progress);
		echo "id: $id" . PHP_EOL;
		echo "data: " . json_encode($d) . PHP_EOL;
		echo PHP_EOL;
		ob_flush();
		flush();
	}

	public function getLogs() {
		$logs='';
		$filename = CAMILA_TMP_DIR."/newfile.log";
		if (file_exists($filename))
			$logs = file_get_contents($filename);
		return $logs;
	}
}

class CamilaAppReset extends LongRunningTaskLogger
{
	public $db;
	public $lang;

	public function reset($tablesDir) {
		if (is_dir($tablesDir)) {
			if ($dh = opendir($tablesDir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..' && substr($file,-3) == 'xml') {
						$result = create_table($tablesDir.'/'.$file, CAMILA_APPLICATION_PREFIX, $this->db);

						if ($result['result'] == 2)
							$this->logMe(time(),$file . ' - OK', 10);
						else
							$this->logMe(time(),$file . ' - KO - ' . $result['sql'] . ' - ' . $result['statements'], 10);
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
							$this->logMe(time(),$file . ' - inserted: ' . $result['processed'], 10);
						else
							$this->logMe(time(),$file . ' - error: ' . $result['error'] . ', failed: ' . $result['failed'] . ', inserted: ' . $result['processed'], 10);
					}
				}
				closedir($dh);
			}
		}

		$files = listdir($tablesDir.'/xls/'.$_REQUEST['lang']); 
		sort($files, SORT_LOCALE_STRING);

		foreach($files as $file) {
			if ($file != '.' && $file != '..' && (substr($file,-3) == 'xls' || substr($file,-4) == 'xlsx')) {
				$result = XLS_import($tablesDir.'/xls/'.$_REQUEST['lang'].'/'.$file, CAMILA_APPLICATION_PREFIX . substr($file,0,-4), $this->db);
			}
		}

		if (is_dir($tablesDir)) {

				foreach($files as $file) {
					if ($file != '.' && $file != '..' && (substr($file,-3) == 'xls' || substr($file,-4) == 'xlsx')) {
						$result = XLS_import2($tablesDir.'/xls/'.$_REQUEST['lang'].'/'.$file, CAMILA_APPLICATION_PREFIX . substr($file,0,-4), $this->db);
						if ($result['result'] == 2)
							$this->logMe(time(), $file . ' - inserted: ' . $result['processed'], 10);
						else
							$this->logMe(time(), $file . ' - error: ' . $result['error'] . ', failed: ' . $result['failed'] . ', inserted: ' . $result['processed'], 10);
					}
		}

			$res = $this->db->Execute('update ' . CAMILA_TABLE_PLANG . ' set full_title=short_title where page_url LIKE '.$this->db->qstr('cf_app.php?cat%') . ' and lang='.$this->db->qstr($_REQUEST['lang']));
			if ($res === false)
				$this->logMe(time(),camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg(),10);

		}
		camila_delete_files(CAMILA_TMP_DIR);
		$this->logMe(time(), 'TERMINATE');
	}
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$camilaReset = new CamilaAppReset();
$db = NewADOConnection(CAMILA_DB_DSN);
$camilaReset->db = $db;
$camilaReset->lang = $_REQUEST['lang'];
if (isset($_REQUEST['plugin'])) {
	//echo CAMILA_APP_PATH . '/plugins/'.$_REQUEST['plugin'].'/tables';
	$camilaReset->reset(CAMILA_APP_PATH . '/plugins/'.$_REQUEST['plugin'].'/tables');
	CamilaFileManagement::copyFiles(CAMILA_APP_PATH . '/plugins/'.$_REQUEST['plugin'].'/templates/'.$camilaReset->lang,CAMILA_TMPL_DIR.'/'.$camilaReset->lang,'txt',false);
	CamilaFileManagement::copyFiles(CAMILA_APP_PATH . '/plugins/'.$_REQUEST['plugin'].'/templates/images/'.$camilaReset->lang,CAMILA_TMPL_DIR.'/images/'.$camilaReset->lang,'',false);
} else {
	$camilaReset->reset(CAMILA_TABLES_DIR);
}
?>