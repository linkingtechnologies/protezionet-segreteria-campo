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


  $text = new CHAW_text('');
  $text->set_br(2);

  $_CAMILA['page']->add_text($text);

  if (!$_CAMILA['auth_page'] && $_CAMILA['page']->camila_export_enabled && $_CAMILA['output'] == HAW_OUTPUT_AUTOMATIC && !$_CAMILA['page']->camila_exporting() /*($_CAMILA['page_url'] != CAMILA_LOGIN_HOME)*/ /*&& ($_CAMILA['page_url'] != CAMILA_HOME)*/ && (count($_POST) == 0 || isset($_REQUEST['camila_phpform_sent']))) {

      //$code = "<div id='camilabottomtoolbar'>";
	  $code = '<section class="footery">'."\n";
	  $code .= '<div class="container">'."\n";

      $js = new CHAW_js($code);
      $_CAMILA['page']->add_userdefined($js);

      $text = new CHAW_text(camila_get_translation('camila.export.convertto'), HAW_TEXTFORMAT_SMALL);
      $text->set_br(0);
      $_CAMILA['page']->add_text($text);

      if ($_CAMILA['page']->camila_num_tables > 0 || $_CAMILA['page']->camila_csv_export_enabled) {
          $myLink = new CHAW_link('CSV', camila_get_export_link('csv'));
          $myLink->set_br(0);
          $_CAMILA['page']->add_link($myLink);
          $text = new CHAW_text(camila_get_translation('camila.export.csv'), HAW_TEXTFORMAT_SMALL);
          $text->set_br(0);
          $_CAMILA['page']->add_text($text);
      }

      if ($_CAMILA['page']->camila_num_tables > 0 || $_CAMILA['page']->camila_xls_export_enabled || $_CAMILA['page']->camila_worktable) {
          $myLink = new CHAW_link('XLS', camila_get_export_link('xls'));
          $myLink->set_br(0);
          $_CAMILA['page']->add_link($myLink);
          $text = new CHAW_text(camila_get_translation('camila.export.xls'), HAW_TEXTFORMAT_SMALL);
          $text->set_br(0);
          $_CAMILA['page']->add_text($text);
      }

      if ($_CAMILA['page']->camila_pdf_export_enabled) {
          $myLink = new CHAW_link('PDF', camila_get_export_link('pdf'));
          $myLink->set_br(0);
          $_CAMILA['page']->add_link($myLink);
          $text = new CHAW_text(camila_get_translation('camila.export.pdf'), HAW_TEXTFORMAT_SMALL);
          $text->set_br(0);
          $_CAMILA['page']->add_text($text);
      }

      if ($_CAMILA['page']->camila_rtf_export_enabled) {
          $myLink = new CHAW_link('RTF', camila_get_export_link('rtf'));
          $myLink->set_br(0);
          $_CAMILA['page']->add_link($myLink);
          $text = new CHAW_text(camila_get_translation('camila.export.rtf'), HAW_TEXTFORMAT_SMALL);
          $text->set_br(0);
          $_CAMILA['page']->add_text($text);
      }

      if ($_CAMILA['page']->camila_xml2pdf_export_enabled) {
          $myLink = new CHAW_link(camila_get_translation('camila.export.xml2pdf'), camila_get_export_link('xml2pdf'));
          $myLink->set_br(0);
          $_CAMILA['page']->add_link($myLink);
          $text = new CHAW_text(camila_get_translation('camila.export.separator'), HAW_TEXTFORMAT_SMALL);
          $text->set_br(0);
          $_CAMILA['page']->add_text($text);
      }

      if ($_CAMILA['page']->camila_worktable_filter != '') {
          $myLink = new CHAW_link(camila_get_translation('camila.export.bookmark'), $_SERVER['PHP_SELF'].'?camila_bookmark='.urlencode($_CAMILA['page']->camila_worktable_filter));
          $myLink->set_br(0);
          $_CAMILA['page']->add_link($myLink);
          $text = new CHAW_text(camila_get_translation('camila.export.separator'), HAW_TEXTFORMAT_SMALL);
          $text->set_br(0);
          $_CAMILA['page']->add_text($text);
      }

      $myLink = new CHAW_link('HTML', camila_get_export_link('print'));
      $myLink->set_br(0);
      $_CAMILA['page']->add_link($myLink);
      $text = new CHAW_text(camila_get_translation('camila.export.print'), HAW_TEXTFORMAT_SMALL);
      $_CAMILA['page']->add_text($text);
      $text = new CHAW_text('');      
      $_CAMILA['page']->add_text($text);


      //$code = "</div>";
	  $code = "</div>\n";
	  $code .= "</section>";
      $js = new CHAW_js($code);
      $_CAMILA['page']->add_userdefined($js);
	  //$_CAMILA['page']->add_footer($code);

  }

  if (CAMILA_SHOW_MICROTIME) {
      $camila_time_end = camila_microtime_float();
      $time = $camila_time_end - $camila_time_start;
      
      $text = new CHAW_text('-' . sprintf("%01.3f", $time));
      
      $_CAMILA['page']->add_text($text);
  }

  //echo $_CAMILA["session_countdown"];
  $_CAMILA['page']->create_page();


  /***********/
  /* logging */
  /***********/

  if (CAMILA_LOG_LEVEL > 0) {

      /*require CAMILA_LIB_DIR.'oolog/class_oolog.inc.php';

      if (isset($_CAMILA['user_surname']))
          $l =& new oolog(CAMILA_LOG_DIR . '/'. $_CAMILA['user_surname'] . '-' . $_CAMILA['user_name'] . ".log", FILE);
      else
          $l =& new oolog(CAMILA_LOG_DIR . '/anon.log', FILE);

      $camila_time_end = camila_microtime_float();
      $time = $camila_time_end - $camila_time_start;      
      $millis = sprintf("%01.3f", $time);
  
      $l->log($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '|' . $millis, FILE);
      $l->closelog();*/
  }

  if (isset($_REQUEST['camila_txt'])) {
      $buffer = ob_get_contents();
      ob_end_clean();
      include 'include/html2text/html2text.php';
      echo html2text($buffer);
  }

//  if (isset($_REQUEST['camila_save']) && $_REQUEST['camila_export_filename']!='') {
//      ob_end_flush();
//      require_once('camila/fm/elements.php');

//      global $_CAMILA;

//      fm_add_file($rel, fm_name($_CAMILA['ob_filename'], $_CAMILA['adm_user_group']), $_CAMILA['adm_user_group'], $_REQUEST['camila_export_dir']);
//  }

  if ($_REQUEST['camila_export_action'] != '' && $_REQUEST['camila_export_action'] != 'download') {
      include(CAMILA_DIR . 'export.php');
  }


?>