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

defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

$_CAMILA['page']->camila_export_enabled = false;

$myText = new CHAW_text('');
$myText->set_br(2);
$_CAMILA['page']->add_text($myText);

$root = CAMILA_FM_ROOTDIR;

//echo $root;
/*    if ($_CAMILA['user_visibility_type'] == 'personal')
        $where .= ' and ' . CAMILA_WORKTABLE_EXT_TABLE_PERSONAL_VISIBILITY_FIELD . '=' . $_CAMILA['db']->qstr($_CAMILA['user']);

	if ($_CAMILA['user_visibility_type'] == 'group')
        $where .= ' and ' . CAMILA_WORKTABLE_EXT_TABLE_GROUP_VISIBILITY_FIELD . '=' . $_CAMILA['db']->qstr($_CAMILA['user_group']);*/

/*      if ($_CAMILA['user_visibility_type']=='personal')
          $filter= ' where codorganizzazione='.$_CAMILA['db']->qstr($_CAMILA['user']);
*/	  
if ($_CAMILA['user_visibility_type']=='group')
	$root .= '/'.$_CAMILA['user_group'];

$fm = new CamilaFileManagement($root);
$fm->canUpload = ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP);
$fm->canDelete = $fm->canUpload;
$fm->draw();

?>