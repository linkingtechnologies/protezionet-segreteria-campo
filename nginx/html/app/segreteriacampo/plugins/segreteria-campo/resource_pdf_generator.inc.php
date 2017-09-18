<?php
$camilaWT  = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

if ($title == '')
	$title = 'Moduli accreditamento ' . $resourceName;

if ($icon=='')
	$icon = 'barcode';

$orderBy = ' ORDER BY ${'.$resourceTable.'.'.$provinceColumn.'}, ${'.$resourceTable.'.'.$organizationColumn.'}';

$where = '';
if ($_CAMILA['user_visibility_type'] == 'personal')
    $where .= ' WHERE ${'.$resourceTable.'.'.$personalVisibilityColumn.'} =' . $_CAMILA['db']->qstr($_CAMILA['user']);

$vSheet = $camilaWT->getWorktableSheetId($resourceTable);

if ($filterColumn != '')
{
	$where = ' WHERE ${'.$resourceTable.'.'.$filterColumn.'} = ' . $_CAMILA['db']->qstr($filterValue);
}

$query = 'SELECT distinct ${'.$resourceTable.'.'.$provinceColumn.'},${'.$resourceTable.'.'.$organizationColumn.'} FROM ${'.$resourceTable.'}' . $where . $orderBy;

$resResult = $camilaWT->startExecuteQuery($query);

$myText = new CHAW_text('');
$_CAMILA['page']->add_text($myText);

$camilaUI->insertTitle($title, $icon);
$camilaUI->insertDivider();

$count = $resResult->RecordCount();
if ($count == 0)
	$camilaUI->insertWarning('Nessun dato disponibile!');

while (!$resResult->EOF) {
	$a = $resResult->fields;
	$link = 'cf_worktable'.$vSheet.'.php?camila_xml2pdf='.urlencode($template).'&camila_w1f=_C_organizzazione&camila_w1c=eq&camila_w1v='.urlencode($a[1]);
	if ($filterColumn != '')
	{
		$link .= '&camila_w2w=and&camila_w2f=_C_'.strtolower($filterColumn).'&camila_w2c=eq&camila_w2v='.urlencode($filterValue);
	}

	$link .= '&filename='.urlencode($a[1]).'&camila_pagnum=-1&submit_button=Filtra+i+dati';
	$myLink = new CHAW_link($title . ' ' . $a[0] . ' ' . $a[1],$link);
	$myLink->set_css_class('btn btn-md btn-default btn-info');
	$myLink->set_br(2);
	$_CAMILA['page']->add_link($myLink);
	$resResult->MoveNext();
}

?>