<?php
$resourceType='ATTREZZATURA';
$serviceColumn = 'SERVIZIO';
$groupColumn = 'ORGANIZZAZIONE';
$resourceTable = 'ATTREZZATURE ATTESE';
$serviceTable = 'SERVIZI';
$brogliaccio = '';
$col1='TIPOLOGIA';
$col2='ATTREZZATURA';
$serviceEndServiceValue = "";

$titleText = 'Pianificazione ' . ucwords(strtolower($resourceTable));
$titleIcon = 'wrench';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_mover.inc.php');
?>