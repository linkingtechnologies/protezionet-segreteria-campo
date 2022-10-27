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


  class CAMILA_REST_deck extends CHAW_deck
  {

//http://www.gen-x-design.com/archives/create-a-rest-api-with-php/
      var $_gva_out = 'json';
      var $_gva_responseHandler = 'google.visualization.Query.setResponse';
      var $_gva_version = '0.5';
      var $_gva_reqId;
      var $_gva_status = 'ok';
      var $_gva_table;
      var $_gva_warnings;
      var $_gva_errors;
      var $_gva_sig;
      var $_gva_tqx;

      function create_page()
      {
          $tqx_pieces = explode(";", $_REQUEST['tqx']);
          foreach ($tqx_pieces as $p) {
              $arr = explode(":", $p);
              $this->_gva_tqx[$arr[0]] = $arr[1]; 
          };

          if ($this->_gva_tqx['reqId'] != '')
              $this->_gva_reqId = $this->_gva_tqx['reqId'];

          if ($this->_gva_tqx['out'] != '')
              $this->_gva_out = $this->_gva_tqx['out'];

          if ($this->_gva_tqx['responseHandler'] != '')
              $this->_gva_responseHandler = $this->_gva_tqx['responseHandler'];

          $i = 0;
          while (isset($this->element[$i])) {
              $page_element = $this->element[$i];
              switch ($page_element->get_elementtype()) {
                  case HAW_TABLE: {
                      $table = $this->element[$i];

                      switch ($this->_gva_out) {
                          case 'csv':
                              //header("Content-type: application/csv");
                              //header("Content-Disposition: filename=\"" . trim($this->title) . ".csv\"");

                              for ($a = 0; $a < $table->number_of_rows; $a++) {
                                  $row = $table->row[$a];
                                  for ($b = 0; $b < $row->number_of_columns; $b++) {
                                      $column = $row->column[$b];
                                      if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                          $text = "\"" . str_replace('"', '""', $column->get_text()) . "\"";
                                          echo isUTF8($text) ? $text : utf8_encode($text);
                                      }
                                      if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                          $text = "\"" . str_replace('"', '""', $column->get_label()) . "\"";
                                          echo isUTF8($text) ? $text : utf8_encode($text);
                                      }
                                      if ($b < $row->number_of_columns - 1)
                                          echo utf8_encode(",");
                                      else
                                          echo utf8_encode("\n");
                                  }
                              }

                              break;

                          case 'json':

                              echo $this->_gva_responseHandler."(";
                              $cols = array();

                              $a = 0;
                              $row = $table->row[$a];
                              for ($b = 0; $b < $row->number_of_columns; $b++) {
                                  $column = $row->column[$b];

                                  if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                      $text = $column->get_text();
                                      $text = isUTF8($text) ? $text : utf8_encode($text);
                                  }
                                  if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                      $text = $column->get_label();
                                      $text = isUTF8($text) ? $text : utf8_encode($text);
                                  }

                                  switch($column->metatype) {
                                      case 'I':
                                      case 'N':
                                          $cols[] = array('id' => $b, 'label' => $text, 'type' => 'number');
                                          break;

                                      default:
                                          $cols[] = array('id' => $b, 'label' => $text, 'type' => 'string');
                                  }
                              }

                              $rows = array();

                              for ($a = 1; $a < $table->number_of_rows; $a++) {
                                  $row = $table->row[$a];
                                  $r = array();
                                  for ($b = 0; $b < $row->number_of_columns; $b++) {
                                      $column = $row->column[$b];
                                      if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                          $text = $column->get_text();
                                          $text = isUTF8($text) ? $text : utf8_encode($text);
                                      }
                                      if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                          $text = $column->get_label();
                                          $text = isUTF8($text) ? $text : utf8_encode($text);
                                      }

                                      switch($column->metatype) {
                                          case 'I':
                                              $r[] = array('v' => (int)$text);
                                              break;

                                          default:
                                              $r[] = array('v' => $text);
                                      }

                                  }
                                  $rows[] = array('c' => $r);
                              }

                              $this->_gva_table = array('cols' => $cols, 'rows' => $rows);
                              $jarr = array('version'=>$this->_gva_version,'status'=>$this->_gva_status,'table'=>$this->_gva_table,'sig'=>md5(serialize($this->_gva_table)));

                              if ($this->_gva_reqId != '')
                                  $jarr['reqId'] = $this->_gva_reqId;

                              $json = new Services_JSON();
                              echo $json->encode($jarr);

                              echo ");";

                              break;
                      }
                      break;
                  }
              }
              $i++;
          }
      }

  }
?>