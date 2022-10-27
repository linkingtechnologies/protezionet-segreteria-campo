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


$camilaUI = new CamilaUserInterface();

  function camila_export_action_select()
  {
      global $_CAMILA;
      $mySelect = new CHAW_select('camila_export_action');

      require_once 'fs.class.php';

      $fs = new fs($_CAMILA['adm_user_group']);

      $mySelect->add_option(camila_get_translation('camila.export.download'), 'download');
      
      if (!CAMILA_FM_EXTFS_ENABLED) {
if (!CAMILA_FM_EXPORT_SAVETO_DISABLED)
{
          $mySelect->add_option(camila_get_translation('camila.export.saveto') . ' ' . camila_get_translation('camila.documents'), '/', $_REQUEST['camila_export_last_action'] == '/' ? HAW_SELECTED : HAW_NOTSELECTED);

          $arr = $fs->scandir(CAMILA_FM_ROOTDIR, true, false);
          sort($arr);
          foreach ($arr as $value) {
              $item = substr($value, strlen(CAMILA_FM_ROOTDIR) + 1);
              if ($item != camila_get_translation('camila.documents.recyclebin')) {
                  if (('/' . $item) == $_REQUEST['camila_export_last_action'])
                      $mySelect->add_option(camila_get_translation('camila.export.saveto') . ' ' . camila_get_translation('camila.documents') . '/' . $item, '/' . $item, HAW_SELECTED);
                  else
                      $mySelect->add_option(camila_get_translation('camila.export.saveto') . ' ' . camila_get_translation('camila.documents') . '/' . $item, '/' . $item);
              }
          }
}
      } else {
          $node = $fs->_tree_get_group_root_node($_CAMILA['adm_user_group']);
          $attributes = array('name');

          $wlk = $fs->_tree_walk_preorder($node);

          while ($curr = $fs->_tree_walk_next($wlk)) {
              $level = $fs->_tree_walk_level($wlk);
              $spaces = str_repeat('--', $level - 1);
              $att = reset($attributes);
              while ($att) {
                  if ($level == 0)
                      $mySelect->add_option(camila_get_translation('camila.export.saveto') . ' ' . camila_get_translation('camila.documents'), $wlk['row']['id']);
                  elseif ($wlk['row']['file'] == '') {
                      $mySelect->add_option(camila_get_translation('camila.export.saveto') . ' ' . camila_get_translation('camila.documents') . '/'. $spaces . $wlk['row'][$att], $wlk['row']['id']);
                  }
                  $att = next($attributes);
              }
          }
      }
      return $mySelect;
  }


  function camila_export_hidden_fields(&$myForm)
  {
      $export_format = '';
      foreach (array_merge($_POST, $_GET) as $key => $value) {
          if (!is_array($value)) {
              $myInput = new CHAW_hidden($key, $value);
              if ($key == 'camila_print' || $key == 'camila_rtf' || $key == 'camila_pdf' || $key == 'camila_xls' || $key == 'camila_csv' || $key == 'camila_pagnum' || $key == 'camila_ods')
                  $export_format = $key;
              else
                  $myForm->add_input($myInput);
          } else {
              foreach ($value as $k => $v) {
                  $myInput = new CHAW_hidden($key . '[' . $k . ']', $v);
                  $myForm->add_input($myInput);
              }
          }
      }
      return $export_format;
  }

  global $_CAMILA;

  if (isset($_REQUEST['camila_print']) && ($_REQUEST['camila_print'] == 'save')) {
      require_once('camila/fm/elements.php');
      $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.export.options'), HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);

      $export_deck_title = new CHAW_text(camila_get_translation('camila.export.options'), $_CAMILA['page_title_attributes']);
	  $export_deck_title->set_color('white', '#000080');
      $export_deck_title->set_br(2);
      //$export_deck_title->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
      $_CAMILA['page']->add_text($export_deck_title);
      
      $myForm = new CHAW_form($_SERVER['PHP_SELF']);

      $export_format = camila_export_hidden_fields($myForm);
      
      $myInput = new CHAW_input('camila_export_filename', $_CAMILA['page_full_title'], 'Nome del documento: ');
      $myInput->set_size(50);
      $myForm->add_input($myInput);
      $text = new CHAW_text('');
      $myForm->add_text($text);

      $mySelect = fm_select_group_dir('camila_export_dir', $_CAMILA['adm_user_group']);

      $text = new CHAW_text(camila_get_translation('camila.fm.intofolder') . ': ');
      $text->set_br(0);
      $myForm->add_text($text);
      $myForm->add_select($mySelect);
      $text = new CHAW_text('');
      $text->set_br(0);
      $myForm->add_text($text);

      $theSubmission = new CHAW_submit('Salva', 'camila_save');
	  $theSubmission->set_css_class('btn btn-md btn-primary');
      $myForm->add_submit($theSubmission);
      $_CAMILA['page']->add_form($myForm);

  } elseif ($_REQUEST['camila_export_action'] != '' && $_REQUEST['camila_export_action'] != 'download') {

      if (!$_CAMILA['page']->camila_export_file_exists || $_REQUEST['camila_export_overwrite'] == 'y') {

          if (CAMILA_FM_EXTFS_ENABLED) {
              require_once 'fs.class.php';
              global $_CAMILA;
              $fs = new fs($_CAMILA['adm_user_group']);
              $fs->_addfile($_CAMILA['adm_user_group'], $_REQUEST['camila_export_action'], substr($_CAMILA['camila_export_last_filename'], 0, -CAMILA_FM_PREFIX), $_CAMILA['camila_export_last_filename']);
          }

          if ($_REQUEST['camila_export_action'] == 'sendmail')
              $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.export.sendmail.ok'), HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);
          else
              $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.fm.filecreated'), HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);

          $myImage1 = new HAW_image(CAMILA_IMG_DIR . 'wbmp/accept.wbmp', CAMILA_IMG_DIR . 'png/accept.png', '-');
          $myImage1->set_br(0);
          $_CAMILA['page']->add_image($myImage1);

          if ($_REQUEST['camila_export_action'] == 'sendmail')
              $export_deck_title = new CHAW_text(' ' . camila_get_translation('camila.export.sendmail.ok'));
          else
              $export_deck_title = new CHAW_text(' ' . camila_get_translation('camila.fm.filecreated'));

          $export_deck_title->set_br(2);
          $_CAMILA['page']->add_text($export_deck_title);

          if ($_REQUEST['camila_export_action'] != 'sendmail') {
              $url = $_CAMILA['page']->camila_export_download_link();
              $myLink = new CHAW_link(camila_get_translation('camila.export.download'), $url);
              $myImage1 = new HAW_image(CAMILA_IMG_DIR . 'wbmp/file.wbmp', CAMILA_IMG_DIR . 'png/file.png', '-');
              $myLink->add_image($myImage1);
              $_CAMILA['page']->add_link($myLink);

              $url = 'cf_docs.php';
              $myLink = new CHAW_link(camila_get_translation('camila.documents'), $url);
              $myImage3 = new HAW_image(CAMILA_IMG_DIR . 'wbmp/folder_go.wbmp', CAMILA_IMG_DIR . 'png/folder_go.png', '-');
              $myLink->add_image($myImage3);
              $_CAMILA['page']->add_link($myLink);
          }

          $url = $_SERVER['PHP_SELF'] . '?' . $_REQUEST['hidden'];
          $url = ereg_replace("&" . $_REQUEST['camila_export_format'], '', $url);
          $url = ereg_replace("\?" . $_REQUEST['camila_export_format'], '', $url);
          $myLink = new CHAW_link(camila_get_translation('camila.back.page'), $url);
          $myImage2 = new HAW_image(CAMILA_IMG_DIR . 'wbmp/resultset_previous.wbmp', CAMILA_IMG_DIR . 'png/resultset_previous.png', '-');
          $myLink->set_css_class('btn btn-md btn-default');
		  $myLink->add_image($myImage2);
          
		  $_CAMILA['page']->add_link($myLink);
      } else {

          $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.fm.fileexists'), HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);

          $myImage1 = new HAW_image(CAMILA_IMG_DIR . 'wbmp/bullet_error.wbmp', CAMILA_IMG_DIR . 'png/bullet_error.png', '-');
          $myImage1->set_br(0);
          $_CAMILA['page']->add_image($myImage1);

          $export_deck_title = new CHAW_text(' ' . camila_get_translation('camila.fm.fileexists'));
          $export_deck_title->set_br(2);
          $_CAMILA['page']->add_text($export_deck_title);
          
          $url = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&' . 'camila_export_overwrite=y';
          $myLink = new CHAW_link(camila_get_translation('camila.fm.fileexists.overwrite'), $url);
          //    $myImage3 = new HAW_image(CAMILA_IMG_DIR.'wbmp/folder_go.wbmp', CAMILA_IMG_DIR.'png/folder_go.png', '-');
          //    $myLink->add_image($myImage3);
          $_CAMILA['page']->add_link($myLink);
          
          $url = $_SERVER['PHP_SELF'] . '?' . $_REQUEST['hidden'] . '&camila_export_last_action=' . urlencode($_REQUEST['camila_export_action']);
          //echo $url;
          $myLink = new CHAW_link(camila_get_translation('camila.fm.fileexists.changename'), $url);
          //    $myImage3 = new HAW_image(CAMILA_IMG_DIR.'wbmp/folder_go.wbmp', CAMILA_IMG_DIR.'png/folder_go.png', '-');
          //    $myLink->add_image($myImage3);
          $_CAMILA['page']->add_link($myLink);
          
          $url = $_SERVER['PHP_SELF'] . '?' . $_REQUEST['hidden'];
          $url = ereg_replace("&" . $_REQUEST['camila_export_format'], '', $url);
          $url = ereg_replace("\?" . $_REQUEST['camila_export_format'], '', $url);
          $myLink = new CHAW_link(camila_get_translation('camila.fm.fileexists.cancel'), $url);
          $myImage2 = new HAW_image(CAMILA_IMG_DIR . 'wbmp/resultset_previous.wbmp', CAMILA_IMG_DIR . 'png/resultset_previous.png', '-');
          $myLink->add_image($myImage2);
          $myLink->set_css_class('btn btn-md btn-default');
		  $_CAMILA['page']->add_link($myLink);
      }

      require(CAMILA_DIR . 'deck_settings.php');
      $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR . 'skin0.css');
      $_CAMILA['page']->create_page();
      exit;
  } else {  
      $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.export.options'), HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);
      
      //$export_deck_title = new CHAW_text(camila_get_translation('camila.export.options'), $_CAMILA['page_title_attributes']);
      //$export_deck_title->set_br(2);
      //$export_deck_title->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
	  //$export_deck_title->set_color('white', '#000080');
      //$_CAMILA['page']->add_text($export_deck_title);
	  
	  $camilaUI->insertTitle(camila_get_translation('camila.export.options'),'cog');
	  $camilaUI->insertLineBreak();

      $myForm = new CHAW_form($_SERVER['PHP_SELF']);
      $export_format = camila_export_hidden_fields($myForm);
      
      if (!isset($_REQUEST['camila_xls']) && !isset($_REQUEST['camila_ods']) && !isset($_REQUEST['camila_csv'])) {
          $myInput = new CHAW_input('camila_export_title', $_CAMILA['page_full_title'], camila_get_translation('camila.export.pagetitle'));
          $myInput->set_size(50);
          $myForm->add_input($myInput);
          $text = new CHAW_text('');
          $myForm->add_text($text);

          $text = new CHAW_text(camila_get_translation('camila.export.pagedim'));
          $text->set_br(0);
          $myForm->add_text($text);
          $mySelect = new CHAW_select('camila_page_dim');
          $mySelect->add_option('A4', 'a4', HAW_SELECTED);
          $mySelect->add_option('A3', 'a3');
          $myForm->add_select($mySelect);
          $text = new CHAW_text('');
          $text->set_br(2);
          $myForm->add_text($text);
          
          $text = new CHAW_text(camila_get_translation('camila.export.pageor'));
          $text->set_br(0);
          $myForm->add_text($text);
          $mySelect = new CHAW_select('camila_page_orient');
          $mySelect->add_option(camila_get_translation('camila.export.pageor.v'), 'v', HAW_SELECTED);
          $mySelect->add_option(camila_get_translation('camila.export.pageor.o'), 'o');
          $myForm->add_select($mySelect);
          $text = new CHAW_text('');
          $text->set_br(2);
          $myForm->add_text($text);
      }
      
      $text = new CHAW_text('');
      $myForm->add_text($text);
      $myCheckbox = new HAW_checkbox('camila_visible_cols_only', 'y', camila_get_translation('camila.export.onlyvisiblecols'), HAW_SELECTED);
      $myForm->add_checkbox($myCheckbox);
      $text = new CHAW_text('');
      $text->set_br(1);
      $myForm->add_text($text);
      
      if (substr($_SERVER['PHP_SELF'], 0, 12) == 'cf_worktable' && substr($_SERVER['PHP_SELF'], 0, 13) != 'cf_worktable_' && (isset($_REQUEST['camila_xls']) || isset($_REQUEST['camila_ods']))) {
          $mySelect = new CHAW_select('camila_worktable_export');
          $mySelect->add_option(camila_get_translation('camila.worktable.export.all'), 'all', HAW_SELECTED);
          $mySelect->add_option(camila_get_translation('camila.worktable.export.dataonly'), 'dataonly');
          $mySelect->add_option(camila_get_translation('camila.worktable.export.confonly'), 'confonly');
          $myForm->add_select($mySelect);
          $text = new CHAW_text('');
          $text->set_br(2);
          $myForm->add_text($text);
      }
      
      if (!isset($_REQUEST['camila_pagnum']) || ((isset($_REQUEST['camila_pagnum']) && $_REQUEST['camila_pagnum'] != '-1'))) {
          $text = new CHAW_text(camila_get_translation('camila.export.manypages'));
          $text->set_br(0);
          
          $myForm->add_text($text);
          $mySelect = new CHAW_select('camila_pagnum');
          if (isset($_REQUEST['camila_pagnum']))
              $mySelect->add_option(camila_get_translation('camila.export.manypages.cur'), $_REQUEST['camila_pagnum']);
          else
              $mySelect->add_option(camila_get_translation('camila.export.manypages.cur'), '1');
          $mySelect->add_option(camila_get_translation('camila.export.manypages.all'), '-1', HAW_SELECTED);
          $myForm->add_select($mySelect);
          
          $text = new CHAW_text('');
          $text->set_br(2);
          $myForm->add_text($text);
      } else {
          $myInput = new CHAW_hidden('camila_pagnum', '-1');
          $myForm->add_input($myInput);
      }
      
      $myInput = new CHAW_input('camila_export_filename', $_CAMILA['page']->camila_export_suggested_filename(), camila_get_translation('camila.export.filename'));
      $myInput->set_size(50);
      $myInput->set_br(0);
      $myForm->add_input($myInput);
      $text = new CHAW_text('.' . $_CAMILA['page']->camila_export_get_ext());
      $myForm->add_text($text);
      
      $text = new CHAW_text(camila_get_translation('camila.export.action'));
      $text->set_br(0);
      $myForm->add_text($text);
      
      $myForm->add_select(camila_export_action_select());
      $text = new CHAW_text('');
      $text->set_br(2);
      $myForm->add_text($text);

      $myInput = new CHAW_hidden('hidden', $_SERVER['QUERY_STRING']);
      $myForm->add_input($myInput);
      
      $myInput = new CHAW_hidden('camila_export_format', $export_format);
      $myForm->add_input($myInput);
      
      $theSubmission = new CHAW_submit(camila_get_translation('camila.exportbutton'), $export_format);
	  $theSubmission->set_css_class('btn btn-md btn-primary');
      $myForm->add_submit($theSubmission);
      $_CAMILA['page']->add_form($myForm);
	  
	  $myText = new CHAW_text('');
	  $_CAMILA['page']->add_text($myText);
      
      $url = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
      $url = preg_replace("&" . $export_format, '', $url);
      $url = preg_replace("\?" . $export_format, '', $url);
      $myLink = new CHAW_link(camila_get_translation('camila.back.page'), $url);
      $myImage2 = new HAW_image(CAMILA_IMG_DIR . 'wbmp/resultset_previous.wbmp', CAMILA_IMG_DIR . 'png/resultset_previous.png', '-');
      $myLink->add_image($myImage2);
	  $myLink->set_css_class('btn btn-md btn-default');
      $_CAMILA['page']->add_link($myLink);
  }
  
  $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR . 'skin0.css');
  
  require(CAMILA_DIR . 'deck_settings.php');
  require(CAMILA_DIR . 'footer.php');
  exit();
?>