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



class form_text_separator extends form_field {

  function form_text_separator(&$form, $text, $textformat = HAW_TEXTFORMAT_BOLD)
  {
    $this->form_field($form, $field, $title);
    $this->process = false;
    $this->dummy = true;
    $this->updatable = false;
    $this->field = 'camila_text_separator_' . preg_replace('/[^a-zA-Z0-9]/', '', $text);
    $this->text = $text;
    $this->textformat = $textformat;
    $form->add($this);
  }

  function draw(&$form)
  {
      parent::draw($form);

      if (isset($_REQUEST['camila_print']))
        return;	

      $text = new CHAW_text('');
      $form->add_text($text);

      if ($form->_collapsible_pending)
          $form->camila_collapsible_end();

      $form->camila_collapsible_start('db_form', true, $this->text);
      $form->_collapsible_pending = true;
  }

  function process()
  {
      if( !$this->process )
          return;
  }

}
