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


class report_byte extends report_field {

  function report_byte($field, $title)
  {
      parent::report_field($field, $title);
  }

  function draw(&$row, $fields)
  {

     $size = $this->value;
     if ($size >= 1073741824) {
          $size = round($size / 1073741824 * 100) / 100 . ' GB';
      } elseif ($size >= 1048576) {
          $size = round($size / 1048576 * 100) / 100 . ' MB';
      } elseif ($size >= 1024) {
          $size = round($size / 1024 * 100) / 100 . ' KB';
      } else
          $size = $size . ' B';
      if ($size == 0)
          $size = '-';

      $this->value = str_replace('.',camila_get_translation('camila.decimal.separator'), $size);
      parent::draw($row, $fields);
  }

}
?>
