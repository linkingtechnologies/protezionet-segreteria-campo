<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2008 Umberto Bresciani

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


require_once(CAMILA_DIR . '/datagrid/form.class.php');
require_once(CAMILA_DIR . '/datagrid/elements/form/static_listbox.php');

$options = '';
if (is_dir(CAMILA_TABLES_DIR)) {
    if ($dh = opendir(CAMILA_TABLES_DIR)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..' && substr($file,-3) == 'xml')
                $options .= $file . ',';
        }
        closedir($dh);
    }
}

$form = new phpform('schema', basename($_SERVER['PHP_SELF']).'?dbschema');

//new form_static_listbox($form, 'prefix', 'Prefix', CAMILA_APP_DB_.','.CAMILA_DB_, true);
new form_static_listbox($form, 'file', 'Schema', $options, true);
new form_static_listbox($form, 'drop', 'Drop', 'N,Y', true);


if ($form->process()) {

    require_once(CAMILA_DIR.'db/schema.inc.php');

    $result = create_table(CAMILA_TABLES_DIR.'/'.$form->fields['file']->value, '', $_CAMILA['db'], ($form->fields['drop']->value == 'Y') ? true : false);

    $results = Array(0 => 'FAILURE', 1 => 'ERRORS', 2 => 'SUCCESSFUL');

    ob_start();
    print_r($results);
    print_r($result);
    $sql_r = ob_get_contents();
    ob_end_clean();

    camila_information_text('EXECUTE SCHEMA ' . $sql_r);
}

$form->draw();

?>
