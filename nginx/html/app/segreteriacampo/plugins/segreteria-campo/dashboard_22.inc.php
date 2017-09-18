<?php
$resourceType='VOLONTARIO';
$serviceColumn = 'SERVIZIO';
$groupColumn = 'ORGANIZZAZIONE';
$resourceTable = 'VOLONTARI ATTESI';
$serviceTable = 'SERVIZI';
$brogliaccio = '';
$col1='COGNOME';
$col2='NOME';

$serviceEndServiceValue = '';

$titleText = 'Pianificazione ' . ucwords(strtolower($resourceTable));
$titleIcon = 'user';

require_once('plugins/'.basename(dirname(__FILE__)).'/resource_mover.inc.php');
?>