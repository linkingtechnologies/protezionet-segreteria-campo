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


require_once(CAMILA_DIR . 'datagrid/report.class.php');

class mt_report extends report {

  function draw_row()
  {
    $this->table = new CHAW_table();

    reset( $this->fields );

    //while( $fld = each( $this->fields ) ) {
	foreach ($this->fields as $key => $val) {
			$fld = [$key, $val];
        if( $fld[1]->print ) {
            $myRow = new CHAW_row();
            $fld[1]->draw_title($myRow);
            $fld[1]->draw($myRow, $this->fields);
            $this->table->add_row($myRow);
        }
    }

    global $_CAMILA;
    $_CAMILA['page']->add_table($this->table);
    $text = new CHAW_text('');
    $_CAMILA['page']->add_text($text);
  }

  function draw()
  {
    $this->draw_footer();
    $text = new CHAW_text('');
    global $_CAMILA;
    $_CAMILA['page']->add_text($text);
    $i = 0;
    $row = array();

    while( $this->load_row( $row, $i++ ) ) {
        if( isset($this->onloadrow) ) {
            $func = $this->onloadrow;
            $ok = $func($this);
        } else $ok = true;
        if( $ok ) {

            if( isset($this->group_field) ) {
                if( $this->group_value != $this->fields[$this->group_field]->value ) {
                    $this->group_value = $this->fields[$this->group_field]->value;
                    $this->draw_group();
                }
            }
            $this->draw_row();
        }
    }   
    $this->draw_footer();
  }
}
?>
