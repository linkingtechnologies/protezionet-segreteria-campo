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


  class CAMILA_RTF_deck extends CHAW_deck {

      var $rtf;

      function create_page() {

          require_once(CAMILA_DIR . 'export/rtf/rtf.php');

          $this->rtf = new rtf();
          if (isset($_REQUEST['camila_page_dim']) && $_REQUEST['camila_page_dim'] == "a3")
              $this->rtf->setPaperSize(4);
          else
              $this->rtf->setPaperSize(5);
          if (isset($_REQUEST['camila_page_orient']) && $_REQUEST['camila_page_orient'] == "o")
              $this->rtf->setPaperOrientation(2);
          else
              $this->rtf->setPaperOrientation(1);

          $this->rtf->setDefaultFontFace(1);
          $this->rtf->setDefaultFontSize(2 * CAMILA_RTF_FONT_SIZE);
          $this->rtf->setAuthor('');
          $this->rtf->setOperator('');
          $this->rtf->setTitle('');
          $this->rtf->addColour("#000000");

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
                                  $text = $form_element->label;
                                  $prefix = '';
                                  $suffix = '';

                                  $this->rtf->addText(isUTF8($text) ? utf8_decode($text) : $text, $prefix, $suffix);

                                  $text = html_entity_decode($form_element->value);
                                  for ($ii = 0; $ii < $form_element->br; $ii++)
                                      $suffix .= "\\par ";

                                  $this->rtf->addText(isUTF8($text) ? utf8_decode($text) : $text, $prefix, $suffix);
                                  break;
                              }

                              case HAW_SELECT: {
                                  $prefix = '';
                                  $suffix = '';

                                  foreach ($form_element->options as $key => $value) {
                                      if ($value['value'] == $form_element->value)
                                          $text = $value['label'];
                                  }

                                  $text = html_entity_decode($text);

                                  $this->rtf->addText(isUTF8($text) ? utf8_decode($text) : $text, $prefix, $suffix);

                                  break;
                              }

                              case HAW_CHECKBOX: {
                                  if (!$form_element->is_checked())
                                      break;

                                  $prefix = '';
                                  $suffix = '';
                                  $nl = 1;
                                  if ($form_element->br > 0)
                                      $nl = $form_element->br;
                                  for ($ii = 0; $ii < $nl; $ii++)
                                      $suffix .= "\\par ";

                                  $text = html_entity_decode($form_element->label);

                                  $this->rtf->addText(isUTF8($text) ? utf8_decode($text) : $text, $prefix, $suffix);
                                  break;
                              }

                              case HAW_PLAINTEXT: {
                                  $this->rtf_plaintext($form_element);
                                  break;
                              }
                          }

                          $i++;
                      }
                  }


                  case HAW_PLAINTEXT: {
                      $this->rtf_plaintext($this->element[$i]);
                      break;
                  }

                  case HAW_LINK: {
                      $link = $this->element[$i];
                      $suffix = '';

                      for ($ii = 0; $ii < $link->br; $ii++)
                          $suffix .= "\\par ";

                      $this->rtf->addText(isUTF8($link->label) ? utf8_decode($link->label) : $link->label, '', $suffix);

                      break;
                  }

                  case HAW_TABLE: {
                      $table = $this->element[$i];
                      $data = array();

                      for ($a = 0; $a < $table->number_of_rows; $a++) {
                          $row = $table->row[$a];
                          for ($b = 0; $b < $row->number_of_columns; $b++) {
                              $column = $row->column[$b];
                              $data[$row][$column] = "";
                              
                              if (!is_array($column)) {
                                  if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                      $text = $column;
                                      $data[$a][$b] = $this->rtf_formatting_from_text($text);
                                  }

                                  if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                      $link = $column;
                                      $data[$a][$b] = $this->rtf_formatting_from_link($link);
                                  }
                              } else {

                                  foreach($column as $key=>$value) {

                                      if (is_object($value) && $value->get_elementtype() == HAW_PLAINTEXT) {
                                          $text = $value;
                                          $data[$a][$b] .= $this->rtf_formatting_from_text($text);
                                      }

                                      if (is_object($value) && $value->get_elementtype() == HAW_LINK) {
                                          $link = $value;
                                          $data[$a][$b] .= $this->rtf_formatting_from_link($link);
                                      }

                                  }

                              }
                          }
                      }
                      $this->rtf->addTable($data);
                      break;
                  }
              }
              $i++;
          }

          if ($_REQUEST['camila_export_action'] != '' && $_REQUEST['camila_export_action'] != 'download') {
              if (!$this->camila_export_file_exists || $_REQUEST['camila_export_overwrite'] == 'y')
                  $this->rtf->getDocument($this->camila_export_filename(), $this->camila_export_get_dir());
          } else
              $this->rtf->getDocument($this->camila_export_safe_filename() . '.' . $this->camila_export_get_ext());
      }


      function rtf_plaintext($text) {
          $size = 2 * CAMILA_RTF_FONT_SIZE;
          $bigsize = 2 * CAMILA_RTF_FONT_BIG;
          $smallsize = 2 * CAMILA_RTF_FONT_SMALL;
          $prefix = '';
          $suffix = '';

          if ($text->attrib & HAW_TEXTFORMAT_SMALL) {
              $prefix .= "\\fs" . $smallsize;
              $suffix .= "\\fs" . $size;
          }

          if ($text->attrib & HAW_TEXTFORMAT_BIG) {
              $prefix .= "\\fs" . $bigsize;
              $suffix .= "\\fs" . $size;
          }

          if ($text->attrib & HAW_TEXTFORMAT_BOLD) {
              $prefix .= "\\b ";
              $suffix .= "\\b0 ";
          }

          if ($text->attrib & HAW_TEXTFORMAT_ITALIC) {
              $prefix .= "\\i ";
              $suffix .= "\\i0 ";
          }

          if ($text->attrib & HAW_TEXTFORMAT_UNDERLINE) {
              $prefix .= "\\ul ";
              $suffix .= "\\ul0 ";
          }

          for ($ii = 0; $ii < $text->br; $ii++)
              $suffix .= "\\par ";

          $this->rtf->addText(isUTF8($text->text) ? utf8_decode($text->text) : $text->text, $prefix, $suffix);
      }

      function rtf_formatting_from_text($text) {
          $size = 2 * CAMILA_RTF_FONT_SIZE;
          $bigsize = 2 * CAMILA_RTF_FONT_BIG;
          $smallsize = 2 * CAMILA_RTF_FONT_SMALL;
          $prefix = '';
          $suffix = '';

          if ($text->attrib & HAW_TEXTFORMAT_SMALL) {
              $prefix .= "\\fs" . $smallsize;
              $suffix .= "\\fs" . $size;
          }

          if ($text->attrib & HAW_TEXTFORMAT_BIG) {
              $prefix .= "\\fs" . $bigsize;
              $suffix .= "\\fs" . $size;
          }

          if ($text->attrib & HAW_TEXTFORMAT_BOLD) {
              $prefix .= "\\b ";
              $suffix .= "\\b0 ";
          }

          if ($text->attrib & HAW_TEXTFORMAT_ITALIC) {
              $prefix .= "\\i ";
              $suffix .= "\\i0 ";
          }

          if ($text->attrib & HAW_TEXTFORMAT_UNDERLINE) {
              $prefix .= "\\ul ";
              $suffix .= "\\ul0 ";
          }

          for ($ii = 0; $ii < $text->br; $ii++)
              $suffix .= "\\par ";

          $code = isUTF8($text->text) ? $prefix . utf8_decode($text->text) . $suffix : $prefix . $text->text . $suffix;
          return $code;

      }

      function rtf_formatting_from_link($link) {

          $suffix = '';

          for ($ii = 0; $ii < $link->br; $ii++)
              $suffix .= "\\par ";

          $code = isUTF8($link->label) ? utf8_decode($link->label).$suffix : $link->label.$suffix;

          return $code;

      }


  }
?>
