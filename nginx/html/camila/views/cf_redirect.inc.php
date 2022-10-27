<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2022 Umberto Bresciani

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
	
camila_save_preferences();

$text = new CHAW_text(camila_get_translation('camila.manualredirect'), HAW_TEXTFORMAT_ITALIC);
$_CAMILA['page']->add_text($text);
$myLink = new CHAW_link(camila_get_translation('camila.clickhere'), $_REQUEST['camila_redirect']);
$_CAMILA['page']->add_link($myLink);
$_CAMILA['page']->set_redirection(0.1, $_REQUEST['camila_redirect']);
$_CAMILA['page']->create_page();

?>