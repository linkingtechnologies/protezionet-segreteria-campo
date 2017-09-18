<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2016 Umberto Bresciani

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


  class CAMILA_PDF_deck extends CHAW_deck {

      var $pdf;
      var $textpending = '';

      function create_page() {

          require_once(CAMILA_LIB_DIR . 'pdf-php/Cezpdf.php');

          if (isset($_REQUEST['camila_page_orient']) && $_REQUEST['camila_page_orient'] == "o")
              $this->pdf = new Cezpdf($_REQUEST['camila_page_dim'],'landscape');
          else
              $this->pdf = new Cezpdf($_REQUEST['camila_page_dim']);

          //$this->pdf->addInfo('Creator',value);
          //$this->pdf->addInfo('Title',value);
          //$this->pdf->addInfo('Author',value);
          //$this->pdf->addInfo('Subject',value);
          //$this->pdf->addInfo('Keywords',value);
          //$this->pdf->addInfo('Creator',value);
          //$this->pdf->addInfo('Producer',value);

          //$mainFont = CAMILA_LIB_DIR . 'pdf-php/fonts/Times-Roman.afm';
          //$this->pdf->selectFont($mainFont);

          $i = 0;
          while (isset($this->element[$i])) {
              $page_element = $this->element[$i];
              switch ($page_element->get_elementtype()) {
                  case HAW_FORM: {
                      $i = 0;
                      while (isset($page_element->element[$i])) {
                          $form_element = $page_element->element[$i];
                          switch ($form_element->get_elementtype()) {
                              //case HAW_IMAGE:
                              //case HAW_RADIO:
                              //case HAW_RULE:
                              case HAW_TABLE:
                              case HAW_INPUT:
                              case HAW_TEXTAREA: {
                                  $text = $form_element->label . ' ' . html_entity_decode($form_element->value);
                                  for ($ii = 0; $ii < $form_element->br; $ii++)
                                      $text .= "\n";
                                  $this->pdf_text(isUTF8($text) ? utf8_decode($text) : $text);
                                  break;
                              }

                              case HAW_SELECT: {
                                  foreach ($form_element->options as $key => $value) {
                                      if ($value['value'] == $form_element->value)
                                          $text = $value['label'];
                                  }

                                  $this->pdf_text(html_entity_decode($text));
                                  break;
                              }

                              case HAW_CHECKBOX: {
                                  if (!$form_element->is_checked())
                                      break;

                                  $text = html_entity_decode($form_element->label);

                                  $nl = 1;
                                  if ($form_element->br > 0)
                                      $nl = $form_element->br;
                                  for ($ii = 0; $ii < $nl; $ii++)
                                      $text .= "\n";
                                  $this->pdf_text(isUTF8($text) ? utf8_decode($text) : $text);
                                  break;
                              }

                              case HAW_PLAINTEXT: {
                                  $this->pdf_plaintext($form_element);
                                  break;
                              }
                          }

                          $i++;
                      }
                  }

                  case HAW_PLAINTEXT: {
                      $this->pdf_plaintext($this->element[$i]);
                      break;
                  }

                  case HAW_LINK: {
                      $link = $this->element[$i];

                      for ($ii = 0; $ii < $link->br; $ii++)
                          $suffix .= "\n";

                      $this->pdf_text(isUTF8($link->label) ? utf8_decode($link->label).$suffix : $link->label.$suffix);

                      break;
                  }

                  case HAW_TABLE: {
                      $this->pdf_plaintext('', true);
                      $table = $this->element[$i];
                      $cols = array();
                      $data = array();

                          $row = $table->row[0];
                          for ($b = 0; $b < $row->number_of_columns; $b++) {
                              $column = $row->column[$b];

                              if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                  $text = $column;
                                  $cols[$b] = isUTF8($text->text) ? utf8_decode($text->text) : $text->text;
                              }

                              if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                  $link = $column;
                                  $cols[$b] = isUTF8($link->label) ? utf8_decode($link->label) : $link->label;
                              }
                          }

                      for ($a = 1; $a < $table->number_of_rows; $a++) {
                          $row = $table->row[$a];
                          $r = Array();
                          for ($b = 0; $b < $row->number_of_columns; $b++) {
                              $column = $row->column[$b];
                              //$data[$row][$column] = '';
                              if (!is_array($column)) {

                                  if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                      $text = $column;
                                      $r[$b] = $this->pdf_html_from_text($text);
                                  }

                                  if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                      $link = $column;
                                      $r[$b] = $this->pdf_html_from_link($link);
                                  }

                              } else {
                                  foreach($column as $key=>$value) {

                                      if (is_object($value) && $value->get_elementtype() == HAW_PLAINTEXT) {
                                          $text = $value;
                                          $r[$b] .= $this->pdf_html_from_text($text);
                                          if (count($column) != $key+1)
                                              $r[$b] .= "\n";
                                      }

                                      if (is_object($value) && $value->get_elementtype() == HAW_LINK) {
                                          $link = $value;
                                          $r[$b] .= $this->pdf_html_from_link($link);
                                          if (count($column) != $key+1)
                                              $r[$b] .= "\n";

                                      }

                                  }

                              }

                          }
                          $data[]=$r;
                      }

                      $width = $this->pdf->ez['pageWidth']-$this->pdf->ez['leftMargin']-$this->pdf->ez['rightMargin'];
                      $options = Array('xPos'=>'left','xOrientation'=>'right','maxWidth'=>$width);
                      if ($table->camila_equal_columns) {
                          $cols_attr=Array();
                          foreach ($cols as $key=>$value) {
                              $cols_attr[$key]=Array('width'=>$width/count($cols));
                          }
                          $options = Array('cols'=>$cols_attr,'xPos'=>'left','xOrientation'=>'right','width'=>$width);
                      }
                      $this->pdf->ezTable($data,$cols,null,$options);
                      break;
                  }
              }
              $i++;
          }


          if ($_REQUEST['camila_export_action'] == '' || $_REQUEST['camila_export_action'] == 'download')
              $this->pdf->ezStream();
          elseif (!$this->camila_export_file_exists || $_REQUEST['camila_export_overwrite'] == 'y') {
              $pdfcode = $this->pdf->ezOutput();
              $fp=fopen($this->camila_export_get_dir() . $this->camila_export_filename(), 'wb');
              fwrite($fp, $pdfcode);
              fclose($fp);
          }

      }


      function pdf_plaintext($text, $forceflush=false) {
          $size = 2 * CAMILA_RTF_FONT_SIZE;
          $bigsize = 2 * CAMILA_RTF_FONT_BIG;
          $smallsize = 2 * CAMILA_RTF_FONT_SMALL;
          $prefix = '';
          $suffix = '';

          if ($text->attrib & HAW_TEXTFORMAT_SMALL) {
//              $prefix .= "\\fs" . $smallsize;
//              $suffix .= "\\fs" . $size;
          }

          if ($text->attrib & HAW_TEXTFORMAT_BIG) {
//              $prefix .= "\\fs" . $bigsize;
//              $suffix .= "\\fs" . $size;
          }

//          if ($text->attrib & HAW_TEXTFORMAT_BOLD) {
//              $prefix .= '<b>';
//          }

//          if ($text->attrib & HAW_TEXTFORMAT_ITALIC) {
//              $prefix .= '<i>';
//          }

//          if ($text->attrib & HAW_TEXTFORMAT_UNDERLINE) {
//              $prefix .= '<u>';
//          }

//          if ($text->attrib & HAW_TEXTFORMAT_UNDERLINE) {
//              $suffix .= '</u>';
//          }

//          if ($text->attrib & HAW_TEXTFORMAT_ITALIC) {
//              $suffix .= '</i>';
//          }

//          if ($text->attrib & HAW_TEXTFORMAT_BOLD) {
//              $suffix .= '</b>';
//          }

//          for ($ii = 1; $ii < $text->br; $ii++)
//              $suffix .= "\n";

            $this->textpending .= $this->pdf_html_from_text($text);
//          $this->textpending .= isUTF8($text->text) ? $prefix.utf8_decode($text->text).$suffix : $prefix.$text->text.$suffix;

          if ($text->br > 0 || $forceflush) {
              //$this->pdf->ezText(!isUTF8($this->textpending) ? utf8_decode($this->textpending) : $this->textpending);
              $this->pdf->ezText($this->textpending);

//              $this->pdf->ezText($this->textpending);
              $this->textpending = '';
          } else {
              $suffix .= "\n";
          }

      }

      function pdf_text($text) {
          $this->textpending .= $text;

      }

      function pdf_html_from_text($text) {

          $prefix = '';
          $suffix = '';

          if ($text->attrib & HAW_TEXTFORMAT_BOLD) {
              $prefix .= '<b>';
          }

          if ($text->attrib & HAW_TEXTFORMAT_ITALIC) {
              $prefix .= '<i>';
          }

          if ($text->attrib & HAW_TEXTFORMAT_UNDERLINE) {
              $prefix .= '<u>';
          }

          if ($text->attrib & HAW_TEXTFORMAT_UNDERLINE) {
              $suffix .= '</u>';
          }

          if ($text->attrib & HAW_TEXTFORMAT_ITALIC) {
              $suffix .= '</i>';
          }

          if ($text->attrib & HAW_TEXTFORMAT_BOLD) {
              $suffix .= '</b>';
          }

          for ($ii = 1; $ii < $text->br; $ii++)
              $suffix .= "\n";

          $html = isUTF8($text->text) ? $prefix.utf8_decode($text->text).$suffix : $prefix.$text->text.$suffix;

          return $html;

      }

      function pdf_html_from_link($link) {

          for ($ii = 1; $ii < $link->br; $ii++)
              $suffix .= "\n";

          $html = isUTF8($link->label) ? utf8_decode($link->label).$suffix : $link->label.$suffix;

          return $html;

      }

  }
?>
