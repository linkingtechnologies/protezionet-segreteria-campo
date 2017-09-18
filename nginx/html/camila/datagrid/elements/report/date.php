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


class report_date extends report_field {

  function date($field, $title)
  {
      parent::field($field, $title);
      $this->type = 'date';
  }

  function draw(&$row, $fields)
  {
      if ($this->value!='' && $this->value!='0000-00-00') {
          global $_CAMILA;

//          $fmt = '';
//          $f = Array();
//          $m = camila_get_translation('camila.dateformat.monthpos');
//          $d = camila_get_translation('camila.dateformat.daypos');
//          $y = camila_get_translation('camila.dateformat.yearpos');
//          $f[$m] = 'm';
//          $f[$d] = 'd';
//          $f[$y] = 'Y';
//          ksort($f);
//          reset($f);
//          $count = 0;
//          while (list($k, $v) = each($f)) {
//              $fmt.=$v;
//              if ($count<2)
//                  $fmt.=camila_get_translation('camila.dateformat.separator');
//              $count++;
//          }

          $this->value = $_CAMILA['db']->UserDate($this->value, $_CAMILA['date_format']);
      }
      else
          $this->value = '';
      parent::draw($row, $fields);

  }
}
?>