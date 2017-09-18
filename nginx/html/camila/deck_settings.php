<?php
/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2009 Umberto Bresciani

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


/*$_CAMILA['page']->set_border(CAMILA_BORDER);
$_CAMILA['page']->set_bgcolor(CAMILA_BGCOLOR);
$_CAMILA['page']->set_disp_bgcolor(CAMILA_DISP_BGCOLOR);
$_CAMILA['page']->set_width(CAMILA_WIDTH);
$_CAMILA['page']->set_height(CAMILA_HEIGHT);*/
$_CAMILA['page']->link_brackets = false;

if ($_CAMILA['user_preferences']['c_ff'] != '')
    $_CAMILA['page']->set_face($_CAMILA['user_preferences']['c_ff']);
else
    $_CAMILA['page']->set_face(CAMILA_FACE);

if ($_CAMILA['user_preferences']['c_fs'] != '')
    $_CAMILA['page']->set_size($_CAMILA['user_preferences']['c_fs']);
else
    $_CAMILA['page']->set_size(CAMILA_SIZE);

$_CAMILA['page']->set_link_color(CAMILA_LINK_COLOR);
$_CAMILA['page']->set_vlink_color(CAMILA_VLINK_COLOR);
?>
