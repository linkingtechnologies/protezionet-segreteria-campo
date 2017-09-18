<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2017 Umberto Bresciani

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

$camilaWT  = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

$rSheet = $camilaWT->getWorktableSheetId('REPORT');
$aSheet = $camilaWT->getWorktableSheetId('BROGLIACCIO');

require(CAMILA_LIB_DIR.'/fpdf/fpdf.php');

class PDF extends FPDF
{

function Header()
{
	$t = new CamilaTemplate('it');
    // Select Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    //$this->Cell(80);
    // Framed title
    $this->Cell(0,10,'Intervento "' . $t->getParameters()['evento'] . '"',0,0,'C');
    // Line break
    $this->Ln(20);
}

function Footer()
{
    // Go to 1.5 cm from bottom
    $this->SetY(-15);
    // Select Arial italic 8
    $this->SetFont('Arial','I',8);
    // Print centered page number
    $this->Cell(0,10,CAMILA_APPLICATION_NAME . ' - Report del '.date('m/d/Y') . ' ore ' . date('H:i').' - Pagina '.$this->PageNo(),0,0,'C');
}

function ImprovedTable2($obj, $data)
{
	$this->Write(10,$obj->title);
    // Column widths
    $w = array(90, 30);
    // Header
    //for($i=0;$i<count($header);$i++)
    //    $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    // Data

	$sum = $obj->sum;
	$total = 0;
    foreach($data as $k=>$v)
    {
        $this->Cell($w[0],6,$k,1);
        $this->Cell($w[1],6,$v,1,1);
        //$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
        //$this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
        //$this->Ln();
		if ($sum != '')
				{
					$total += $v;
				}
    }
	
			if ($total>0) {
				$this->Cell($w[0],6,'',1);
        $this->Cell($w[1],6,$total,1,1);

			}
	
    // Closing line
    //$this->Cell(array_sum($w),0,'','T');
}

}

$camilaReport = new CamilaReport($_CAMILA['db']);
$reports = $camilaReport->loadXmlFromFile(CAMILA_HOMEDIR.'/plugins/'.basename(dirname(__FILE__)).'/conf/reports.xml');

if (isset($_REQUEST['gid'])) {

	foreach ($reports as $k => $v) {
		if ($_REQUEST['rid']== ($v->id)) {
			$query = $v->query;
			$data = $camilaReport->camilaWT->queryWorktableDatabase($query);

			foreach ($v->graphs as $k2 => $v2) {
				if ($_REQUEST['gid'] == $v2->graph->id) {
					if (count($data)>0)
						$camilaReport->createGraph($v2->graph->id, $v2->graph, $data);
				}
			}
		}
	}
	exit;
}


if (!isset($_REQUEST['export'])) {
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
	$camilaUI->insertButton('?dashboard=m1&export=pdf', 'Situazione attuale', 'file');
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
	$camilaUI->insertButton('cf_worktable'.$aSheet.'.php', 'Brogliaccio attività', 'list');
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
	$camilaUI->insertButton('cf_worktable'.$rSheet.'.php', 'Report operativi', 'list');
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));

	$camilaUI->insertDivider();
	
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));	
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));	
	$camilaUI->insertButton('?dashboard=12', 'Mappa servizi', 'map-marker');
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
	$camilaUI->insertButton('?dashboard=12l', 'Mappa servizi (locale)', 'map-marker');
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
	$camilaUI->insertButton('?dashboard=01', 'Riepilogo ospiti e risorse', 'list-alt');
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
	
	$camilaUI->insertDivider();

	$camilaUI->insertTitle('Situazione attuale', 'signal');

	$camilaUI->insertDivider();
		
	foreach ($reports as $k => $v) {
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));	
		$query = $v->query;
		$data = $camilaReport->camilaWT->queryWorktableDatabase($query);

		$arr = $v->graphs->graph;
		for ($i=0; $i<count($arr);$i++)
		{
			$v3 = $arr[$i];
			if ($v3->type == 'pie' || $v3->type == 'bar') {
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
				$image1 = new HAW_image("?dashboard=m1&rid=".$v2->id.'&gid='.$v3->id, "?dashboard=m1&rid=".$v->id.'&gid='.$v3->id, ":-)");
				$image1->set_br(1);
				if (count($data)>0)
				{
					$_CAMILA['page']->add_image($image1);
				}
				else
				{
					$camilaUI->insertWarning($v3->title . ' - Nessun dato!');
				}
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
			}
			if ($v3->type == 'table') {
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
				$myDiv = new HAW_raw(HAW_HTML, $camilaReport->createTable($v3->id, $v3, $data));
				$_CAMILA['page']->add_raw($myDiv);
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
			}
		}
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
		
		$camilaUI->insertDivider();
	}
}
else
{
		/*ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	*/

	$pdf = new PDF();
	// Column headings
	//$header = array('Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)');
	// Data loading
	//$data = $pdf->LoadData('countries.txt');
	$pdf->SetFont('Arial','',10);
	//$pdf->AddPage();
	//$pdf->BasicTable($header,$data);
	//$pdf->AddPage();
	//$pdf->ImprovedTable($header,$data);
	//$pdf->AddPage();
	//$pdf->FancyTable($header,$data);
	
	$pageAdded = false;

	foreach ($reports as $k => $v) {
		$query = $v->query;
		$data = $camilaReport->camilaWT->queryWorktableDatabase($query);

		$arr = $v->graphs->graph;
		for ($i=0; $i<count($arr);$i++)
		{
			$v3 = $arr[$i];
			if ($v3->type == 'pie' || $v3->type == 'bar') {
				
				if (count($data)>0) {
					$pdf->AddPage();
					$pageAdded = true;
					$f = CAMILA_TMP_DIR.'/g'.$v->id.'_'.$v3->id.'.png';
					$camilaReport->createGraph($v3->id, $v3, $data, $f);

					$pdf->Image($f,null,null,null,null,'PNG');
				}
			}
			if ($v3->type == 'table') {
				//print_r($data);
				if (count($data)>0) {
					if (!$pageAdded) {
						$pdf->AddPage();
						$pageAdded = true;
					}
					$pdf->ImprovedTable2($v3,$data);
					$pdf->Ln();
				}
			}
		}
	}
	$date = $_CAMILA['db']->UserDate(date('Y-m-d'), camila_get_locale_date_adodb_format());
	$pdf->SetTitle('Report '.$date.'.pdf');
	$pdf->Output('I', 'Report '.$date.'.pdf');
	exit;
}

?>