<?php
$resourceTable1='VOLONTARI';
$resourceTable2='MEZZI';
$serviceTable = 'SERVIZI';
$serviceColumn = 'SERVIZIO';
$groupColumn = 'ORGANIZZAZIONE';

$col1='COGNOME';
$col2='NOME';

$camilaWT = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

$conn = $_CAMILA['db'];
global $conn;

function drawMap() {
	global $_CAMILA;
	global $camilaWT;
	global $mapName;
	
	$resourceTable1='VOLONTARI';
	$resourceTable2='MEZZI';
	$serviceTable = 'SERVIZI';
	$serviceColumn = 'SERVIZIO';
	$groupColumn = 'ORGANIZZAZIONE';

	$mapName = 'temp';

	if ($mapName != '')
	{
		$sub1 = '(SELECT count(*) FROM  ${'.$resourceTable1.'} WHERE ${'.$resourceTable1.'.'.$serviceColumn.'} = ${'.$serviceTable.'.NOME}) as tot';
		$sub2 = '(SELECT count(*) FROM  ${'.$resourceTable2.'} WHERE ${'.$resourceTable2.'.'.$serviceColumn.'} = ${'.$serviceTable.'.NOME}) as tot2';
		$queryList = 'SELECT ${'.$serviceTable.'.LATITUDINE},${'.$serviceTable.'.LONGITUDINE},${'.$serviceTable.'.DESCRIZIONE}, ${'.$serviceTable.'.NOME}, ${'.$serviceTable.'.LETTERA} as lettera, '.$sub1.', '.$sub2.' FROM ${'.$serviceTable.'} WHERE ${'.$serviceTable.'.LATITUDINE} <> "" ORDER BY ${'.$serviceTable.'.ORDINE}';

		require_once(CAMILA_LIB_DIR.'tbs/tbs_class.php');
		require_once(CAMILA_DIR.'tbs/plugins/tbsdb_jladodb.php');

		$TBS = new clsTinyButStrong();
		$TBS->SetOption(array('render'=>TBS_OUTPUT));
		$TBS->SetOption('noerr', false);

		$TBS->LoadTemplate(CAMILA_APP_PATH.'/plugins/'.basename(dirname(__FILE__)).'/templates/tbs/it/resources_gmap.htm');
		$TBS->MergeBlock('res','adodb',$camilaWT->parseWorktableSqlStatement($queryList));
		$TBS->MergeBlock('res2','adodb',$camilaWT->parseWorktableSqlStatement($queryList));
		$_CAMILA['page']->add_userdefined(new CHAW_tbs($TBS, true));		
	}
	else
	{
		$camilaUI->insertLineBreak();
		$camilaUI->insertWarning('Nessun intervento in corso!');
	}
}

drawMap();
?>