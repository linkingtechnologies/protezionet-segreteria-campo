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



    $count = 0;
    $litems = '';

    if ($dh = opendir('./')) {
        while (($file = readdir($dh)) !== false) {
            if (is_file('./'.$file.'/var/config.php')) {

                if ($dh2 = opendir('./'.$file.'/lang/')) {
                        while (($file2 = readdir($dh2)) !== false) {
                            if (substr($file2,-9) == '.lang.php') {

                                $url = $file.'/index.php?username=admin&pwd=admin&js=enabled&camila_pwloginbox=yes&submit=yes&lang='.substr($file2,0,2);
                                $litems .= '<li><a href=\''.$url.'\'>'.$file.' ('.substr($file2,0,2).')</a></li>';
                                $count++;

                            }
                        }
                        closedir($dh2);
                 }
            }
        }
        closedir($dh);
    }

    if ($count>1) {
        echo $litems;
    } else {
        $location='http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'/'.$url;
        header("Location: $location");
    }
?>