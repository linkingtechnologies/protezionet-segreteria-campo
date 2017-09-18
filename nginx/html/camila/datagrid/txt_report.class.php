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


require_once(CAMILA_DIR . 'datagrid/report.class.php');

class txt_report extends report {

  function draw_row()
  {
    reset( $this->fields );

    while( $fld = each( $this->fields ) ) {
        if( $fld[1]->print ) {
          $fld[1]->print_title($myRow);
          $fld[1]->print_value($myRow);
        }
    }
    $text = new CHAW_text("");
    global $_CAMILA;
    $_CAMILA['page']->add_text($text);
  }

  function draw()
  {
    $this->draw_footer();
    $text = new CHAW_text("");
    global $_CAMILA;
    $_CAMILA['page']->add_text($text);
    $i = 0;
    $row = array();
    //$this->draw_header();
    while( $this->load_row( $row, $i++ ) ) {
        if( isset($this->onloadrow) ) {
            $func = $this->onloadrow;
            $ok = $func($this);
        } else $ok = true;
        if( $ok ) {
            // check if group changed
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
