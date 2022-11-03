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

require_once('config.inc.php');
require_once('i18n.inc.php');
  


  //require(CAMILA_LIB_DIR.'phpolait/JSON.php');


/*  function camila_logout() {
      global $_CAMILA;
	  
	  $sessionLogin = CAMILA_APPLICATION_NAME . '_logged_user';
	  $_SESSION[$sessionLogin] = '';
		  

      $result = $_CAMILA['db']->Execute('UPDATE '. CAMILA_TABLE_USERS . ' SET session_id=? WHERE id=?', Array('',$_CAMILA['user_id']));
      if ($result === false)
          camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
  }*/



  function camila_utf8_decode_array(&$arr)
  {
      foreach (array_keys($arr) as $k) {
          if (!is_array($arr[$k]))
              $arr[$k] = utf8_decode($arr[$k]);
          else
              camila_utf8_decode_array($arr[$k]);
      }
  }

  function camila_utf8_encode_array(&$arr)
  {
      foreach (array_keys($arr) as $k) {
          if (!is_array($arr[$k])) {
              if (!isUTF8($arr[$k]))
                  $arr[$k] = utf8_encode($arr[$k]);
          }
          else
              camila_utf8_encode_array($arr[$k]);
      }
  }

  function camila_stripslashes_array(&$arr)
  {
      foreach (array_keys($arr) as $k) {
          if (!is_array($arr[$k]))
              $arr[$k] = stripslashes($arr[$k]);
          else
              camila_stripslashes_array($arr[$k]);
      }
  }

  function camila_isUTF8($string)
  {
      if (is_array($string)) {
          $enc = implode('', $string);
          return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
       } else {
          return (utf8_encode(utf8_decode($string)) == $string);
      }
   }

  function camila_strtoupper_utf8($string){
      if(isUTF8($string))
          $string=utf8_decode($string);
      $string=strtoupper($string);
      $string=utf8_encode($string);
      return $string;
  }

  function camila_strtolower_utf8($string){
      if(isUTF8($string))
          $string=utf8_decode($string);
      $string=strtolower($string);
      $string=utf8_encode($string);
      return $string;
  }

  function camila_get_export_link($type)
  {
      $link = $_SERVER['PHP_SELF'];
      if ($_SERVER['QUERY_STRING'] != '') {
          $qs = str_replace('camila_phpform_sent', '_camila_phpform_sent', $_SERVER['QUERY_STRING']);

          $link .= '?' . urldecode($qs) . '&camila_' . $type;
      } elseif (isset($_REQUEST['camila_phpform_sent'])) {
          $app = '';
          foreach ($_REQUEST as $key => $value) {
              if (!is_array($value)) {
                  //if ($key != 'camila_phpform_sent')
                      $app .= '&' . $key . '=' . $value;
              } else {
                  foreach ($value as $k => $v)
                      $app .= '&' . $key.'[' . $k . ']' . '=' . $v;
              }
          }
          $link = '?' . substr($app, 1) . '&camila_' . $type;
      }
      else
          $link .= '?camila_' . $type;

      if ($type=='print')
          $link.='&camila_visible_cols_only=y&camila_pagnum=-1';
      return $link;
  }

  function camila_append_query_string($qs)
  {
      $link = $_SERVER['PHP_SELF'];
      if ($_SERVER['QUERY_STRING'] != '')
          $link .= '?' . urldecode($_SERVER['QUERY_STRING']) . '&' . $qs;
      else
          $link .= '?' . $qs;
      return $link;
  }

  function camila_save_preferences()
  {
      global $_CAMILA;

      if (isset($_REQUEST['submit'])) {

          if (isset($_REQUEST['camila_rows_per_page']) && $_REQUEST['camila_rows_per_page'] > 0) {
              $_CAMILA['user_preferences']['c_rp'] = $_REQUEST['camila_rows_per_page'];
          }

          if (isset($_REQUEST['camila_font_face']) && $_REQUEST['camila_font_face'] != '') {
              $_CAMILA['user_preferences']['c_ff'] = $_REQUEST['camila_font_face'];
          }

          if (isset($_REQUEST['camila_font_size']) && $_REQUEST['camila_font_size'] != '') {
              $_CAMILA['user_preferences']['c_fs'] = $_REQUEST['camila_font_size'];
          }


          if (isset($_REQUEST['camila_table_font_face']) && $_REQUEST['camila_table_font_face'] != '') {
              $_CAMILA['user_preferences']['c_tf'] = $_REQUEST['camila_table_font_face'];
          }

          if (isset($_REQUEST['camila_table_font_size']) && $_REQUEST['camila_table_font_size'] != '') {
              $_CAMILA['user_preferences']['c_ts'] = $_REQUEST['camila_table_font_size'];
          }

          if (isset($_REQUEST['camila_javascript']) && ($_REQUEST['camila_javascript']=='off') )
              setcookie ('camila_js_enabled', '0', time()+3600*3600);
          else
              setcookie ('camila_js_enabled', '1', time()+3600*3600);

          if ( (isset($_REQUEST['camila_skin_number'])) && ($_REQUEST['camila_skin_number']!='-') ) {
              setcookie ('camila_skin', $_REQUEST['camila_skin_number'], time()+3600*3600);
              $_CAMILA['user_preferences']['c_sk'] = $_REQUEST['camila_skin_number'];
          } else {
              $_CAMILA['user_preferences']['c_sk'] = 0;
              setcookie ('camila_skin', '0', time()+3600*3600);
          }
     }

      $result = $_CAMILA['db']->Execute('UPDATE ' . CAMILA_TABLE_USERS . ' SET preferences=? WHERE id=?', Array(serialize($_CAMILA['user_preferences']), $_CAMILA['user_id']));
      if ($result === false)
          camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
  }

  function camila_form_in_update_mode($formname) {
      return ( (isset($_REQUEST['camila_update']) && $_REQUEST['camila_update'] != '') || (isset($_REQUEST['camila_delete']) && $_REQUEST['camila_delete'] != '') || isset($_REQUEST[$formname . '_phpform_sent']) );
  }

  /*Returns the Unix timestamp corresponding to the */
  function camila_dst_timestamp()
  {
      $TZOffset = 1;
      $myMEZ = mktime(gmdate('H') + $TZOffset);
      $myMEZ_year = date('Y', $myMEZ);
      $tmp = strtotime('last Sunday', mktime(0, 0, 0, 4, 1, $myMEZ_year));
      $myLastSundayMarch = mktime(2, 0, 0, date('m', $tmp), date('d', $tmp), date('Y', $tmp));
      $tmp = strtotime('last Sunday', mktime(0, 0, 0, 11, 1, $myMEZ_year));
      $myLastSundayOctober = mktime(3, 0, 0, date('m', $tmp), date('d', $tmp), date('Y', $tmp));

      if (($myMEZ >= $myLastSundayMarch) && ($myMEZ < $myLastSundayOctober)) {
          // summertime
          return mktime(gmdate('H') + $TZOffset + 1);
      } else {
          // normal time ('wintertime')
          return mktime(gmdate('H') + $TZOffset);
      }
  }

  function camila_get_locale_date_format()
  {
      global $_CAMILA;
      $tFormat = '';
      $f = Array();
      $m = camila_get_translation('camila.dateformat.monthpos');
      $d = camila_get_translation('camila.dateformat.daypos');
      $y = camila_get_translation('camila.dateformat.yearpos');
      $f[$m] = 'm';
      $f[$d] = 'd';
      $f[$y] = 'Y';
      ksort($f);
      reset($f);
      $count = 0;
	  foreach ($f as $k => $v) {
          $tFormat.=camila_get_translation('camila.dateformat.placeholder.'.$v);
          if ($count<2) {
              $tFormat.=camila_get_translation('camila.date.separator');
          }
          $count++;
      }
      return $tFormat;
  }


  function camila_get_locale_date_adodb_format()
  {
      global $_CAMILA;
      $fmt = '';
      $f = Array();
      $m = camila_get_translation('camila.dateformat.monthpos');
      $d = camila_get_translation('camila.dateformat.daypos');
      $y = camila_get_translation('camila.dateformat.yearpos');
      $f[$m] = 'm';
      $f[$d] = 'd';
      $f[$y] = 'Y';
      ksort($f);
      reset($f);
      $count = 0;
	  foreach ($f as $k => $v) {
          $fmt.=$v;
          $tFormat.=camila_get_translation('camila.dateformat.placeholder.'.$v);
          if ($count<2) {
              $fmt.=camila_get_translation('camila.dateformat.separator');
          }
          $count++;
      }
      return $fmt;
  }


  function camila_microtime_float()
  {
      list($usec, $sec) = explode(' ', microtime());
      return((float)$usec + (float)$sec);
  }

  function camila_challenge_numbers(&$c1, &$c2, &$c3)
  {
      $m = date('i');
      
      if ($m < 15)
          $m = '1';
      elseif ($m < 30)
          $m = '2';
      elseif ($m < 45)
          $m = '3';
      else
          $m = '4';
      
      $codicesessione = $m + /*date('ydLzg')*/ + date('z') + 1;
      srand($codicesessione);

      $c1 = rand(1, CAMILA_PASSWORD_LENGTH);
      $c2 = rand(1, CAMILA_PASSWORD_LENGTH);
      $c3 = rand(1, CAMILA_PASSWORD_LENGTH);
  }

  function camila_error_handler($errno, $errmsg, $filename, $linenum, $vars)
  {
      $dt = date('Y-m-d H:i:s (T)');
      
      $errortype = array(E_ERROR => 'Error', E_WARNING => 'Warning', E_PARSE => 'Parsing Error', E_NOTICE => 'Notice', E_CORE_ERROR => 'Core Error', E_CORE_WARNING => 'Core Warning', E_COMPILE_ERROR => 'Compile Error', E_COMPILE_WARNING => 'Compile Warning', E_USER_ERROR => 'User Error', E_USER_WARNING => 'User Warning', E_USER_NOTICE => 'User Notice');
      
      // indica gli errori per i quali fare salvare la trace delle variabili
      
      $user_errors = array(E_ERROR, E_USER_ERROR, E_WARNING, E_USER_WARNING);
      
      $err = '';
      $err .= $dt . '|' . $errno . '|' . $errortype[$errno] . '|' . $errmsg . '|' . $filename . '|' . $linenum;
      
      if (in_array($errno, $user_errors)) {
          $err .= $dt . '|' . $errno . '|' . $errortype[$errno] . '|' . $errmsg . '|' . $filename . '|' . $linenum;
          //camila_error_page($errmsg, $filename, $linenum, CAMILA_ADMINISTRATOR_EMAIL);
      }
      
      //if (CAMILA_SHOW_ERRORS==true)
      
      {
          //$text = new CHAW_text($err);
          
          //$GLOBALS['page']->add_text($text);
      }
  }

  function camila_hash($n)
  {
      return substr(md5(microtime()), 0, $n);
  }

  function camila_token($n)
  {
      return session_id().md5($n);
  }

  function camila_var_export_string($var)
  {
      if (is_string($var))
          return('"' . str_replace(array("\x00", "\x0a", "\x0d", "\x1a", "\x09"), array('\0', '\n', '\r', '\Z', '\t'), $var) . '"');
      elseif (is_bool($var)) {
          if ($var)
              return('true');
          else
              return('false');
      } elseif (is_array($var)) {
          $result = 'array( ';
          $comma = '';
          foreach ($var as $key => $val) {
              $result .= $comma . camila_print_var($key) . ' => ' . camila_print_var($val);
              $comma = ', ';
          }
          $result .= ' )';
          return($result);
      }
      
      // anything else, just let php try to print it
      return(var_export($var, true));
  }
  
  function camila_print_var($var)
  {
      if (is_string($var))
          return('"' . str_replace(array("\x00", "\x0a", "\x0d", "\x1a", "\x09"), array('\0', '\n', '\r', '\Z', '\t'), $var) . '"');
      elseif (is_bool($var)) {
          if ($var)
              return('true');
          else
              return('false');
      } elseif (is_array($var)) {
          $result = 'array( ';
          $comma = '';
          foreach ($var as $key => $val) {
              $result .= $comma . camila_print_var($key) . ' => ' . camila_print_var($val);
              $comma = ', ';
          }
          $result .= ' )';
          return($result);
      }
      
      // anything else, just let php try to print it
      return(var_export($var, true));
  }
  
  function camila_error_page($err_msg, $err_text = '', $link_obj = null, $email = CAMILA_ADMINISTRATOR_EMAIL)
  {
      // if error has been supressed with an @
      //if (error_reporting() == 0) {
      //    return;
      //}

      global $_CAMILA;
      $_CAMILA['error'] = 1;

      $trace = array_reverse(debug_backtrace());
      $t_file = array();
      $t_line = array();
      $t_func = array();
      $count = 0;
      $func = '';
      $msg = '';

      foreach ($trace as $val) {
          $t_file[$count] = str_replace(CAMILA_HOMEDIR, '', $val['file']);
          $t_line[$count] = $val['line'];
          $t_func[$count] = $func;

          if ($val['function'] == 'include' || $val['function'] == 'require' || $val['function'] == 'include_once' || $val['function'] == 'require_once')
              $func = '';
          else {
              $func = $val['function'] . '(';
              
              if (isset($val['args'][0])) {
                  $func .= ' ';
                  $comma = '';
                  foreach ($val['args'] as $val) {
                      $func .= $comma . camila_var_export_string($val);
                      $comma = ', ';
                  }
                  $func .= ' ';
              }
              
              $func .= ')';
          }

          $count++;
      }

      $count = 0;
      foreach ($t_file as $val) {
          $msg .= $t_file[$count] . ' (' . $t_line[$count] . ') ' . $t_func[$count] . "\n";
          $count++;
      }

      $_CAMILA['page'] = new CHAW_deck('Errore');
      $_CAMILA['page']->use_simulator('css/skin0.css');
      include(CAMILA_DIR . 'deck_settings.php');

      if ($err_text != '') {
          $text = new CHAW_text($err_text, HAW_TEXTFORMAT_BOLD);
          $text->set_br(2);
          $_CAMILA['page']->add_text($text);

          if (is_object($link_obj)) {
              $_CAMILA['page']->add_link($link_obj);

          }

          $_CAMILA['page']->create_page();
          exit();
      }

      $text = new CHAW_text(camila_get_translation('camila.erroroccurred')."\n" . $err_msg, HAW_TEXTFORMAT_BOLD);
      $text->set_br(2);

      $_CAMILA['page']->add_text($text);

      $text = new CHAW_text($msg, HAW_TEXTFORMAT_NORMAL | HAW_TEXTFORMAT_BOXED);
      $text->set_color("white", "red");
      $text->set_br(2);
      
      $_CAMILA['page']->add_text($text);

      $text = new CHAW_text(camila_get_translation('camila.erroroccurred.adminf'), HAW_TEXTFORMAT_ITALIC);
      $text->set_br(2);

      if (CAMILA_MAIL_ON_ERROR)
          $_CAMILA['page']->add_text($text);

      $text = new CHAW_text("");
      $text->set_br(2);

      $_CAMILA['page']->add_text($text);

      //$msg .= " (script <" . $script . ">, linea <" . $line . ">)";
      $msg = $msg . "\nURL=" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "\n";

      //while (list($key, $value) = each($_CAMILA)) {
      //    $msg = $msg . $key . "=" . $value . "\n";
      //}

      $_CAMILA['page']->create_page();

      if (CAMILA_MAIL_ON_ERROR) {
          require('var/mailonerror.inc.php');
      }
 
      exit();
  }


function camila_worktable_filter_decode($str, $wt_id)
{
    global $_CAMILA;
    $adoMetaColumns=$_CAMILA['db']->MetaColumns(camila_worktable_get_table_name($wt_id));

    $connOr = ' ' . camila_get_translation('camila.report.or') . ' ';
    $connAnd = ' ' . camila_get_translation('camila.report.and') . ' ';

    $connOrCount = substr_count($str, $connOr);
    $connAndCount = substr_count($str, $connAnd);
    $connCount = $connOrCount + $connAndCount;
    $offset = 0;
    $result = Array();
    $condArray = camila_get_translation_array('camila.report.condstring');

    if ($connCount > 0)
    {
        for ($i=0; $i<$connCount; $i++)
        {
            $pos1 = stripos($str, $connOr, $offset);
            $pos2 = stripos($str, $connAnd, $offset);
            $pos1 = ($pos1>0) ? $pos1 : 999;
            $pos2 = ($pos2>0) ? $pos2 : 999;
            $pos = min($pos1,$pos2);

            $criteria = substr($str,$offset,$pos).' ';
            $field = substr($criteria, 1, stripos($criteria, "\"",1)-1);
            $cond = trim(substr($criteria, strlen($field)+3, stripos(substr($criteria, strlen($field)+3), "\"")-1));
            $value = substr($criteria, strlen($field)+strlen($cond)+5,-2);
            $offset = $offset+$pos+(($pos1 == $pos)?strlen($connOr):strlen($connAnd));
            $colInfo = camila_worktable_get_col_name($wt_id, $field);
            $result['camila_w'.($i+1).'f'] = '_'.camila_worktable_get_schema_type($colInfo['type']).'_'.$colInfo['col_name'];
            $result['camila_w'.($i+1).'c'] = array_search($cond, $condArray);
            $result['camila_w'.($i+1).'v'] = $value;
            $result['camila_w'.($i+2).'w'] = ($pos1 == $pos)? 'or' : 'and';
        
        }

            $criteria = substr($str,$offset,$pos) . ' ';
            $field = substr($criteria, 1, stripos($criteria, "\"",1)-1);
            $cond = substr($criteria, strlen($field)+3, stripos(substr($criteria, strlen($field)+3), "\"")-1);
            $value = substr($criteria, strlen($field)+strlen($cond)+5,-2);
            $colInfo = camila_worktable_get_col_name($wt_id, $field);
            $result['camila_w'.($i+1).'f'] = '_'.$_CAMILA['db']->MetaType($adoMetaColumns[strtoupper($colInfo['col_name'])]->type).'_'.$colInfo['col_name'];
            $result['camila_w'.($i+1).'c'] = array_search($cond, $condArray);
            $result['camila_w'.($i+1).'v'] = $value;

    } else {

            $criteria = $str.' ';
            $field = substr($criteria, 1, stripos($criteria, "\"",1)-1);
            $cond = trim(substr($criteria, strlen($field)+3, stripos(substr($criteria, strlen($field)+3), "\"")-1));
            $value = substr($criteria, strlen($field)+strlen($cond)+5,-2);
            $colInfo = camila_worktable_get_col_name($wt_id, $field);
            $result['camila_w1f'] = '_'.$_CAMILA['db']->MetaType($adoMetaColumns[strtoupper($colInfo['col_name'])]->type).'_'.$colInfo['col_name'];
            $result['camila_w1v'] = $value;
            $result['camila_w1c'] = array_search($cond, $condArray);
    }

    return $result;
}


function camila_worktable_get_schema_type($type)
{
    $schema_type = '';
    switch ($type) {
        case 'integer':
        case 'integer-listofvalues';
        case 'autoincrement';

            $schema_type = "I";
            break;

        case 'date':
            $schema_type = "D";
            break;

        case 'datetime':
            $schema_type = "T";
            break;

        case 'textarea':
            $schema_type = "X";
            break;

        default:
            $schema_type = "C";

        }

    return $schema_type;
}



function camila_worktable_get_col_name($wt_id, $name)
{
    global $_CAMILA;
    $result = $_CAMILA['db']->Execute('select col_name,type from ' . CAMILA_TABLE_WORKC . ' where wt_id='.$_CAMILA['db']->qstr($wt_id) . ' and name_abbrev='.$_CAMILA['db']->qstr($name) );
    if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
    return $result->fields;
}

function camila_worktable_get_table_name($wt_id)
{
    return CAMILA_TABLE_WORKP.$wt_id;
}

function camila_worktable_get_table_id($name)
{
    global $_CAMILA;

    $result = $_CAMILA['db']->Execute('select page_url from ' . CAMILA_TABLE_PLANG . ' where short_title='.$_CAMILA['db']->qstr($name) . ' AND page_url NOT LIKE '.$_CAMILA['db']->qstr('cf_app.php?cat%') . ' and lang='.$_CAMILA['db']->qstr($_CAMILA['lang']));
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

    return $result->fields['page_url'];
}


  function camila_parse_default_expression($expression, $id, $dataImport = false) {

    global $_CAMILA;

    if (($dataImport == true) && ($expression == camila_get_translation('camila.date.today')))
        return $_CAMILA['db']->BindTimeStamp(gmdate("Y-m-d", time()));

    if (($dataImport == true) && ($expression == camila_get_translation('camila.time.now')))
        return $_CAMILA['db']->BindTimeStamp(gmdate("Y-m-d H:i:s", time()));

    if (($dataImport == true) && ($expression == camila_get_translation('camila.worktable.field.default.lastval')))
        return '';

    require_once (CAMILA_LIB_DIR . 'minitemplator/MiniTemplator.class.php');

    $ttemp = new MiniTemplator();
    $ttemp->setTemplateString($expression);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.id'), $id, true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.j'), date('j'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.d'), date('d'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.z'), date('z'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.W'), date('W'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.dayoftheweek'), camila_get_translation('camila.week.day.' . date('w')), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.month'), camila_get_translation('camila.month.' . date('n')), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.n'), date('n'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.m'), date('m'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.y'), date('y'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.Y'), date('Y'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.g'), date('g'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.h'), date('h'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.G'), date('G'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.H'), date('H'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.i'), date('i'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.s'), date('s'), true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.A'), date('A'), true);

    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.login'), $_CAMILA['user'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.name'), $_CAMILA['user_name'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.surname'), $_CAMILA['user_surname'], true);

    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib01'), $_CAMILA['user_attrib_01'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib02'), $_CAMILA['user_attrib_02'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib03'), $_CAMILA['user_attrib_03'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib04'), $_CAMILA['user_attrib_04'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib05'), $_CAMILA['user_attrib_05'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib06'), $_CAMILA['user_attrib_06'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib07'), $_CAMILA['user_attrib_07'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib08'), $_CAMILA['user_attrib_08'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib09'), $_CAMILA['user_attrib_09'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib10'), $_CAMILA['user_attrib_10'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib11'), $_CAMILA['user_attrib_11'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib12'), $_CAMILA['user_attrib_12'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib13'), $_CAMILA['user_attrib_13'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib14'), $_CAMILA['user_attrib_14'], true);
    $ttemp->setVariable(camila_get_translation('camila.worktable.field.default.user.attrib15'), $_CAMILA['user_attrib_15'], true);


    $dir = CAMILA_TMPL_DIR . '/' . $_CAMILA['lang'] . '/';
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $ttemp->setVariable(substr($file,0,-4), file_get_contents($dir.$file), true);
            }
        }
        closedir($dh);
    }

    $ttemp->generateOutputToString($expression);

    return $expression;
  }
  
  function camila_session_cookie_name() {
	  //return preg_replace('/\s+/', '', CAMILA_APPLICATION_NAME);
	  return preg_replace('/[^a-z0-9]/', '', CAMILA_APPLICATION_NAME);
  }


  require(CAMILA_DIR.'camila_hawhaw.php');
  
  require_once('database.inc.php');

  
  if (!$_CAMILA['db'])
    camila_error_page("DB ".CAMILA_DB_DSN." NOT AVAILABLE... ".$_CAMILA['db_error']);

?>