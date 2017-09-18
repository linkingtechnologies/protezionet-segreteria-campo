<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2009 Umberto Bresciani

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



  class form_intertable_checklist extends form_field
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
      var $cols = 2;
      var $checklist_values;
      var $checklist_checkboxes;
      var $_checkboxes_loaded = false;

      function form_intertable_checklist(&$form, $field, $title, $query, $label_field, $key_field, $intertable, $akey, $formkey, $bkey, $required, $validation = false)
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

          $select_all = new CHAW_js('<div class="camilacheckall" id="camilacheckall"><a id="CheckAll_'.$this->key.'" href="">'.camila_get_translation('camila.form.checkall').'</a> '.camila_get_translation('camila.form.checkallseparator').' <a id="UncheckAll_'.$this->key.'" href="">'.camila_get_translation('camila.form.uncheckall').'</a></div>');
          $form->add_userdefined($select_all);

          $count = 0;
          foreach ($this->checklist_checkboxes as $index => $checkbox) {
              $checkbox->cols = $this->cols;
              if ($this->updatable) {
                  $count++;
                  if (($count % $this->cols) != 0)
                      $checkbox->set_br(0);
                  $form->add_checkbox($checkbox);
              } else {
                  if ($checkbox->is_checked()) {
                      $myText = new CHAW_text($checkbox->label);
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

          $sql2='select '.$this->bkey.','.$this->bkey.' as bis from '.$this->intertable.' where '.$this->akey.'=\''.$avalue.'\'';

          $res2=$_CAMILA['db']->GetAssoc($sql2);
          if ($res2 === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          if (isset($_REQUEST[$this->key])) {
              $add_arr = array_diff($_REQUEST[$this->key],$res2);
              foreach ($add_arr as $key) {
                  $data[0] = $avalue;
                  $data[1] = $key;
                  $result1 = $_CAMILA['db']->Execute("INSERT INTO " . $this->intertable . "(".$this->akey.", ".$this->bkey.") VALUES (?,?)", $data);
                  if ($result1 === false)
                      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
              }
          }

          if (!isset($_REQUEST[$this->key]) || !is_array($_REQUEST[$this->key]) || count($_REQUEST[$this->key])==0 )
              $del_arr = $res2;
          else
              $del_arr = array_diff($res2,$_REQUEST[$this->key]);

          foreach ($del_arr as $key) {
              if (in_array($key, $this->checklist_values)) {
                  $data[0] = $avalue;
                  $data[1] = $key;
                  $result2 = $_CAMILA['db']->Execute("DELETE FROM " . $this->intertable . " where ".$this->akey."=? AND ".$this->bkey." = ?", $data);
                  if ($result2 === false)
                      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
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

          $sql2='select '.$this->bkey.','.$this->bkey.' as bis from '.$this->intertable.' where '.$this->akey.'=\''.$this->form->fields[$this->formkey]->value.'\'';

          $res2=$_CAMILA['db']->GetAssoc($sql2);
          if ($res2 === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          $result = $_CAMILA['db']->Execute($this->query);

          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $count = 0;
          while (!$result->EOF) {
              if ((!$processing && in_array($result->fields[$this->key_field], $res2)) || ($processing && $_REQUEST[$this->key][$result->fields[$this->key_field]]!='') ) {
                  $this->checked++;
                  $myCheckbox = new CHAW_checkbox($this->key . '['.$result->fields[$this->key_field].']', $result->fields[$this->key_field], $result->fields[$this->label_field], HAW_SELECTED);
              } else
                  $myCheckbox = new CHAW_checkbox($this->key . '['.$result->fields[$this->key_field].']', $result->fields[$this->key_field], $result->fields[$this->label_field]);

              $this->checklist_values[$result->fields[$this->key_field]] = $result->fields[$this->key_field];
              $this->checklist_checkboxes[$count] = $myCheckbox;
              $count++;
              $result->MoveNext();
          }

          $this->_checkboxes_loaded = true;
      }

  }
?>
