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


require_once(CAMILA_DIR . 'datagrid/report.class.php');

class selectform_report extends report {

  function draw_row()
  {
      global $_CAMILA;
      $myRow = new CHAW_row();

      reset($this->fields );
      while($fld = each($this->fields)) {
          if ($fld[1]->print && !($_CAMILA['page']->camila_exporting() && !(strpos($fld[1]->field, 'camilakey_') === false) ) ) {
              $fld[1]->draw($myRow, $this->fields);
          }
      }
      $this->table->add_row($myRow);
  }
  
}

?>
