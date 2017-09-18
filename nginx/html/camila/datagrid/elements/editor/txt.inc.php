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

  require_once(CAMILA_DIR . 'datagrid/elements/form/editarea_textarea.php');

  $init = 'save_callback:"camila_editarea_save",gecko_spellcheck:true,language: "' . CAMILA_LANG . '",toolbar:"save,|,search,|,undo,redo,|,select_font"';
  $c = new form_editarea_textarea($form, 'content', $name, false, $init, 20, 80);
  $form->fields['content']->value = $content;
?>