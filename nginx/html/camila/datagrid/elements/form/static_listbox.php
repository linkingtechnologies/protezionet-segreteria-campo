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
   along with Camila Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */




class form_static_listbox extends form_field {

    var $options = array();
    var $options2 = array();

    // options can be an array or string
    function form_static_listbox(&$form, $field, $title, $options, $required = false, $validation = '')
    {
        $this->options2 = array();
        $this->form_field($form, $field, $title, $required, $validation);
        if (is_array($options))
            $this->options = $options;
        else {
            $tok = strtok ($options, ',');
            while( $tok ) {
                $pos = strpos($tok, ';');
                if ($pos === false) {
                    $arr = array($tok, $tok);
                    $this->options[] = $arr;
                    $this->options2[$arr[0]]=$arr[1];
                } else {
                    $arr = array(substr($tok, 0, $pos), substr($tok, $pos + 1));
                    $this->options[] = $arr;
                    $this->options2[$arr[0]]=$arr[1];
                }
                $tok = strtok (',');
                }
            }

        global $_CAMILA;
        $_CAMILA['options'][$field] = $this->options2;

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
        
		if (trim($this->title) != '')
			$form->add_text($myText);

        if ($this->updatable) {
	        $mySelect = new CHAW_select($this->key);
	        while( $tok = each($this->options) ) {
	            $selected = ($tok[1][0] == $this->value) ? HAW_SELECTED : HAW_NOTSELECTED;
	            $mySelect->add_option($tok[1][1], $tok[1][0], $selected);
	        }

	        $form->add_select($mySelect);
	        $text = new CHAW_text('');
			$text->set_br($this->br);
	        $form->add_text($text);
        }
	}


    function process()
    {
		//echo $this->key;
		//print_r($_REQUEST);
        if($this->updatable && isset($_REQUEST[$this->key])) {
            $this->value = isUTF8($_REQUEST[$this->key]) ? $_REQUEST[$this->key] : utf8_encode($_REQUEST[$this->key]);
        }
    }


    function validate()
    {
        if (!$this->updatable || strpos(strtolower($this->validation),'ignore')!==false)
            return true;

        parent::validate();

        $found = false;

        reset ($this->options);
        while( $tok = each($this->options) ) {
            $curr = isUTF8($tok[1][0]) ? $tok[1][0] : utf8_encode($tok[1][0]);
            if ($curr == $this->value)
                $found = true;

        }

        if (!$found)
            $this->form->validator->setError($this->field, 901);
    }

}
?>
