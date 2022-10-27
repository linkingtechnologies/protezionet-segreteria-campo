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



//require(CAMILA_LIB_DIR . '/axmls/adodb-xmlschema.inc.php');

require_once(CAMILA_DIR . '/datagrid/form.class.php');
require_once(CAMILA_DIR . '/datagrid/elements/form/static_listbox.php');
require_once(CAMILA_DIR . '/datagrid/elements/form/textarea.php');
require_once(CAMILA_DIR . '/datagrid/elements/form/hidden.php');


$form = new phpform('camila', basename($_SERVER['PHP_SELF']).'?dbquery');

//new form_static_listbox($form, 'sql', 'Prefix', CAMILA_APP_DB_.','.CAMILA_DB_, true);
//new form_static_listbox($form, 'file', 'Schema', $options, true);

new form_textarea($form, 'custom', 'SQL Query', true, 10, 70);
//new form_hidden($form, 'custom', 'sqlquery');

if ($form->process() || isset($_REQUEST['camila_custom'])) {
    require(CAMILA_DIR.'datagrid/report.class.php');

    $report = new report(($form->fields['custom']->value=='') ? $_REQUEST['camila_custom'] : $form->fields['custom']->value);
    $report->process();
    $report->draw();

    }
else
    $form->draw();
?>
