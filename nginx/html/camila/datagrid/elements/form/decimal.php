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
   along with Camila Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */



  class form_decimal extends form_field
  {

      function __construct(&$form, $field, $title, $required = false, $precision=5, $scale=2, $validation='')
      {
          parent::__construct($form, $field, $title, $required, $validation);
          $this->precision = $precision;
          $this->scale = $scale;
          $form->add($this);
      }

      function draw(&$form)
      {
	      parent::draw($form);
	      
	      $value = str_replace('.',camila_get_translation('camila.decimal.separator'), $this->value);

          if ($this->updatable) {
	          $myInput = new CHAW_input($this->key, $value, $this->title . $this->labelseparator);
		      if (($this->precision+$this->scale) > 0)
		          $myInput->set_maxlength($this->precision+$this->scale+1);
		          
		      $form->add_input($myInput);
	      } else {
		      $myText = new CHAW_text($this->title.$this->labelseparator.' '.$value);
		      $form->add_text($myText);		      
	      }
      }

      function process()
      {
          if ($this->updatable && isset($_REQUEST[$this->key])) {
              $this->value = str_replace(camila_get_translation('camila.decimal.separator'), '.', $_REQUEST[$this->key]);;
          }
      }

      function validate()
      {
          parent::validate();

          $ar = explode('.',$this->value);
          $count = 0;

          foreach ($ar as $val) {
	          $strlen = strlen($val);

	          if ($strlen > 0) {
                  if (!preg_match("/^[0-9]{".$strlen."}$/", $val)) 
                      $this->form->validator->setError($this->field, 908);
              }
              $count++;
          }

          if ($count>2) 
              $this->form->validator->setError($this->field, 908);

      }
  }
?>
