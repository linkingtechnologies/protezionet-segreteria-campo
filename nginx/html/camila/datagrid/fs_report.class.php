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


  require_once(CAMILA_DIR . 'datagrid/elements/report/field.php');
  require_once(CAMILA_DIR . 'datagrid/elements/report/string.php');
  require_once(CAMILA_DIR . 'datagrid/elements/report/byte.php');
  require_once(CAMILA_DIR . 'datagrid/elements/report/timestamp.php');
  require_once(CAMILA_DIR . 'datagrid/elements/report/icon.php');

  require_once(CAMILA_DIR . 'fs.class.php');

  function camila_formdownloadfile(&$field, &$row, $fields)
  {
      global $_CAMILA;      
      if ($field->report->extfsenabled && $field->value == '')
          $myLink = new CHAW_link(substr($field->title, 0, -CAMILA_FM_PREFIX), basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_download=' . urlencode($fields['name']->title));
      elseif ($field->report->extfsenabled) {
          if ($field->value == 'up')
              $myLink = new CHAW_link($field->title, basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_dir=' . $field->report->dir_id . '&camila_up=yes');
          else
              $myLink = new CHAW_link($field->title, basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_dir=' . $fields['name']->value);
      } else
          $myLink = new CHAW_link($field->title, basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_download=' . $field->title);
      $myLink->set_br(0);
      $row->add_column($myLink);
  }

  function camila_formdeletefile(&$field, &$row, $fields)
  {
      global $_CAMILA;
      if ($fields['name']->value == 'up') {
          $myText = new CHAW_text('');
          $myText->set_br(0);
          $row->add_column($myText);
      } elseif ($fields['name']->value) {
          $arr = array('dir' => $fields['name']->value);
          $myLink = new CHAW_link(camila_get_translation('camila.delete'), basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_delete=' . serialize($arr) . '&camila_returl=' . urlencode($_CAMILA['returl']) . '&camila_token=' . camila_token(serialize($arr)));
          $myLink->set_br(0);
          $row->add_column($myLink);
      } else {
          $arr = array('name' => $fields['name']->title);
          $myLink = new CHAW_link(camila_get_translation('camila.delete'), basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_delete=' . serialize($arr) . '&camila_returl=' . urlencode($_CAMILA['returl']) . '&camila_token=' . camila_token(serialize($arr)));
          $myLink->set_br(0);
          $row->add_column($myLink);
      }
  }

  function camila_formmoveitem(&$field, &$row, $fields)
  {
      global $_CAMILA;
      if ($fields['name']->value == 'up') {
          $myText = new CHAW_text('');
          $myText->set_br(0);
          $row->add_column($myText);
      } elseif ($fields['name']->value) {
          $arr = array('dir' => $fields['name']->value);
          $myLink = new CHAW_link(camila_get_translation('camila.move'), basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_move=' . serialize($arr) . '&camila_returl=' . urlencode($_CAMILA['returl']) . '&camila_token=' . camila_token(serialize($arr)));
          $myLink->set_br(0);
          $row->add_column($myLink);
      } else {
          $arr = array('name' => $fields['name']->title);
          $myLink = new CHAW_link(camila_get_translation('camila.move'), basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_move=' . serialize($arr) . '&camila_returl=' . urlencode($_CAMILA['returl']) . '&camila_token=' . camila_token(serialize($arr)));
          $myLink->set_br(0);
          $row->add_column($myLink);
      }
  }

  function camila_formupdatefile(&$field, &$row, $fields)
  {
      global $_CAMILA;
      if (!$field->report->extfsenabled)
          $filename = $fields['name']->title;
      else
          $filename = substr($fields['name']->title, 0, -CAMILA_FM_PREFIX);

      if ($fields['name']->value == '' && in_array(substr($filename, strrpos($filename, '.') + 1), $field->report->editabletypes)) {
          $arr = array('name' => $fields['name']->title);
          $myLink = new CHAW_link(camila_get_translation('camila.edit'), basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_update=' . serialize($arr) . '&camila_returl=' . urlencode($_CAMILA['returl']) . '&camila_token=' . camila_token(serialize($arr)));
          $myLink->set_br(0);
          $row->add_column($myLink);
      } else {
          $myText = new CHAW_text('');
          $myText->set_br(0);
          $row->add_column($myText);
      }
  }

  class fs_report extends fs
  {
      var $stmt;
      //report_field classes
      var $fields;
      // rows per page
      var $rows = CAMILA_REPORT_RPP;
      // db limit offset
      var $offset;
      // current page viewed
      var $page;
      //$result->FieldCount();
      //var $numfields;
      //$result->RecordCount();
      var $totalrows;
      var $title;
      var $condition = '';
      var $condappurl;
      var $additional_links;
      var $filternum = 0;
      var $keys;
      var $caninsert;
      var $canupdate;
      var $candelete;
      var $canmove;

      var $drawfilterbox = false;
      var $drawnavigationbox = true;
      var $filter;
      var $currentdir;
      var $file_list;
      var $filetype_list;
      var $totalfilesize;
      var $editabletypes;
      var $dir;
      var $totalfiles;
      var $dir_id;
      var $is_root_dir = true;

      function fs_report($basedir, $extfsenabled = false, $usergroup, $title, $orderby = 'name', $direction = 'asc', $canupdate = true, $candelete = true, $caninsert = true, $canmove = true)
      {
          $this->filter = $filter;
          $this->canupdate = $canupdate;
          $this->candelete = $candelete;
          $this->caninsert = $caninsert;
          $this->canmove = $canmove;

          if (!$extfsenabled)
              $this->canmove = false;

          $this->extfsenabled = $extfsenabled;
          $this->usergroup = $usergroup;

          if (!((isset($_REQUEST['camila_w1f'])) || (isset($_REQUEST['count']) && $_REQUEST['count'] > 0) || (isset($_REQUEST['camila_count']) && $_REQUEST['camila_count'] > 0))) {
              foreach ($this->filter as $k => $v) {
                  $_REQUEST[$k] = $v;
              }
          }

          if ($extfsenabled)
              $this->dir = $basedir . '/' . $usergroup;
          else
              $this->dir = $basedir;

          global $_CAMILA;

          if ($_CAMILA['user_preferences']['c_rp'] > 0)
              $this->rows = $_CAMILA['user_preferences']['c_rp'];
          
          $this->stmt = $basedir;

          $this->currentdir = $basedir;
          $this->title = $title;
          $this->rowsloaded = 0;
          $this->totalrows = 0;
          $this->offset = 0;
          $this->page = 1;
          $this->orderby = $orderby;
          $this->direction = $direction;

          if ($this->caninsert) {
              $this->additional_links[camila_get_translation('camila.fm.uploadfile')] = camila_append_query_string('camila_upload=y');
              if ($this->extfsenabled)
                  $this->additional_links[camila_get_translation('camila.fm.newdir')] = camila_append_query_string('camila_update=newdir');
          }
          if ($this->caninsert && $this->canupdate)
              $this->additional_links[camila_get_translation('camila.fm.newfile')] = camila_append_query_string('camila_update=new');

          $this->editabletypes = array('htm', 'txt', 'csv', 'xml');
          if ($mapping != '')
              $this->mapping = $this->mappingseparator . $mapping . $this->mappingseparator;

          $this->order_mapping = $ordermapping;

          $this->table = new CHAW_table();

          if (isset($_REQUEST['d'])) {
              if ($_REQUEST['d'] == 0)
                  $this->direction = 'asc';
              else
                  $this->direction = 'desc';
          }

          if (isset($_REQUEST['rpp']))
              $this->rows = $_REQUEST['rpp'];

          if (strpos($_CAMILA['page_url'], '?') !== false)
              $this->urlappend = substr($_CAMILA['page_url'],strpos($_CAMILA['page_url'],'?')) . '&count=-1';
          else
              $this->urlappend = '?count=-1';

          if ((isset($_REQUEST['f0']) && $_REQUEST['f0'] != '') || (isset($_REQUEST['camila_f0']) && $_REQUEST['camila_f0'] != '')) {
              if (isset($_REQUEST['f0']))
                  $oby = $_REQUEST['f0'];
              else
                  $oby = $_REQUEST['camila_f0'];
              $this->orderby = $oby;
              
              if ((array_key_exists($oby, $this->order_mapping)))
                  $this->orderby = /*$_CAMILA['db']->Quote(*/$this->order_mapping[$oby]/*)*/;
          }

          $this->urlappend .= $this->condappurl;

          if (isset($_REQUEST['rpp']))
              $this->urlappend .= '&rpp=' . $_REQUEST['rpp'];

          if (isset($_REQUEST['camila_addfilter']))
              $this->filternum += $_REQUEST['camila_addfilter'];

          if (isset($_REQUEST['custom']))
              $this->urlappend .= '&custom=' . urlencode($_REQUEST['custom']);

          if (isset($_REQUEST['camila_custom']))
              $this->urlappend .= '&camila_custom=' . urlencode($_REQUEST['camila_custom']);

          if (isset($_REQUEST['camila_dir']))
              $this->urlappend .= '&camila_dir=' . urlencode($_REQUEST['camila_dir']);

          $this->urlappendnoorder = $this->urlappend;

          if (isset($_REQUEST['d']))
              $this->urlappend .= '&d=' . $_REQUEST['d'];

          if ((isset($_REQUEST['f0']) && $_REQUEST['f0'] != '') || (isset($_REQUEST['camila_f0']) && $_REQUEST['camila_f0'] != ''))
              $this->urlappend .= '&camila_f0=' . $this->orderby;

          $_CAMILA['returl'] = $this->urlappend;
          if (isset($_REQUEST['camila_pagnum']))
              $_CAMILA['returl'] .= '&camila_pagnum=' . intval($_REQUEST['camila_pagnum']);
      }

      function process()
      {
          if (isset($_REQUEST['camila_download']) && $_REQUEST['camila_download'] != '') {
              $this->_download($_REQUEST['camila_download']);
              exit();
          }

          if (isset($_REQUEST['camila_update']) || isset($_REQUEST['camila_delete']) || isset($_REQUEST['camila_move'])) {
              if (isset($_REQUEST['camila_update'])) {
                  $this->keyvalue = unserialize(stripslashes($_REQUEST['camila_update']));
                  $check = camila_token($_REQUEST['camila_update']);
              } elseif (isset($_REQUEST['camila_move'])) {
                  $this->keyvalue = unserialize(stripslashes($_REQUEST['camila_move']));
                  $check = camila_token($_REQUEST['camila_move']);
              } else {
                  $this->keyvalue = unserialize(stripslashes($_REQUEST['camila_delete']));
                  $check = camila_token($_REQUEST['camila_delete']);
              }
              
              if ($check != $_REQUEST['camila_token'] && $_REQUEST['camila_update'] != 'new' && $_REQUEST['camila_update'] != 'newdir')
                  camila_error_page(camila_get_translation('camila.pageforbidden'));
          }

          if (!$this->drawnavigationbox) {
              $this->rows = -1;
              $this->page = 1;
          }

          if (isset($_REQUEST['camila_pagnum'])) {
              $this->page = intval($_REQUEST['camila_pagnum']);
              if ($this->page < 0) {
                  $this->rows = -1;
                  $this->page = 1;
              }
              $this->offset = ($this->page - 1) * $this->rows;
          }

          //$this->numfields = 5;

          $this->fields['icon'] = new report_icon('icon', '');
          $this->fields['icon']->report = $this;

          $this->fields['name'] = new report_string('name', camila_get_translation('camila.fm.nameheader'));
          $this->fields['name']->report = $this;

          $this->fields['size'] = new report_byte('size', camila_get_translation('camila.fm.sizeheader'));
          $this->fields['size']->report = $this;

          $this->fields['type'] = new report_string('type', camila_get_translation('camila.fm.typeheader'));
          $this->fields['type']->report = $this;

          $this->fields['mod'] = new report_timestamp('mod', camila_get_translation('camila.fm.lastmodifyheader'));
          $this->fields['mod']->report = $this;

          if ($this->canupdate) {
              $this->fields['camila_update'] = new report_string('camila_update', '');
              $this->fields['camila_update']->report = $this;
          }

          if ($this->canmove) {
              $this->fields['camila_move'] = new report_string('camila_move', '');
              $this->fields['camila_move']->report = $this;
          }

          if ($this->candelete) {
              $this->fields['camila_delete'] = new report_string('camila_delete', '');
              $this->fields['camila_delete']->report = $this;
          }

          $myArr = array();
          
          if (isset($_REQUEST['camila_dir']))
              $this->is_root_dir = false;

          if ($this->extfsenabled) {
              if (isset($_REQUEST['camila_dir']) && $_REQUEST['camila_dir'] != '') {
                  if ($_REQUEST['camila_up'] == 'yes') {
                      $ancnode = $this->_tree_get_ancestor_node($this->_tree_get_node_by_id($_REQUEST['camila_dir']));
                      if ($ancnode['usergroup'] == '') {
                          $this->is_root_dir = true;
                          $myArr = $this->_tree_get_file_subtree($this->_tree_get_group_root_node($this->usergroup), array('name'), $this->usergroup);
                      } else {
                          $this->dir_id = $ancnode['id'];
                          $myArr = $this->_tree_get_file_subtree($ancnode, array('name'), $this->usergroup);
                      }
                  } else {
                      $this->dir_id = $_REQUEST['camila_dir'];
                      $myArr = $this->_tree_get_file_subtree($this->_tree_get_node_by_id($_REQUEST['camila_dir']), array('name'), $this->usergroup);
                  }
              } else
                  $myArr = $this->_tree_get_file_subtree($this->_tree_get_group_root_node($this->usergroup), array('name'), $this->usergroup);
          } else {
              $count = 0;
              if (is_dir($this->dir)) {
                  if ($dh = opendir($this->dir)) {
                      while (($file = readdir($dh)) !== false) {
                          if ($file != '.' && $file != '..' /*&& substr($file,-3) == 'csv'*/)
                              $myArr[$count] = $file;
                          $count++;
                      }
                      closedir($dh);
                  }
              }

          }

          $tot_file_size = 0;
          $num_items = 0;
          foreach (array_keys($myArr) as $k) {
              $new_item = $myArr[$k];

              if ($new_item != '..') {
                  //$abs_new_item = get_abs_item($usergroup, $new_item);
                  $abs_new_item = $this->dir . '/' . $new_item;

                  $new_file_size = filesize($abs_new_item);
                  $tot_file_size += $new_file_size;
                  $num_items++;

                  if (!@file_exists($abs_new_item)) {
                      if ($_REQUEST['f0'] == 'mod') {
                          $dir_list[$new_item] = 0;
                          //@filemtime($abs_new_item);
                      } else {
                          // order == "size", "type" or "name"
                          $dir_list[$new_item] = $new_item;
                      }
                      $this->filetype_list[$new_item] = $k;
                  } else {
                      if ($_REQUEST['f0'] == 'size') {
                          $file_list[$new_item] = $new_file_size;
                      } elseif ($_REQUEST['f0'] == 'mod') {
                          $file_list[$new_item] = @filemtime($abs_new_item);
                      } elseif ($_REQUEST['f0'] == 'type') {
                          $file_list[$new_item] = $this->_getmimetype($abs_new_item);
                      } else {
                          // order == 'name'
                          $file_list[$new_item] = $new_item;
                      }
                  }
              }
          }

          if ($this->extfsenabled && isset($_REQUEST['camila_dir']) && $_REQUEST['camila_dir'] != 'up')
              $this->totalrows = $num_items + 1;
          else
              $this->totalrows = $num_items;

          $this->totalfilesize = $tot_file_size;
          $this->totalfiles = $num_items;

          if (is_array($dir_list) && $this->extfsenabled) {
              if ($_REQUEST['f0'] == 'mod') {
                  if ($this->direction == 'asc')
                      arsort($dir_list);
                  else
                      asort($dir_list);
              } else {
                  if ($this->direction == 'asc')
                      ksort($dir_list);
                  else
                      krsort($dir_list);
              }
          }

          if (is_array($file_list)) {
              if ($_REQUEST['f0'] == 'mod') {
                  if ($this->direction == 'asc')
                      arsort($file_list);
                  else
                      asort($file_list);
              } elseif ($_REQUEST['f0'] == 'size' || $_REQUEST['f0'] == 'type') {
                  if ($this->direction == 'asc')
                      asort($file_list);
                  else
                      arsort($file_list);
              } else {
                  if ($this->direction == 'asc')
                      ksort($file_list);
                  else
                      krsort($file_list);
              }
          }

          if ($this->extfsenabled) {
              if (is_array($file_list) && is_array($dir_list))
                  $this->file_list = array_slice($dir_list + $file_list, ($this->page - 1) * $this->rows, ($this->rows == -1) ? null : $this->rows);
              elseif (is_array($dir_list))
                  $this->file_list = array_slice($dir_list, ($this->page - 1) * $this->rows, ($this->rows == -1) ? null : $this->rows);
              else
                  $this->file_list = array_slice($file_list, ($this->page - 1) * $this->rows, ($this->rows == -1) ? null : $this->rows);
          } else
              $this->file_list = array_slice($file_list, ($this->page - 1) * $this->rows, ($this->rows == -1) ? null : $this->rows);

          if ($this->extfsenabled && !$this->is_root_dir) {
              if (is_array($this->file_list))
                  $this->file_list = array('..' => $_REQUEST['camila_dir']) + $this->file_list;
              else
                  $this->file_list = array('..' => $_REQUEST['camila_dir']);
          }

      }

      function draw_header()
      {
          global $_CAMILA;

          if ($this->title != '') {
              $text = new CHAW_text($this->title, HAW_TEXTFORMAT_BIG);
              $text->set_br(2);
              $_CAMILA['page']->add_text($text);
          }

          $myRow = new CHAW_row();

          reset($this->fields);
          $noprint = 0;
          while ($fld = each($this->fields)) {
              if (!$fld[1]->print )
                  $noprint++;
          }

          //$this->numfields -= $noprint;

          $orderby = $this->orderby;

          $count = 0;
          reset($this->fields);
          while ($fld = each($this->fields)) {
              if ($fld[1]->print && !($_CAMILA['page']->camila_exporting() && $fld[1]->dummy) && !($_CAMILA['page']->camila_exporting() && !(strpos($fld[1]->field, 'camilakey_') === false))) {
                  if (($this->direction == 'asc') && ($fld[1]->field == $orderby))
                      $fld[1]->draw_header($myRow, basename($_SERVER['PHP_SELF']) . $this->urlappendnoorder . '&d=1&f0=' . urlencode($fld[1]->field));
                  else
                      $fld[1]->draw_header($myRow, basename($_SERVER['PHP_SELF']) . $this->urlappendnoorder . '&d=0&f0=' . urlencode($fld[1]->field));
              }
              $count++;
          }

          $this->table->add_row($myRow);
      }

      function load_row(&$row, $line)
      {
          if ((list($item, ) = each($this->file_list))) {
              if ($item == '..') {
                  $this->fields['name']->title = 'Cartella sup.';
                  $this->fields['name']->value = 'up';
                  $this->fields['type']->value = '-';
                  $this->fields['icon']->value = $this->_getmimetype_icon('up');
                  $this->fields['mod']->value = '-';
              } else {
                  $this->fields['name']->title = $item;
                  $this->fields['name']->value = $this->filetype_list[$item];
                  $this->fields['size']->value = @filesize($this->dir . '/' . $item);

                  if ($this->extfsenabled) {
                      if ($this->fields['name']->value == '') {
                          $this->fields['type']->value = $this->_getmimetype(strtolower(substr($item, 0, -CAMILA_FM_PREFIX)));
                          $this->fields['icon']->value = $this->_getmimetype_icon(strtolower(substr($item, 0, -CAMILA_FM_PREFIX)));
                          $this->fields['icon']->imagepath = $this->_getmimetype_icon(strtolower(substr($item, 0, -CAMILA_FM_PREFIX)));

                      } else {
                          $this->fields['type']->value = camila_get_translation('camila.fm.folder');
                          $this->fields['icon']->value = $this->_getmimetype_icon('..');
                          $this->fields['icon']->imagepath = $this->_getmimetype_icon('..');

                      }
                  } else {
                      $this->fields['type']->value = $this->_getmimetype(strtolower($item));
                      $this->fields['icon']->value = $this->_getmimetype_icon(strtolower($item));
                      $this->fields['icon']->imagepath = $this->_getmimetype_icon(strtolower($item));
                  }
                  if ($this->fields['name']->value == '')
                      $this->fields['mod']->value = @filemtime($this->dir . '/' . $item);
                  else
                      $this->fields['mod']->value = '-';
              }

              $this->fields['name']->onprint = 'camila_formdownloadfile';

              if ($this->canupdate)
                  $this->fields['camila_update']->onprint = 'camila_formupdatefile';

              if ($this->canmove)
                  $this->fields['camila_move']->onprint = 'camila_formmoveitem';

              if ($this->candelete)
                  $this->fields['camila_delete']->onprint = 'camila_formdeletefile';

              ++$this->rowsloaded;
              return true;
          } else
              return false;
      }

      function draw_row()
      {
          global $_CAMILA;
          $myRow = new CHAW_row();
          
          reset($this->fields);
          while ($fld = each($this->fields)) {
              if ($fld[1]->print && !($_CAMILA['page']->camila_exporting() && $fld[1]->dummy) && !($_CAMILA['page']->camila_exporting() && (!(strpos($fld[1]->field, 'camilakey_') === false)))) {
                  $fld[1]->draw($myRow, $this->fields);
              }
          }
          $this->table->add_row($myRow);
      }

      function draw()
      {

          if (isset($_REQUEST['camila_upload']) && $_REQUEST['camila_upload'] == 'y') {
              if (!$this->_upload())
                  return;
              else
                  $this->process();
          }

          if (isset($_REQUEST['camila_delete']) && $_REQUEST['camila_delete'] != '') {
              if (!$this->_delete())
                  return;
              else
                  $this->process();
          }

          if (isset($_REQUEST['camila_move']) && $_REQUEST['camila_move'] != '') {
              if (!$this->_move())
                  return;
          }

          if (isset($_REQUEST['camila_update']) && $_REQUEST['camila_update'] != '') {
              $ret = false;

              if ($_REQUEST['camila_update'] == 'new')
                  $ret = $this->_new();
              elseif ($_REQUEST['camila_update'] == 'newdir')
                  $ret = $this->_newdir();
              else
                  $this->_update();

              if (!$ret)
                  return;
              $this->process();
          }

          //$this->draw_filter();

          if ($this->totalfiles>0) {
              $row = array();
              $this->draw_header();
              while ($this->load_row($row, $i++)) {
                  if (isset($this->onloadrow)) {
                      $func = $this->onloadrow;
                      $ok = $func($this);
                  } else
                      $ok = true;
                  if ($ok) {
                      $this->draw_row();
                  }
              }

              $this->draw_footer();

              $text = new CHAW_text('');

              global $_CAMILA;
              $_CAMILA['page']->add_text($text);

              $_CAMILA['page']->add_table($this->table);

              $text = new CHAW_text('');
              $_CAMILA['page']->add_text($text);
              $this->draw_footer();

          } else {
              global $_CAMILA;
              camila_information_text(camila_get_translation('camila.fm.nofiles'));

              if (isset($this->additional_links))
              foreach ($this->additional_links as $key => $value) {
                  $text = new CHAW_text(' | ');
                  $text->set_br(0);
                  $_CAMILA['page']->add_text($text);
                  $link = new CHAW_link($key, $value);
                  $link->set_br(0);
                  $_CAMILA['page']->add_link($link);
              }

              $text = new CHAW_text('');
              $_CAMILA['page']->add_text($text);
          }

      }

      function draw_footer()
      {
          if (!$this->drawnavigationbox)
              return;

          global $_CAMILA;

          if ($this->rows > 0 && !$_CAMILA['page']->camila_exporting()) {
              if ($this->page > 2) {
                  $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.first'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=1');
                  $myLink->set_br(0);
                  $_CAMILA['page']->add_link($myLink);
                  
                  $text = new CHAW_text(' | ');
                  $text->set_br(0);
                  $_CAMILA['page']->add_text($text);
              }

              if ($this->page > 1) {
                  $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.prev'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ($this->page - 1));
                  $myLink->set_br(0);
                  $_CAMILA['page']->add_link($myLink);
              }

              $text = new CHAW_text(' ' . camila_get_translation('camila.report.navbox.page') . ' ' . $this->page . '/' . ceil($this->totalrows / $this->rows) . ' ');
              $text->set_br(0);
              $_CAMILA['page']->add_text($text);

              if ($this->page < ceil($this->totalrows / $this->rows)) {
                  $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.next'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ($this->page + 1));
                  $myLink->set_br(0);
                  $_CAMILA['page']->add_link($myLink);
              }

              if ($this->page < (ceil($this->totalrows / $this->rows) - 1)) {
                  $text = new CHAW_text(' | ');
                  $text->set_br(0);
                  $_CAMILA['page']->add_text($text);
                  
                  $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.last'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ceil($this->totalrows / $this->rows));
                  $myLink->set_br(0);
                  $_CAMILA['page']->add_link($myLink);
              }

              $text = new CHAW_text(' | ');
              $text->set_br(0);
              $_CAMILA['page']->add_text($text);

              $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.allpages').' (' . $this->totalfiles . ' ' . camila_get_translation('camila.report.navbox.files') . ' ' .$this->_convertfilesize($this->totalfilesize) . ' ' . camila_get_translation('camila.report.navbox.occupied').')', basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=-1');
              $myLink->set_br(0);
              if ($this->page > 0)
                  $_CAMILA['page']->add_link($myLink);
          }

          if (!$_CAMILA['page']->camila_exporting()) {

              if (isset($this->additional_links))
                  foreach ($this->additional_links as $key => $value) {
                      $text = new CHAW_text(' | ');
                      $text->set_br(0);
                      $_CAMILA['page']->add_text($text);
                      $link = new CHAW_link($key, $value);
                      $link->set_br(0);
                      $_CAMILA['page']->add_link($link);
                  }

              $text = new CHAW_text('');
              $_CAMILA['page']->add_text($text);
          }
      }

      function draw_filter()
      {
          if (!$this->drawfilterbox)
              return;
      }

      function _upload()
      {
          $this->_tree_checkup();

          require_once(CAMILA_DIR . 'datagrid/form.class.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/filebox.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');

          $form = new phpform('camila');
          $form->submitbutton = camila_get_translation('camila.fm.uploadfile');
          $form->drawrules = false;
          new form_hidden($form, 'upload', 'y');

          global $_CAMILA;
          $pos = strrpos($_CAMILA['page_url'], '?');
          if ($pos !== false)
              new form_hidden($form, substr($_CAMILA['page_url'], $pos + 1));

          if ($this->extfsenabled)
              new form_filebox($form, 'file', camila_get_translation('camila.fm.uploadfile'), 50, $this->stmt, CAMILA_FM_MAX_UPLOAD_SIZE, true, $this->usergroup, true);
          else
              new form_filebox($form, 'file', camila_get_translation('camila.fm.uploadfile'), 50, $this->stmt);

          $fp = $form->process();

          if ($fp)
              camila_information_text(camila_get_translation('camila.uploadsuccessfull'));
          $form->draw();

          return false;
      }

      function _download($filename)
      {
          $abs_item = $this->dir . '/' . $filename;

          header('Content-type: application/octet-stream');
          if ($this->extfsenabled)
              header("Content-Disposition: filename=\"" . substr($filename, 0, strlen($filename) - CAMILA_FM_PREFIX) . "\"");
          else
              header("Content-Disposition: filename=\"" . $filename . "\"");
          @readfile($abs_item);
      }

      function _delete()
      {
          require_once(CAMILA_DIR . 'datagrid/form.class.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');

          $form = new phpform('camila');
          $form->submitbutton = camila_get_translation('camila.delete');
          $form->drawrules = false;
          new form_hidden($form, 'delete', $_REQUEST['camila_delete']);
          new form_hidden($form, 'token', $_REQUEST['camila_token']);
          global $_CAMILA;
          $pos = strrpos($_CAMILA['page_url'], '?');
          if ($pos !== false)
              new form_hidden($form, substr($_CAMILA['page_url'], $pos + 1));

          if ($this->extfsenabled)
              $filename = substr($this->keyvalue['name'], 0, -CAMILA_FM_PREFIX);
          else
              $filename = $this->keyvalue['name'];

          $fp = $form->process();

          if ($fp) {
              if ($this->extfsenabled) {
                  if ($this->keyvalue['dir'] == '') {
                      $item = $this->stmt . '/' . $this->usergroup . '/' . $this->keyvalue['name'];
                      $this->_tree_delete_files($this->_tree_get_node_by_filename($this->keyvalue['name'], $this->usergroup), array('name'));
                      camila_information_text(camila_get_translation('camila.fm.filedeleted'));
                  } else {
                      $item = $this->stmt . '/' . $this->usergroup . '/' . $this->keyvalue['name'];
                      $this->_tree_delete_files($this->_tree_get_node_by_id($this->keyvalue['dir']), array('name'));
                      camila_information_text(camila_get_translation('camila.fm.filedeleted'));
                  }
              } else {
                  $item = $this->stmt . '/' . $this->keyvalue['name'];
                  
                  if ($this->keyvalue['name'] != '' && (@is_link($item) || @is_file($item)))
                      $ok = @unlink($item);
                  if ($ok)
                      camila_information_text(camila_get_translation('camila.fm.filedeleted'));
                  else
                      camila_information_text(camila_get_translation('camila.fm.filedeleted.error'));
              }
          } else {
              if ($this->extfsenabled && $this->keyvalue['dir'] != '')
                  camila_information_text(camila_get_translation('camila.fm.dir.del.confirm') . "\n" . $filename);
              else
                  camila_information_text(camila_get_translation('camila.fm.file.del.confirm') . "\n" . $filename);
              $form->draw();
          }

          return $fp;
      }

      function _move()
      {
          $this->_tree_checkup();

          require_once(CAMILA_DIR . 'datagrid/form.class.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/fm_exclude_current_dir_listbox.php');
          
          $form = new phpform('camila');
          $form->submitbutton = camila_get_translation('camila.move');
          $form->drawrules = false;
          new form_hidden($form, 'move', $_REQUEST['camila_move']);
          new form_hidden($form, 'token', $_REQUEST['camila_token']);
          
          global $_CAMILA;
          $pos = strrpos($_CAMILA['page_url'], '?');
          if ($pos !== false)
              new form_hidden($form, substr($_CAMILA['page_url'], $pos + 1));
          
          if ($this->extfsenabled)
              $filename = substr($this->keyvalue['name'], 0, -CAMILA_FM_PREFIX);
          else
              $filename = $this->keyvalue['name'];
          
          if ($this->keyvalue['dir'] == '') {
              $node = $this->_tree_get_node_by_filename($this->keyvalue['name'], $this->usergroup);
          } else {
              $node = $this->_tree_get_node_by_id($this->keyvalue['dir']);
          }
          
          new form_fm_exclude_current_dir_listbox($form, 'dir', 'Sposta nella cartella', $node, $this->usergroup, true);

          $fp = $form->process();

          if ($fp) {
              if ($this->keyvalue['dir'] == '') {
                  $this->_tree_move_to_last_child($node, $this->_tree_get_node_by_id($form->fields['dir']->value));
              } else {
                  $this->_tree_move_to_first_child($node, $this->_tree_get_node_by_id($form->fields['dir']->value));
              }
              camila_information_text('Elemento spostato con successo');
          } else {
              $form->draw();
          }

          return $fp;

      }

      function _update()
      {
          require_once(CAMILA_DIR . 'datagrid/form.class.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');

          $form = new phpform('camila');
          $form->submitbutton = camila_get_translation('camila.save');
          $form->drawrules = false;
          new form_hidden($form, 'update', $_REQUEST['camila_update']);
          new form_hidden($form, 'token', $_REQUEST['camila_token']);

          global $_CAMILA;
          //$_CAMILA['page']->camila_export_enabled = false;
          $pos = strrpos($_CAMILA['page_url'], '?');
          if ($pos !== false)
              new form_hidden($form, substr($_CAMILA['page_url'], $pos + 1));

          $pos = strrpos($this->keyvalue['name'], '.');

          if ($this->extfsenabled) {
              $type = substr($this->keyvalue['name'], $pos + 1, -CAMILA_FM_PREFIX);
              $name = substr($this->keyvalue['name'], 0, -CAMILA_FM_PREFIX);
          } else {
              $type = substr($this->keyvalue['name'], $pos + 1);
              $name = $this->keyvalue['name'];
          }

          if ($this->extfsenabled)
              $item = $this->stmt . '/' . $this->usergroup . '/' . $this->keyvalue['name'];
          else
              $item = $this->stmt . '/' . $this->keyvalue['name'];

          ob_start();
          @readfile($item);
          $content = ob_get_contents();
          ob_end_clean();

          $file = $item;

          if ($type == 'txt') {
              require_once(CAMILA_DIR . 'datagrid/elements/editor/txt.inc.php');
          } elseif ($type == 'xml') {
              require_once(CAMILA_DIR . 'datagrid/elements/editor/xml.inc.php');
          } elseif ($type == 'csv') {
              require_once(CAMILA_DIR . 'datagrid/elements/editor/csv.inc.php');
          } elseif ($type == 'htm') {
              require_once(CAMILA_DIR . 'datagrid/elements/editor/html.inc.php');
          }

          $fp = $form->process();

          if ($fp) {
              if ($this->keyvalue['name'] != '' && (@is_link($item) || @is_file($item)))
                  $ok = @unlink($item);
              if ($ok)
                  camila_information_text('File aggiornato con successo');
              else
                  camila_information_text('Problemi nell\'eliminazione del file!');

              $f = fopen($item, 'w');
              fwrite($f, $form->fields['content']->value);
              $form->draw();
          } else {
              $form->draw();
          }

          return $fp;
      }

      function _new()
      {
          if ($this->extfsenabled)
              $this->_tree_checkup();

          require_once(CAMILA_DIR . 'datagrid/form.class.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/static_listbox.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/fm_dir_listbox.php');

          $form = new phpform('camila');
          $form->submitbutton = camila_get_translation('camila.save');
          $form->drawrules = false;
          new form_hidden($form, 'update', 'new');

          global $_CAMILA;
          $pos = strrpos($_CAMILA['page_url'], '?');
          if ($pos !== false)
              new form_hidden($form, substr($_CAMILA['page_url'], $pos + 1));

          new form_textbox($form, 'name', 'Nome file', true, 30);

          $types;
          foreach ($this->editabletypes as $k => $v) {
              $types .= $v . ';' . '.' . $v . ' - ' . camila_get_translation('camila.mimetype.' . $v) . ',';
          }

          new form_static_listbox($form, 'type', 'Tipo', $types, true);
          if ($this->extfsenabled)
              new form_fm_dir_listbox($form, 'dir', camila_get_translation('camila.fm.intofolder'), $this->usergroup, true);

          $fp = $form->process();

          if ($fp) {
              $filename = $form->fields['name']->value . '.' . $form->fields['type']->value;

              if ($this->extfsenabled) {
                  $sfile = $filename . camila_hash(CAMILA_FM_PREFIX);
                  $arr = array('name' => $sfile);
                  $item = $this->stmt . '/' . $this->usergroup . '/' . $sfile;
                  $f = fopen($item, 'w');
                  $this->_addfile($this->usergroup, $form->fields['dir']->value, $filename, $sfile);
              } else {
                  $arr = array('name' => $filename);
                  $item = $this->stmt . '/' . $filename;
                  $f = fopen($item, 'w');
              }

              camila_information_text(camila_get_translation('camila.fm.filecreated'));

              $myLink = new CHAW_link(camila_get_translation('camila.edit') . ' ' . $filename, basename($_SERVER['PHP_SELF']) . $_CAMILA['returl'] . '&camila_update=' . serialize($arr) . '&camila_token=' . camila_token(serialize($arr)));
              $myLink->set_br(2);
              $_CAMILA['page']->add_link($myLink);
          } else {
              $form->draw();
          }

          return $fp;
      }

      function _newdir()
      {
          $this->_tree_checkup();

          require_once(CAMILA_DIR . 'datagrid/form.class.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
          require_once(CAMILA_DIR . 'datagrid/elements/form/fm_dir_listbox.php');
          
          $form = new phpform('camila');
          $form->submitbutton = camila_get_translation('camila.create');
          $form->drawrules = false;
          new form_hidden($form, 'update', 'newdir');
          
          global $_CAMILA;
          $pos = strrpos($_CAMILA['page_url'], '?');
          if ($pos !== false)
              new form_hidden($form, substr($_CAMILA['page_url'], $pos + 1));
          
          new form_textbox($form, 'name', camila_get_translation('camila.fm.createdirwithname'), true, 30);
          new form_fm_dir_listbox($form, 'dir', camila_get_translation('camila.fm.intofolder'), $this->usergroup, true);

          $fp = $form->process();

          if ($fp) {
              $this->_createdir($this->usergroup, $form->fields['dir']->value, $form->fields['name']->value);
              camila_information_text(camila_get_translation('camila.fm.dircreated'));
          } else {
              $form->draw();
          }
          return $fp;
      }

  }
?>