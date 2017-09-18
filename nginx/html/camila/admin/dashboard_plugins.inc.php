<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2017 Umberto Bresciani

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

global $_CAMILA;

$code = "<div id='toolbar' class='btn-group'>
    <button id='addPlugin' type='button' class='btn btn-default'>
        <i class='glyphicon glyphicon-plus'></i> ".camila_get_translation('camila.admin.action.install')."
    </button>
</div>";
$js = new CHAW_js($code);
$_CAMILA['page']->add_userdefined($js);

$myTable = new CHAW_table();
$_CAMILA['page']->add_table($myTable);

$_CAMILA['page']->camila_add_js("<link href=\"".CAMILA_LIB_DIR."bootstrap-table/bootstrap-table.css\" rel=\"stylesheet\">\n");
$_CAMILA['page']->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."bootstrap-table/bootstrap-table.min.js'></script>\n");
$_CAMILA['page']->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."bootstrap-table/bootstrap-table-locale-all.min.js'></script>\n");
$code = "<script>
\$( document ).ready(function() {
\$('#table0').bootstrapTable({
    locale:'en-US',
	singleSelect: true,
	clickToSelect:true,
    selectItemName:'myRadioName',
	url: 'cf_app.php?admin&dashboard=plugins&json=plugins',
	toolbar: '#toolbar',
    columns: [
	{
        field: 'state',
        radio: true
    },
	{
        field: 'id',
        title: '".camila_get_translation('camila.admin.title.plugin')."'
    }, {
        field: 'status',
        title: '".camila_get_translation('camila.admin.title.status')."'
    },]
});

    var \$table = \$('#table0'),
        \$addPluginButton = \$('#addPlugin');
    \$(function () {
        \$addPluginButton.click(function () {
			var sel = \$table.bootstrapTable('getSelections');
			if (sel.length > 0) {
				camila_waiting_box();
				window.location.href='cf_app.php?admin&dashboard=plugins&json=install-plugin&lang=".$_CAMILA['lang']."&plugin='+sel[0].id;
			}
        });
    });

});
</script>";
$js = new CHAW_js($code);
$_CAMILA['page']->add_userdefined($js);

?>