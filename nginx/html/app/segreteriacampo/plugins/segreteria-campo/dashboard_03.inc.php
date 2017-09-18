<?php

$resourceType='ATTREZZATURA';
$serviceColumn = 'SERVIZIO';
$groupColumn = 'ORGANIZZAZIONE';
$resourceTable = 'ATTREZZATURE';
$serviceTable = 'SERVIZI';
$brogliaccio = 'MOV. RISORSE';
$col1='TIPOLOGIA';
$col2='ATTREZZATURA';
$serviceEndServiceValue = "USCITA DEFINITIVA";

$titleText = 'Movimentazione ' . ucwords(strtolower($resourceTable));
$titleIcon = 'wrench';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_mover.inc.php');
?>