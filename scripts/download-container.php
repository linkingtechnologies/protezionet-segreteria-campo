<?php
$phpVersion = '8.1.26';
$zipFile = bin2hex(random_bytes(10)).'.zip';
file_put_contents($zipFile, file_get_contents('https://github.com/linkingtechnologies/win-local-server-php'.$phpVersion.'/archive/refs/heads/main.zip'));
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
	$zip->extractTo('./');
	$zip->close();
	rename('win-local-server-php'.$phpVersion.'-main', 'segreteria-campo');
	unlink($zipFile);
	$zipFile = bin2hex(random_bytes(10)).'.zip';
	file_put_contents($zipFile, file_get_contents('https://github.com/linkingtechnologies/camila-php-framework/archive/refs/heads/master.zip'));
	$zip = new ZipArchive;
	if ($zip->open($zipFile) === TRUE) {
		$zip->extractTo('segreteria-campo/nginx');
		$zip->close();
		rename('segreteria-campo/nginx/camila-php-framework-master', 'segreteria-campo/nginx/html');
		unlink($zipFile);
	} else {
		//$this->error('Error extracting template zip file');
	}

} else {
//				$this->error('Error extracting template zip file');
}
?>