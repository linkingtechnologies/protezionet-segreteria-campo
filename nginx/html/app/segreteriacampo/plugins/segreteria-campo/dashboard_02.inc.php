<?php

$resourceType='VOLONTARIO';
$serviceColumn = 'SERVIZIO';
$groupColumn = 'ORGANIZZAZIONE';
$resourceTable = 'VOLONTARI';
$serviceTable = 'SERVIZI';
$brogliaccio = 'MOV. RISORSE';
$col1='COGNOME';
$col2='NOME';
$serviceEndServiceValue = "USCITA DEFINITIVA";

$titleText = 'Movimentazione ' . ucwords(strtolower($resourceTable));
$titleIcon = 'user';


require_once('plugins/'.basename(dirname(__FILE__)).'/resource_mover.inc.php');
?>