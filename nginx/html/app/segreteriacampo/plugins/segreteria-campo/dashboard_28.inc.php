<?php
$resourceTable = 'MEZZI';
$organizationColumn = 'ORGANIZZAZIONE';
$provinceColumn = 'PROVINCIA';
$personalVisibilityColumn = 'COD. ORGANIZZAZIONE';
$resourceName = 'mezzi';
$title = 'Attestati ' . $resourceName . ' (USCITA DEFINITIVA)';
$icon = 'duplicate';

$filterColumn = 'SERVIZIO';
$filterValue = 'USCITA DEFINITIVA';

$camilaTemplate = new CamilaTemplate('it');
$params = $camilaTemplate->getParameters();
$template = $params['modelloattestato'.$resourceName].'.xml';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_pdf_generator.inc.php');
?>