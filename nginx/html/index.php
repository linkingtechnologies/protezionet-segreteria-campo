<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2016 Umberto Bresciani

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

    function camila_read_constant_from_config($file, $constant) {

        $value = '';

        $file_handle = fopen($file, 'r');

        while (!feof($file_handle)) {
            $line = fgets($file_handle);

            $pos = strpos($line, $constant);

            if ($pos !== false) {

                $pos = strpos($line, 'define');

                if ($pos !== false) {

                    $arr = explode(',', $line);
                    $value = camila_extract_string($arr[1]);
                }
            }
        }

        fclose($file_handle);

        return $value;

    }


    function camila_extract_string($string) {

        $value = '';

        if (preg_match('/"([^"]+)"/', $string, $m)) {
            $value = $m[1];   
        } else {

            if (preg_match('/\'([^\']+)\'/', $string, $m)) {
                $value = $m[1];   
            } else {}
        }

        return $value;

    }


    $count = 0;
    $litems = '';

    if ($dh = opendir('./app/')) {
        while (($file = readdir($dh)) !== false) {
			//echo $file;
            $file2 ='./app/'.$file.'/var/config.php';
			if (is_file($file2)) {

                $name = camila_read_constant_from_config($file2, 'CAMILA_APPLICATION_NAME');

                $appname = $file;
                if ($name != '')
                    $appname = $name;

                $url = $file;
                $litems .= '<li><a href=\'app/'.$url.'/\'>'.$appname.'</a></li>';
                $count++;
            }
        }
        closedir($dh);
    }

    if ($count>1) {
        echo $litems;
    } else {
        $location='app/'.$url.'/';
        header("Location: $location");
    }


?>