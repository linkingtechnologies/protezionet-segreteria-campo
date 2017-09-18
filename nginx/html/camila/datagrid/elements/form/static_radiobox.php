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




class form_static_radiobox extends form_field {
    // array of value, text
    var $options = array();

    function form_static_radiobox(&$form, $field, $title, $options)
    {
        $this->form_field($form, $field, $title);
        if( is_array($options) )
            $this->options = $options;
        else {
            $tok = strtok ($options, ',');
            while( $tok )
            {
                $pos = strpos($tok, ';');
                if ($pos === false) {
                    $this->options[] = array($tok, $tok);
                } else {
                    $this->options[] = array(substr($tok, 0, $pos), substr($tok, $pos + 1) );
                }
                $tok = strtok (',');
            }
        }
        $form->add($this);
    }


    function draw(&$form)
    {
        parent::draw($form);
        reset ($this->options);
        if ($this->updatable) {
            $myText = new CHAW_text($this->title.$this->labelseparator);
            $myText->set_br(0);
        }
        else {
            while( $tok = each($this->options) ) {
		//$mySelect->add_option($tok[1][1], $tok[1][0], $selected);
            if ($tok[1][0] == $this->value)
		$value = $tok[1][1];
            }

            $myText = new CHAW_text($this->title.$this->labelseparator.' '.$value);
        }
        $form->add_text($myText);

        if ($this->updatable) {
	        $mySelect = new CHAW_select($this->key);
	        while( $tok = each($this->options) ) {
	            $selected = ($tok[1][0] == $this->value) ? HAW_SELECTED : HAW_NOTSELECTED;
	            $mySelect->add_option($tok[1][1], $tok[1][0], $selected);
	        }

	        $form->add_select($mySelect);
	        $text = new CHAW_text('');
	        $form->add_text($text);
        }
     }



    function process()
    {
        if( isset($_POST[$this->key]) ) {
            $this->value = $_POST[$this->key];
        }
    }
}
?>
