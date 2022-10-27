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



  class form_intertable_int_checklist extends form_field
  {
      var $query;
      var $key_field;
      var $label_field;
      var $intertable;
      var $akey;
      var $bkey;
      var $formkey;
      var $order;
      var $checked = 0;
      var $cols = 1;
      var $checklist_values;
      var $checklist_checkboxes;
      var $_checkboxes_loaded = false;
      var $value_field;

      function form_intertable_int_checklist(&$form, $field, $title, $query, $label_field, $key_field, $intertable, $akey, $formkey, $bkey, $value_field, $required, $validation = false)
      {
          $this->form_field($form, 'camilafield_'.$field, $title, $required, $validation);
          $this->query = $query;
          $this->label_field = $label_field;
          $this->key_field = $key_field;
          $this->akey=$akey;
          $this->formkey=$formkey;
          $this->bkey=$bkey;
          $this->intertable=$intertable;
          $this->updatable=true;
          $this->value_field=$value_field;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);

          if (!$this->_checkboxes_loaded)
              $this->_checkboxes(false);

          if ($this->form->noproc)
              $this->_checkboxes(true);

          $text = new CHAW_text($this->title . $this->labelseparator);
          $text->set_br(1);
          if (!empty($this->title))
              $form->add_text($text);

          $count = 0;
          foreach ($this->checklist_checkboxes as $index => $checkbox) {
              if ($this->updatable) {
                  $count++;
                  if (($count % $this->cols) != 0)
                      $checkbox->set_br(0);
                  $form->add_input($checkbox);
              } else {
                  if ($checkbox->value>0) {
                      $myText = new CHAW_text($checkbox->label.$this->labelseparator.' '.$checkbox->value);
                      $form->add_text($myText);
                  }
              }
          }
      }

      function process()
      {
          if (!$this->_checkboxes_loaded)
              $this->_checkboxes(true);

          global $_CAMILA;

          if ($this->form->fields[$this->formkey]->value == '' || !isset($this->form->fields[$this->formkey]->value)) {
              $avalue = $_CAMILA['db']->Insert_ID();
              $this->form->fields[$this->formkey]->value = $avalue;
          } else {
              $avalue = $this->form->fields[$this->formkey]->value;
          }

          if ($avalue == '')
              return;

          $sql2='select '.$this->bkey.','.$this->value_field.' from '.$this->intertable.' where '.$this->akey.'=\''.$avalue.'\'';

          $res2=$_CAMILA['db']->GetAssoc($sql2);
          if ($res2 === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());


          $sql3='delete from '.$this->intertable.' where '.$this->akey.'=\''.$avalue.'\'';
          $res3=$_CAMILA['db']->Execute($sql3);
          if ($res3 === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());


          if (isset($_REQUEST[$this->key])) {
              foreach ($_REQUEST[$this->key] as $key=>$value) {
                  $data[0] = $avalue;
                  $data[1] = $key;
                  $data[2] = $value;
                  if ($value>0) {
                      $result1 = $_CAMILA['db']->Execute("INSERT INTO " . $this->intertable . "(".$this->akey.", ".$this->bkey.",".$this->value_field.") VALUES (?,?,?)", $data);
                      if ($result1 === false)
                          camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                  }
              }
          }

          $this->value = $_REQUEST[$this->key];
          reset ($_REQUEST[$this->key]);
          foreach ($_REQUEST[$this->key] as $key => $value) {
              if ($value != '') {
                  $this->checked++;
              }
          }

      }

      function validate()
      {
          foreach ($_REQUEST[$this->key] as $key => $value) {
              if ($value != '') {
                  $this->checked++;
              }
          }

          if ($this->required && $this->checked == 0) {
              $this->form->validator->setError($this->field, 903);
          }
      }

      function _checkboxes($processing) {

          global $_CAMILA;

          $sql2='select '.$this->bkey.','.$this->value_field.' from '.$this->intertable.' where '.$this->akey.'=\''.$this->form->fields[$this->formkey]->value.'\'';

          $res2=$_CAMILA['db']->GetAssoc($sql2);
          if ($res2 === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          $result = $_CAMILA['db']->Execute($this->query);

          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          $count = 0;
          while (!$result->EOF) {
              if ((!$processing && array_key_exists($result->fields[$this->key_field], $res2)) || ($processing && $_REQUEST[$this->key][$result->fields[$this->key_field]]!='') ) {
                  $this->checked++;
                  if ($processing)
                      $myCheckbox = new CHAW_input($this->key . '['.$result->fields[$this->key_field].']', $_REQUEST[$this->key][$result->fields[$this->key_field]], $result->fields[$this->label_field]);
                  else
                      $myCheckbox = new CHAW_input($this->key . '['.$result->fields[$this->key_field].']', $res2[$result->fields[$this->key_field]], $result->fields[$this->label_field]);

              } else
                  $myCheckbox = new CHAW_input($this->key . '['.$result->fields[$this->key_field].']', '', $result->fields[$this->label_field]);

              $this->checklist_values[$result->fields[$this->key_field]] = $result->fields[$this->value_field];
              $this->checklist_checkboxes[$count] = $myCheckbox;
              $count++;
              $result->MoveNext();
          }

          $this->_checkboxes_loaded = true;
      }

  }
?>
