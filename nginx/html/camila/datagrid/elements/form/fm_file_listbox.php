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



  require_once(CAMILA_DIR . 'fs.class.php');


  class form_fm_file_listbox extends form_field
  {
      var $usergroup;
      var $fs;
      var $basedir;

      function form_fm_file_listbox(&$form, $field, $title, $basedir, $extfsenabled=false, $usergroup='', $extensions='', $required = false, $validation = '')
      {
          $this->form_field($form, $field, $title, $required, $validation);
          $this->usergroup = $usergroup;
          $this->fs = new fs($usergroup);
          $this->basedir = $basedir;
          $form->add($this);
      }

      function draw(&$form)
      {
          parent::draw($form);
          $text = new CHAW_text($this->title.$this->labelseparator);
          $text->set_br(0);
          if (!empty($this->title))
              $form->add_text($text);

          $mySelect = $this->_get_select();
		  //$mySelect->set_css_class($this->css_class);

          if ($this->updatable) {
              $form->add_select($mySelect);
              $text = new CHAW_text('');
              $form->add_text($text);
          } else {
              $text = new CHAW_text($val);
              $form->add_text($text);
          }
      }

      function process()
      {
          if (isset($_REQUEST[$this->key])) {
              $this->value = $_REQUEST[$this->key];
          }
      }

      function _get_select()
      {
          $mySelect = new CHAW_select($this->key);
          if (is_dir($this->basedir)) {
              if ($dh = opendir($this->basedir)) {
                  while (($file = readdir($dh)) !== false) {
                      if ($file != '.' && $file != '..' && !is_dir($this->basedir . '/' . $file)) {
                           $ext = $this->_find_extension($file);
                           if (strlen($ext)>0)
                               $filename = substr($file, 0, -strlen($ext)-1);
                           else
                               $filename = $file;

                           if ($file == $this->value)
                               $mySelect->add_option($filename, $file, HAW_SELECTED);
                           else
                               $mySelect->add_option($filename, $file);
                      }
                  }
              closedir($dh);
              }
          }


          return $mySelect;
      }

      function _get_select_extfs()
      {

          $mySelect = new CHAW_select($this->key);

          $node = $this->fs->_tree_get_group_root_node($this->usergroup);
          $attributes = array('name');

          $mySelect = new CHAW_select($name);
          $wlk = $this->fs->_tree_walk_preorder($node);
          $mySelect->add_option('(seleziona allegato)', '.');
          $currentDir='';

          while ($curr = $this->fs->_tree_walk_next($wlk)) {
	        $level = $this->fs->_tree_walk_level($wlk);
	        $spaces=str_repeat('--', $level-1);
	        $att = reset($attributes);
	        while($att){
	          if ($level!=0) {
	            if ($wlk['row']['file']!='') {
	              if (isset($_REQUEST[$name]) && $_REQUEST[$name] != '' && $wlk['row']['file'] == $_REQUEST[$name])
	                $mySelect->add_option($spaces.$wlk['row']['name'], $wlk['row']['file'], HAW_SELECTED);
	              else
	                $mySelect->add_option($spaces.$wlk['row']['name'], $wlk['row']['file'], HAW_NOTSELECTED);
	            }
	            else
	              $mySelect->add_option($spaces.'--'.'['.$wlk['row']['name'].']', '..', HAW_NOTSELECTED);
	          }
                $att = next($attributes);
          }
          }
          return $mySelect;
        }

        function _find_extension($filename)
        {
            $filename = strtolower($filename) ;
            $exts = explode(".", $filename) ;
            $n = count($exts)-1;
            $exts = $exts[$n];
            return $exts;
        }

  }
?>
