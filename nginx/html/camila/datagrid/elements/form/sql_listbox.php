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




  class form_sql_listbox extends form_field
  {
      var $query;
      var $lbkey;
      var $lbvalue;

      function __construct(&$form, $field, $title, $query, $key, $value, $required = false, $validation = '')
      {
          if (strpos($query, 'where') !== false)
              $tables = explode(',', substr($query, strpos($query, 'from')+4, strpos($query, 'where') - 5 - strpos($query, 'from')));
          else
              $tables = explode(',', substr($query, strpos($query, 'from')+4));

          parent::__construct($form, $field, $title, $required, $validation);
          $this->query = $query;
          $this->lbkey = $key;
          $this->lbvalue = $value;
          $this->inline_field = trim($tables[0]) . '__' . $value;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          $text = new CHAW_text($this->title.$this->labelseparator);
          $text->set_br(0);
          if (!empty($this->title))
              $form->add_text($text);

          $mySelect = new CHAW_select($this->key);

          global $_CAMILA;

          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          $result = $_CAMILA['db']->Execute($this->query);
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $val = '';
          while (!$result->EOF) {
              if ($result->fields[$this->lbkey] == $this->value) {
                  $mySelect->add_option($result->fields[$this->lbvalue], $result->fields[$this->lbkey], HAW_SELECTED);
                  $val = $result->fields[$this->lbvalue];
              }
              else
                  $mySelect->add_option($result->fields[$this->lbvalue], $result->fields[$this->lbkey]);
              $result->MoveNext();
          }

          if ($this->updatable) {
              $form->add_select($mySelect);
              $text = new CHAW_text('');
              $form->add_text($text);
          } else {
              $text = new CHAW_text($val);
              $form->add_text($text);
          }
      }

      function process()
      {
          if (isset($_REQUEST[$this->key])) {
              $this->value = $_REQUEST[$this->key];
          }
      }
  }
?>
