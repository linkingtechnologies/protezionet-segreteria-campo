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

$camilaUI = new CamilaUserInterface();

$_CAMILA['page']->camila_export_enabled = false;


if (isset($_REQUEST['dashboard'])) {
	$currentTab = $camilaUI->printHomeMenu(CAMILA_HOMEDIR.'/plugins/'.basename(dirname(__FILE__)).'/conf/menu.xml');
    require('plugins/'.basename(dirname(__FILE__)).'/dashboard_' . $_REQUEST['dashboard'] . '.inc.php');
} else {
	$defaultId = 'm0';
	$currentTab = $camilaUI->printHomeMenu(CAMILA_HOMEDIR.'/plugins/'.basename(dirname(__FILE__)).'/conf/menu.xml', $defaultId);
	require('plugins/'.basename(dirname(__FILE__)).'/dashboard_' . $defaultId . '.inc.php');
}

?>