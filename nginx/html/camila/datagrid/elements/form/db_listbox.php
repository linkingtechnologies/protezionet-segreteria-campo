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



  class form_db_listbox extends form_field
  {
      var $table;
      var $lbkey;
      var $lbvalue;
      var $order;

      // separate fields with comma
      var $fields_from;

      // separate fields with comma
      var $fields_to;

      function __construct(&$form, $field, $title, $table, $key, $value, $order, $required, $fields_from, $fields_to, $validation)
      {
          parent::__construct($form, $field, $title, $required, $validation);
          $this->table = $table;
          $this->lbkey = $key;
          $this->lbvalue = $value;
          $this->order = $order;
          $this->fields_from = $fields_from;
          $this->fields_to = $fields_to;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          $text = new CHAW_text($this->title.$this->labelseparator);
          $text->set_br(0);
          if (!empty($this->title))
              $form->add_text($text);
          
          $query = "select {$this->lbkey}, {$this->lbvalue} {$this->fields_from} from {$this->table} order by {$this->order}";

          $mySelect = new CHAW_select($this->key);
          
          global $_CAMILA;

          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_NUM);
          $result = $_CAMILA['db']->Execute($query);
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $val = '';
          if ($this->updatable) {
              while (!$result->EOF) {
                  if ($result->fields[0] == $this->value) {
                      $mySelect->add_option($result->fields[1], $result->fields[0], HAW_SELECTED);
                      $val = $result->fields[0];
                  }
                  else
                      $mySelect->add_option($result->fields[1], $result->fields[0]);
                  $result->MoveNext();
              }

              $form->add_select($mySelect);
              $text = new CHAW_text('');
              $form->add_text($text);
          } else {
              while (!$result->EOF) {
                  if ($result->fields[0] == $this->value) {
                      $val = $result->fields[1];
                      $text = new CHAW_text($val);
                      $form->add_text($text);
                  }

                  $result->MoveNext();
              }



          }
      }

      function process()
      {
          if (isset($_POST[$this->key])) {
              $this->value = $_POST[$this->key];

              if ($_POST["noproc_{$this->field}"] == 1) {
                  $this->form->noproc = true;

                  $stmt = "select {$this->fields_from} from {$this->table} where {$this->lbkey}='" . addslashes($this->value) . "'";

                  global $_CAMILA;

                  $_CAMILA['db']->SetFetchMode(ADODB_FETCH_NUM);
                  $result = $_CAMILA['db']->Execute($query);
                  if ($result === false)
                      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

                  $tok = strtok($this->fields_to, ',');

                  $i = 0;
                  while ($tok) {
                      $this->form->fields[$tok]->value = $result->fields[$i++];
                      $this->form->fields[$tok]->process = false;
                      $tok = strtok(',');
                  }
              }
          }
      }
  }
?>
