<?php
  /* This File is part of Camila PHP Framework
   Copyright (C) 2006-2009 Umberto Bresciani
   
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

// $form
// $name
// $content
// $file

  require_once(CAMILA_DIR . 'datagrid/elements/form/tinymce_textarea.php');

  $init = 'language: "' . CAMILA_LANG . '",';
  $init .= 'mode : "textareas", theme : "advanced",';
  $init .= 'plugins : "save,fullpage,safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",';
  $init .= 'theme_advanced_buttons1 : "save,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",';
  $init .= 'theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",';
  $init .= 'theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,advhr,|,print,|,ltr,rtl,|,fullscreen",';
  $init .= 'theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,|,visualchars,nonbreaking,template,pagebreak",';
  $init .= 'theme_advanced_toolbar_location : "top",';
  $init .= 'theme_advanced_toolbar_align : "left",';
  //$init.='theme_advanced_statusbar_location : "bottom",';
  $init .= 'theme_advanced_resizing : true,';
  $init .= 'content_css : "' . CAMILA_LIB_DIR . 'tinymce/examples/css/word.css",';
  // Drop lists for link/image/media/template dialogs
  $init .= 'template_external_list_url : "lists/template_list.js",';
  $init .= 'external_link_list_url : "lists/link_list.js",';
  $init .= 'external_image_list_url : "lists/image_list.js",';
  $init .= 'media_external_list_url : "lists/media_list.js",';
  $init .= 'valid_elements : "*[*]"';
  //$init.='init_instance_callback : "resizeEditorBox"';
  $c = new form_tinymce_textarea($form, 'content', $name, false, $init, 20, 80);
  $form->fields['content']->value = $content;
?>