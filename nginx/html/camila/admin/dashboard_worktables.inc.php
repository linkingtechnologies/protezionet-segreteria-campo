<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2016 Umberto Bresciani

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

$camilaWT  = new CamilaWorkTable();
	
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));

$camilaUI->insertButton('cf_worktable_admin.php', camila_get_translation('camila.admin.title.worktables'),'wrench');

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));

$camilaUI->insertButton('cf_worktable_admin_cat.php', camila_get_translation('camila.admin.title.categories'), 'wrench');

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));

$camilaUI->insertButton('?admin&dashboard=bookmarks', camila_get_translation('camila.admin.title.bookmarks'), 'wrench');

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div><br/>'));
?>