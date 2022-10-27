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


  class CAMILA_INLINE_deck extends CHAW_deck
  {
      var $updating = false;

      function create_page() {
          $j = 0;
          $_array = Array();

          while (isset($this->element[$j])) {
              $page_element = $this->element[$j];
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
                              case HAW_INPUT: {
                                  $text = html_entity_decode($form_element->value);
                                  //for ($ii = 0; $ii < $form_element->br; $ii++)
                                  //    $text .= "\n";
                                  $_array['type'] = 'text';
                                  $_array['value'] = !isUTF8($text) ? utf8_encode($text) : $text;
                                  $_array['name'] = $form_element->name;
                                  $_array['size'] = $form_element->size;
                                  $_array['maxlength'] = $form_element->maxlength;
                                  break;
                              }

			      case HAW_TEXTAREA: {
                                  $text = html_entity_decode($form_element->value);
                                  //for ($ii = 0; $ii < $form_element->br; $ii++)
                                  //    $text .= "\n";
                                  $_array['type'] = 'textarea';
                                  $_array['value'] = !isUTF8($text) ? utf8_encode($text) : $text;
                                  $_array['name'] = $form_element->name;
                                  $_array['size'] = $form_element->size;
                                  $_array['maxlength'] = $form_element->maxlength;
                                  break;
                              }
                              case HAW_SELECT: {
                                  foreach ($form_element->options as $key => $value) {
                                      $val = !isUTF8($value['value']) ? utf8_encode($value['value']) : $value['value'];
                                      $label = !isUTF8($value['label']) ? utf8_encode($value['label']) : $value['label'];
                                      $_array['options'][$val] = $label;
                                  }

                                  $text = html_entity_decode($form_element->value);
                                  $_array['type'] = 'select';
                                  $_array['value'] = !isUTF8($text) ? utf8_encode($text) : $text;
                                  $_array['name'] = $form_element->name;
                                  //$_array['size'] = $form_element->size;
                                  //$_array['maxlength'] = $form_element->maxlength;
                                  break;
                              }

                              case HAW_HIDDEN: {
                                  $text = html_entity_decode($form_element->value);
                                  $_array[$form_element->name] = !isUTF8($text) ? utf8_encode($text) : $text;
                                  if (substr($form_element->name, strlen($form_element->name) - 13) == '_phpform_sent' && $_REQUEST[$form_element->name] == '1')
                                      $this->updating = true;
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
                                  //$this->pdf_text(isUTF8($text) ? utf8_decode($text) : $text);

                                  //$this->pdf->ezText(isUTF8($text) ? utf8_decode($text) : $text);
                                  break;
                              }

                              case HAW_PLAINTEXT: {
                                  break;
                              }
                          }

                          $i++;
                      }
                  }

              }
              $j++;
          }

          global $_CAMILA;

          if ($this->updating) {
              $_array['result'] = $_CAMILA['update_result'];
              $_array['error_desc'] = $_CAMILA['validation_errors'];
          } else {
              if ($_array['name'] == '')
                  $_array['result'] = 'KO';
              else
                  $_array['result'] = 'OK';

          }
          //$json = new Services_JSON();
          //echo $json->encode($_array);
		  echo json_encode($_array);
		  

      }
  }
?>