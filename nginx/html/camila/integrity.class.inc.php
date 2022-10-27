<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2022 Umberto Bresciani

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

//require_once(CAMILA_LIB_DIR.'adodb5/adodb.inc.php');
require_once(CAMILA_VENDOR_DIR . '/adodb/adodb-php/adodb.inc.php');

require_once('worktable.class.inc.php');

class CamilaIntegrity
{
    public $camilaWT;
	private $confXml;

    function CamilaIntegrity($xmlFile)
    {	
		$this->confXml = $xmlFile;
    }

	function loadXmlFromFile() {
		$conf = new SimpleXMLElement(file_get_contents($this->confXml));
		return $conf;
	}

	function createGraph($name, $obj, $data) {
		require(CAMILA_DIR.'export/phpgraphlib/phpgraphlib.php');
		require(CAMILA_DIR.'export/phpgraphlib/phpgraphlib_pie.php');
		if (count($data)>0)
		{
			if ((string)$obj->type == 'pie')
			{
				$graph = new PHPGraphLibPie((int)$obj->width, (int)$obj->height);
				$graph->addData($data);
				$graph->setTitle($obj->title);
				$graph->setLabelTextColor('50,50,50');
				$graph->setLegendTextColor('50,50,50');
				$graph->createGraph();
			}
			else if ((string)$obj->type == 'bar')
			{
				$graph = new PHPGraphLib((int)$obj->width, (int)$obj->height);
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
	
	
	
	function getChecks() {
		$conf = $this->loadXmlFromFile();
		return $conf->checks;
	}
	
	function check($obj) {
		$result = $this->camilaWT->startExecuteQuery($obj->query);
		$count = $result->RecordCount();
		$ret = new stdClass;
		
		if ($count > 0)
		{
			$ret->code = (string)$obj->result->multi->code;
			$ret->message = (string)$obj->result->multi->message;
			$ret->count = $count;
		}
		else
		{
			$ret->code = (string)$obj->result->none->code;
			$ret->message = (string)$obj->result->none->message;
		}
		$this->camilaWT->endExecuteQuery();
		//print_r($data);
		return $ret;
	}


}

?>