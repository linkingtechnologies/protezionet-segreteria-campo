<?php
$resourceTable = 'VOLONTARI ATTESI';
$organizationColumn = 'ORGANIZZAZIONE';
$provinceColumn = 'PROVINCIA';
$personalVisibilityColumn = 'COD. ORGANIZZAZIONE';
$resourceName = 'volontari';

$camilaTemplate = new CamilaTemplate('it');
$params = $camilaTemplate->getParameters();
$template = $params['modelloaccreditamentovolontari'].'.xml';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_pdf_generator.inc.php');
?>