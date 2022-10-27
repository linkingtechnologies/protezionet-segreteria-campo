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


  require_once(CAMILA_DIR . 'datagrid/form.class.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/filebox.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/checklist.php');

  global $_CAMILA;

  $export_deck_title = new CHAW_text(camila_get_translation('camila.export.options'), $_CAMILA['page_title_attributes']);
  $export_deck_title->set_br(2);
  $export_deck_title->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
  $_CAMILA['page']->add_text($export_deck_title);

  $form = new phpform('camila_bookmark');
  $form->submitbutton = camila_get_translation('camila.save');
  $form->drawrules = false;
  $form->preservecontext = true;

  global $_CAMILA;

  new form_hidden($form, 'base_url', basename($_SERVER['PHP_SELF']));
  new form_textbox($form, 'title', 'Titolo', true, 50, 50);

  $export_format = 'camila_bookmark';
  //$url = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
  //$url = ereg_replace("&" . $export_format, '', $url);
  //$url = ereg_replace("\?" . $export_format, '', $url);
  $url = $_SERVER['PHP_SELF'] . '?filter=' . $_REQUEST['camila_bookmark'];

  new form_hidden($form, 'url', $url);


  if ($form->process())
  {
        $record=Array();
        $record['id']=$_CAMILA['db']->GenID(CAMILA_APPLICATION_PREFIX.'bookmarkseq', 10000).camila_hash(10);
        $record['lang']=$_CAMILA['lang'];
        $record['url']=$form->fields['url']->value;
        $record['base_url']=$form->fields['base_url']->value;
        $record['title']=$form->fields['title']->value;

        $insertSQL = $_CAMILA['db']->AutoExecute(CAMILA_APPLICATION_PREFIX.'camila_bookmarks', $record, 'INSERT');
        if (!$insertSQL) {
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
            $success = false;
        }

        $url = $form->fields['url']->value;

  }
    else
      $form->draw();

  $myLink = new CHAW_link(camila_get_translation('camila.back.page'), $url);
  $myImage = new HAW_image(CAMILA_IMG_DIR.'wbmp/resultset_previous.wbmp', CAMILA_IMG_DIR.'png/resultset_previous.png', '-');
  $myLink->add_image($myImage);
  $_CAMILA['page']->add_link($myLink);


  $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR . 'skin0.css');

  require(CAMILA_DIR . 'deck_settings.php');
  require(CAMILA_DIR . 'footer.php');
  exit();
?>