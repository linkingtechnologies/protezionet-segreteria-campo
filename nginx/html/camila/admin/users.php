<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2016 Umberto Bresciani

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


require_once(CAMILA_DIR."datagrid/db_form.class.php");

require_once(CAMILA_DIR . '/datagrid/elements/form/static_listbox.php');
require_once(CAMILA_DIR . '/datagrid/elements/form/textbox.php');
require_once(CAMILA_DIR . '/datagrid/elements/form/hidden.php');
require_once(CAMILA_DIR.  '/datagrid/elements/form/password.php');
require_once(CAMILA_DIR.  '/datagrid/elements/form/generate_password.php');


$form = new dbform(CAMILA_TABLE_USERS, 'id', 'id,username,surname,name,grp,level,visibility_type,token', 'username', 'asc', 'username <> ' . $_CAMILA['db']->qstr($_CAMILA['user']), true, true, true, false, true);
$form->mapping=camila_get_translation('camila.mapping.admin.users');

new form_textbox($form, 'id', camila_get_translation('camila.worktable.field.id'), false, 50, 50);

new form_textbox($form, 'username', camila_get_translation('camila.login.username'), true, 50, 50);
new form_textbox($form, 'surname', camila_get_translation('camila.login.surname'), false, 50, 50);
new form_textbox($form, 'name', camila_get_translation('camila.login.name'), false, 50);
new form_password($form, 'password', camila_get_translation('camila.login.password'));
//new form_static_listbox($form, 'grp', camila_get_translation('camila.login.group'), camila_get_translation('camila.login.options.group'));
new form_textbox($form, 'grp', camila_get_translation('camila.login.group'), false, 20);
new form_static_listbox($form, 'visibility_type', camila_get_translation('camila.login.visibility'), camila_get_translation('camila.login.options.visibility'));

if (is_object($form->fields['grp']))
    $form->fields['grp']->defaultvalue = 'default';
new form_static_listbox($form, 'level', camila_get_translation('camila.login.level'), camila_get_translation('camila.login.options.level'));

new form_generate_password($form, 'token', camila_get_translation('camila.login.token'), 100, false, 'alpha', 75);



$form->process();
$form->draw();

?>