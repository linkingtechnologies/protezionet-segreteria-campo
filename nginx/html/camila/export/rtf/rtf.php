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

  class rtf
  {
      // {\colortbl;\red 0\green 0\blue 0;\red 255\green 0\ blue0;\red0 ...}
      
      var $colour_table = array();
      var $colour_rgb;
      
      // {\fonttbl{\f0}{\f1}{f...}}
      
      var $font_table = array();
      var $font_face;
      var $font_size;
      
      // {\info {\title <title>} {\author <author>} {\operator <operator>}}
      
      var $info_table = array();
      var $page_width;
      var $page_height;
      var $page_size;
      var $page_orientation;
      var $rtf_version;
      var $tab_width;
      var $document;
      var $buffer;

      function rtf($config = "rtf_config.php")
      {
          $font_face = 0;
          $font_size = 24;
          $rtf_version = 1;
          $tab_width = 360;
          $paper_size = 5;
          $paper_orientation = 1;

          $this->setDefaultFontFace($font_face);
          $this->setDefaultFontSize($font_size);
          $this->setPaperSize($paper_size);
          $this->setPaperMargins("2cm", "2cm", "2cm", "2cm");
          $this->setPaperOrientation($paper_orientation);
          $this->rtf_version = $rtf_version;
          $this->tab_width = $tab_width;
      }

      function setDefaultFontFace($face)
      {
          // $font is interger
          $this->font_face = $face;
      }

      function setDefaultFontSize($size)
      {
          $this->font_size = $size;
      }
      
      function setSmallFontSize($size)
      {
          $this->font_size_small = $size;
      }
      
      function setBigFontSize($size)
      {
          $this->font_size_big = $size;
      }

      function setTitle($title = "")
      {
          $this->info_table["title"] = $title;
      }

      function setAuthor($author = "")
      {
          $this->info_table["author"] = $author;
      }

      function setOperator($operator = "")
      {
          $this->info_table["operator"] = $operator;
      }

      function setPaperSize($size = 0)
      {
          // Measurements
          // 1 inch = 1440 twips
          // 1 cm = 567 twips
          // 1 mm = 56.7 twips 
          $inch = 1440;
          $cm = 567;
          $mm = 56.7;

          // 1 => Letter (8.5 x 11 inch)
          // 2 => Legal (8.5 x 14 inch)
          // 3 => Executive (7.25 x 10.5 inch)
          // 4 => A3 (297 x 420 mm)
          // 5 => A4 (210 x 297 mm)
          // 6 => A5 (148 x 210 mm)
          // Orientation considered as Portrait

          switch ($size) {
              case 1:
                  $this->page_width = floor(8.5 * $inch);
                  $this->page_height = floor(11 * $inch);
                  $this->page_size = 1; 
                  break;
                  
              case 2:
                  $this->page_width = floor(8.5 * $inch);
                  $this->page_height = floor(14 * $inch);
                  $this->page_size = 5;
                  break;
                  
              case 3:
                  $this->page_width = floor(7.25 * $inch);
                  $this->page_height = floor(10.5 * $inch);
                  $this->page_size = 7;
                  break;
                  
              case 4:
                  $this->page_width = floor(297 * $mm);
                  $this->page_height = floor(420 * $mm);
                  $this->page_size = 8;
                  break;
                  
              case 5:
              default:
                  $this->page_width = floor(210 * $mm);
                  $this->page_height = floor(297 * $mm);
                  $this->page_size = 9;
                  break;
                  
              case 6:
                  $this->page_width = floor(148 * $mm);
                  $this->page_height = floor(210 * $mm);
                  $this->page_size = 10;
                  break;
          }
      }

      function setPaperMargins($left, $right, $top, $bottom)
      {
          $this->margin_left = $this->getTwips($left);
          $this->margin_right = $this->getTwips($right);
          $this->margin_top = $this->getTwips($top);
          $this->margin_bottom = $this->getTwips($bottom);
      }

      function setPaperOrientation($orientation = 0)
      {
          // 1 => Portrait
          // 2 => Landscape

          switch ($orientation) {
              case 1:
              default:
                  $this->page_orientation = 1;
                  
                  break;
                  
              case 2:
                  $this->page_orientation = 2;
                  break;
          }
      }

      function addColour($hexcode)
      {
          // Get the RGB values
          $this->hex2rgb($hexcode);

          // Register in the colour table array
          $this->colour_table[] = array("red" => $this->colour_rgb["red"], "green" => $this->colour_rgb["green"], "blue" => $this->colour_rgb["blue"]);
      }

      // Convert HEX to RGB (#FFFFFF => r255 g255 b255)

      function hex2rgb($hexcode)
      {
          $hexcode = str_replace("#", "", $hexcode);
          $rgb = array();
          $rgb["red"] = hexdec(substr($hexcode, 0, 2));
          $rgb["green"] = hexdec(substr($hexcode, 2, 2));
          $rgb["blue"] = hexdec(substr($hexcode, 4, 2));
          $this->colour_rgb = $rgb;
      }

      // Convert newlines into \par
      
      function nl2par($text)
      {
          $text = str_replace("\n", "\\par ", $text);
          return $text;
      }

      // Add a text string to the document buffer

      function addText($text, $prefix = "", $suffix = "")
      {
          $text = str_replace("\n", "", $text);
          $text = str_replace("\t", "", $text);
          $text = str_replace("\r", "", $text);
          $text = $this->specialCharacters($text);

          $this->document .= $this->nl2par($prefix . $text . $suffix);
      }

      function addTable($data, $control = array())
      {
          if ($this->page_orientation == 1)
              $l = ($this->page_width - $this->margin_left - $this->margin_right) / sizeof($data[0]);
          
          else
              $l = ($this->page_height - $this->margin_left - $this->margin_right) / sizeof($data[0]);
          
          
          $startTable = "{";
          $endTable = "}";

          $startRow = '\qc \trowd \trgaph70';
          $endRow = '\pard \intbl \row';
          $startCell = '\pard \intbl';
          $endCell = '\cell';
          
          $rtf = "";
          $rtf .= $startTable;
          
          foreach ($data as $id => $row) {
              $rtf .= $startRow;
              
              foreach ($data[0] as $idx => $val) {
                  //if (($id==0) AND (array_key_exists("headercolor",$control )) AND ($control['headercolor']=="gray")) $rtf.="\clcbpat2";
                  
                  $rtf .= '\cellx' . round($l + $l * $idx, 0);
              }
              
              $rtf .= " ";
              
              
              
              foreach ($row as $idx => $cell) {
                  $text = $cell;
                  
                  $cellJust = ' \ql ';
                  
                  if (($id == 0) and (array_key_exists("header", $control)) and ($control['header'] == "bold"))
                      $text = '{\b ' . $text . '}';
                  
                  if (($id == 0) and (array_key_exists("headerjust", $control)) and ($control['headerjust'] == "center"))
                      $cellJust = "\qc ";
                  
                  if (($id == 0) and (array_key_exists("headerjust", $control)) and ($control['headerjust'] == "left"))
                      $cellJust = "\ql ";
                  
                  $rtf .= $startCell . $cellJust . $text . $endCell;
              }
              
              $rtf .= $endRow;
          }
          $rtf .= $endTable;
          
          $this->document .= $rtf;
      }

      // Ouput the RTF file
      function getDocument($filename = "rtf.rtf", $dir = "")
      {
          $this->buffer .= "{";
          
          // Header
          
          $this->buffer .= $this->getHeader();
          
          // Font table
          
          $this->buffer .= $this->getFontTable();
          
          // Colour table
          
          $this->buffer .= $this->getColourTable();
          
          // File Information
          
          $this->buffer .= $this->getInformation();
          
          // Default font values
          
          $this->buffer .= $this->getDefaultFont();
          
          // Page display settings
          
          $this->buffer .= $this->getPageSettings();
          
          // Parse the text into RTF
          
          $this->buffer .= $this->document;
          
          $this->buffer .= "}";

          if ($dir == '')
          {
              header("Content-Type: text/enriched\n");
              header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
              echo $this->buffer;
          }
          else
          {
              $file = fopen($dir.$filename,'w');
              fwrite($file, $this->buffer);
              fclose($file);

          }

      }

      // Header
      
      function getHeader()
      {
          $header_buffer = "\\rtf{$this->rtf_version}\\ansi\\deff0\\deftab{$this->tab_width}\n\n";
          
          
          
          return $header_buffer;
      }

      // Font table
      
      function getFontTable()
      {
          // Fonts
          
          $fonts_array = array();
          
          // Array structure - array(
          
          //  "name"    =>  Name given to the font,
          
          //  "family"  =>  [nil, roman, swiss, modern, script, decor, tech, bidi],
          
          //  "charset"  =>  0
          
          // );

          $fonts_array[] = array("name" => "Arial", "family" => "swiss", "charset" => 0);
          $fonts_array[] = array("name" => "Times New Roman", "family" => "roman", "charset" => 0);
          $fonts_array[] = array("name" => "Verdana", "family" => "swiss", "charset" => 0);
          $fonts_array[] = array("name" => "Symbol", "family" => "roman", "charset" => 2);
          $font_buffer = "{\\fonttbl\n";

          foreach ($fonts_array as $fnum => $farray) {
              $font_buffer .= "{\\f{$fnum}\\f{$farray['family']}\\fcharset{$farray['charset']} {$farray['name']};}\n";
          }

          $font_buffer .= "}\n\n";

          return $font_buffer;
      }

      function getColourTable()
      {
          $colour_buffer = "";
          
          if (sizeof($this->colour_table) > 0) {
              $colour_buffer = "{\\colortbl;\n";
              
              foreach ($this->colour_table as $cnum => $carray) {
                  $colour_buffer .= "\\red{$carray['red']}\\green{$carray['green']}\\blue{$carray['blue']};\n";
              }
              
              $colour_buffer .= "}\n\n";
          }
          
          
          
          return $colour_buffer;
      }

      function getInformation()
      {
          $info_buffer = "";
          
          if (sizeof($this->info_table) > 0) {
              $info_buffer = "{\\info\n";
              
              foreach ($this->info_table as $name => $value) {
                  $info_buffer .= "{\\{$name} {$value}}";
              }
              
              $info_buffer .= "}\n\n";
          }
          
          
          
          return $info_buffer;
      }

      function getDefaultFont()
      {
          $font_buffer = "\\f{$this->font_face}\\fs{$this->font_size}\n";
  
          return $font_buffer;
      }

      function getPageSettings()
      {
          if ($this->page_orientation == 1)
              $page_buffer = "\\paperw{$this->page_width}\\paperh{$this->page_height}\n";
          
          else
              $page_buffer = "\\paperw{$this->page_height}\\paperh{$this->page_width}\\landscape\n";
          
          //$rtf .= '\paperw' . $this->getTwips( $this->doc_fmt['width'] );
          //$rtf .= '\paperh' . $this->getTwips( $this->doc_fmt['height'] );
          $page_buffer .= '\margl' . $this->margin_left;
          $page_buffer .= '\margr' . $this->margin_right;
          $page_buffer .= '\margt' . $this->margin_top;
          $page_buffer .= '\margb' . $this->margin_bottom;
          $page_buffer .= "\\pgncont\\pgndec\\pgnstarts1\\pgnrestart\n";
          
          return $page_buffer;
      }

      // Convert special characters to ASCII
      function specialCharacters($text)
      {
          $text_buffer = "";
          
          for ($i = 0; $i < strlen($text); $i++)
              $text_buffer .= $this->escapeCharacter($text[$i]);
 
          return $text_buffer;
      }

      // Convert special characters to ASCII
      function escapeCharacter($character)
      {
          $escaped = "";
          
          if (ord($character) >= 0x00 && ord($character) < 0x20)
              $escaped = "\\'" . dechex(ord($character));

          if ((ord($character) >= 0x20 && ord($character) < 0x80) || ord($character) == 0x09 || ord($character) == 0x0A)
              $escaped = $character;

          if (ord($character) >= 0x80 and ord($character) < 0xFF)
              $escaped = "\\'" . dechex(ord($character));

          switch (ord($character)) {
              case 0x5C:
              case 0x7B:
              case 0x7D:
                  $escaped = "\\" . $character;
                  
                  break;
          }

          return $escaped;
      }
      
      
      
      // Parse the text input to RTF
      
      function parseDocument()
      {
          //$doc_buffer = $this->specialCharacters($this->document);
          //if(preg_match("/<UL>(.*?)<\/UL>/mi", $doc_buffer)) {
          //  $doc_buffer = str_replace("<UL>", "", $doc_buffer);
          //  $doc_buffer = str_replace("</UL>", "", $doc_buffer);
          //  $doc_buffer = preg_replace("/<LI>(.*?)<\/LI>/mi", "\\f3\\'B7\\tab\\f{$this->font_face} \\1\\par", $doc_buffer);
          //}
          //$doc_buffer = preg_replace("/<P>(.*?)<\/P>/mi", "\\1\\par ", $doc_buffer);
          //$doc_buffer = preg_replace("/<B>(.*?)<\/B>/mi", "\\b \\1\\b0 ", $doc_buffer);
          //$doc_buffer = preg_replace("/<I>(.*?)<\/I>/mi", "\\i \\1\\i0 ", $doc_buffer);
          //$doc_buffer = preg_replace("/<U>(.*?)<\/U>/mi", "\\ul \\1\\ul0 ", $doc_buffer);
          //$doc_buffer = preg_replace("/<STRIKE>(.*?)<\/STRIKE>/mi", "\\strike \\1\\strike0 ", $doc_buffer);
          //$doc_buffer = preg_replace("/<SUB>(.*?)<\/SUB>/mi", "{\\sub \\1}", $doc_buffer);
          //$doc_buffer = preg_replace("/<SUP>(.*?)<\/SUP>/mi", "{\\super \\1}", $doc_buffer);
          //$doc_buffer = preg_replace("/<SMALL>(.*?)<\/SMALL>/mi", "\\fs{$this->font_size_small}\\b \\1\\b0\\fs{$this->font_size} ", $doc_buffer);
          //$doc_buffer = preg_replace("/<BIG>(.*?)<\/BIG>/mi", "\\fs{$this->font_size_big}\\b \\1\\b0\\fs{$this->font_size} ", $doc_buffer);
          //$doc_buffer = preg_replace("/<H1>(.*?)<\/H1>/mi", "\\pard\\qc\\fs40 \\1\\par\\pard\\fs{$this->font_size} ", $doc_buffer);
          //$doc_buffer = preg_replace("/<H2>(.*?)<\/H2>/mi", "\\pard\\qc\\fs32 \\1\\par\\pard\\fs{$this->font_size} ", $doc_buffer);
          //$doc_buffer = preg_replace("/<H1>(.*?)<\/H1>/mi", "\\fs48\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
          //$doc_buffer = preg_replace("/<H2>(.*?)<\/H2>/mi", "\\fs36\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
          //$doc_buffer = preg_replace("/<H3>(.*?)<\/H3>/mi", "\\fs27\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
          //$doc_buffer = preg_replace("/<HR(.*?)>/i", "\\brdrb\\brdrs\\brdrw30\\brsp20 \\pard\\par ", $doc_buffer);
          //$doc_buffer = str_replace("<BR>", "\\par ", $doc_buffer);
          //$doc_buffer = str_replace("<TAB>", "\\tab ", $doc_buffer);

          $doc_buffer = $this->nl2par($doc_buffer);

          return $doc_buffer;
      }

      function getTwips($measure)
      {
          if (ereg('(\-?[0-9]+(\.[0-9]+)?)( )*(mm|cm|in|px|p|pt)?', strtolower($measure), $arg)) {
              switch ($arg[4]) {
                  case 'mm':
                      return intval(floatval($arg[1]) * 5670);
                  case 'cm':
                      return intval(floatval($arg[1]) * 567);
                  case 'in':
                      return intval(floatval($arg[1]) * 1440);
                  case 'px':
                  case 'p':
                  case 'pt':
                      return intval(floatval($arg[1]) * 20);
                  default:
                      return intval(floatval($arg[1]));
              }
          } else {
              $this->error("Illegal measure ('" . $measure . "')", ERROR);
              return 0;
          }
      }
  }
?>
