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


  class fs
  {
      var $stmt;
      var $usergroup;


      function __construct($usergroup = '')
      {
          //$this->stmt=$stmt;
          $this->usergroup = $usergroup;
      }


      function scandir($directory, $recursive = true, $showFiles = true)
      {
          $array_items = array();
          if ($handle = opendir($directory)) {
              while (false !== ($file = readdir($handle))) {
                  if ($file != "." && $file != "..") {
                      if (is_dir($directory . "/" . $file)) {
                          if ($recursive) {
                              $array_items = array_merge($array_items, $this->scandir($directory . "/" . $file, $recursive, $showFiles));
                          }
                          $file = $directory . "/" . $file;
                          
                          $array_items[] = preg_replace("/\/\//si", "/", $file);
                      } elseif ($showFiles) {
                          $file = $directory . "/" . $file;
                          $array_items[] = preg_replace("/\/\//si", "/", $file);
                      }
                  }
              }
              closedir($handle);
          }
          return $array_items;
      }


      function _getmimetype($filename)
      {
          $pos = strrpos($filename, '.');
          if ($pos === false) {
              camila_get_translation('camila.mimetype.unknownfile');
          } else {
              if (camila_get_translation('camila.mimetype' . substr($filename, $pos)) != '')
                  return camila_get_translation('camila.mimetype' . substr($filename, $pos));
              else
                  return camila_get_translation('camila.mimetype.unknownfile');
          }
      }


      function _getmimetype_icon($filename)
      {
          if ($filename == '..') {
              return CAMILA_IMG_DIR . 'png/mt_folder.png';
          } elseif ($filename == 'up') {
              return CAMILA_IMG_DIR . 'png/mt_folder_up.png';
          } else {
              $pos = strrpos($filename, '.');
              if ($pos === false) {
                  return CAMILA_IMG_DIR . 'png/mt_unknown.png';
              } else {
                  if (is_file(CAMILA_IMG_DIR . 'png/mt_' . substr($filename, $pos + 1) . '.png'))
                      return CAMILA_IMG_DIR . 'png/mt_' . substr($filename, $pos + 1) . '.png';
                      //return 'mt_' . substr($filename, $pos + 1);
                  else
                      return CAMILA_IMG_DIR . 'png/mt_unknown.png';
              }
          }
      }


      function _convertfilesize($size)
      {
          if ($size >= 1073741824) {
              $size = round($size / 1073741824 * 100) / 100 . ' GB';
          } elseif ($size >= 1048576) {
              $size = round($size / 1048576 * 100) / 100 . ' MB';
          } elseif ($size >= 1024) {
              $size = round($size / 1024 * 100) / 100 . ' KB';
          } else
              $size = $size . ' B';
          
          return str_replace('.', camila_get_translation('camila.decimal.separator'), $size);
      }


      function _createdir($usergroup, $dir, $name)
      {
          $this->_tree_new_first_child($this->_tree_get_node_by_id($dir), '', $name, $usergroup);
      }


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
          $_CAMILA['db']->Execute('UPDATE ' . CAMILA_TABLE_FILES . " SET lft=lft+$delta WHERE lft>=$first");
          $_CAMILA['db']->Execute('UPDATE ' . CAMILA_TABLE_FILES . " SET rgt=rgt+$delta WHERE rgt>=$first");
          $_CAMILA['db']->CompleteTrans();
      }


      function _tree_shift_RL_range($first, $last, $delta)/* adds '$delta' to all L and R values that are >= '$first' and <= '$last'. '$delta' can also be negative.
       returns the shifted first/last values as node array.
       */
      {
          global $_CAMILA;
          $_CAMILA['db']->StartTrans();
          $_CAMILA['db']->Execute('UPDATE ' . CAMILA_TABLE_FILES . " SET lft=lft+$delta WHERE lft>=$first AND lft<=$last");
          $_CAMILA['db']->Execute('UPDATE ' . CAMILA_TABLE_FILES . " SET rgt=rgt+$delta WHERE rgt>=$first AND rgt<=$last");
          $_CAMILA['db']->CompleteTrans();
          return array('l' => $first + $delta, 'r' => $last + $delta);
      }


      function _tree_insert_new_node($node, $filename = '', $name = '', $usergroup = '')
      {
          global $_CAMILA;
          $res = $_CAMILA['db']->Execute('INSERT INTO ' . CAMILA_TABLE_FILES . ' (lft,rgt,file,name,usergroup) VALUES (?,?,?,?,?)', array($node['l'], $node['r'], $filename, $name, $usergroup));
          if ($res === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
      }


      function _tree_new_root($filename, $name, $usergroup)
      {
          $newnode['l'] = 1;
          $newnode['r'] = 2;
          $this->_tree_insert_new_node($newnode, $filename, $name, $usergroup);
          return $newnode;
      }


      function _tree_get_node_by_id($id)
      {
          $noderes['l'] = 0;
          $noderes['r'] = 0;
          global $_CAMILA;
          
          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          
          $res = $_CAMILA['db']->Execute('SELECT * FROM ' . CAMILA_TABLE_FILES . ' WHERE id = ?', array($id));
          
          if ($res === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          else {
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
                  $noderes['l'] = $row['lft'];
                  $noderes['r'] = $row['rgt'];
              }
          }
          return $noderes;
      }


      function _tree_valid_node($node)
      {
          return($node['l'] < $node['r']);
      }


      function _tree_checkup()
      {
          if (!$this->_tree_valid_node($this->_tree_get_group_root_node('files'))) {
              $this->_tree_new_root('', 'files', '');
          }
          
          if (!$this->_tree_valid_node($this->_tree_get_group_root_node($this->usergroup))) {
              $this->_tree_new_first_child($this->_tree_get_group_root_node('files'), '', $this->usergroup, '');
          }
      }


      function _tree_get_file_subtree($node, $attributes, $usergroup)
      {
          $arr = array();
          
          $wlk = $this->_tree_walk_preorder($node);
          while ($curr = $this->_tree_walk_next($wlk)) {
              $level = $this->_tree_walk_level($wlk);
              $spaces = str_repeat('  ', $level);
              $att = reset($attributes);
              
              while ($att) {
                  $id = $wlk['row']['id'];
                  $file = $wlk['row']['file'];
                  $name = $wlk['row']['name'];
                  
                  if ($level == 0 && $wlk['row']['usergroup'] == $usergroup)
                      $arr[$id] = '..';
                  
                  if ($level == 1) {
                      if ($file != '')
                          $arr[$id] = $file;
                      else
                          $arr[$id] = $name;
                  }
                  
                  $att = next($attributes);
              }
          }
          return $arr;
      }


      function _tree_walk_preorder($node)
      {
          global $_CAMILA;
          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          
          $res = $_CAMILA['db']->Execute('SELECT * FROM ' . CAMILA_TABLE_FILES . " WHERE lft>=" . $node['l'] . " AND rgt<=" . $node['r'] . " ORDER BY lft");
          
          if ($res === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          
          //returns a walk handler
          return array('recset' => $res, 'prevl' => $node['l'], 'prevr' => $node['r'], // needed to efficiently calculate the level
          'level' => -2);
      }


      function _tree_walk_next(&$walkhand)
      {
          if (!$walkhand['recset']->EOF) {
              // calc level
              $row = $walkhand['recset']->fields;
              $walkhand['level'] += $walkhand['prevl'] - $row['lft'] + 2;
              // store current node
              $walkhand['prevl'] = $row['lft'];
              $walkhand['prevr'] = $row['lft'];
              $walkhand['row'] = $row;
              $walkhand['recset']->MoveNext();
              return array('l' => $row['lft'], 'r' => $row['rgt']);
          } else {
              return false;
          }
      }


      function _tree_walk_level($walkhand)
      {
          return $walkhand['level'];
      }


      function _tree_get_ancestor_node($node)
      {
          $noderes['l'] = 0;
          $noderes['r'] = 0;
          global $_CAMILA;
          
          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          
          $res = $_CAMILA['db']->Execute('SELECT * FROM ' . CAMILA_TABLE_FILES . ' WHERE lft<' . $node['l'] . ' AND rgt>' . $node['r'] . ' ORDER BY rgt');
          
          if ($res === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          else {
              if (!$res->EOF) {
                  $row = $res->fields;
                  $noderes['l'] = $row['lft'];
                  $noderes['r'] = $row['rgt'];
                  $noderes['id'] = $row['id'];
                  $noderes['usergroup'] = $row['usergroup'];
              }
          }
          return $noderes;
      }


      function _tree_delete_files($node, $attributes)
      {
          $arr = array();
          
          $dir = $this->stmt . '/' . $this->usergroup;
          
          $wlk = $this->_tree_walk_preorder($node);
          while ($curr = $this->_tree_walk_next($wlk)) {
              $level = $this->_tree_walk_level($wlk);
              $spaces = str_repeat('  ', $level);
              $att = reset($attributes);
              
              while ($att) {
                  $id = $wlk['row']['id'];
                  $file = $wlk['row']['file'];
                  $name = $wlk['row']['name'];
                  $item = $dir . '/' . $file;
                  
                  if ($file != '' && (@is_link($item) || @is_file($item)))
                      $ok = @unlink($item);
                  
                  $att = next($attributes);
              }
          }
          
          global $_CAMILA;
          $leftanchor = $node['l'];
          $res = $_CAMILA['db']->Execute('DELETE FROM ' . CAMILA_TABLE_FILES . ' WHERE lft>=' . $node['l'] . ' AND rgt<=' . $node['r']);
          
          if ($res === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          
          $this->_tree_shift_RL_values($node['r'] + 1, $node['l'] - $node['r'] - 1);
          
          //return tree_get_node_where ($camila_tree, $camila_tree['lvalname']."<".$leftanchor ." ORDER BY ".$camila_tree['lvalname']." DESC");
      }


      function _tree_get_node_by_filename($filename, $usergroup)
      {
          $noderes['l'] = 0;
          $noderes['r'] = 0;
          global $_CAMILA;
          
          $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
          
          $res = $_CAMILA['db']->Execute('SELECT * FROM ' . CAMILA_TABLE_FILES . ' WHERE file = ? AND usergroup = ?', array($filename, $usergroup));
          
          if ($res === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          else {
              if (!$res->EOF) {
                  $row = $res->fields;
                  $noderes['l'] = $row['lft'];
                  $noderes['r'] = $row['rgt'];
              }
          }
          return $noderes;
      }


      function _tree_move_to_first_child($src, $dst)
      {
          return $this->_tree_move_subtree($src, $dst['l'] + 1);
      }


      function _tree_move_to_last_child($src, $dst)
      {
          return $this->_tree_move_subtree($src, $dst['r']);
      }


      function _tree_move_subtree($src, $to)/* '$src' is the node/subtree, '$to' is its destination l-value */
      {
          $treesize = $src['r'] - $src['l'] + 1;
          $this->_tree_shift_RL_values($to, $treesize);
          if ($src['l'] >= $to) {
              // src was shifted too?
              $src['l'] += $treesize;
              $src['r'] += $treesize;
          }
          /* now there's enough room next to target to move the subtree*/
          $newpos = $this->_tree_shift_RL_range($src['l'], $src['r'], $to - $src['l']);
          /* correct values after source */
          $this->_tree_shift_RL_values($src['r'] + 1, -$treesize);
          if ($src['l'] <= $to) {
              // dst was shifted too?
              $newpos['l'] -= $treesize;
              $newpos['r'] -= $treesize;
          }
          return $newpos;
      }
  }
?>