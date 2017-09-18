<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2012 Umberto Bresciani

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


class CAMILA_XML_deck extends CHAW_deck
{
    function create_page()
    {
        global $_CAMILA;
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        echo "<result>\n";
        
        $i = 0;
        while (isset($this->element[$i])) {
            $page_element = $this->element[$i];
            switch ($page_element->get_elementtype()) {
                case HAW_TABLE: {
                    echo "<table>\n";
                    echo $this->xmlTag('count', null, $_CAMILA['report_record_count']);

                    $table = $this->element[$i];

if ($_REQUEST['camila_export_no_table_data']!='y')
{
                    echo "<cols>\n";
                    
                    $a   = 0;
                    $row = $table->row[$a];
                    for ($b = 0; $b < $row->number_of_columns; $b++) {
                        $column = $row->column[$b];
                        
                        if (is_array($column))
                            $column = $column[0];
                        
                        if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                            $text = $column->get_text();
                            $text = isUTF8($text) ? $text : utf8_encode($text);
                        }
                        if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                            $text = $column->get_label();
                            $text = isUTF8($text) ? $text : utf8_encode($text);
                        }
                        
                        echo "<col>\n";
                        echo $this->xmlTag('id', null, $b + 1);
                        echo $this->xmlTag('name', null, isUTF8($column->field) ? $column->field : utf8_encode($column->field));
                        echo $this->xmlTag('label', null, $text);
                        echo $this->xmlTag('type', null, isUTF8($column->metatype) ? $column->metatype : utf8_encode($column->metatype));
                        echo "</col>\n";
                        
                        
                    }
                    echo "</cols>\n";
                    
                    echo "<rows>\n";
                    for ($a = 1; $a < $table->number_of_rows; $a++) {
                            echo "<row id=\"".$a."\">\n";

                        $row = $table->row[$a];
                        for ($b = 0; $b < $row->number_of_columns; $b++) {
                            $column = $row->column[$b];
                            $text   = "";
                            if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT) {
                                $text = $column->get_text();
                                $text = isUTF8($text) ? $text : utf8_encode($text);
                            }
                            if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                $text = $column->get_label();
                                $text = isUTF8($text) ? $text : utf8_encode($text);
                            }

                            echo "<col>\n";
                            echo $this->xmlTag('id', null, $b + 1);
                            echo $this->xmlTag('value', null, $text);
                            echo "</col>\n";

                        }
                            echo "</row>\n";                        
                        
                    }
                    
                    echo "</rows>\n";
}                    
                    echo "</table>\n";
                    break;
                }
                    
            }
            $i++;
        }
        
        echo "</result>\n";

    }
    
    
    function xmlsafe($s, $intoQuotes = 0)
    {
        if ($intoQuotes)
            return htmlspecialchars($s);
        else
            return htmlspecialchars($s,ENT_NOQUOTES);
    }

    function xmlTag($element, $attribs, $contents = NULL)
    {
        $out = '<' . $element;
        foreach ($attribs as $name => $val)
            $out .= ' ' . $name . '="' . $this->xmlsafe($val, 1) . '"';
        if ($contents === '' || is_null($contents))
            $out .= '/>';
        else
            $out .= '>' . $this->xmlsafe($contents) . "</$element>";
        return $out . "\n";
    }
    
}
?>