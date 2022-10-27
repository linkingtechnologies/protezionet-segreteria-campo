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



  class form_checklist extends form_field
  {

      var $labels;
      var $values;
      var $order;
      var $checked = 0;
      var $cols = 2;

      function form_checklist(&$form, $field, $title, $labels, $values, $required = false, $validation = false)
      {
          //new form_hidden ($form, $field.'_labels');
          for ($i=0; $i<count($labels); $i++)
              new form_hidden($form, $field.'_labels_'.$i, $labels[$i]);
          new form_hidden($form, $field.'_count', count($labels));

          $this->form_field($form, $field, $title, $required, $validation);
          $this->labels = $labels;
          $this->values = $values;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          $text = new CHAW_text($this->title . $this->labelseparator);
          $text->set_br(1);
          if (!empty($this->title))
              $form->add_text($text);

          if (count($this->labels) > 1) {
              $select_all = new CHAW_js('<div class="camilacheckall" id="camilacheckall"><a id="CheckAll_'.$this->key.'" href="">'.camila_get_translation('camila.form.checkall').'</a> '.camila_get_translation('camila.form.checkallseparator').' <a id="UncheckAll_'.$this->key.'" href="">'.camila_get_translation('camila.form.uncheckall').'</a></div>');
              $form->add_userdefined($select_all);
          }

          global $_CAMILA;
          
          $count = 0;

          foreach ($this->values as $key => $value) {
              $myHidden = new CHAW_hidden($this->key . '_labels_'.$count, $this->labels[$count]);
              $form->add_input($myHidden);
              
              if ($this->values[$count] == '') {
                  $myImage = new CHAW_image(CAMILA_IMG_DIR . 'wbmp/nocheck.wbmp', CAMILA_IMG_DIR . 'png/nocheck.png', '-');
                  $myImage->set_br(0);
                  $form->add_image($myImage);
                  $text = new CHAW_text($this->labels[$count]);
                  $form->add_text($text);
              } else {
                  if (in_array($fields[$count], $this->value)) {
                      $myCheckbox = new CHAW_checkbox($this->key . '_'.$count, $this->values[$count], $this->labels[$count], HAW_SELECTED);
                  } else
                      $myCheckbox = new CHAW_checkbox($this->key . '_'.$count, $this->values[$count], $this->labels[$count]);

                  if ((($count+1) % $this->cols) != 0 && count($this->labels) > 1)
                      $myCheckbox->set_br(0);
                  $form->add_checkbox($myCheckbox);
                  $this->checked++;
              }
              $count++;
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
