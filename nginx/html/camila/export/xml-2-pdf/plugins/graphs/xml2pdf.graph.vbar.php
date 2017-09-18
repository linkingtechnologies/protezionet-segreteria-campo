<?php
/**
 * Xml2Pdf vertical bar graph plugin file
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.vbar.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 */

// doc {{{

/**
 * Plugin class xml2pdf_graph_vbar
 * 
 * Draw a vertical bar graph.
 *
 * <code>
 * <graph type='vbar' width='100' height='100' color='#0197cb' legend='%l' max='190' repere='10'>
 *   <value legend='2003' data='150'/>
 *   <value legend='2004' data='42'/>
 *   <value legend='2005' data='180'/>
 * </graph>
 * </code>
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.vbar.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 * @tutorial Xml2Pdf/Xml2Pdf.Graph.vbar.pkg
 */ // }}}
class xml2pdf_graph_vbar {
    // xml2pdf_graph_vbar::__construct() {{{    
    
    /**
     * Constructeur
     *
     */
    public function __construct() {}

    // }}}
    // xml2pdf_graph_vbar::render() {{{

    /**
     * Draw a vertical bar graph.
     *
     * @param Object $graph Object Xml2Pdf_Tag_graph
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
        $XDiag = $XPage + $marge * 2;
        $lDiag = floor($graph->width - $marge * 3);
        if($graph->color == null) {
            $graph->color = Xml2Pdf::getColor();
        }
        if (!$graph->maxValRepere) {
            $graph->maxValRepere = max($graph->data);
        }
        $valIndRepere = ceil($graph->maxValRepere / $graph->nbIndRepere);
        $graph->maxValRepere = $valIndRepere * $graph->nbIndRepere;
        $lRepere = floor($hDiag / $graph->nbIndRepere);
        $hDiag = $lRepere * $graph->nbIndRepere;
        $unite = $hDiag / $graph->maxValRepere;
        $hBaton = floor($lDiag / ($graph->nbVal + 1));
        $lDiag = $hBaton * ($graph->nbVal + 1);
        $eBaton = floor($hBaton * 80 / 100);
 
        $graph->pdf->SetLineWidth(0.2);
        $graph->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
 
        //Echelles
        $tab = Xml2Pdf::ConvertColor('#dcdcdc');
        $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
        for ($i = 0; $i <= $graph->nbIndRepere; $i++) {
            $ypos = $YDiag+$hDiag - $lRepere * $i;
            if($i>0 && $i<$graph->nbIndRepere) {
                $graph->pdf->Line($XDiag,$ypos , $XDiag + $lDiag,$ypos);
            }
            $val = $i * $valIndRepere;
            $xpos = $XDiag - $graph->pdf->GetStringWidth($val) -5;
            $graph->pdf->Text($xpos, $ypos, $val);
        }
        //Barre
        $tab = Xml2Pdf::ConvertColor('#000000');
        $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
        $graph->pdf->SetFont('Courier', '', $graph->fontSize);
        $tab = Xml2Pdf::ConvertColor($graph->color);
        $graph->pdf->SetFillColor($tab['r'], $tab['g'], $tab['b']);
        $i=0;
        foreach($graph->data as $val) {
            $lval = $eBaton;
            $hval = (int)($val * $unite);
            $xval = $XDiag + ($i +1) * $hBaton-$eBaton/2;
            $yval = $YDiag+$hDiag-$hval;
            $graph->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
            //Légende
            $graph->pdf->SetXY($xval, $YDiag+$hDiag);
            $graph->pdf->Cell($graph->pdf->GetStringWidth($graph->legends[$i])+$marge, 5, $graph->legends[$i],0,0,'R');
            $i++;
        }
    }
    
    // }}}    
}
 
?>
