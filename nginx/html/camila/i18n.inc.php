<?php
  require_once(CAMILA_LIB_DIR.'m2translator/M2Translator.class.php');

  function camila_translation_init($force=false) {

      global $_CAMILA;
      if (!is_readable(CAMILA_TMP_DIR.'/'.$_CAMILA['lang'].'.lang.php') || $force) {
          $content = @file_get_contents(CAMILA_DIR.'lang/'.$_CAMILA['lang'].'.lang.php')."\r\n";
          $content .= @file_get_contents(CAMILA_LANG_DIR.$_CAMILA['lang'].'.lang.php');
          $tmpFile = CAMILA_TMP_DIR.'/'.$_CAMILA['lang'].'.lang.php';
          $fh = fopen($tmpFile, 'w') or die("Can't open TEMP lang file!");
          fwrite($fh, $content);
          fclose($fh);
      }

      $_CAMILA['i18n'] = new M2Translator($_CAMILA['lang'], CAMILA_TMP_DIR.'/');

  }

    function camila_get_translation($string)
  {
      global $_CAMILA;

      if (!is_object($_CAMILA['i18n']))
          camila_translation_init();

      if ($_CAMILA['i18n']->get($string) != '*' . $string . '*')
          return $_CAMILA['i18n']->get($string);
      else
          return '';
  }

  function camila_get_translation_array($options_string)
  {
      global $_CAMILA;

      $arr1 = explode(',', camila_get_translation($options_string));

      $tr = Array();
      foreach($arr1 as $name => $value) {
          $arr2 = explode(';', $value);
	  $tr[$arr2[0]] = $arr2[1]; 
      }
      
      return $tr;
  }

    function camila_error_text($msg)
  {
      global $_CAMILA;
      if (is_object($_CAMILA['page'])) {
          //if (camila_isUTF8($msg))
          //    $msg=utf8_decode($msg);
          if (!isUTF8($msg))
              $msg=utf8_encode($msg);

          $text = new CHAW_text($msg, HAW_TEXTFORMAT_BOLD);
          //$text->set_color('red');
          //$text->set_br(2);
          $msg = str_replace("\n", '\n', $msg);

		  $myHtmlCode = '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span>';
		  $myDiv = new HAW_raw(HAW_HTML, $myHtmlCode);
		  $_CAMILA['page']->add_raw($myDiv);
	
		  
          $_CAMILA['page']->add_text($text);
          /*$_CAMILA['page']->camila_add_js("<script type=\"text/javascript\">\n");
          $_CAMILA['page']->camila_add_js("  camila_addDOMLoadEvent ( function()\n");
          $_CAMILA['page']->camila_add_js("  {alert ('" . str_replace("'", "\'", $msg) . "') })");
          $_CAMILA['page']->camila_add_js("</script>\n");*/
		  
		  $myHtmlCode = '</div>';
		  $myDiv = new HAW_raw(HAW_HTML, $myHtmlCode);
		  $_CAMILA['page']->add_raw($myDiv);
      } else {
          echo $msg;
      }
  }


  function camila_information_text($msg)
  {
	  global $_CAMILA;
	  if (php_sapi_name() == "cli" || $_CAMILA['cli']) {
			echo $msg."\n";
		} else {
      
	  $myHtmlCode = '<div class="alert alert-success" role="alert">';
	  $myDiv = new HAW_raw(HAW_HTML, $myHtmlCode);
	  $_CAMILA['page']->add_raw($myDiv);
      $text = new CHAW_text($msg, HAW_TEXTFORMAT_BOLD);
      //$text->set_br(2);
      //$text->set_color('red');
      $_CAMILA['page']->add_text($text);
	  $myHtmlCode = '</div>';
	  $myDiv = new HAW_raw(HAW_HTML, $myHtmlCode);
	  $_CAMILA['page']->add_raw($myDiv);
	}
  }


?>