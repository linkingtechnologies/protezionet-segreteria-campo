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



  class form_db_key_textbox extends form_field
  {

      var $alphanumeric = false;

      function form_db_key_textbox(&$form, $field, $title, $required = false, $size = 0, $maxlength = 0, $validation = '')
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
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          if ($this->form->mode == 'insert') {
              $myInput = new CHAW_input($this->key, $this->value, $this->title . $this->labelseparator);
              if ($this->maxlength > 0)
                  $myInput->set_maxlength($this->maxlength);
              if ($this->size > 0)
                  $myInput->set_size($this->size);
              
              $form->add_input($myInput);

          } else {
              $myHidden = new CHAW_hidden($this->key, $this->value);
              $form->add_hidden($myHidden);
              $myText = new CHAW_text($this->title . $this->labelseparator . $this->value);
              $form->add_text($myText);
          }

      }

      function process()
      {
          //if ($this->form->mode != 'insert')
          //    return;
          
          if (isset($_REQUEST[$this->key])) {
              $this->value = $_REQUEST[$this->key];
          }
      }

      function validate()
      {
          if ($_REQUEST[$this->form->table.'_sess_mode'] != 'insert')
              return true;

          parent::validate();
          if ($this->value != '' and $this->maxlength > 0)
              $this->form->validator->length($this->field, '<=', $this->maxlength);

          if ($this->alphanumeric)
              $this->form->validator->alphaNumeric($this->field);
          else
              $this->form->validator->alpha($this->field);
          
          if ($this->value != '') {
              global $_CAMILA;
              $query = 'select * from '.$this->form->table.' where '.$this->field.'='.$_CAMILA['db']->Quote($this->value);
              $result = $_CAMILA['db']->Execute($query);
              if ($result === false)
                  camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
              if ($result->RecordCount() > 0) {
                  $this->form->validator->setError($this->field, 907);
              }
          }
      }
  }
?>
