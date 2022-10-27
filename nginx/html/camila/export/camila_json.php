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


require_once('camila_gva.php');


class CAMILA_JSON_deck extends CAMILA_GVA_deck
{

function create_page()
{
    global $_CAMILA;

    $this->custom_properties = Array('camila_record_count'=>$_CAMILA['report_record_count'],'camila_last_page'=>$_CAMILA['report_last_page']);

//    if ($_REQUEST['camila_response_handler'] != '')
        $this->_gva_responseHandler = $_REQUEST['camila_response_handler'];
    parent::create_page();
}



};

?>
