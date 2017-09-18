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


require(CAMILA_LIB_DIR . '/adodb5/toexport.inc.php');


$options = '';

if( is_array( $tables = $_CAMILA['db']->MetaTables('TABLES'))) {
    foreach ($tables as $table) {
        $options .= $table . ',';
    }
}

$form = new phpform('table', basename($_SERVER['PHP_SELF']).'?dbexport');

new form_static_listbox($form, 'table', 'Table', $options, true);

if ($form->process()) {

    $file = CAMILA_TABLES_DIR.'/'.$form->fields['table']->value.'.csv';

    require_once(CAMILA_DIR.'db/export.inc.php');

    $result = csv_export($form->fields['table']->value, $file, $_CAMILA['db']);

    ob_start();
    print_r($result);
    $sql_r = ob_get_contents();
    ob_end_clean();

    camila_information_text('DATA EXPORT ' . $sql_r);

}

$form->draw();

?>
