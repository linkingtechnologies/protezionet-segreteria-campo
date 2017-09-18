<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2017 Umberto Bresciani

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



  class form_password extends form_textbox
  {

      function draw(&$form)
      {
          parent::base_draw($form);
          if ($this->updatable) {
              $myInput = new CHAW_input($this->key, $this->value, $this->title . $this->labelseparator);
              if ($this->maxlength > 0)
                  $myInput->set_maxlength($this->maxlength);
              if ($this->size > 0)
                  $myInput->set_size($this->size);
              $myInput->set_type(HAW_INPUT_PASSWORD);
              $form->add_input($myInput);
          } else {
              //$myHidden = new CHAW_hidden($this->key, $this->value);
              //$form->add_input($myHidden);
              $myText = new CHAW_text($this->title.$this->labelseparator.' **********');
              $form->add_text($myText);
          }
      }
	  
	  function process()
      {
		  if (isset($_REQUEST[$this->key]) && $_REQUEST[$this->key] != '') {
		  $this->value = $_REQUEST[$this->key];
		  global $_CAMILA;
		  $camilaAuth = new CamilaAuth();
		  //$camilaAuth->db = $_CAMILA['db'];
		  $this->value = $camilaAuth->hashPassword($this->value);
		  }
	  }

  }
?>
