<?php
$camilaUI->insertTitle('Backup dati','download');

global $_CAMILA;
$camilaWT  = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

$result = $camilaWT->getWorktableSheets();

$wSheets = Array();
while (!$result->EOF) {
	$a = $result->fields;
	$id = $a['id'];
	$title = $a['short_title'];
	$wSheets[$id]=$title;
	$result->MoveNext();
}

foreach ($wSheets as $k=>$val)
{
	$count = $camilaWT->countWorktableRecords($k);
	if ($count>0)
	{
		$date = $_CAMILA['db']->UserDate(date('Y-m-d'), camila_get_locale_date_adodb_format());
		$params = "camila_visible_cols_only=n&camila_worktable_export=dataonly&camila_pagnum=-1&camila_export_action=download&hidden=camila_xls&camila_export_format=camila_xls&camila_xls=Esport&camila_export_filename=";
		$camilaUI->insertButton('cf_worktable'.$k.'.php?'.$params.urlencode($val).'+'.$date, 'BACKUP ' . $val, 'hdd');
	}
}

?>