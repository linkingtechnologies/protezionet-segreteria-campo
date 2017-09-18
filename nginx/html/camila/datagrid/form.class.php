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


  require_once(CAMILA_DIR.'datagrid/validator/Validator.php');
  require_once(CAMILA_DIR.'datagrid/elements/form/field.php');


  class phpform
  {
      var $name;
      var $action;
      var $fields;
      var $noproc;
      var $hasdblink = false;
      var $form;
      var $validator;
      var $deletebutton;
      var $submitbutton;
      var $drawrules = true;
      var $method;
      var $preservecontext = false;
      var $drawheadersubmitbutton = false;


      function phpform($name, $action = '', $method=HAW_METHOD_POST)
      {
          $this->method = $method;
          $this->fields = array();
          $this->name = $name;

          if ($action == '')
              $action = basename($_SERVER['PHP_SELF']);

          $this->action = $action;

          $this->submitbutton = camila_get_translation('camila.submitbutton');
          $this->deletebutton = camila_get_translation('camila.deletebutton');

          global $_CAMILA;
          $_CAMILA['datagrid_form'] = $this;
      }

      function add(&$field)
      {
          if ( (is_array($this->keys) && in_array($field->field, $this->keys)) || !isset($_REQUEST['camila_inline_field']) || (isset($_REQUEST['camila_inline_field']) && ($_REQUEST['camila_inline_field'] == $field->field || $_REQUEST['camila_inline_field'] == $field->inline_field)))
              $this->fields[$field->field] = $field;
      }

      function draw_submit($button_text)
      {
          $theSubmission = new CHAW_submit($button_text, 'submit_button');
		  
		  $theSubmission->set_css_class('btn btn-md btn-primary');
          $this->form->add_submit($theSubmission);
      }

      function draw_delete($button_text)
      {
          $theSubmission = new CHAW_submit($button_text, 'submit_delete');
		  $theSubmission->set_css_class('btn btn-danger');
          $this->form->add_submit($theSubmission);
      }

      function draw_header()
      {
          $this->form = new CHAW_form($this->action, $this->method);
		  $this->form->set_css_class('form-group');

          if ($this->preservecontext) {
              $export_format = '';
              foreach ($_REQUEST as $key => $value) {
                  if (!is_array($value)) {
                      $myInput = new CHAW_hidden($key, $value);
                      if ($key == 'camila_print' || $key == 'camila_rtf' || $key == 'camila_pdf' || $key == 'camila_pagnum' || $key == 'camila_xml2pdf')
                          $export_format = $key;
                      else
                          $this->form->add_input($myInput);
                  } else {
                      foreach ($value as $k => $v) {
                          $myInput = new CHAW_hidden($key.'[' . $k . ']', $v);
                          $this->form->add_input($myInput);
                      }
                  }
              }
          }

          $myHidden = new CHAW_hidden($this->name . '_phpform_sent', '1');
          $this->form->add_hidden($myHidden);
          if (isset($_REQUEST['dashboard'])) {
               $myHidden = new CHAW_hidden('dashboard', $_REQUEST['dashboard']);
               $this->form->add_hidden($myHidden);
          }
		  if (isset($_REQUEST['camila_popup'])) {
               $myHidden = new CHAW_hidden('camila_popup', $_REQUEST['camila_popup']);
               $this->form->add_hidden($myHidden);
          }
          if (isset($_REQUEST['camila_inline_field'])) {
              $myHidden = new CHAW_hidden('camila_inline_field', $_REQUEST['camila_inline_field']);
              $this->form->add_hidden($myHidden);
          }

          if ($this->drawheadersubmitbutton) {
			  
			  //Nascondo tasto se non posso aggiornare
			  if (!$this->canupdate && $this->mode == 'update')
				  return;

              if ($this->mode == 'insert') {
                  $js = new CHAW_js('<button class="btn btn-md btn-primary btn-space" type="submit" name="submitandnew_button_header" id="hawinputsubmitandnewheader">'.camila_get_translation('camila.insertandnewbutton').'</button>');
                  $this->form->add_userdefined($js);
              }

              if (isset($_REQUEST['camila_delete']) && $this->mode != 'insert')
                  $js = new CHAW_js('<button class="btn btn-md btn-primary btn-space" type="submit" name="submit_button_header" id="hawinputsubmitheader">'.camila_get_translation('camila.deletebutton').'</button><br /><br />');
              else
                  $js = new CHAW_js('<button class="btn btn-md btn-primary btn-space" type="submit" name="submit_button_header" id="hawinputsubmitheader">'.camila_get_translation('camila.' . $this->mode . 'button').'</button><br /><br />');

              $this->form->add_userdefined($js);

          }

      }

      function draw_footer()
      {
          global $_CAMILA;
          $_CAMILA['page']->add_form($this->form);
      }

      function draw_errors()
      {
          $errors = $this->validator->getErrors();
          $errtext = camila_get_translation('camila.formfielderror')."\n";

          foreach ($errors as $key => $value) {

          if (!is_array($value)) {
              if (strstr($key, '|')) {
                  $key = str_replace('|', camila_get_translation('camila.and') . ' ', $key);
              }
              $errtext .= camila_get_translation('camila.preformfielderror');
              $errtext .= isUTF8($this->fields[$key]->title) ? $this->fields[$key]->title . camila_get_translation('camila.postformfielderror') : utf8_encode($this->fields[$key]->title) . camila_get_translation('camila.postformfielderror');
              $errtext .= ' ' . camila_get_translation('camila.formvalidationerror' . $value);
              $errtext .= "\n";
          } else {
              foreach ($value as $key2 => $value2) {
                  if (strstr($key2, '|')) {
                      $key2 = str_replace('|', camila_get_translation('camila.and') . ' ', $key2);
                  }
                  $errtext .= camila_get_translation('camila.preformfielderror');
                  $errtext .= $this->fields[$key]->title . camila_get_translation('camila.postformfielderror');
                  $errtext .= ' ' . camila_get_translation('camila.formvalidationerror' . $value2);
                  $errtext .= "\n";
              }
          }
          }

          global $_CAMILA;
          $_CAMILA['validation_errors'] = $errtext;
          camila_error_text($errtext);
      }

      function add_hidden($name, $value)
      {
          $myHidden = new CHAW_hidden($name, $value);
          $this->form->add_hidden($myHidden);
      }

      function draw($drawSubmit=true)
      {
          $this->draw_header();
          reset($this->fields);
          $myRule = new CHAW_rule('100%');

          while ($field = each($this->fields)) {

	          if (!(!$field[1]->updatable  && $_REQUEST[$this->table.'_phpform_sent'] == '1' && count($this->validator->getErrors()) == 0) || $field[1]->forcedraw) {

                  $field[1]->draw($this->form);
                  if (!$field[1]->hidden && $this->drawrules)
                      $this->form->add_rule($myRule);
              }
          }

		  //$this->submitbutton->set_br(0);
		  if ($drawSubmit)
			  $this->draw_submit($this->submitbutton);
          $this->draw_footer();
      }

      function process($force=false)
      {
          $req = array();

          if (!$force && !isset($_REQUEST[$this->name.'_phpform_sent']))
              return false;

          $this->noproc = false;

          reset($this->fields);
          while ($field = each($this->fields)) {
              if ($field[1]->field != '' && !(substr(trim($field[1]->field), 0, strlen('camilafield_')) == 'camilafield_')) {
                  $this->fields[$field[1]->field]->process();
                  if ($this->fields[$field[1]->field]->field != '')
                      $req[$this->fields[$field[1]->field]->field] = $this->fields[$field[1]->field]->value;
              }
          }

          $this->validator = new Validator($req);
          reset($this->fields);
          while ($field = each($this->fields)) {
              if ($field[1]->field != '') {
                  $this->fields[$field[1]->field]->validate();

                  if ($this->fields[$field[1]->field]->write_value_to_file != '') {
                      $file = fopen ($this->fields[$field[1]->field]->write_value_to_file, 'w'); 
                      fwrite($file, $this->fields[$field[1]->field]->value); 
                      fclose($file);
                  }

              }
          }

          if (count($this->validator->getErrors()) > 0 && $_REQUEST[$this->name.'_sess_mode'] != 'delete') {
              $this->noproc = true;
              $this->draw_errors();
          }

          if ($this->noproc)
              return false;
          else
              return true;
      }

      function clear()
      {
          reset($this->fields);
          while ($field = each($this->fields)) {
              if ($this->fields[$field[1]->field]->cleanable)
                  $this->fields[$field[1]->field]->value = '';
          }

          reset($this->fields);
          while ($field = each($this->fields)) {
              if ($this->fields[$field[1]->field]->defaultvalue != '')
                  $this->fields[$field[1]->field]->value = $this->fields[$field[1]->field]->defaultvalue;
          }
      }
  }
?>