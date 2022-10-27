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
  
  
  class CAMILA_XML2PDF_deck extends CHAW_deck
  {
      var $pdf;
      var $textpending = '';

      function create_page()
      {
          global $_CAMILA;
          require_once(CAMILA_DIR . 'export/xml-2-pdf/Xml2Pdf.php');
          
		  $camilaTemplate = new CamilaTemplate($_CAMILA['lang']);

          //$xmlfile = CAMILA_TMPL_DIR . '/' . $_CAMILA['lang'] . '/' . $_REQUEST['camila_xml2pdf'];
		  $xmlfile = $camilaTemplate->getXmlTemplatePath($_REQUEST['camila_xml2pdf']);

          $xml = '';

		  if ($_REQUEST['filename'] != '')
		  {
			  $this->title = $this->filter_filename($_REQUEST['filename'], true);
		  }

          $t = new MiniTemplator;
          $t->readTemplateFromFile($xmlfile);

          if ($_REQUEST['camila_xml2pdf_checklist_options_0'] != 'y')
          {
              $format = camila_get_locale_date_adodb_format();
              $text=date($format);
              $t->setVariable(camila_get_translation('camila.export.template.date'), isUTF8($text) ? utf8_decode($text) : $text, true);

              $text=date($format.' H:i');
              $t->setVariable(camila_get_translation('camila.export.template.timestamp'), isUTF8($text) ? utf8_decode($text) : $text, true);
			  
			  //2016
			  $t->setVariable(camila_get_translation('camila.export.template.worktable.filter'), isUTF8($text) ? utf8_decode($_CAMILA['page']->camila_worktable_filter) : $_CAMILA['page']->camila_worktable_filter, true);
			  
			  //2019
			  foreach ($_CAMILA['page']->camila_worktable_filter_values as $k => $v) {
				  $t->setVariable(camila_get_translation('camila.export.template.worktable.filter') . ' ' . $k, isUTF8($text) ? utf8_decode($v) : $v, true);
			  }

			  $sheetName = substr ( $_REQUEST['camila_xml2pdf'] , 0 , strpos($_REQUEST['camila_xml2pdf'], '_'));
			  $t->setVariable(camila_get_translation('camila.export.template.worktable.name'), isUTF8($text) ? utf8_decode($sheetName) : $sheetName, true);
          }

          $i = 0;
          while (isset($this->element[$i])) {
              $page_element = $this->element[$i];
              switch ($page_element->get_elementtype()) {
                  case HAW_FORM: {
                      $i = 0;
                      while (isset($page_element->element[$i])) {
                          $form_element = $page_element->element[$i];
                          $form_fieldname = substr($form_element->name, strlen($_CAMILA['datagrid_form']->name) + 1);
                          $form_label = $_CAMILA['datagrid_form']->fields[$form_fieldname]->title;


                          switch ($form_element->get_elementtype()) {
                              //case HAW_IMAGE:
                              //case HAW_RADIO:
                              //case HAW_RULE:
                              case HAW_HIDDEN:
                              case HAW_INPUT:
                              case HAW_TEXTAREA: {
                                  $text = html_entity_decode($form_element->value);

                                  for ($ii = 0; $ii < $form_element->br - 1; $ii++)
                                      $text .= "\n";
                                  $t->setVariable($form_element->label, htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);
                                  $t->setVariable($form_label, htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);

                                  break;
                              }

                              case HAW_SELECT: {
                                  foreach ($form_element->options as $key => $value) {
                                      if ($value['value'] == $form_element->value)
                                          $text = $value['label'];
                                  }
                                  
                                  $text = html_entity_decode($text);
                                  $t->setVariable($form_element->label, htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);
                                  $t->setVariable($form_label, htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);

                                  break;
                              }

                              case HAW_CHECKBOX: {
                                  if (!$form_element->is_checked())
                                      break;
                                  
                                  //$text = html_entity_decode($form_element->label);
                                  
                                  $nl = 1;
                                  if ($form_element->br > 0)
                                      $nl = $form_element->br;
                                  for ($ii = 0; $ii < $nl; $ii++)
                                      $text .= "\n";

                                  $t->setVariable($form_element->name, htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);
                                  $t->setVariable($form_label, htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);

                                  break;
                              }

                              case HAW_PLAINTEXT: {
                                  break;
                              }
                          }
                          
                          $i++;
                      }
                      $t->addBlock('form');
                      break;

                  }

                  case HAW_PLAINTEXT: {
                      if ($this->element[$i]->text == camila_get_translation('camila.nodatafound') && $_CAMILA['datagrid_nodata'] == 1) {

                          $rowsperpage = 0;
                          if ($t->blockExists('row1')) {
                              $rowsperpage = 1;
                              while ($t->blockExists('row'.($rowsperpage+1))){
                                  $rowsperpage++;
                          }

                          if ($rowsperpage > 0) {

                              for ($ii=0; $ii<$rowsperpage; $ii++) {

                                  $t->addBlock('row'.($ii + 1));

                              }
                              $t->addBlock('table');

                          }

                      }

                      }
                      break;
                  }
                  
                  case HAW_LINK: {
                      $link = $this->element[$i];
                      
                      for ($ii = 0; $ii < $link->br; $ii++)
                          $suffix .= "\n";

                      //$this->pdf_text(isUTF8($link->label) ? utf8_decode($link->label).$suffix : $link->label.$suffix);
                      break;
                  }
                  
                  case HAW_TABLE: {
                      $table = $this->element[$i];

                      $cols = array();
                      $rowsperpage = 0;
                      $rownum = 1;
                      $pagnum = 1;
                      $multitable = false;

                      if ($t->blockExists('row1')) {
                          $multitable = true;
                          $rowsperpage = 1;
                          while ($t->blockExists('row'.($rowsperpage+1))){
                              $rowsperpage++;
                          }

                      }

                      if ($_REQUEST['camila_xml2pdf_checklist_options_0'] != 'y')
                      {
                          $row = $table->row[0];

                          for ($b = 0; $b < $row->number_of_columns; $b++) {
                              $column = $row->column[$b];
                              $cols[$b] = strtolower($column->text);
                          }

                          $t->setVariable(camila_get_translation('camila.xml2pdf.table.totalrows'),intval($table->number_of_rows)-1, true);

                          for ($a = 1; $a < $table->number_of_rows; $a++) {

                              $row = $table->row[$a];

                              for ($b = 0; $b < $row->number_of_columns; $b++) {
                                  $column = $row->column[$b];

                                  if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                      $text = $column->get_text();
                                  }

                                  if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                      $text = $column->get_label();
                                  }

                                  //$t->setVariable($cols[$b], isUTF8($text) ? utf8_decode($text) : $text, true);
								  $t->setVariable($cols[$b], htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);
                                  $t->setVariable(camila_get_translation('camila.xml2pdf.table.row.num'),$a,true);

                              }


                              if (!$multitable)
                                  $t->addBlock('row');
                              else
                                  $t->addBlock('row'.$rownum);

                              $rownum++;

                              if ($rownum>$rowsperpage) {
                                  $rownum = 1;
                                  $pagnum++;
                                  $t->addBlock('table');
                              }
                          }

                          if (!$multitable || ($rownum>1 && $rownum<=$rowsperpage) || ($multitable && $pagnum==1))
                              $t->addBlock('table');
                      }
                      else
                      {

                          if ($rowsperpage > 0) {

                              for ($ii=0; $ii<$rowsperpage; $ii++) {

                                  $t->addBlock('row'.($ii + 1));

                              }
                              $t->addBlock('table');

                          }
                      }

                              $a = 1;
                              $row = $table->row[$a];

                              for ($b = 0; $b < $row->number_of_columns; $b++) {
                                  $column = $row->column[$b];

                                  if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                      $text = $column->get_text();
                                  }

                                  if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                      $text = $column->get_label();
                                  }

                                  $t->setVariable($cols[$b], htmlspecialchars(isUTF8($text) ? utf8_decode($text) : $text, ENT_XML1, 'ISO-8859-1'), true);
                                  $t->setVariable(camila_get_translation('camila.xml2pdf.table.row.num'),$a,true);

                              }

                      break;
                  }
              }
              $i++;
          }
		  
          $t->generateOutputToString($xml);
		  
		  //echo $xml;

          $obj = new Xml2Pdf($xml);
          $pdf = $obj->render();
          $pdf->Output($this->title . '.pdf', 'I');
      }
	  
	 function filter_filename($filename, $beautify=true) {
			// sanitize filename
			$filename = preg_replace(
				'~
				[<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
				[\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
				[\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
				[#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
				[{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
				~x',
				'-', $filename);
			// avoids ".", ".." or ".hiddenFiles"
			$filename = ltrim($filename, '.-');
			// optional beautification
			if ($beautify) $filename = $this->beautify_filename($filename);
			// maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
			return $filename;
		}
		
		function beautify_filename($filename) {
			// reduce consecutive characters
			$filename = preg_replace(array(
				// "file   name.zip" becomes "file-name.zip"
				'/ +/',
				// "file___name.zip" becomes "file-name.zip"
				'/_+/',
				// "file---name.zip" becomes "file-name.zip"
				'/-+/'
			), '-', $filename);
			$filename = preg_replace(array(
				// "file--.--.-.--name.zip" becomes "file.name.zip"
				'/-*\.-*/',
				// "file...name..zip" becomes "file.name.zip"
				'/\.{2,}/'
			), '.', $filename);
			// lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
			$filename = mb_strtolower($filename, mb_detect_encoding($filename));
			// ".file-name.-" becomes "file-name"
			$filename = trim($filename, '.-');
			return $filename;
		}
  }
?>