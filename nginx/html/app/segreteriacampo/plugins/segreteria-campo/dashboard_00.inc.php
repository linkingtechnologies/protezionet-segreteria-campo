<?php
$camilaUI->insertTitle(camila_get_translation('camila.serveraddress'),'globe');

if (getenv('COMPUTERNAME') != '') {
	$url  = 'http://' . getenv('COMPUTERNAME') . ':' . $_SERVER['SERVER_PORT'] . '/app/' . CAMILA_APP_DIR;
	$link = new CHAW_link($url, $url);
	$_CAMILA['page']->add_link($link);
}

if ($_SERVER['SERVER_ADDR'] != '') {
	$url  = 'http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '/app/' . CAMILA_APP_DIR;
	$link = new CHAW_link($url, $url);
	$_CAMILA['page']->add_link($link);
}

$localIP = getHostByName(getHostName());
if ($localIP != '') {
	$url  = 'http://' . $localIP . ':' . $_SERVER['SERVER_PORT'] . '/app/' . CAMILA_APP_DIR;
	$link = new CHAW_link($url, $url);
	$_CAMILA['page']->add_link($link);
}

	$text = new CHAW_text('Versione PHP: ' . phpversion());
	$_CAMILA['page']->add_text($text);
	
	$text = new CHAW_text('Spazio libero su disco: ' . disk_free_space( '/' ) . ' byte');
	$_CAMILA['page']->add_text($text);

?>