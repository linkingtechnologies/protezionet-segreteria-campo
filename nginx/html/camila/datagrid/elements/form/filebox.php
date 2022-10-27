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



  class form_filebox extends form_field
  {
      var $maxsize;
      var $uploadfolder; // base directory
      var $uploaderrors;
      var $selectuploadfolder;
      var $override = true;
	  var $showmaxsizewarning = true;

      function form_filebox(&$form, $field, $title, $size = 50, $uploadfolder, $maxsize = CAMILA_FM_MAX_UPLOAD_SIZE, $extfsenabled = false, $usergroup = '', $selectuploadfolder = true)
      {
          new form_hidden($form, $field.'_camilauploaded');

          $this->extfsenabled = $extfsenabled;
          $this->usergroup = $usergroup;
          $this->form_field($form, $field, $title);
          $this->maxsize = $maxsize;
          $this->selectuploadfolder = $selectuploadfolder;

          if ($extfsenabled)
              $this->uploadfolder = $uploadfolder . '/' . $usergroup;
          else
              $this->uploadfolder = $uploadfolder;

          $this->size = $size;

          $form->add($this);
          if ($extfsenabled) {
              require_once(CAMILA_DIR.'datagrid/elements/form/fm_dir_listbox.php');
              if ($this->selectuploadfolder)
                  new form_fm_dir_listbox($form, $field.'_camiladir', camila_get_translation('camila.fm.intofolder'), $usergroup, true);
          }
      }

      function draw(&$form)
      {
          parent::draw($form);      
          global $_CAMILA;

          if (is_array($this->form->fields[$this->field.'_camilauploaded']->value)) {
              $text = new CHAW_text(camila_get_translation('camila.fm.filealreadyuploaded'));
              $text->set_br(1);
              $form->add_text($text);
          }

          foreach ($this->form->fields[$this->field.'_camilauploaded']->value as $key => $value) {
              if ($this->extfsenabled)
                  $text = new CHAW_text(substr($value, 0, -CAMILA_FM_PREFIX));
              else
                  $text = new CHAW_text($value);

              //$text = new CHAW_text(fm_name($value, $this->usergroup));

              $text->set_br(1);
              $form->add_text($text);
          }

          $text = new CHAW_text('');
          $text->set_br(1);
          $form->add_text($text);

          $myFile = new CHAW_file($this->key . '[]', $this->title . $this->labelseparator, $this->size, $this->maxsize);
          $myFile->set_br(1);

          //$mySelect = fm_select_group_dir($this->key.'_camiladir[]', $this->usergroup);

          for ($i = 0; $i < 1; $i++) {
              $form->add_userdefined($myFile);
          //    $text = new CHAW_text(camila_get_translation('camila.fm.intofolder') . ': ');
          //    $text->set_br(0);
          //    if ($this->selectuploadfolder) {
          //        $form->add_text($text);
                  //$form->add_select($mySelect);
          //        $text = new CHAW_text('');
          //        $text->set_br(0);
                  $form->add_text($text);
          //    }
          }

          if ($this->showmaxsizewarning) {
		     $text = new CHAW_text(camila_get_translation('camila.fm.maxfilesizeallowed') . ': ' . $this->_convertfilesize($this->maxsize));
             $text->set_br(2);
             $form->add_text($text);
		  }

      }

      function process()
      {
		  //print_r($_FILES[$this->key]);
          //$this->value = $this->form->fields[$this->field.'_camilauploaded']->value;
		  
		  $this->value = Array();

          global $_CAMILA;

          if (isset($_FILES[$this->key]) ) {
              $cnt = count($_FILES[$this->key]['name']);
              $err = false;
              $err_avaliable = isset($_FILES[$this->key]['error']);

              for ($i = 0; $i < $cnt; $i++) {
                  $this->uploaderrors[$i] = null;
                  $tmp = $_FILES[$this->key]['tmp_name'][$i];
                  $items[$i] = stripslashes($_FILES[$this->key]['name'][$i]);
                  if ($err_avaliable)
                      $up_err = $_FILES[$this->key]['error'][$i];
                  else
                      $up_err = (file_exists($tmp) ? 0 : 4);
                  //$rel = $items[$i] . camila_hash(CAMILA_FM_PREFIX);
                  //$abs = get_abs_item($this->usergroup, $rel);

                  if ($this->extfsenabled)
                      $rel = $items[$i] . camila_hash(CAMILA_FM_PREFIX);
                  else
                      $rel = $items[$i];

                  $abs = $this->uploadfolder . '/' . $rel;
				  


                  if ($items[$i] == '' || $up_err == 4)
                      continue;
                  
                  if ($up_err == 1 || $up_err == 2) {
                      $this->uploaderrors[$i] = 904;
                      $err = true;
                      continue;
                  }

                  if ($up_err == 3) {
                      $this->uploaderrors[$i] = 905;
                      $err = true;
                      continue;
                  }

                  if (!@is_uploaded_file($tmp)) {
                      $this->uploaderrors[$i] = 906;
                      $err = true;
                      continue;
                  }

                  if (!$this->override && @file_exists($abs)) {
                      $this->uploaderrors[$i] = 906;
                      $err = true;
                      continue;
                  }

                  if (function_exists('move_uploaded_file')) {
                      $ok = @move_uploaded_file($tmp, $abs);
                  } else {
                      $ok = @copy($tmp, $abs);
                      // trying to delete file...
                      @unlink($tmp);
                  }

                  if ($ok === false) {
                      $this->uploaderrors[$i] = 906;
                      $err = true;
                      continue;
                  }

                  if (!$err) {
                      $uparr = $this->form->fields[$this->field.'_camilauploaded']->value;
                      $eluparr = !is_array($uparr) ? 0 : count($uparr);
					  $this->value[count($this->value)] = $rel;
					  //echo $this->value;
                      $this->form->fields[$this->field.'_camilauploaded']->value[$eluparr] = $rel;

                      //if ($this->selectuploadfolder)
                      //    fm_add_file($rel, fm_name($rel, $this->usergroup), $this->usergroup, $_POST[$this->key.'_camiladir'][$i] );
                      //else
                      //    fm_add_file($rel, , $this->usergroup );

                      if ($this->extfsenabled) {

                          if ($this->selectuploadfolder)
                              $this->_addfile($this->usergroup, $_POST[$this->key.'_camiladir'], substr($rel, 0, -CAMILA_FM_PREFIX), $rel);
                          else {
                              $rootnode = $this->_tree_get_group_root_node($this->usergroup);
                              $this->_addfile($this->usergroup, $rootnode['id'], substr($rel, 0, -CAMILA_FM_PREFIX), $rel);
                          }
                      }

                      //$this->value = $this->form->fields[$this->field.'_camilauploaded']->value;
					  
					  //$this->form->fields['filename']->value[0] = $rel;

                  }

              }

          }

          if ($err == true)
              return false;
          else
              return true;
      }

      function validate()
      {
          parent::validate();

          if (is_array($this->uploaderrors))
          foreach ($this->uploaderrors as $key => $value) {
              if ($this->uploaderrors[$key]== null)
                  continue;
              $this->form->validator->setError($this->field, $this->uploaderrors[$key]);
          }
      }

      function _convertfilesize($size) {
          if ($size >= 1073741824) {
              $size = round($size / 1073741824 * 100) / 100 . ' GB';
          } elseif ($size >= 1048576) {
              $size = round($size / 1048576 * 100) / 100 . ' MB';
          } elseif ($size >= 1024) {
              $size = round($size / 1024 * 100) / 100 . ' KB';
          } else
              $size = $size . ' B';

          return str_replace('.',camila_get_translation('camila.decimal.separator'), $size);
      }

//  function _addfile($item, $filename, $usergroup, $directory_id = null)
//  {
      
      //if ($directory_id == null)
      //    tree_new_last_child($thandle, tree_get_node_where($thandle, "name='" . CAMILA_FM_DEFAULT_FOLDER . "' AND usergroup='" . $usergroup . "' AND file =''"), "file='" . mysql_escape_string($item) . "',name='" . mysql_escape_string($filename) . "',usergroup='" . $usergroup . "'");
      //else
      //    tree_new_last_child($thandle, tree_get_node_where($thandle, "id='" . $directory_id . "'"), "file='" . mysql_escape_string($item) . "',name='" . mysql_escape_string($filename) . "',usergroup='" . $usergroup . "'");
//  }

    function _addfile($usergroup, $dir, $name, $filename)
    {
        $this->_tree_new_first_child($this->_tree_get_node_by_id($dir), $filename, $name, $usergroup);
    }

    function _tree_new_first_child($node, $filename, $name, $usergroup)
    {
        $newnode['l'] = $node['l'] + 1;
        $newnode['r'] = $node['l'] + 2;
        $this->_tree_shift_RL_values($newnode['l'], 2);
        $this->_tree_insert_new_node($newnode, $filename, $name, $usergroup);
        return $newnode;
     }

    function _tree_shift_RL_values($first, $delta)
    {
        global $_CAMILA;
        $_CAMILA['db']->StartTrans();
        $_CAMILA['db']->Execute('UPDATE '.CAMILA_TABLE_FILES." SET lft=lft+$delta WHERE lft>=$first");
        $_CAMILA['db']->Execute('UPDATE '.CAMILA_TABLE_FILES." SET rgt=rgt+$delta WHERE rgt>=$first");
        $_CAMILA['db']->CompleteTrans();
    }

    function _tree_insert_new_node($node, $filename='', $name='', $usergroup='')
    {
        global $_CAMILA;
        $res = $_CAMILA['db']->Execute('INSERT INTO '.CAMILA_TABLE_FILES.' (lft,rgt,file,name,usergroup) VALUES (?,?,?,?,?)', Array($node['l'], $node['r'], $filename, $name, $usergroup));
        if ($res === false)
            camila_error_page(camila_get_translation('camila.sqlerror').' '.$_CAMILA['db']->ErrorMsg());
    }

    function _tree_get_node_by_id($id)
    {
        $noderes['l'] = 0;
        $noderes['r'] = 0;
        global $_CAMILA;

        $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);

        $res = $_CAMILA['db']->Execute('SELECT * FROM ' . CAMILA_TABLE_FILES . ' WHERE id = ?', Array($id));

        if ($res === false)
            camila_error_page(camila_get_translation('camila.sqlerror').' '.$_CAMILA['db']->ErrorMsg());
        else{
            if (!$res->EOF) {
                $row = $res->fields;
                $noderes['l'] = $row['lft'];
                $noderes['r'] = $row['rgt'];
            }
        }
      return $noderes;
    }

    function _tree_get_group_root_node($usergroup)
    {
        $noderes['l'] = 0;
        $noderes['r'] = 0;
        global $_CAMILA;

        $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);

        $res = $_CAMILA['db']->Execute('SELECT * FROM ' . CAMILA_TABLE_FILES . ' WHERE name = ? AND usergroup = ?', array($usergroup, ''));

        if ($res === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
        else {
            if (!$res->EOF) {
                $row = $res->fields;
                $noderes['id'] = $row['id'];
                $noderes['l'] = $row['lft'];
                $noderes['r'] = $row['rgt'];
             }
        }
        return $noderes;
    }
  }
?>
