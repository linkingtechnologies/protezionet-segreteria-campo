<?php
/**
 * Xml2Pdf horizontal bar graph plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.hbar.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 */

// doc {{{

/**
 * Plugin class xml2pdf_graph_hbar
 * 
 * Draw an horizontal bar graph .
 *
 * <code>
 * <graph type='hbar' width='100' height='30' left='80' top='-30' legend='%l : %v (%p)' max='190'>
 *   <value legend='valeur' data='150'/>
 *   <value legend='moins' data='42'/>
 *   <value legend='plus' data='180'/>
 * </graph>
 * </code>
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.hbar.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 * @tutorial Xml2Pdf/Xml2Pdf.Graph.hbar.pkg
 */ // }}}
class xml2pdf_graph_hbar {
    // xml2pdf_graph_hbar::__construct() {{{
    
    /**
     * Constructor.
     *
     */
    public function __construct() {}

    // }}}
    // xml2pdf_graph_hbar::render() {{{

    /**
     * Draw an horizontal bar graph.
     *
     * @param Object $graph object xml2pdf_tag_graph
     * @return void 
     */
    public static function render($graph) {
        $graph->pdf->SetFont('Courier', '', $graph->fontSize);
        $graph->setLegends();
 
        $XPage = $graph->pdf->GetX();
        $YPage = $graph->pdf->GetY();
        $marge = 2;
        $YDiag = $YPage + $marge;
        $hDiag = floor($graph->height - $marge * 2);
        $XDiag = $XPage + $marge * 2 + $graph->legendWidth;
        $lDiag = floor($graph->width - $marge * 3 - $graph->legendWidth);
        if($graph->color == null) {
            $graph->color = Xml2Pdf::getColor();
        }
        if ($graph->maxValRepere == 0) {
            $graph->maxValRepere = max($graph->data);
        }
        $valIndRepere = ceil($graph->maxValRepere / $graph->nbIndRepere);
        $graph->maxValRepere = $valIndRepere * $graph->nbIndRepere;
        $lRepere = floor($lDiag / $graph->nbIndRepere);
        $lDiag = $lRepere * $graph->nbIndRepere;
        $unite = $lDiag / $graph->maxValRepere;
        $hBaton = floor($hDiag / ($graph->nbVal + 1));
        $hDiag = $hBaton * ($graph->nbVal + 1);
        $eBaton = floor($hBaton * 80 / 100);
 
        $graph->pdf->SetLineWidth(0.2);
        $graph->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
 
        //Echelles
        $tab = Xml2Pdf::ConvertColor('#dcdcdc');
        $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
        for ($i=0 ; $i<=$graph->nbIndRepere ; $i++) {
            $xpos = $XDiag + $lRepere * $i;
            if($i>0 && $i<$graph->nbIndRepere) {
                $graph->pdf->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
            }
            $val = $i * $valIndRepere;
            $xpos = $XDiag + $lRepere * $i - $graph->pdf->GetStringWidth($val) / 2;
            $ypos = $YDiag + $hDiag - $marge;
            $graph->pdf->Text($xpos, $ypos, $val);
        }
 
        $tab = Xml2Pdf::ConvertColor('#000000');
        $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
        $graph->pdf->SetFont('Courier', '', $graph->fontSize);
        $tab = Xml2Pdf::ConvertColor($graph->color);
        $graph->pdf->SetFillColor($tab['r'], $tab['g'], $tab['b']);
        $i=0;
        foreach($graph->data as $val) {
            //Barre
            $xval = $XDiag;
            $lval = (int)($val * $unite);
            $yval = $YDiag + ($i + 1) * $hBaton - $eBaton / 2;
            $hval = $eBaton;
            $graph->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
            //Légende
            $graph->pdf->SetXY(0, $yval);
            $graph->pdf->Cell($xval - $marge, $hval, $graph->legends[$i],0,0,'R');
            $i++;
        }
    }
    
    // }}}
}
 
?>
