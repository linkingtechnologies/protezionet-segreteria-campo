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



  class form_textarea extends form_field
  {
      var $maxlength;

      function form_textarea(&$form, $field, $title, $required = false, $rows=30, $cols=80, $maxlength = null, $validation = '')
      {
          $this->form_field($form, $field, $title, $required, $validation);
          $this->cols = $cols;
          $this->rows = $rows;
          $this->maxlength = $maxlength;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);

          if ($this->updatable) {

              if ($this->maxlength > 0) {
                  global $_CAMILA;
                  $code = '<script type="text/javascript" src="' . CAMILA_LIB_DIR . 'ylib/ylib.js"></script>';
                  $code .= '<script type="text/javascript" src="' . CAMILA_LIB_DIR . 'ylib/y_util.js"></script>';
                  $code .= '<script type="text/javascript" src="' . CAMILA_LIB_DIR . 'ylib/y_TextCounter.js"></script>';
                  $_CAMILA['page']->camila_add_js($code,'textcounter');
                  $code = "<script> camila_addDOMLoadEvent ( function(){";
                  $code .= ' var camila_counter = new ylib.widget.TextCounter("'.$this->key.'", '.$this->maxlength.', "'.camila_get_translation('camila.form.presmscharleft').' ","'.camila_get_translation('camila.form.postsmscharleft').'");';
                  $code .= "} );</script>";
                  $_CAMILA['page']->camila_add_js($code);
                  $code = "<script>document.write('<div>');</script>";
                  $js = new CHAW_js($code);
                  $form->add_userdefined($js);
              }

              $myTextarea = new CHAW_textarea($this->key, $this->value, $this->title . $this->labelseparator, $this->rows, $this->cols);
			  //$myTextarea->set_css_class($this->css_class);
              $form->add_textarea($myTextarea);

              if ($this->maxlength > 0) {
                  $js = new CHAW_js("<script>document.write('</div>')</script>");
                  $form->add_userdefined($js);
              }
          } else {
              $myText = new CHAW_text($this->title.$this->labelseparator.' '.$this->value);
              $form->add_text($myText);	
          }
      }

      function process()
      {
          if (isset($_REQUEST[$this->key])) {
              $this->value = $_REQUEST[$this->key];
          }

      }

      function validate()
      {
          if (!$this->updatable || strpos(strtolower($this->validation), 'ignore') !== false)
              return true;

          parent::validate();

          if ($this->value != '' and $this->maxlength > 0)
              $this->form->validator->length($this->field, '<=', $this->maxlength);
      }

  }
?>
