<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2017 Umberto Bresciani

    Camila PHP Framework is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Camila PHP Framework is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Camila PHP Framework. If not, see <http://www.gnu.org/licenses/>. */

$camilaIntegrity = new CamilaIntegrity('plugins/'.basename(dirname(__FILE__)).'/conf/integrity.xml');

$checks = $camilaIntegrity->getChecks();

global $_CAMILA;

if ($_REQUEST['camila_custom']!='')
{
	$camilaUI->insertButton('?dashboard='.$_REQUEST['dashboard'],'TORNA INDIETRO','chevron-left',false);

	foreach ($checks as $k => $v) {
		$arr = $v->check;
		for ($i=0; $i<count($arr);$i++)
		{
			$item = $arr[$i];
			if($item->id == $_REQUEST['camila_custom'])
			{
				$camilaWT  = new CamilaWorkTable();
				$camilaWT->db = $_CAMILA['db'];
				
				$oSheet = $camilaWT->getWorktableSheetId(strtoupper($item->object));
				$camilaUI->insertButton('cf_worktable'.$oSheet.'.php', 'SCHEDA '.strtoupper($item->object), 'list');

				$camilaUI->insertTitle($item->title, 'warning-sign');
				$camilaUI->insertDivider();

				$title = $_REQUEST['error'];
				$stmt = $camilaWT->parseWorktableSqlStatement($item->query);
				$orderBy = '';
				$orderDir = '';

				$report = new report($stmt, $title, $orderBy, $orderDir);
				$report->drawfilterbox = false;
				$report->process();
				$report->draw();

				$_CAMILA['page']->camila_export_enabled = true;
			}
		}
	}

} else {
	$camilaUI->insertTitle('Controllo dati', 'warning-sign');
	$camilaUI->insertDivider();

	$camilaIntegrity->camilaWT = new CamilaWorkTable();
	$camilaIntegrity->camilaWT->wtTable = CAMILA_TABLE_WORKT;
	$camilaIntegrity->camilaWT->wtColumn = CAMILA_TABLE_WORKC;
	$camilaIntegrity->camilaWT->db = $_CAMILA['db'];

	foreach ($checks as $k => $v) {
		$arr = $v->check;
		for ($i=0; $i<count($arr);$i++)
		{
			$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));	
			$item = $arr[$i];
			$check = $camilaIntegrity->check($item);	
			if ($check->code == 'success') {
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
				$camilaUI->insertSuccess($item->title);
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
				$camilaUI->insertSubTitle('OK', 'thumbs-up');
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
			} else {
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
				$camilaUI->insertWarning($check->message);
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
				$camilaUI->insertButton('?dashboard=ic1&camila_custom='.$item->id.'&error='.urlencode($check->message), 'Visualizza','list',false,$check->count);
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
			}
			$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
		}
		$camilaUI->insertDivider();
	}

}

?>