<?php
$resourceType='MEZZO';
$serviceColumn = 'SERVIZIO';
$groupColumn = 'ORGANIZZAZIONE';
$resourceTable = 'MEZZI ATTESI';
$serviceTable = 'SERVIZI';
$brogliaccio = '';
$col1='MARCA E MODELLO';
$col2='TARGA';
$serviceEndServiceValue = "";

$titleText = 'Pianificazione ' . ucwords(strtolower($resourceTable));
$titleIcon = 'plane';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_mover.inc.php');

?>