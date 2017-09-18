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

  
  
  class form_db_checklist extends form_field
  {
      var $query;
      var $key_field;
      var $value_field;
      var $order;
      var $checked = 0;
      var $values;
      var $labels;

      function form_db_checklist(&$form, $field, $title, $query, $label_field, $value_field, $required, $validation = false)
      {

          global $_CAMILA;

          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          $result = $_CAMILA['db']->Execute($query);

          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $this->values = Array();
          $this->labels = Array();

          $i=0;         
          while (!$result->EOF) {
              new form_hidden ($form, $field.'_labels_'.$i, $result->fields[$label_field]);
              $this->labels[$i] = $result->fields[$label_field];
              $this->values[$i] = $result->fields[$value_field];
              $i++;
              $result->MoveNext();
          }

          new form_hidden ($form, $field.'_count', $i);

//          for ($i=0; $i<$result->RecordCount(); $i++)
//              new form_hidden ( $form, $field.'_labels_'.$i );

          $this->form_field($form, $field, $title, $required, $validation);
          $this->query = $query;
          $this->label_field = $label_field;
          $this->value_field = $value_field;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          $text = new CHAW_text($this->title . $this->labelseparator);
          $text->set_br(1);
          if (!empty($this->title))
              $form->add_text($text);

          $select_all = new CHAW_js('<div class="camilacheckall" id="camilacheckall"><a id="CheckAll_'.$this->key.'" href="">'.camila_get_translation('camila.form.checkall').'</a> '.camila_get_translation('camila.form.checkallseparator').' <a id="UncheckAll_'.$this->key.'" href="">'.camila_get_translation('camila.form.uncheckall').'</a></div>');
          $form->add_userdefined($select_all);

          global $_CAMILA;

          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          $result = $_CAMILA['db']->Execute($this->query);

          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $count = 0;

//          while (!$result->EOF) {
            for ($i=0; $i<count($this->values); $i++) {
//              $myHidden = new CHAW_hidden($this->key . '_labels_'.$count, $result->fields[$this->label_field]);
//              $form->add_input($myHidden);

              if ($this->values[$i] == '') {
                  $myImage = new CHAW_image(CAMILA_IMG_DIR . 'wbmp/nocheck.wbmp', CAMILA_IMG_DIR . 'png/nocheck.png', '-');
                  $myImage->set_br(0);
                  $form->add_image($myImage);
                  $text = new CHAW_text($this->labels[$i]);
                  $form->add_text($text);
              } else {
                  if (in_array($this->values[$i], $this->value)) {
                      $myCheckbox = new CHAW_checkbox($this->key . '_'.$i, $this->values[$i], $this->labels[$i], HAW_SELECTED);
                  } else
                      $myCheckbox = new CHAW_checkbox($this->key . '_'.$i, $this->values[$i], $this->labels[$i]);

                  $form->add_checkbox($myCheckbox);
                  $this->checked++;
              }
              $count++;
//              $result->MoveNext();
          }

//          $myHidden = new CHAW_hidden($this->key . '_count', $count);
//          $form->add_input($myHidden);

      }

      function process()
      {
//          if (isset($_POST[$this->key])) {
//              $this->value = $_POST[$this->key];
//              $newarr=Array();

//              foreach ($_POST[$this->key] as $key => $value) {
//                  if ($value != '') {
//                      $this->checked++;
//                  }
//              }

//          }

          if (isset($_REQUEST[$this->key . '_count'])) {
              $newarr=Array();

              for ($i=0; $i < $_REQUEST[$this->key . '_count']; $i++) {

                  $value = $_REQUEST[$this->key . '_' . $i];
                  $newarr[$i] = $value;
                      
                  if ($value != '') {
                      $this->checked++;
                  }
              }

              $this->value = $newarr;
              
          }

      }

      function validate()
      {
          if ($this->required && $this->checked == 0) {
              $this->form->validator->setError($this->field, 903);
          }
      }
  }
?>
