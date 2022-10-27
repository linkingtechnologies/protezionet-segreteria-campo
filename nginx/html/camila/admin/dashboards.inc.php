<?php
/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2022 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */

defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

$camilaUI = new CamilaUserInterface();
$_CAMILA['page']->camila_export_enabled = false;

$camilaUI->insertLineBreak();
$camilaUI->insertLineBreak();

if (isset($_REQUEST['json'])) {
	$exit = false;
	switch($_REQUEST['json']) {
		case 'plugins':
			echo json_encode(CamilaPlugins::getList($_CAMILA['db']));
			$exit = true;
			break;
		case 'install-plugin':
			CamilaPlugins::install($_CAMILA['db'], $_CAMILA['lang'], $_REQUEST['plugin']);
			break;
	}
	if ($exit)
		exit();
}
else
{		  
if (isset($_REQUEST['dashboard']))

{
	$currentTab = $camilaUI->printHomeMenu(CAMILA_DIR.'/admin/conf/menu.xml');
    require(CAMILA_DIR.'/admin/dashboard_' . $_REQUEST['dashboard'] . '.inc.php');
} else {
	$defaultId = 'worktables';
	$currentTab = $camilaUI->printHomeMenu(CAMILA_DIR.'/admin/conf/menu.xml', $defaultId);
	require(CAMILA_DIR.'/admin/dashboard_' . $defaultId . '.inc.php');
}

$camilaUI->insertLineBreak();
}
?>