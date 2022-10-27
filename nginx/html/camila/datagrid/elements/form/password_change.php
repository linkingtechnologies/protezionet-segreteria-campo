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


  require_once(CAMILA_DIR.'datagrid/elements/form/field.php');


  class form_password_change extends form_field
  {
      function form_password_change(&$form, $field, $title, $required = true, $size = CAMILA_PASSWORD_LENGTH, $maxlength = CAMILA_PASSWORD_LENGTH, $validation = '')
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
              $myOldInput = new CHAW_input($this->key.'_old', '', camila_get_translation('camila.form.oldpassword') . $this->labelseparator);
              if ($this->maxlength > 0)
                 $myOldInput->set_maxlength($this->maxlength);
              if ($this->size > 0)
                  $myOldInput->set_size($this->size);
              $myOldInput->set_type(HAW_INPUT_PASSWORD);
              $form->add_input($myOldInput);

              $myInput = new CHAW_input($this->key, '', $this->title . $this->labelseparator);
              if ($this->maxlength > 0)
                  $myInput->set_maxlength($this->maxlength);
              if ($this->size > 0)
                  $myInput->set_size($this->size);
              $myInput->set_type(HAW_INPUT_PASSWORD);

              $form->add_input($myInput);

              $myConfirmInput = new CHAW_input($this->key.'_confirm', '', camila_get_translation('camila.form.confirmpassword') . $this->labelseparator);
              if ($this->maxlength > 0)
                  $myConfirmInput->set_maxlength($this->maxlength);
              if ($this->size > 0)
                  $myConfirmInput->set_size($this->size);
              $myConfirmInput->set_type(HAW_INPUT_PASSWORD);
              $form->add_input($myConfirmInput);

          } else {
              $myText = new CHAW_text($this->title.$this->labelseparator.' *******');
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
          }
      }

      function validate()
      {
          parent::validate();

          if ($this->value != '') {
              global $_CAMILA;
              $query = 'select password from ' . CAMILA_TABLE_USERS . ' where id='.$_CAMILA['db']->Quote($_CAMILA['user_id']);
              $result = $_CAMILA['db']->Execute($query);
              if ($result === false)
                  camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
              if ($result->RecordCount() == 1) {
                  if ($result->fields['password'] != $_REQUEST[$this->key.'_old'])
                      $this->form->validator->setError($this->field, 910);
              } else
                  $this->form->validator->setError($this->field, 910);
          }

          if ($_REQUEST[$this->key.'_confirm'] != $this->value)
                  $this->form->validator->setError($this->field, 911);

      }
  }
?>
