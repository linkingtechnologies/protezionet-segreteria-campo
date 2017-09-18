<?php

$resourceType='MEZZO';
$serviceColumn = 'SERVIZIO';
$groupColumn = 'ORGANIZZAZIONE';
$resourceTable = 'MEZZI';
$serviceTable = 'SERVIZI';
$brogliaccio = 'MOV. RISORSE';
$col1='MARCA E MODELLO';
$col2='TARGA';
$serviceEndServiceValue = "USCITA DEFINITIVA";

$titleText = 'Movimentazione ' . ucwords(strtolower($resourceTable));
$titleIcon = 'plane';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_mover.inc.php');

?>