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



  class form_datetime extends form_field
  {
      var $calendar_theme_file = 'calendar-system.css';
      var $calendar_lang_file = 'lang/calendar-it.js';
      var $calendar_setup_file = 'calendar-setup.js';
      var $form;
      var $value_hour;
      var $value_minute;
      var $tDate;
      var $tFormat;

      var $hslots=12;

      function __construct(&$form, $field, $title, $required = false, $validation='')
      {
          parent::__construct($form, $field, $title, $required, $validation);
          $this->size = 12;
          $this->maxlength = 10;
          $form->add($this);
          $this->form = $form;
      }

      function draw(&$form)
      {
          parent::draw($form);

          global $_CAMILA;
          $this->tDate = '';
          $this->tFormat = '';
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
          //while (list($k, $v) = each($f)) {
		  foreach ($f as $k => $v) {
              $fmt.=$v;
              $fmt2.='%'.$v;
              $this->tFormat.=camila_get_translation('camila.dateformat.placeholder.'.$v);
              if ($count<2) {
                  $fmt.=camila_get_translation('camila.dateformat.separator');
                  $fmt2.=camila_get_translation('camila.dateformat.separator');
                  $this->tFormat.=camila_get_translation('camila.date.separator');
              }
              $count++;
          }
		  
		  //echo '('.$this->value.')';

          if ($this->value != '') {
              $this->tDate = $_CAMILA['db']->UserTimeStamp($this->value , $fmt);
              $this->value_hour = $_CAMILA['db']->UserTimeStamp($this->value , 'H');
              $this->value_minute = $_CAMILA['db']->UserTimeStamp($this->value , 'i');
          }

          if ($this->updatable && !isset($_REQUEST['camila_inline_field'])) {
              $myInput = new CHAW_input($this->key, $this->tDate, $this->title .' - Data ('.$this->tFormat.')'. $this->labelseparator);
              if ($this->maxlength > 0)
                  $myInput->set_maxlength($this->maxlength);
              if ($this->size > 0)
                  $myInput->set_size($this->size);
              $myInput->set_br(1);

              $form->add_input($myInput);

              $code  = ( '<link rel="stylesheet" type="text/css" media="all" href="' . CAMILA_LIB_DIR . 'jscalendar/' . $this->calendar_theme_file . '" />');
              $code .= ( '<script src=\'' . CAMILA_LIB_DIR . 'jscalendar/calendar.js\' type=\'text/javascript\'></script>' );
              $code .= ( '<script src=\'' . CAMILA_LIB_DIR . 'jscalendar/' . $this->calendar_lang_file .'\'  type=\'text/javascript\'></script>' );
              $code .= ( '<script src=\'' . CAMILA_LIB_DIR . 'jscalendar/' . $this->calendar_setup_file . '\' type=\'text/javascript\'></script>' );
              $_CAMILA['page']->camila_add_js($code,'jscalendar');

              $code = ( '<script type=\'text/javascript\'>xParent(\''.$this->key.'\',true).removeChild(xNextSib(\''.$this->key.'\',\'br\'))</script>' );
              $js = new CHAW_js($code);
              $form->add_userdefined($js);

              $popup = new CHAW_js("<a href=\"#\" id=\"f-calendar-trigger-" . $this->key . "\"><img src=\"" . CAMILA_LIB_DIR . "jscalendar/img.gif\" alt=\"\" align=\"middle\" border=\"0\"></a><script type=\"text/javascript\">Calendar.setup({\"ifFormat\":\"".$fmt2."\",\"daFormat\":\"".$fmt2."\",\"firstDay\":1,\"showsTime\":false,\"showOthers\":false,\"timeFormat\":12,\"inputField\":\"" . $this->key . "\",\"button\":\"f-calendar-trigger-" . $this->key . "\"});</script>");
              $form->add_userdefined($popup);

              $myText = new CHAW_text(' Ora'.$this->labelseparator);
              $myText->set_br(0);
              $form->add_text($myText);

              $tok = strtok('00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23', ',');
              while ($tok) {
                  $pos = strpos($tok, ';');
                  if ($pos === false)
                      $this->hour_options[] = array( $tok, $tok );
                  else
                      $this->hour_options[] = array( substr($tok, 0, $pos), substr($tok, $pos + 1) );
                  $tok = strtok (',');
              }

              $mySelect = new CHAW_select($this->key.'_hour');
              //while ($tok = each($this->hour_options) ) {
			  foreach ($this->hour_options as $key => $val) {
				$tok = [$key, $val];
                  $selected = ($tok[1][0] == $this->value_hour) ? HAW_SELECTED : HAW_NOTSELECTED;
                  $mySelect->add_option($tok[1][1], $tok[1][0], $selected);
              }

              $form->add_select($mySelect);
              $text = new CHAW_text(':');
              $text->set_br(0);
              $form->add_text($text);

              $this->minute_options=array();

              for ($i=0; $i<60; $i += (60/$this->hslots)) {
                  $this->minute_options[] = array( str_pad($i,2,'0',STR_PAD_LEFT), str_pad($i,2,'0',STR_PAD_LEFT) );
              }

              $mySelect = new CHAW_select($this->key.'_minute');
              //while( $tok = each($this->minute_options) ) {
			  foreach ($this->minute_options as $key => $val) {
				$tok = [$key, $val];
                  $selected = ($tok[1][0] == $this->value_minute) ? HAW_SELECTED : HAW_NOTSELECTED;
                  $mySelect->add_option($tok[1][1], $tok[1][0], $selected);
              }

              $form->add_select($mySelect);
              $text = new CHAW_text('');
              $form->add_text($text);

          } elseif (isset($_REQUEST['camila_inline_field'])) {

              $fVal = $this->tDate . ' '.$this->value_hour.camila_get_translation('camila.time.separator').$this->value_minute.camila_get_translation('camila.time.separator').'00';
              $mySelect = new CHAW_select($this->key);

              if ($this->value == '-' || $this->value == '')
                  $mySelect->add_option('-', '-', HAW_SELECTED);
              else {
                  $mySelect->add_option($fVal, $fVal, HAW_SELECTED);
                  $mySelect->add_option('-', '-', HAW_NOTSELECTED);
              }

              $now = $_CAMILA['db']->UserTimeStamp(date('Y-m-d H:i:s'), $fmt) . ' ' . date('H').camila_get_translation('camila.time.separator').date('i').camila_get_translation('camila.time.separator').'00';
              $fVal = $this->tDate . ' '.$this->value_hour.camila_get_translation('camila.time.separator').$this->value_minute.camila_get_translation('camila.time.separator').'00';
              if ($fVal == $now)
                  $mySelect->add_option($now, $now, HAW_SELECTED);
              else
                  $mySelect->add_option($now, $now, HAW_NOTSELECTED);

              $form->add_select($mySelect);


          } else {

              if ($this->value_hour != '00' || $this->value_minute != '00')
                  $myText = new CHAW_text($this->title . $this->labelseparator . ' ' . $this->tDate . ' - Ore '.$this->value_hour.camila_get_translation('camila.time.separator').$this->value_minute);
              else
                  $myText = new CHAW_text($this->title . $this->labelseparator . ' ' . $this->tDate);

              $form->add_text($myText);          
          }

      }

      function process()
      {

          if (isset($_REQUEST[$this->key]) && $_REQUEST[$this->key] != '-') {

              if (strlen($_REQUEST[$this->key]) == 10) {
                  $this->value = $_REQUEST[$this->key];

                  if (isset($_REQUEST[$this->key.'_hour']))
                      $this->value_hour = $_REQUEST[$this->key.'_hour'];

                  if (isset($_REQUEST[$this->key.'_minute']))
                      $this->value_minute = $_REQUEST[$this->key.'_minute'];
               } elseif (strlen($_REQUEST[$this->key]) >= 10) {
                  $this->value = substr($_REQUEST[$this->key], 0, 10);
                  $this->value_hour = substr($_REQUEST[$this->key], 11, 2);
                  $this->value_minute = substr($_REQUEST[$this->key], 14, 2);;
               }
          }
      }

      function validate()
      {
          global $_CAMILA;
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
          //while (list($k, $v) = each($f)) {
		  foreach ($f as $k => $v) {
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
                  $this->value = $_CAMILA['db']->BindTimeStamp($yyyy.'-'.$mm.'-'.$dd.' '.$this->value_hour.':'.$this->value_minute.':00');
              }
          }

          parent::validate();
      }
  }
?>
