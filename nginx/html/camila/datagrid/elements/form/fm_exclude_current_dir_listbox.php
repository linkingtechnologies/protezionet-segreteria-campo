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




  class form_fm_exclude_current_dir_listbox extends form_fm_dir_listbox
  {
      var $node;

      function __construct(&$form, $field, $title, $node, $usergroup, $required, $validation)
      {
          parent::__construct($form, $field, $title, $usergroup, $required, $validation);
          $this->node = $node;
          $form->add($this);
      }

      function _get_select()
      {
          $exclude = array();
          $wlk = $this->_tree_walk_preorder($this->node);
          while (!$wlk['recset']->EOF) {
              $row = $wlk['recset']->fields;
              $exclude[$row['id']] = 'yes';
              $wlk['recset']->MoveNext();
          }

          $mySelect = new CHAW_select($this->key);

          $node = $this->_tree_get_group_root_node($this->usergroup);
          $attributes = array('name');

          $wlk = $this->_tree_walk_preorder($node);

          while ($curr = $this->_tree_walk_next($wlk)) {
              $level = $this->_tree_walk_level($wlk);
              $spaces = str_repeat('--', $level - 1);
              $att = reset($attributes);
              while ($att) {
                  if ($level == 0)
                      $mySelect->add_option('(seleziona cartella)', $wlk['row']['id']);
                  elseif ($wlk['row']['file'] == '' && !isset($exclude[$wlk['row']['id']]))
                      $mySelect->add_option($spaces . $wlk['row'][$att], $wlk['row']['id']);
                  $att = next($attributes);
              }
          }

          return $mySelect;
      }

  }
?>
