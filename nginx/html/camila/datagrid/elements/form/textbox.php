<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2016 Umberto Bresciani

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



  class form_textbox extends form_field
  {
      function form_textbox(&$form, $field, $title, $required = false, $size = 0, $maxlength = 0, $validation = '')
      {
          $this->form_field($form, $field, $title, $required, $validation);
          
          if ($form->hasdblink) {
              if ($size == 0)
                  $size = $form->dbfields[$field]['maxlength'];

              if ($maxlength == 0)
                  $maxlength = $form->dbfields[$field]['maxlength'];
          }

          $this->size = $size;
          $this->maxlength = $maxlength;
          $this->validation = $validation;
          $form->add($this);

      }

      function draw(&$form)
      {
          parent::draw($form);

          if ($this->updatable) {
              $myInput = new CHAW_input($this->key, $this->value, $this->title . $this->labelseparator);
			  $myInput->set_br($this->br);
			  $myInput->set_css_class($this->css_class);
              if ($this->maxlength > 0)
                  $myInput->set_maxlength($this->maxlength);
              if ($this->size > 0)
                  $myInput->set_size($this->size);
              $form->add_input($myInput);
          } else {
              $myHidden = new CHAW_hidden($this->key, $this->value);
              $form->add_hidden($myHidden);
              $myText = new CHAW_text($this->title.$this->labelseparator.' '.$this->value);
              $form->add_text($myText);
          }
      }

      function base_draw(&$form)
      {
          parent::draw($form);

      }

      function process()
      {
          if (isset($_REQUEST[$this->key])) {
              $this->value = $_REQUEST[$this->key];
              if ($this->forcedraw)
                  $this->defaultvalue = $this->value;
          }
      }

      function validate()
      {
          if (!$this->updatable || strpos(strtolower($this->validation),'ignore')!==false)
              return true;

          parent::validate();

          if ($this->value != '' and $this->maxlength > 0)
              $this->form->validator->length($this->field, '<=', $this->maxlength);
      }
  }
?>
