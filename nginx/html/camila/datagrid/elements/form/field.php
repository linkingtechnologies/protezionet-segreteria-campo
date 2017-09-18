<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2017 Umberto Bresciani

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


class form_field {

  var $form;
  var $field;
  var $title;
  var $size;
  var $maxlength;
  var $cols;
  var $rows;
  var $type;
  var $value;
  var $key;
  var $hidden = false;
  var $metatype;
  var $defaultvalue;

  var $updatable = true;
  var $process = true;
  var $cleanable = true;
  var $dummy = false;

  var $labelseparator;

  var $autosuggest_maxresults = 5;
  var $autosuggest_script = 'index.php';
  var $autosuggest_advanced_script = '';
  var $autosuggest_callback;
  var $autosuggest_timeout = 5000;
  var $autosuggest_delay = 100;
  var $autofocus = false;

  var $write_value_to_file = '';
  
  var $br = 1;
  
  var $css_class;
  
  var $help;

  function form_field(&$form, $field, $title, $required=false, $validation='')
  {
    $this->form = &$form;
    $this->field = $field;
    $this->title = $title;
    if ($this->form->name != '')
        $this->key = $this->form->name . '_' . $this->field;
    else
        $this->key = $this->field;
    $this->required = $required;
    $this->validation = $validation;
  }

  function draw(&$form)
  {
      global $_CAMILA;
      $this->labelseparator = camila_get_translation('camila.formfieldseparator');
      if ($this->required && $this->updatable && !$_CAMILA['page']->camila_exporting())
          $this->labelseparator = camila_get_translation('camila.preformfieldrequired') . $this->labelseparator . camila_get_translation('camila.postformfieldrequired');

if ($this->autosuggest_advanced_script == '')
      $this->autosuggest_callback = 'camila_autosuggest_cb_' . $this->field;

      if ($this->updatable && $this->autosuggest_table!='') {
          $code  = ( '<link rel="stylesheet" type="text/css" media="all" href="' .CAMILA_LIB_DIR .'autosuggest/css/autosuggest_inquisitor.css?v=271115" />');
          $code .= ( '<script src=\''.CAMILA_LIB_DIR.'autosuggest/js/bsn.AutoSuggest_2.1.3.js?v=271115\'></script>' );
          $_CAMILA['page']->camila_add_js($code,'autosuggest');

          if ($this->autosuggest_advanced_script != '')
              $url = $this->autosuggest_advanced_script;
          else
              $url = $this->autosuggest_script . "?camila_autosuggest&table=".$this->autosuggest_table."&field=".$this->autosuggest_field."&id=".$this->autosuggest_idfield."&infofields=".urlencode($this->autosuggest_infofields)."&pickfields=".urlencode($this->autosuggest_pickfields)."&maxresults=".$this->autosuggest_maxresults."&";

          $script = "var options = { callback: ". ($this->autosuggest_callback != "" ? $this->autosuggest_callback : "\"\"") .", script: ".($this->autosuggest_advanced_script != '' ? $url : "\"".$url."\"").",
	json: true,cache:false,minchars:2,shownoresults:false, delay:".$this->autosuggest_delay.", maxresults: ".$this->autosuggest_maxresults.", timeout:" . $this->autosuggest_timeout . "
          };var as = new bsn.AutoSuggest('".$this->key."', options);";

          $code = "<script>\n";
          if ($this->autosuggest_advanced_script == '')
              $code .= 'function ' . $this->autosuggest_callback . '(o){ camila_autosuggest_pickvalues(\''.$url .'\',o.id ,\'' . $this->autosuggest_pickfields . '\', \'' . $this->autosuggest_destfields . '\', \'' . $this->form->name . '_' . '\'); }';
          $code .= ' camila_addDOMLoadEvent(function(){ ' . $script . ' });';
          $code .= "</script>\n";
          $_CAMILA['page']->camila_add_js($code);

      }

      if ($this->autofocus) {
          $code = "<script>\n";
          $code .= ' camila_addDOMLoadEvent(function(){ xGetElementById(\''.$this->key.'\').focus();});';
          $code .= "</script>\n";
          $_CAMILA['page']->camila_add_js($code);
      }

	  if ($this->help != '' && !$this->hidden && $this->updatable && !$_CAMILA['page']->camila_exporting()) {
          $code = '<button type="button" tabindex="-1" class="btn btn-sm btn-info" data-toggle="modal" data-target="#helpmodal'.$this->field.'"><span class="glyphicon glyphicon-info-sign"></span></button>
<div id="helpmodal'.$this->field.'" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">'.$this->title.'</h4>
      </div>
      <div class="modal-body">
        <p>'.$this->help.'</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">'.camila_get_translation('camila.close').'</button>
      </div>
    </div>
  </div>
</div>';
		   $js = new CHAW_js($code);
        $form->add_userdefined($js);
      }
  }

  function process() {}

  function validate() {
    if (!$this->updatable || strpos(strtolower($this->validation),'ignore') !== false)
        return true;

    if (strpos(strtolower($this->validation),'uppercase') !== false) {
        $this->value = $this->strtoupper_utf8($this->value);
    }

    if (strpos(strtolower($this->validation),'lowercase') !== false) {
        $this->value = $this->strtolower_utf8($this->value);
    }

    if (strpos(strtolower($this->validation),'ucwords') !== false) {
        $this->value = ucwords($this->value);
    }

    if (strpos(strtolower($this->validation),'ucwordsforce') !== false) {
        $this->value = ucwords(strtolower($this->value));
    }

    if (strpos(strtolower($this->validation),'ucfirst') !== false) {
        $this->value = ucfirst($this->value);
    }

    if (strpos(strtolower($this->validation),'ucfirstforce') !== false) {
        $this->value = ucfirst(strtolower($this->value));
    }

    if ($this->required && strpos(strtolower($this->validation),'fit') !== false) {
        $this->form->validator->length($this->field,'=',$this->maxlength);
    }

    if ($this->required) {
        $this->form->validator->filledIn($this->field);
    }

    if (strpos(strtolower($this->validation),'unique') !== false) {

      if ($this->value != '') {

          global $_CAMILA;

          $sess = $_REQUEST['camila_update'];
          if ($sess == '')
              $sess = $_REQUEST[$this->form->table.'_sess_key'];
          $stmt = '';

          if ($sess != 'new') {
              $ar = unserialize($sess);
              reset($this->form->keys);

              $i = 0;
              while ($akey = each($this->form->keys)) {
                      $stmt .= ' AND ';
                  $k = trim($this->form->keys[$i]);
                  $v = $ar['camilakey_'.$k];

                  $stmt .= $k . ' <> ' . $_CAMILA['db']->Quote($v);
                  $i++;
              }

          }

          $query = 'select * from '.$this->form->table.' where '.$this->field.'='.$_CAMILA['db']->Quote($this->value) . $stmt;
          $result = $_CAMILA['db']->Execute($query);
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
          if ($result->RecordCount() > 0) {
              $this->form->validator->setError($this->field, 907);
          }
      }
    }
  }


  function strtoupper_utf8($string){
      if(!isUTF8($string))
          $string=utf8_encode($string);
      return mb_strtoupper($string, 'UTF-8');
  }

  function strtolower_utf8($string){
      if(isUTF8($string))
          $string=utf8_encode($string);
      return mb_strtolower($string, 'UTF-8');
  }
  
  function set_br($num) {
	  $this->br = $num;
  }
  
  function set_css_class($css_class) {
	  $this->css_class = $css_class;
  }

}
?>
