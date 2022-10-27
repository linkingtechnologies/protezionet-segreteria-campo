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




  class form_os3grid_csv_textarea extends form_field
  {
      var $init;
      var $filename;

      function form_os3grid_csv_textarea(&$form, $field, $title, $required = true, $init, $rows, $cols, $validation)
      {
          $this->form_field($form, $field, $title, $required, $validation);
          $this->cols = $cols;
          $this->rows = $rows;
          $this->init = $init;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          $grid_id = $this->key.'_os3grid';
          global $_CAMILA;
          $myText = new CHAW_text($this->title.$this->labelseparator);
          //$form->add_text($myText);
          $_CAMILA['page']->add_text($myText);
          $code = '<link type="text/css" rel="stylesheet" href="' . CAMILA_LIB_DIR . 'os3grid/os3grid.css" />'."\n";
          $code .= '<script type="text/javascript" src="' . CAMILA_LIB_DIR . 'os3grid/os3grid.js" type=\'text/javascript\'></script>';
          $code .= '<script type="text/javascript" src="' . CAMILA_DIR . 'js/csv.js" type=\'text/javascript\'></script>';
          $_CAMILA['page']->camila_add_js($code,'os3grid');

          $code = "<div class='camila_toolbar' id='toolbar_1'>";
          $cl = 'class="camilaButtonNormal" onmouseover="camila_switch_class(this,\'camilaButtonOver\');" onmouseout="camila_restore_class(this);" onmousedown="camila_restore_and_switch_class(this,\'camilaButtonDown\');"';
          $code.= "<a id=\"save\" href=\"javascript:camila_os3grid_save('".$this->key."_os3grid',true)\" onclick=\"camila_os3grid_save('".$this->key."_os3grid',true);return false;\" /><img id=\"os3grid_save\" $cl src=\"" . CAMILA_IMG_DIR ."png/disk.png\" border=\"0\"/></a>";
          $code.= "<img src=\"" . CAMILA_IMG_DIR ."png/spacer.png\" class=\"camilaSeparatorLine\" width=\"1\" height=\"15\">";
          $code.= "<a id=\"os3grid_newrow\" href=\"javascript:camila_os3grid_newrow()\" onclick=\"camila_os3grid_newrow();return false;\" /><img id=\"os3grid_newrow\" $cl src=\"" . CAMILA_IMG_DIR ."png/table_row_insert.png\" border=\"0\"/></a>";
          $code.= "<a id=\"os3grid_delrows\" href=\"javascript:camila_os3grid_delrows('$grid_id')\" onclick=\"camila_os3grid_delrows('$grid_id');return false;\" /><img id=\"os3grid_delrows\" $cl src=\"" . CAMILA_IMG_DIR ."png/table_row_delete.png\" border=\"0\"/></a>";
          $code.= "</div>";
          $js = new CHAW_js($code);
          //problems with IE
          //$form->add_userdefined($js);
          $_CAMILA['page']->add_userdefined($js);

          $code = "<div id='" . $this->key . "_os3grid'></div>";
          $code .= "<script type='text/javascript'>\nfunction " . $this->key . "_csv_init()\n{var g = new OS3Grid();\n";
          $code .= "camila_os3grid_init(g, '".$this->key."');\n";

          global $_CAMILA;
          $filename = CAMILA_TMP_DIR . '/csv_' . $_CAMILA['user_id'];// . camila_hash(CAMILA_FM_PREFIX);
          $this->filename = $filename;
          $f = fopen($filename, 'w');
          fwrite($f, $this->value);
          fclose($f);

          $csv = & new csv_bv($this->filename, ',', '"' , '\\');
          $csv->SkipEmptyRows(true);
          $csv->TrimFields(false);
          $header = $csv->NextLine();

          $row = '';
          $count = 0;
          $editable = '';
          $newrow .= "os3grid_get_grid('" . $this->key ."_os3grid').add_row(";
          foreach ($header as $key=>$value) {
              $row .= "'".str_replace("'", "\'", $value)."',";
              $editable .= "g.set_col_editable($count, 'txt');\n";
              $editable .= "g.set_click_cb ($count, 'camila_os3grid_col_click_cb(this,\'".$this->key."_os3grid\')' );\n";
              $newrow .= "'',";
              $count++;
          }
          $newrow = substr($newrow, 0, -1) . ");";

          if ($count>0) {

              $code2 = "<script type='text/javascript'>\n";
              $code2 .= 'function camila_os3grid_newrow(){' . $newrow . 'os3grid_get_grid(\'' . $this->key .'_os3grid\').render();}';
              $code2 .= "</script>\n";
              $_CAMILA['page']->camila_add_js($code2);

              $code .= "g.set_headers(" . substr($row, 0, -1) . ");\n";
              $code .= $editable;

              $count = 0;
              while ($arr_data = $csv->NextLine()){
                  $row = '';
                  $code .= "g.add_row(";
                  $count = 0;
                  foreach ($arr_data as $key=>$value) {
                      $row .= "'".str_replace("'", "\'", $value)."',";
                      $count++;
                  }
                  $code .= substr($row, 0, -1) . ");\n";
              }
          } else {
              $code2 = "<script  type='text/javascript'>\n";
              $code2 .= 'function camila_os3grid_newrow(){os3grid_get_grid(\'' . $this->key .'_os3grid\').add_row(\'\',\'\',\'\',\'\',\'\');os3grid_get_grid(\'' . $this->key .'_os3grid\').render();}';
              $code2 .= "</script>\n";
              $_CAMILA['page']->camila_add_js($code2);

              $code .= "g.set_headers('A','B','C','D','E');\n";
              $code .= "g.add_row('Doppio click per cambiare il valore','...','...','...','...');\n";
              $code .= "g.set_col_editable(0, 'txt');\n";
              $code .= "g.set_col_editable(1, 'txt');\n";
              $code .= "g.set_col_editable(2, 'txt');\n";
              $code .= "g.set_col_editable(3, 'txt');\n";
              $code .= "g.set_col_editable(4, 'txt');\n";
          }

          $code .= "g.render('". $this->key ."_os3grid');\n};";
          $code .= ' camila_addDOMLoadEvent('.$this->key."_csv_init);</script>";

          $js = new CHAW_js($code);
          //Problems with IE
          //$form->add_userdefined($js);
          $_CAMILA['page']->add_userdefined($js);
          $myHidden = new CHAW_hidden($this->key, $this->value);
          $form->add_hidden($myHidden);
      }

      function process()
      {
          if (isset($_POST[$this->key])) {
              $this->value = $_POST[$this->key];
          }

      }

      function validate()
      {
          if (!$this->updatable || strpos(strtolower($this->validation),'ignore')!==false)
              return true;

          parent::validate();

          if ($this->value != '' and $this->maxchars > 0)
              $this->form->validator->length($this->field, '<=', $this->maxchars);
      }
      
  }
?>
