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


  class form_fm_dir_listbox extends form_field
  {
      var $usergroup;
      var $fs;

      function __construct(&$form, $field, $title, $usergroup, $required, $validation)
      {
          $this->form_field($form, $field, $title, $required, $validation);
          $this->usergroup = $usergroup;
          $this->fs = new fs($usergroup);
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

          $node = $this->fs->_tree_get_group_root_node($this->usergroup);
          $attributes = array('name');

          $wlk = $this->fs->_tree_walk_preorder($node);

          while ($curr = $this->fs->_tree_walk_next($wlk)) {
              $level = $this->fs->_tree_walk_level($wlk);
              $spaces = str_repeat('--', $level - 1);
              $att = reset($attributes);
              while ($att) {
                  if ($level == 0) 
                      $mySelect->add_option('(' . camila_get_translation('camila.fm.selectfolder') . ')', $wlk['row']['id']);
                  elseif ($wlk['row']['file'] == '') {
                      if ($wlk['row']['id'] == $_REQUEST['camila_dir'])
                          $mySelect->add_option($spaces . $wlk['row'][$att], $wlk['row']['id'], HAW_SELECTED);
                      else
                          $mySelect->add_option($spaces . $wlk['row'][$att], $wlk['row']['id']);
                  }
                  $att = next($attributes);
              }
          }

          return $mySelect;
      }

  }
?>
