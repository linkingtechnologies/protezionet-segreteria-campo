<?php

class CamilaLogger {
	public static function logMe($id, $value, $progress)  {
		/*$myfile = fopen(CAMILA_TMP_DIR."/newfile.log", "w") or die("Unable to open file!");
		fwrite($myfile, $value);
		fclose($myfile);*/

		/*$d = array('message' => $value , 'progress' => $progress);
		
		echo "id: $id" . PHP_EOL;
		echo "data: " . json_encode($d) . PHP_EOL;
		echo PHP_EOL;
		ob_flush();
		flush();*/
		global $_CAMILA;
		if (php_sapi_name() == "cli" || $_CAMILA['cli']) {
			echo $value."\n";
		} else {
			CamilaUserInterface::insertSuccess($value);
		}
	}

	public function getLogs() {
		$logs='';
		$filename = CAMILA_TMP_DIR."/newfile.log";
		if (file_exists($filename))
			$logs = file_get_contents($filename);
		return $logs;
	}
}

?>