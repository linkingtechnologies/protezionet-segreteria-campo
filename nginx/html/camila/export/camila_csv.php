<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2010 Umberto Bresciani

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


class CAMILA_CSV_deck extends CHAW_deck
{
	function create_page()
	{
	      global $_CAMILA;

		if ($_REQUEST['camila_export_action'] == '' || $_REQUEST['camila_export_action'] == 'download')
		{
			header("Content-type: application/csv");
			header("Content-Disposition: filename=\"".$this->camila_export_safe_filename().".csv\"");
		}
		else
		{
			$_CAMILA['ob_filename'] = $this->camila_export_safe_filename() . '.' . $this->camila_export_get_ext();
                        if (!$this->camila_export_file_exists || $_REQUEST['camila_export_overwrite'] == 'y')
			    $_CAMILA['ob_file'] = fopen($this->camila_export_get_dir() . $this->camila_export_filename(),'w');
			ob_start('camila_ob_file_callback');
		}

		$i = 0;
		while (isset($this->element[$i]))
		{
			$page_element = $this->element[$i];
			switch ($page_element->get_elementtype())
			{
				case HAW_TABLE:
				{
					$table = $this->element[$i];
					for ($a = 0; $a < $table->number_of_rows; $a++)
					{
						$row = $table->row[$a];
						for ($b = 0; $b < $row->number_of_columns; $b++)
						{
							$column = $row->column[$b];
							if (is_object($column) && $column->get_elementtype()==HAW_PLAINTEXT)
							{
								$text = "\"".str_replace('"', '""', $column->get_text())."\"";
								echo isUTF8($text) ? $text : utf8_encode($text);
							}
							if (is_object($column) && $column->get_elementtype()==HAW_LINK)
							{
								$text = "\"".str_replace('"', '""', $column->get_label())."\"";
								echo isUTF8($text) ? $text : utf8_encode($text);
							}
							if ($b < $row->number_of_columns-1)
							   echo utf8_encode(",");
							else
							   echo utf8_encode("\n");
						}
					}
				  break;
				  }
			}
			$i++;
		}

		if ($_REQUEST['camila_export_action'] != '' && $_REQUEST['camila_export_action'] != 'download')
			ob_end_flush();

	}
};

?>
