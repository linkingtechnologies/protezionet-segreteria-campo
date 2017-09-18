<?php
  require_once(CAMILA_DIR.'datagrid/elements/form/field.php');

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


  class form_date extends form_field
  {
      var $calendar_theme_file = 'calendar-system.css';
      var $calendar_lang_file = 'lang/calendar-it.js';
      var $calendar_setup_file = 'calendar-setup.js';
      var $form;

      function form_date(&$form, $field, $title, $required = false, $validation)
      {
          $this->form_field($form, $field, $title, $required, $validation);
          $this->size = 12;
          $this->maxlength = 10;
          $form->add($this);
          $this->form = $form;
      }

      function draw(&$form)
      {
          parent::draw($form);

          global $_CAMILA;
          $tDate = '';
          $tFormat = '';
          $fmt = '';
          $fmt2 = '';
          $f = Array();
          $m = camila_get_translation('camila.dateformat.monthpos');
          $d = camila_get_translation('camila.dateformat.daypos');
          $y = camila_get_translation('camila.dateformat.yearpos');
          $f[$m] = 'm';
          $f[$d] = 'd';
          $f[$y] = 'Y';
          ksort($f);
          reset($f);
          $count = 0;
          while (list($k, $v) = each($f)) {
              $fmt.=$v;
              $fmt2.='%'.$v;
              $tFormat.=camila_get_translation('camila.dateformat.placeholder.'.$v);
              if ($count<2) {
                  $fmt.=camila_get_translation('camila.dateformat.separator');
                  $fmt2.=camila_get_translation('camila.dateformat.separator');
                  $tFormat.=camila_get_translation('camila.date.separator');
              }
              $count++;
          }

          if ($this->value!='' && $this->value!='0000-00-00') {
              $tDate = $_CAMILA['db']->UserDate($this->value , $fmt);
          }
          else
              $this->value = '';

        if ($this->updatable) {

            $myInput = new CHAW_input($this->key, $tDate, $this->title.' ('.$tFormat.')'.$this->labelseparator);
            if ($this->maxlength > 0)
              $myInput->set_maxlength($this->maxlength);
            if ($this->size > 0)
                $myInput->set_size($this->size);
            $myInput->set_br(1);

            $form->add_input($myInput);

            global $_CAMILA;
            $code  = ( '<link rel="stylesheet" type="text/css" media="all" href="' .CAMILA_LIB_DIR .'jscalendar/' . $this->calendar_theme_file . '" />');
            $code .= ( '<script src=\''.CAMILA_LIB_DIR.'jscalendar/calendar.js\'></script>' );
            $code .= ( '<script src=\''.CAMILA_LIB_DIR.'jscalendar/' . $this->calendar_lang_file .'\'></script>' );
            $code .= ( '<script src=\''.CAMILA_LIB_DIR.'jscalendar/' . $this->calendar_setup_file . '\'></script>' );
            $_CAMILA['page']->camila_add_js($code,'jscalendar');

            $code = ( '<script>xParent(\''.$this->key.'\',true).removeChild(xNextSib(\''.$this->key.'\',\'br\'))</script>' );
            $js = new CHAW_js($code);
            $form->add_userdefined($js);

            $popup = new CHAW_js("<a href=\"#\" id=\"f-calendar-trigger-" . $this->key . "\"><img src=\"".CAMILA_LIB_DIR."jscalendar/img.gif\" alt=\"\" align=\"middle\" border=\"0\"></a><script type=\"text/javascript\">Calendar.setup({\"ifFormat\":\"".$fmt2."\",\"daFormat\":\"".$fmt2."\",\"firstDay\":1,\"showsTime\":false,\"showOthers\":false,\"timeFormat\":12,\"inputField\":\"" . $this->key . "\",\"button\":\"f-calendar-trigger-" . $this->key . "\"});</script>");
            $form->add_userdefined($popup);
        } else {
              $myText = new CHAW_text($this->title.$this->labelseparator.' '.$tDate);
              $form->add_text($myText);	
        }

      }

      function process()
      {
          if (isset($_REQUEST[$this->key]))
              $this->value = $_REQUEST[$this->key];
      }

      function validate()
      {
          $fmt = '';
          $f = Array();
          $m = camila_get_translation('camila.dateformat.monthpos');
          $d = camila_get_translation('camila.dateformat.daypos');
          $y = camila_get_translation('camila.dateformat.yearpos');
          $f[$m] = 'mm';
          $f[$d] = 'dd';
          $f[$y] = 'yyyy';
          ksort($f);
          reset($f);
          $count = 0;
          while (list($k, $v) = each($f)) {
              $fmt.=$v;
              if ($count<2) {
                  $fmt.=camila_get_translation('camila.dateformat.separator');
              }
              $count++;
          }

          if ($this->value != '') {
              if ($this->form->validator->date($this->field, $fmt)) {
                  $mm = substr($this->value, camila_get_translation('camila.dateformat.monthpos'), 2);
                  $dd = substr($this->value, camila_get_translation('camila.dateformat.daypos'), 2);
                  $yyyy = substr($this->value, camila_get_translation('camila.dateformat.yearpos'), 4);
                  $this->value = date('Y-m-d', mktime(0,0,0,$mm,$dd,$yyyy));
              }
          }

          parent::validate();
      }
  }
?>
