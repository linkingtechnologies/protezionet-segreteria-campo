<?php

class CamilaTemplate
{
	private $dir;
	public $lang;
	public $db;

	function CamilaTemplate($lang='en')
    {
		$this->lang = $lang;
		$this->dir = CAMILA_TMPL_DIR . '/' . $this->lang . '/';
		
		global $_CAMILA;
		if (defined('CAMILA_TABLE_TEMPL')) {
			global $_CAMILA;
			$this->db = $_CAMILA['db'];
		}		
    }

	function getParameters() {
		$arr = array();
		/*if ($dh = opendir($this->dir)) {
			while (($file = readdir($dh)) !== false) {
				if (substr($file, -4) == '.txt') {
					$name = substr($file,0,-4);
					$value = file_get_contents($this->dir.$file);
					$arr[$name]=$value;
				}
			}
			closedir($dh);
		}
		return $arr;*/
		$files = array();

		if (is_dir($this->dir)) {
			$dir = $this->dir;
			$dh = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				$files[] = $filename;
			}
		}
		sort($files);

		foreach ($files as $name => $file)
		{
			if ($file != '.' && $file != '..' && !is_dir($this->basedir . '/' . $file)) {
            
				if (substr($file, -4) == '.txt') {
					$name = substr($file,0,-4);
					$value = file_get_contents($this->dir.$file);
					$arr[$name]=$value;
				}
			}
		
		}
		
		return $arr;

	}

	function setParameter($name, $value) {
		file_put_contents($this->dir.$name.'.txt', $value);

		if (defined('CAMILA_TABLE_TEMPL')) {
			if (is_object($this->db)) {
				$record = Array();
				$record['name'] = $name;
				$record['lang'] = $this->lang;
				$record['value'] = $value;
				$updateSQL = $this->db->AutoExecute(CAMILA_TABLE_TEMPL, $record, 'INSERT', 'name=' . $this->db->qstr($name) . ' and lang=' . $this->db->qstr($lang));
				
				echo $updateSQL;


				if (!$updateSQL) {
					//$success = false;
				} else  {
					//echo "OK";
				}
			}
		}
	}

	function getXmlTemplatePath($templateName) {
		$templatePath = $this->dir . $templateName;
		if (is_file($templatePath)) {
		} else {
			$pDir = CAMILA_APP_PATH . '/plugins/';
			$pdh  = opendir($pDir);
			while (false !== ($dir = readdir($pdh))) {
				$tDir = $pDir. $dir . '/templates/' . $this->lang;
				if ($dir != '.' && $dir != '..' && is_dir($tDir)) {
					$dh2  = opendir($tDir);
					while (false !== ($filename = readdir($dh2))) {
						if  ($filename == $templateName)
						{							
							$templatePath = $tDir . '/' . $filename;
						}
					}
				}
			}
		}
		
		return $templatePath;
	}
}
?>