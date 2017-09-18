<?php
$camilaUI->insertTitle('Backup intero database','download');

global $_CAMILA;
if ($_CAMILA['db']->databaseType == 'sqlite3')
{
	$camilaUI->insertButton('?dashboard='.$_REQUEST['dashboard'].'&download=yes', 'BACKUP INTERO DATABASE', 'hdd');
}

if (isset($_REQUEST['download'])) {
	
	$file = CAMILA_VAR_ROOTDIR.'/db/camila.db';

	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		exit;
	}
}

?>