<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2011 Umberto Bresciani

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
require_once(CAMILA_DIR.  '/datagrid/elements/form/textarea.php');
require_once(CAMILA_DIR.  '/datagrid/elements/form/integer.php');


$form = new dbform(CAMILA_APPLICATION_PREFIX.'camila_bookmarks', 'id', 'id,sequence,base_url,title,url', 'sequence', 'asc', 'lang = ' . $_CAMILA['db']->qstr($_CAMILA['lang']), true, true, true, false, true);
$form->mapping=camila_get_translation('camila.mapping.admin.users');

new form_hidden($form, 'id', $_CAMILA['db']->GenID(CAMILA_APPLICATION_PREFIX.'bookmarkseq', 10000).camila_hash(10));
new form_integer($form, 'sequence', camila_get_translation('camila.login.username'), true);
new form_textbox($form, 'base_url', camila_get_translation('camila.login.username'), true, 50, 50);
new form_textbox($form, 'title', camila_get_translation('camila.login.username'), true, 50, 50);
new form_textarea($form, 'url', camila_get_translation('camila.login.surname'), true);
new form_hidden($form, 'lang', $_CAMILA['lang']);


$form->process();
$form->draw();

?>
