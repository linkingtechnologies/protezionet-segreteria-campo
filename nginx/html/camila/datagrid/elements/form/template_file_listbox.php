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


  class form_template_file_listbox extends form_fm_file_listbox
  {

      function _get_select()
      {

          global $_CAMILA;


          $mySelect = new CHAW_select($this->key);

          /*if (is_dir($this->basedir)) {
              if ($dh = opendir($this->basedir)) {
                  while (($file = readdir($dh)) !== false) {
                      if ($file != '.' && $file != '..' && !is_dir($this->basedir . '/' . $file)) {
                           $ext = $this->_find_extension($file);
                           if (strlen($ext)>0)
                               $filename = substr($file, 0, -strlen($ext)-1);
                           else
                               $filename = $file;

                           //if ($file == $this->value)
                           //    $mySelect->add_option($filename, $file, HAW_SELECTED);
                           //else
//							   echo substr(strtolower($file), 0, strlen($_CAMILA['page_short_title'])).'_';
			   if (substr(strtolower($file), 0, strlen($_CAMILA['page_short_title'])+1) == strtolower($_CAMILA['page_short_title'] . '_'))
                               $mySelect->add_option(substr($filename, strlen($_CAMILA['page_short_title']) + 1), $file);
                      }
                  }
              closedir($dh);
              }
          }*/
		  
			if (is_dir($this->basedir)) {
				$dir = $this->basedir;
				$dh  = opendir($dir);
				while (false !== ($filename = readdir($dh))) {
					$files[] = $filename;
				}
			}
			
			

			$pDir = CAMILA_APP_PATH . '/plugins/';
			$pdh  = opendir($pDir);

			if (file_exists($pDir)) {
				while (false !== ($dir = readdir($pdh))) {
					$tDir = $pDir. $dir . '/templates/' . $_CAMILA['lang'];
					if ($dir != '.' && $dir != '..' && is_dir($tDir)) {
						$dh2  = opendir($tDir);
						while (false !== ($filename = readdir($dh2))) {
							$files[] = $filename;
						}
					}
				}
			}


			sort($files);
			
			foreach ($files as $name => $file)
			{
				if ($file != '.' && $file != '..' && !is_dir($this->basedir . '/' . $file)) {
                           $ext = $this->_find_extension($file);
                           if (strlen($ext)>0)
                               $filename = substr($file, 0, -strlen($ext)-1);
                           else
                               $filename = $file;

			   if (substr(strtolower($file), 0, strlen($_CAMILA['page_short_title'])+1) == strtolower($_CAMILA['page_short_title'] . '_'))
                               $mySelect->add_option(substr($filename, strlen($_CAMILA['page_short_title']) + 1), $file);
                      }
			}	  
			//print_r($files);
			
			
			return $mySelect;
      }

  }
?>
