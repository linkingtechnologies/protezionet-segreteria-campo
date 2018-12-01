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

$camilaUI->insertTitle(CAMILA_APPLICATION_NAME . ' ' . (String)CamilaPlugins::getPluginInformation(basename(dirname(__FILE__)))->info->versionName . ' (' . (String)CamilaPlugins::getPluginInformation(basename(dirname(__FILE__)))->info->versionNumber . ')', 'question-sign');
$camilaUI->insertDivider();

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));

$camilaUI->insertButton('http://www.protezionet.it/dokuwiki/segreteria-campo', 'Manuale','globe');

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));


$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div><br/><br/><br/><br/><br/><br/>'));

$camilaUI->insertDivider();
$camilaUI->insertText('Powered by Camila PHP Framework - Copyright (C) 2006-2018 Umberto Bresciani');
$camilaUI->insertText('Programma rilasciato sotto licenza GNU GPL');
//$camilaUI->insertButton('https://it.wikipedia.org/wiki/GNU_General_Public_License', 'Licenza d\'uso','globe');

$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));

?>