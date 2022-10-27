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


require_once(CAMILA_VENDOR_DIR . '/adodb/adodb-php/toexport.inc.php');

function CSV_export($table, $file, $db) {

    $rs = $db->Execute('select * from ' . $table);
    if (!$rs)
        echo $db->ErrorMsg();

    $f = fopen($file,'w');
    rs2csvfile($rs,$f);
    fclose($f);

    $result['result'] = 2; //success

    return $result;
}

?>