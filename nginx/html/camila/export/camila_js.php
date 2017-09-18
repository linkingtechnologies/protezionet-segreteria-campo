<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2008 Umberto Bresciani

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


class CAMILA_JS_deck extends CHAW_deck
{
	function create_page()
	{
		$i = 0;
		$content='';
		while (isset($this->element[$i]))
		{
			$page_element = $this->element[$i];
			switch ($page_element->get_elementtype())
			{
				case HAW_PLAINTEXT:
				{
					$text=$this->element[$i];
					$content.=!isUTF8(stripslashes($text->text)) ? utf8_encode(stripslashes($text->text)) : stripslashes($text->text);
				  break;
				}
				case HAW_LINK:
				{
					$link = $this->element[$i];

				  break;
				}
			case HAW_TABLE:
			{
					$table = $this->element[$i];

					echo utf8_encode("var camila_table = new Array();\n");

					for ($a = 0; $a < $table->number_of_rows; $a++)
					{
						$row = $table->row[$a];

						echo utf8_encode("var camila_row");
						echo !isUTF8($a) ? utf8_encode($a) : $a;
						echo utf8_encode(" = new Array();\n");

						for ($b = 0; $b < $row->number_of_columns; $b++)
						{
						  $column = $row->column[$b];

						  if (is_object($column) && $column->get_elementtype()==HAW_PLAINTEXT) {
							   echo utf8_encode("camila_row");
							   echo !isUTF8($a) ? utf8_encode($a) : $a;
							   echo utf8_encode("[");
							   echo !isUTF8($b) ? utf8_encode($b) : $b;
							   echo utf8_encode("] = '");
							   echo !isUTF8(addslashes($column->text)) ? utf8_encode(addslashes($column->text)) : addslashes($column->text);
							   echo utf8_encode("';\n");
							}

							if (is_object($column) && $column->get_elementtype()==HAW_LINK) {
							   echo utf8_encode("camila_row");
							   echo !isUTF8($a) ? utf8_encode($a) : $a;
							   echo utf8_encode("[");
							   echo !isUTF8($b) ? utf8_encode($b) : $b;
							   echo utf8_encode("] = '");
							   echo !isUTF8(addslashes($column->label)) ? utf8_encode(addslashes($column->label)) : addslashes($column->label);
							   echo utf8_encode("';\n");          
							}
						}
						echo utf8_encode("camila_table[");
						echo !isUTF8($a) ? utf8_encode($a) : $a;
						echo utf8_encode("] = camila_row");
						echo !isUTF8($a) ? utf8_encode($a) : $a;
						echo utf8_encode(";\n");
					}

				  break;
			}
			}
			$i++;!isUTF8(stripslashes($text->text)) ? utf8_encode(stripslashes($text->text)) : stripslashes($text->text);
    }

    echo utf8_encode("var camila_content='");
    echo $content;
    echo utf8_encode("';\n");
  }
};

?>
