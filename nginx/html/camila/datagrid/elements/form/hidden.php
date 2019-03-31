<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2019 Umberto Bresciani

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



class form_hidden extends form_field {

  function form_hidden(&$form, $field, $value = '') {
  	$this->form_field($form, $field, '');
  	$this->value = $value;
  	$this->cleanable = false;
  	$this->hidden = true;
  	$form->add($this);
  }


  function draw(&$form)
  {
    parent::draw($form);
    if (!is_array($this->value)) {
      $myHidden = new CHAW_hidden($this->key, $this->value);
      $form->add_input($myHidden);
    } else {
      foreach ($this->value as $key => $value) {
        $myHidden = new CHAW_hidden($this->key.'[]', $value);
        $form->add_input($myHidden);
      }
    }
  }


  function process()
  {
    if( isset( $_REQUEST[$this->key] ) ) {
      $this->value = $_REQUEST[$this->key];
    }
  }

}