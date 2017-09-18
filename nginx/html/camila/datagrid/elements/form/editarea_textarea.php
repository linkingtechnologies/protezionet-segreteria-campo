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



  class form_editarea_textarea extends form_field
  {
      var $init;

      function form_editarea_textarea(&$form, $field, $title, $required = true, $init, $rows, $cols, $validation)
      {
          $this->form_field($form, $field, $title, $required, $validation);
          $this->cols = $cols;
          $this->rows = $rows;
          $this->init = $init;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          global $_CAMILA;
          $code = '<script type="text/javascript" src="' . CAMILA_LIB_DIR . 'editarea/edit_area/edit_area_full.js"></script>';
          $code .= "<script>\n";
          $code .= 'function camila_editarea_save(id, content){xGetElementById(id).value=content; xGetElementById(id).parentNode.submit();}';
          $code .= "</script>\n";
          $_CAMILA['page']->camila_add_js($code,'editarea');

          $code = "<script>\n";
          $code .= ' camila_addDOMLoadEvent(function(){editAreaLoader.init({id:"' . $this->key . '",' . str_replace("'", "\'", $this->init) . '})})';
          $code .= "</script>\n";
          $_CAMILA['page']->camila_add_js($code);
          $myTextarea = new CHAW_textarea($this->key, $this->value, $this->title . $this->labelseparator, $this->rows, $this->cols);
          $form->add_textarea($myTextarea);
      }

      function process()
      {
          if (isset($_POST[$this->key])) {
              $this->value = $_POST[$this->key];
          }

      }

      function validate()
      {
          if (!$this->updatable || strpos(strtolower($this->validation),'ignore')!==false)
              return true;

          parent::validate();

          if ($this->value != '' and $this->maxchars > 0)
              $this->form->validator->length($this->field, '<=', $this->maxchars);
      }
      
  }
?>
