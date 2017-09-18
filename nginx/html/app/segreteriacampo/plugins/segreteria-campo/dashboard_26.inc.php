<?php
$resourceTable = 'MEZZI ATTESI';
$organizationColumn = 'ORGANIZZAZIONE';
$provinceColumn = 'PROVINCIA';
$personalVisibilityColumn = 'COD. ORGANIZZAZIONE';
$resourceName = 'mezzi';

$camilaTemplate = new CamilaTemplate('it');
$params = $camilaTemplate->getParameters();
$template = $params['modelloaccreditamentomezzi'].'.xml';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_pdf_generator.inc.php');
?>