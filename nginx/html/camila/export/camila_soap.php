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


require_once(CAMILA_LIB_DIR . 'nusoap/nusoap.php');

class CAMILA_SOAP_deck extends CHAW_deck
{
  function GetTasks($parentTaskId)
  {global $page;
   $xmlData="";
    $i = 0;
		while (isset($page->element[$i]))
		{
		  
			$page_element = $page->element[$i];

			switch ($page_element->get_elementtype())
			{
			
				case HAW_TABLE:
				{

					$table = $page->element[$i];

					for ($a = 0; $a < $table->number_of_rows; $a++)
					{
					  $xmlData.= "<row>";
						$row = $table->row[$a];
						for ($b = 0; $b < $row->number_of_columns; $b++)
						{
							$column = $row->column[$b];
							if (is_object($column) && $column->get_elementtype()==HAW_PLAINTEXT)
							{
							  $text = '<col>'.$column->get_text().'</col>';

								$xmlData .= !isUTF8($text) ? $text : utf8_encode($text);
							}
							if (is_object($column) && $column->get_elementtype()==HAW_LINK)
							{
							  $text = '<col>'.$column->get_label().'</col>';
							  $xmlData .= !isUTF8($text) ? $text : utf8_encode($text);
							}

						}
						$xmlData.= "</row>";
					}
				  break;
				  }
			}
			$i++;
			}

			return array("somearray" => array(6 => 5, 13 => 9, "a" => 42));
			//return $xmlData;
  }
  
	function create_page()
	{
    $s = new soap_server;

    $s->register('CAMILA_SOAP_deck.GetTasks');

    $s->service(file_get_contents('php://input'));
  }

};

?>
