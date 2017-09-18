<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2008 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */



  class form_generate_password extends form_textbox
  {
      function form_generate_password(&$form, $field, $title, $size = CAMILA_PASSWORD_LENGTH, $required=true, $type='numeric',$length = CAMILA_PASSWORD_LENGTH)
      {
          $this->form_textbox($form, $field, $title, $required, $size, $length, 'FIT');
          $this->pwdtype = $type;
          $this->pwdlength = $length;

          $form->add($this);
      }

      function draw(&$form)
      {
	      parent::draw($form);

	      if ($this->updatable) {
    	      global $_CAMILA;
    	      $code = '<script type="text/javascript" src="camila/js/camila_security.js"></script>';
    	      $_CAMILA['page']->camila_add_js($code,'camila_security');
    	      $js = new CHAW_js("<script>document.write('<a href=\'javascript:void(0)\' onclick=\'document.forms[0].".$this->key.".value=camila_generate_password(".$this->pwdlength.",\"".$this->type."\");\'>Genera codice casuale</a>')</script>");
    	      $form->add_userdefined($js);
	      }
      }

  }
?>
