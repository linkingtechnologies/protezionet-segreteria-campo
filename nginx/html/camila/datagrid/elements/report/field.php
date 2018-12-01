<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2010 Umberto Bresciani

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


class report_field {

  var $field;
  var $title;
  var $type;
  var $value;
  var $row;
  var $print;     // will this field be shown in the report?
  var $onprint;   // function onfield($value) - called before printing the field
  var $metatype;
  var $dummy = false;
  var $report;
  var $inline = true;
  var $orderable = true;



  function report_field($field, $title)
  {
    $this->field = $field;
    $this->title = $title;
    $this->value = '';
    $this->print = true;
  }

  function draw_header(&$row, $link)
  {

    global $_CAMILA;
    $_CAMILA['page']->header_cols_count++;
    if ( ($_CAMILA['page']->camila_exporting() ) || isset($this->onprint) || !$this->orderable)
    {
      if ($this->title == 'camilakey_id') {
          $text = new CHAW_image(CAMILA_IMG_DIR.'wbmp/wrench_orange.wbmp', CAMILA_IMG_DIR.'png/spacer.png', 'v');
          //$text = new CHAW_text('X');
          $text->set_id('camilatablemenu');
      }
      elseif (substr($this->title, 0, strlen('camilakey_')) == 'camilakey_' || substr($this->title, 0, strlen('camila_')) == 'camila_')
          $text = new CHAW_text('');
      else
          $text = new CHAW_text($this->title);

      $text->metatype = $this->metatype;
      $text->field = $this->field;

      $row->add_column($text);
    }
    else
    {
      $myLink = new CHAW_link($this->title, $link);
      $myLink->set_br(0);

      $orderby = $this->report->orderby;
      //if (strpos($this->report->orderby, '.') !== false)
      //    $orderby = substr($this->report->orderby, 0, strpos($this->report->orderby, '.')) . '__' . substr($this->report->orderby, strpos($this->report->orderby, '.') + 1);

      $ar = Array();
      $ar[0]=$myLink;
      if (/*isset($_REQUEST['f0']) && $_REQUEST['f0']*/ $orderby == $this->field) {
          if ($_REQUEST['d']=='1' || $this->report->direction == 'desc')
              $image = new CHAW_image(CAMILA_IMG_DIR.'wbmp/icon_sort_up.wbmp', CAMILA_IMG_DIR.'png/icon_sort_up.png', '^');
          else
              $image = new CHAW_image(CAMILA_IMG_DIR.'wbmp/icon_sort_down.wbmp', CAMILA_IMG_DIR.'png/icon_sort_down.png', 'v');
          $image->set_br(0);
          $ar[0]=$myLink;
          $ar[1]=$image;
      } else {
          $ar[0]=$myLink;
      }

      $row->add_column($ar);
    }
  }

  function draw(&$row, &$fields)
  {

      if ( isset($this->onprint) ) {
          $func = $this->onprint;
          $func($this, $row, $fields);
      }
      else
      {

        if (strpos($this->value, 'http') == 0) {
            if ($this->is_valid_URL($this->value)) {
                $link = new CHAW_link($this->value, $this->value);
                $row->add_column($link);
	        $this->inline = false;
                return;
	    }
        }

        //if (strpos($this->value, 'a href') == 1) {
        //    $c1 = preg_match_all('/(href=")(.*?)(")/i', $this->value, $patterns);
        //    $c2 = preg_match_all("/(<a.*>)(\w.*)(<.*>)/ismU", $this->value, $patterns2);
        //    $link = new CHAW_link($patterns2[2][0], $patterns[2][0]);
        //    $row->add_column($link);
	//    $this->inline = false;
        //    return;
        //}
            //STATIC LISTBOX FORM ELEMENT IN A DB FORM
            global $_CAMILA;


            if ($this->report->fields[$this->field]->metatype == 'C' && $this->value != '' && isset ($_CAMILA['options'][$this->field][$this->value]))
                $this->value = $_CAMILA['options'][$this->field][$this->value];

            //SQLITE
			
			//echo $this->value;

            if ($this->report->fields[$this->field]->metatype == 'D' && $this->value != '')
                $text = new CHAW_text($_CAMILA['db']->UserDate($this->value , $_CAMILA['date_format']));
            elseif ($this->report->fields[$this->field]->metatype == 'T' && $this->value != '') {
                $text = new CHAW_text($_CAMILA['db']->UserTimeStamp($this->value , $_CAMILA['date_format'].' H:i:s'));
                $this->inline = true;
            }
            elseif(strlen($this->value)==19 && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/", $this->value)) {
                $text = new CHAW_text($_CAMILA['db']->UserTimeStamp($this->value , $_CAMILA['date_format'].' H:i:s'));
                $this->report->fields[$this->field]->metatype = 'T';
                //$this->inline = false;
            }
            elseif(strlen($this->value)==10 && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/", $this->value)) {
                $text = new CHAW_text($_CAMILA['db']->UserDate($this->value , $_CAMILA['date_format']));
                $this->report->fields[$this->field]->metatype = 'D';
            }
            else
                $text = new CHAW_text($this->value);

        $text->metatype = $this->metatype;

        if (!in_array($this->field, $this->report->keys) && $this->report->inline_editing) {
            $text->set_css_class('cf_editText');
        }

        $text->set_br(0);

        if ($this->inline && $this->report->inline_editing) {
            $id = $this->field;

            foreach($this->report->keys as $k=>$v) {
                  $v = str_replace('.', '__', $v);
                  $id .= '__cf__'.$fields['camilakey_'.$v]->value;
            }
            $text->set_id($id);
        }

        $row->add_column($text);

      }
  }

  function print_value()
  {
      global $_CAMILA;
      if(!isset($this->onprint) && $this->value!='') {
        $text = new CHAW_text($this->value);
        $_CAMILA['page']->add_text($text);
      }
  }

  function print_title()
  {
    global $_CAMILA;
    $text = new CHAW_text($this->title.': ', HAW_TEXTFORMAT_BOLD);
    $text->set_br(0);
    if ($this->value != '')
      $_CAMILA['page']->add_text($text);
  }

  function draw_title(&$row)
  {
    $text = new CHAW_text($this->title);
    $row->add_column($text);
  }

  function is_valid_URL($url)
  {
     return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
  } 

}
?>