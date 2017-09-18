<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2016 Umberto Bresciani

    Camila PHP Framework is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Camila PHP Framework is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Camila PHP Framework. If not, see <http://www.gnu.org/licenses/>. */

require_once(CAMILA_LIB_DIR.'adodb5/adodb.inc.php');
require_once('worktable.class.inc.php');

class CamilaReport
{
    public $camilaWT;

    function CamilaReport($db)
    {
		$this->camilaWT = new CamilaWorkTable();
		$this->camilaWT->wtTable = CAMILA_TABLE_WORKT;
		$this->camilaWT->wtColumn = CAMILA_TABLE_WORKC;
		$this->camilaWT->db = $db;
    }

	function loadXmlFromFile($file) {
		$reports = new SimpleXMLElement(file_get_contents($file));
		return $reports;
	}

	function createGraph($name, $obj, $data, $filename = null) {
		require_once(CAMILA_DIR.'export/phpgraphlib/phpgraphlib.php');
		require_once(CAMILA_DIR.'export/phpgraphlib/phpgraphlib_pie.php');
		if (count($data)>0)
		{
			if ((string)$obj->type == 'pie')
			{
				$graph = new PHPGraphLibPie((int)$obj->width, (int)$obj->height, $filename);
				$graph->addData($data);
				$graph->setTitle($obj->title);
				$graph->setLabelTextColor('50,50,50');
				$graph->setLegendTextColor('50,50,50');
				$graph->createGraph();
			}
			else if ((string)$obj->type == 'bar')
			{
				$graph = new PHPGraphLib((int)$obj->width, (int)$obj->height, $filename);
				$graph->addData($data);
				$graph->setTitle($obj->title);
				//$graph->setLabelTextColor('50,50,50');
				//$graph->setLegendTextColor('50,50,50');
				$graph->setupXAxis(40);
				$graph->createGraph();

			}
		}
	}

	function createTable($name, $obj, $data) {
		$html = '';
		if (count($data)>0) {
			$html = "<p>$obj->title</p><table>";
			$sum = $obj->sum;
			$total = 0;
			foreach($data as $key => $val) {
				$html .= "<tr><td>$key</td><td>$val</td></tr>";
				if ($sum != '')
				{
					$total += $val;
				}
			}
			if ($total>0) {
				$html .= "<tr><td></td><td>$total</td></tr>";
			}

			$html .= '</table>';
		}
		return $html;
	}

}

?>