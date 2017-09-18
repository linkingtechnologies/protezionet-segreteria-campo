<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2015 Umberto Bresciani

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


class report_icon extends report_field {
    
  function report_icon($field, $title)
  {
      $this->orderable = false;
      parent::report_field($field, $title);
  }

  function draw_header(&$row, $link)
  {

    global $_CAMILA;

      $text = new CHAW_text('');
      $text->metatype = $this->metatype;
      $text->field = $this->field;

      if (!$_CAMILA['page']->camila_exporting()) {
          $row->add_column($text);
          $_CAMILA['page']->header_cols_count++;
      }
  }

  function draw(&$row, &$fields)
  {
      global $_CAMILA;

      if ($_CAMILA['page']->camila_exporting())
      {
      }
      elseif( isset($this->onprint) ) {
          $func = $this->onprint;
          $func($this, $row, $fields);
      }
      else
      {
          if ($this->imagepath != '')
              $icon = $this->imagepath;
          else
              $icon = '../../camila/images/png/'.$this->field . '_' . $this->value.'.png';
          $wbmp=str_replace('png/', 'wbmp/', $icon);
          $wbmp=str_replace('.png', '.wbmp', $wbmp);
          $image = new CHAW_image($wbmp,$icon, '');

        if (!in_array($this->field, $this->report->keys) && $this->report->inline_editing) {
            $image->set_css_class('cf_editText');
        }

        if ($this->inline && $this->report->inline_editing) {
            $id = $this->field;

            foreach($this->report->keys as $k=>$v) {
                  $v = str_replace('.', '__', $v);
                  $id .= '__cf__'.$fields['camilakey_'.$v]->value;
            }
            $image->set_id($id);
        }

          $row->add_column($image);
      }
  }

}
?>
