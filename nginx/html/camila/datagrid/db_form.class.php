<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2017 Umberto Bresciani

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


require_once(CAMILA_DIR . 'datagrid/form.class.php');

  function camila_db_ondelete_message($lform)
  {
      global $_CAMILA;
      $text = new CHAW_text('');
	  $text->set_br(2);
      $_CAMILA['page']->add_text($text);
      camila_information_text(camila_get_translation('camila.form.data.deleted'));
      return true;
  }

  function camila_db_oninsert_message($lform)
  {
      global $_CAMILA;
      $text = new CHAW_text('');
	  $text->set_br(2);
      $_CAMILA['page']->add_text($text);
      camila_information_text(camila_get_translation('camila.form.data.inserted'));
      return true;
  }

  function camila_db_onupdate_message($lform)
  {
      global $_CAMILA;
      $text = new CHAW_text('');
	  $text->set_br(2);
      $_CAMILA['page']->add_text($text);
      $_CAMILA['update_result_desc'] = camila_get_translation('camila.form.data.updated');
      camila_information_text($_CAMILA['update_result_desc']);
      return true;
  }

  class dbform extends phpform
  {
      var $table;

      // fields from database
      var $dbfields;

      // keys that identifies one unique row, use commas for more than one
      var $keys;

      // form for selecting rows
      var $selform;

      var $orderdirection;

      var $whereclause;

      // mode of the form, insert or update
      var $mode;

      // value of the key, if updatemode
      var $keyvalue;

      // Events fired just before executing the specified action
      // it must return true, so the process may continue
      // if it returns false, no action is taken (it's assumed that the event did it)
      var $oninsert = 'camila_db_oninsert_message';
      var $onupdate = 'camila_db_onupdate_message';
      var $ondelete = 'camila_db_ondelete_message';

      var $caninsert = true;
      var $candelete = false;
      
      var $_data_inserted = false;
      var $_data_updated = false;
      
      var $mapping;
      var $mappingseparator = '#';

      var $drawfilterbox = true;
      var $drawnavigationbox = true;

      var $recordcount;

      // table - table name
      // keys - fields separeted by comma that select an unique row
      // sel_fields - fields shown at the selection box
      // sel_order - order used to sort the list at the selection box
      function dbform($table, $keys, $sel_fields = '', $sel_order = '', $orderdirection = 'asc', $whereclause = '', $candelete = false, $caninsert = false, $canupdate=true, $drawfilterbox=true, $drawnavigationbox=true)
      {
          $this->table = $table;
          $this->keys = explode(',', $keys);
          $this->orderdirection = $orderdirection;
          $this->whereclause = $whereclause;
          global $_CAMILA;
          $this->phpform($table, $_CAMILA['page_url']);
          $this->hasdblink = true;
          $this->candelete = $candelete;
          $this->caninsert = $caninsert;
          $this->canupdate = $canupdate;
          $this->drawfilterbox = $drawfilterbox;
          $this->drawnavigationbox = $drawnavigationbox;

          // if sel_fields == "" then the user doesn't want the select form!
          if ($sel_fields == '')
              $this->selform = 0;
          else {
              // if sel_order == "" then we use the keys as the order for listing the select form

              if ($sel_order == '')
                  $sel_order = $keys;

              require_once CAMILA_DIR . 'datagrid/selectform.class.php';

              $this->selform = new selectform($this->table, $keys, $sel_fields, $sel_order, $orderdirection, $whereclause, $this->candelete, $this->caninsert);
              if ($this->caninsert) {
                  $pos = strpos($_CAMILA['page_url'], '?');
                  if ($pos === false)
                      $link = basename($_SERVER['PHP_SELF']) . '?camila_update=new';
                  else {
                      $link = $_CAMILA['page_url'] . '&camila_update=new';
					  if (isset($_REQUEST['dashboard']))
						  $link .= '&dashboard='.$_REQUEST['dashboard'];
				  }

                  $this->selform->additional_links = array(camila_get_translation('camila.report.insertnew') => $link);
              }

              $this->selform->drawfilterbox = $drawfilterbox;
              $this->selform->drawnavigationbox = $drawnavigationbox; 

          }

          // at the beggining, the form starts in insert mode
          // then, at the process we check if it has a key defined, it enters update mode
          // after a delete action, it should enter insert mode
          $this->mode = 'insert';

          // fill field metadata
          $this->dbfields = $this->get_fields($table);

      }


      function get_fields($table)
      {
          global $_CAMILA;

          $stmt = 'select * from ' . $table;
          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          $result = $_CAMILA['db']->Execute($stmt);
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $numfields = $result->FieldCount();

          $ret = array();
          for ($i = 0; $i < $numfields; $i++) {
              $curr_field = $result->FetchField($i);

              $field = $curr_field->name;
              $type = $result->MetaType($curr_field->type);
              $length = $curr_field->max_length;
              $not_null = $curr_field->not_null;
              $ret[$field]['not_null'] = $not_null;
              $ret[$field]['type'] = $type;
              $ret[$field]['maxlength'] = $length;

              $result->MoveNext();
          }
          return $ret;
      }


      function select_data()
      {
          if (!$this->keyvalue)
              return false;

          global $_CAMILA;

          $stmt = 'select ';

          $tot_fields = count($this->fields);

          $i = 1;

          reset($this->fields);
          while ($afield = each($this->fields)) {
              if (!$afield[1]->dummy && $afield[1]->field != '' && !(substr(trim($afield[1]->field), 0, strlen('camilafield_')) == 'camilafield_')) {
                  if ($afield[1]->field != 'group')
                      $stmt .= $afield[1]->field;
                  else
                      $stmt .= $this->table.'.'.$afield[1]->field;
                  $stmt .= ', ';
              } else
                  $i++;
          }

          $stmt = substr($stmt, 0, strlen($stmt) - 2);
          $stmt .= ' from '.$this->table.' where ';

          if ($this->whereclause != '') {
              $stmt .= ' (' . $this->whereclause . ')';
              
              if (count($this->keyvalue) > 0)
                  $stmt .= ' AND ';
          }

          // read values from keys
          reset($this->keyvalue);

          $i = 0;
          while ($akey = each($this->keyvalue)) {
              if ($i > 0)
                  $stmt .= ' AND ';

              $stmt .= trim($this->keys[$i]) . ' = ' . $_CAMILA['db']->Quote($akey[1]);
              $i++;
          }

          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          $result = $_CAMILA['db']->Execute($stmt);

//          $this->recordcount = $result->RecordCount();

          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $vals = $result->FetchRow();

          if (!$vals)
              return false;

          if (isset($_REQUEST['camila_print']))
              $this->drawrules = false;

          reset($vals);
          reset($this->fields);
          while ($afield = each($this->fields)) {
              if (isset($_REQUEST['camila_print']))
                  $this->fields[$afield[1]->field]->updatable = false;
              if (!$afield[1]->dummy && $afield[1]->field != '') {
                  $val = each($vals);
                  $this->fields[$afield[1]->field]->value = $val[1];
              }
          }

          return true;
      }

      function insert_data()
      {
		  global $_CAMILA;

		  $isPostgres = ($_CAMILA['db']->databaseType == 'postgres9');
          $stmt = 'insert into ' . $this->table . ' ( ';
          $first = false;

          reset($this->fields);
          while ($afield = each($this->fields)) {
              if ((!$afield[1]->updatable && !isset($afield[1]->defaultvalue)) || $afield[1]->field == '' || (substr(trim($afield[1]->field), 0, strlen('camilafield_')) == 'camilafield_') )
                  continue;
              if ($first)
                  $stmt .= ', ';
              else
                  $first = true;
              
              if ($afield[1]->field != 'group')
                  $stmt .= $afield[1]->field;
              else
                  $stmt .= $this->table.'.'.$afield[1]->field;
          }

          

          if ($_CAMILA['page']->camila_worktable)
              $stmt .= ', created, created_by, created_src, created_by_surname, created_by_name, last_upd, last_upd_by, last_upd_src, last_upd_by_surname, last_upd_by_name, mod_num'; 


          $stmt .= ' ) values ( ';
          $first = false;
          
          $count = 0;
          $data = Array();
          
          reset($this->fields);
          while ($afield = each($this->fields)) {
              if ((!$afield[1]->updatable && !isset($afield[1]->defaultvalue)) || $afield[1]->field == '' || (substr(trim($afield[1]->field), 0, strlen('camilafield_')) == 'camilafield_'))
                  continue;
              if ($first)
                  $stmt .= ', ';
              else
                  $first = true;
 
              $stmt .= '?';
			  //print_r($this->dbfields[$afield[1]->field]);
			  //2017
              if (($this->dbfields[$afield[1]->field]['not_null'] == '0' || $isPostgres )&& $this->fields[$afield[1]->field]->value == '')
                  $data[$count] = null;
              else
                  $data[$count] = $this->fields[$afield[1]->field]->value;
              $count++;
          }

          if ($_CAMILA['page']->camila_worktable) {
              $stmt .= ',?,?,?,?,?,?,?,?,?,?,?';

              $now = $_CAMILA['db']->BindTimeStamp(date("Y-m-d H:i:s", time()));
              $data[$count] = $now;
              $data[$count+1] = $_CAMILA['user'];
              $data[$count+2] = 'application';
              $data[$count+3] = $_CAMILA['user_surname'];
              $data[$count+4] = $_CAMILA['user_name'];
	      $data[$count+5] = $now;
              $data[$count+6] = $_CAMILA['user'];
              $data[$count+7] = 'application';
              $data[$count+8] = $_CAMILA['user_surname'];
              $data[$count+9] = $_CAMILA['user_name'];
              $data[$count+10] = 0;
          }

          $stmt .= ')';

          $result = $_CAMILA['db']->Execute($stmt, $data);
		  //print_r($data);
		  //echo $stmt;
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

      }

      function update_data()
      {
          $stmt = 'update ' . $this->table . ' set ';
          $first = false;
          $data = Array();
          $count = 0;

          reset($this->fields);
          while ($afield = each($this->fields)) {
              if (!$afield[1]->updatable || $afield[1]->field == '' || (substr(trim($afield[1]->field), 0, strlen('camilafield_')) == 'camilafield_'))
                  continue;
              if ($first)
                  $stmt .= ', ';
              else
                  $first = true;

              if ($afield[1]->field != 'group')
                  $stmt .= $afield[1]->field . ' = ?';
              else
                  $stmt .= $this->table.'.'.$afield[1]->field . ' = ?';

              if ($this->dbfields[$afield[1]->field]['not_null'] == 0 && $this->fields[$afield[1]->field]->value == '')
                  $data[$count] = null;
              else
                  $data[$count] = $this->fields[$afield[1]->field]->value;
              $count++;
          }

          global $_CAMILA;

          if ($_CAMILA['page']->camila_worktable) {
              $stmt .= ', last_upd = ?, last_upd_by = ?, last_upd_src = ?, last_upd_by_surname = ?, last_upd_by_name = ?, mod_num = mod_num + 1 '; 

              $now = $_CAMILA['db']->BindTimeStamp(date("Y-m-d H:i:s", time()));
              $data[$count] = $now;
              $data[$count+1] = $_CAMILA['user'];
              $data[$count+2] = 'application';
              $data[$count+3] = $_CAMILA['user_surname'];
              $data[$count+4] = $_CAMILA['user_name'];
 	  }

	  $stmt .= ' where ';

          reset($this->keyvalue);
          $i = 0;
          while ($akey = each($this->keyvalue)) {
              if ($i > 0)
                  $stmt .= ' AND ';

              //$stmt .= trim($this->keys[$i]) . " = '{$akey[1]}'";

              $stmt .= trim($this->keys[$i]) . " = ?";

              $data[] = $akey[1];
              $i++;
          }


          $result = $_CAMILA['db']->Execute($stmt, $data);
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          else {
			  if ($_CAMILA['page']->camila_worktable) {
				  
			  }
			  
			  $_CAMILA['update_result'] = 'OK';
			  
		  }
              

      }

      function delete_data()
      {

          $stmt = 'delete from ' . $this->table . ' where ';

          reset($this->keyvalue);

          $i = 0;
          while ($akey = each($this->keyvalue)) {
              if ($i > 0)
                  $stmt .= ' AND ';

              $stmt .= trim($this->keys[$i]) . " = '{$akey[1]}'";

              $i++;
          }

          global $_CAMILA;
          $_CAMILA['db']->Execute($stmt);
      }

      function draw_submit($button_text)
      {
          if (isset($_REQUEST['camila_print']))
             return;

          if (isset($_REQUEST['camila_delete']) && $this->mode != 'insert')
              parent::draw_delete(camila_get_translation('camila.deletebutton'));
          else
              parent::draw_submit(camila_get_translation('camila.' . $this->mode . 'button'));
      }

      function draw_header()
      {
          phpform::draw_header();
          phpform::add_hidden($this->table . '_sess_key', serialize($this->keyvalue));
          phpform::add_hidden($this->table . '_sess_mode', $this->mode);
          if (isset($_REQUEST['camila_returl']) && ($_REQUEST['camila_returl'] != ''))
              phpform::add_hidden('camila_returl', $_REQUEST['camila_returl']);
          if (isset($_REQUEST['camila_preferences']) )
              phpform::add_hidden('camila_preferences', $_REQUEST['camila_preferences']);

      }

      function draw()
      {
          if ($this->mapping != '')
              $this->selform->mapping = $this->mappingseparator.$this->mapping.$this->mappingseparator;

          if ($this->formupdatelinktext != '')
              $this->selform->formupdatelinktext = $this->formupdatelinktext;

          if ($this->_data_inserted == true) {
              if ($this->selform != 0)
                  $this->selform->draw();

              global $_CAMILA;

              $pos = strpos($_CAMILA['page_url'], '?');
              if ($pos === false)
                  $link = basename($_SERVER['PHP_SELF']) . '?camila_update=new';
              else
                  $link = $_CAMILA['page_url'] . '&camila_update=new';

              if ($this->caninsert) {
                      $myLink = new CHAW_link(camila_get_translation('camila.report.insertnew'), $link);
	              $myLink->set_br(2);
				  $myLink->set_css_class('btn btn-md btn-default');
	              $_CAMILA['page']->add_link($myLink);
              }


              if (isset($_REQUEST['submitandnew_button_header']) && $this->caninsert) {

                  $_CAMILA['page']->set_redirection(1, $link);
              }

              return;
          }

          if ($this->selform != 0 && !isset($_GET['camila_delete']) && !isset($_GET['camila_update']) && (/*$_POST["{$this->table}_sess_mode"] == 'delete' || */!isset($_POST["{$this->table}_sess_mode"])))
              $this->selform->draw();

          global $_CAMILA;

          if ($this->selform == 0 || isset($_REQUEST['camila_delete']) || isset($_GET['camila_update']) || $_REQUEST[$this->table.'_sess_mode'] == 'update' || $_REQUEST[$this->table.'_sess_mode'] == 'insert') {
              if (!isset($_REQUEST['camila_popup']) && !$_CAMILA['page']->camila_exporting() && (isset ($_REQUEST['camila_returl']) && $_REQUEST['camila_returl'] != '') && (!is_object($this->validator) || count($this->validator->getErrors()) == 0)) {
	              $text = new CHAW_text('');
				  $text->set_br(1);
				  $_CAMILA['page']->add_text($text);
				  $myLink = new CHAW_link(camila_get_translation('camila.back.table'), $_REQUEST['camila_returl']);
				  $myLink->set_css_class('btn btn-md btn-default');
	              $myLink->set_br(1);
	              $_CAMILA['page']->add_link($myLink);
	          }

              //if (!$this->_data_inserted && !$this->_data_updated)
              phpform::draw();

          }
      }

      function process()
      {
          if ($this->mapping != '')
              $this->selform->mapping = $this->mappingseparator.$this->mapping.$this->mappingseparator;

          if ($this->selform != 0)
              $selformprocessed = $this->selform->process();
          elseif (isset($_REQUEST['camila_update']) || isset ($_REQUEST['camila_delete'])) {
	          if( isset($_REQUEST['camila_update']) ) {
                  $this->keyvalue = unserialize(stripslashes($_REQUEST['camila_update']));
                  $check = camila_token($_REQUEST['camila_update']);
              } else {
                  $this->keyvalue = unserialize(stripslashes($_REQUEST['camila_delete']));
                  $check = camila_token($_REQUEST['camila_delete']);
              }
             if ($check != $_REQUEST['camila_token'] && $_REQUEST['camila_update'] != 'new')
                 camila_error_page(camila_get_translation(camila.pageforbidden));

          }

          //if (!($this->selform == 0 || isset($_GET['camila_update']) ))
          //    return false;


          if (!parent::process() && !$this->noproc) {
              // if this form didn't processed, see if select processed
              // first check if there is a select form 
              $selected = false;

              if ($this->selform != 0) {
                  // See if any key was selected by selform
                  $selected = $selformprocessed;

                  if ($selected && !$this->selform->value)
                      $selected = false;

                  if ($selected)
                      $this->keyvalue = $this->selform->value;
              }

              // If there was no selform, or selform selected nothing
              // try to see if the user has set keyvalue
              // how user can set keyvalue? using $form->keyvalue = "xxx,xxx"
              if (!$selected && count($this->keyvalue) > 0)
                  $selected = true;

              // Something filled keyvalue, try loading the values into phpdbform
              if ($selected) {
                  if ($this->select_data()) {
                      // found data!
                      $this->mode = 'update';
                  } else {
                      // some error occurred, clear phpdbform and set insertmode
                      $this->clear();
                      $this->mode = 'insert';
                  }

                  // nothing was selected, go to insertmode
                  if (isset($_GET['camila_delete']))
                      $this->mode = 'delete';
              } else
                  $this->mode = 'insert';
              
              // if there is a select form, fill it with data
              if ($this->selform != 0)
                  $this->selform->select_data();

              return;
          }

          // the form processed anything, let's work
          // first get key and value from session
          if (isset($_REQUEST[$this->table.'_sess_mode'])) {
              $this->mode = $_REQUEST[$this->table.'_sess_mode'];

              // can be a hack...
              if ($this->mode != 'insert' && $this->mode != 'update' && $this->mode != 'delete')
                  die('Invalid mode:' . $this->mode);

              $temp = $_REQUEST[$this->table.'_sess_key'];

              //if (get_magic_quotes_gpc())
              //    $temp = stripslashes($temp);
              $this->keyvalue = unserialize($temp);
          }

          if (!$this->noproc) {
              // if delete button was pressed, goto deletemode
              if (isset($_REQUEST['camila_delete']))
                  $this->mode = 'delete';

              if ($this->mode == 'update') {
                  if ($this->selform != 0)
                      $this->selform->value = $this->keyvalue;

                  // update data
				  //echo '.'.$this->onupdate;
                  if (isset($this->onupdate)) {
                      if (call_user_func($this->onupdate, $this))
                          $this->update_data();
                  } else
                      $this->update_data();

                  $this->_data_updated = true;

                  reset($this->fields);
                  while ($field = each($this->fields)) {
                      if ((substr(trim($field[1]->field), 0, strlen('camilafield_')) == 'camilafield_')) {
                          $this->fields[$field[1]->field]->process();
                          //$req[$this->fields[$field[1]->field]->field] = $this->fields[$field[1]->field]->value;
                      }
                  }
              } elseif ($this->mode == 'insert') {
                 // insert data
				 //echo $this->oninsert;
                  if (isset($this->oninsert)) {
                      if (call_user_func($this->oninsert, $this)) {
                          $this->insert_data();
                          $this->_data_inserted = true;
                      }
                  } else {
                      $this->insert_data();
                      $this->_data_inserted = true;
                  }

                  reset($this->fields);
                  while ($field = each($this->fields)) {
                      if ((substr(trim($field[1]->field), 0, strlen('camilafield_')) == 'camilafield_')) {
                          $this->fields[$field[1]->field]->process();
                          ////                              //$req[$this->fields[$field[1]->field]->field] = $this->fields[$field[1]->field]->value;
                      }
                  }

                  // clear values
                  $this->clear();
              } elseif ($this->mode == 'delete') {
                  if (isset($this->ondelete)) {
                      if (call_user_func($this->ondelete, $this))
                          $this->delete_data();
                  } else
                      $this->delete_data();

                  $this->clear();
                  $this->keyvalue = '';
                  $this->mode = 'insert';
              }
          } else {
              //$this->draw_errors();
              if ($this->mode == 'update') {
                  if ($this->selform != 0)
                      $this->selform->value = $this->keyvalue;
                  // update data
              }
          }

          // if there is a select form, fill it with data
          if ($this->selform != 0)
              $this->selform->select_data();
      }
  }
?>