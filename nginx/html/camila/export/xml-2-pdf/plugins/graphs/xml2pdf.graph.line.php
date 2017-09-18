<?php
/**
 * Xml2Pdf curve graph plugin file
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.line.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 */

// doc {{{

/**
 * Plugin class xml2pdf_graph_line
 * 
 * Draw a curve graph.
 *
 * <code>
 * <graph type='line' width='120' height='50' left='60' 
 *     top='220' position='absolute' color='#0197cb' legend='' repere='10'>
 *   <line legend='courbe1'>
 *     <point x='0' y='10'/>
 *     <point x='5' y='15'/>
 *     <point x='10' y='22'/>
 *     <point x='15' y='1'/>
 *     <point x='20' y='12'/>
 *     <point x='25' y='15'/>
 *   </line>
 *   <line legend='courbe2' color='#0197cb'>
 *     <point x='0' y='17'/>
 *     <point x='8' y='15'/>
 *     <point x='12' y='5'/>
 *     <point x='15' y='6'/>
 *     <point x='20' y='12'/>
 *     <point x='25' y='8'/>
 *   </line>
 * </graph>
 * </code>
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.line.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 * @tutorial Xml2Pdf/Xml2Pdf.Graph.line.pkg
 */ // }}}
class xml2pdf_graph_line {
    // xml2pdf_graph_line::__construct() {{{
    
    /**
     * Constructor
     *
     */
    public function __construct() {}

    // }}}
    // xml2pdf_graph_line::render() {{{

    /**
     * Draw a curve type graph
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
        $hDiag = $graph->height;
        $lDiag = $graph->width - $graph->legendWidth - 5 * $marge-5;
        $XDiag = $XPage + $marge;
        $YDiag = $YPage + $marge;
 
        $xMax=0;
        $yMax=0;
        foreach($graph->data as $line) {
            if($xMax < max($line['x'])) {
                $xMax = max($line['x']);
            }
            if($yMax < max($line['y'])) {
                $yMax = max($line['y']);
            }
        }
 
        $uniteX = $lDiag /$xMax;
        $uniteY = ($hDiag) / ($yMax+5);
        $graph->pdf->SetLineWidth(0.2);
        $graph->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
 
        //reperes
        $tab = Xml2Pdf::ConvertColor('#dcdcdc');
        $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
 
        $deltaX = $lDiag / ($graph->nbIndRepere);
        $deltaY = $hDiag / ($graph->nbIndRepere);
        $graph->pdf->SetLineWidth(0.2);
 
        for($i=0 ; $i<=$graph->nbIndRepere ; $i++) {
            if($i>0 && $i<$graph->nbIndRepere) {
                $graph->pdf->Line($XDiag, $YDiag+($i*$deltaY), 
                                  $XDiag+$lDiag, $YDiag+($i*$deltaY));
                $graph->pdf->Line($XDiag+($i*$deltaX), $YDiag, 
                                  $XDiag+($i*$deltaX), $YDiag+$hDiag);
            }
            $valY = floor(($hDiag-($i*$deltaY))/$uniteY);
            $valX = floor(($i*$deltaX)/$uniteX);
            $lenY = $graph->pdf->GetStringWidth($valY);
            $lenX = $graph->pdf->GetStringWidth($valX);
            $graph->pdf->Text($XDiag-$lenY-2, $YDiag+($i*$deltaY), $valY);
            $graph->pdf->Text($XDiag+($i*$deltaX)-$lenX/2, $YDiag+$hDiag+5, $valX);
        }
        //lignes
        $nbPoint=0;
        $ligne=0;
        $graph->pdf->SetLineWidth(0.5);
        foreach($graph->data as $key=>$line) {
            if ($graph->colors[$key] == null) {
                $graph->colors[$key] = Xml2Pdf::getColor();
            }
            $tab = Xml2Pdf::ConvertColor($graph->colors[$key]);
            $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
            $nbPoint=min(count($line['x']), count($line['y']));
            for($i=0 ; $i<$nbPoint-1 ; $i++) {
                $xd = $XDiag + $line['x'][$i]*$uniteX;
                $yd = $YDiag+ $hDiag-$line['y'][$i]*$uniteY;
                $xf = $XDiag + $line['x'][$i+1]*$uniteX;
                $yf = $YDiag+ $hDiag-$line['y'][$i+1]*$uniteY;
                $graph->pdf->Line($xd, $yd, $xf, $yf);
            }
            $ligne++;
        }
        //Légende
        $graph->pdf->SetLineWidth(0.2);
        $tab = Xml2Pdf::ConvertColor('#000000');
        $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
 
        $graph->pdf->SetFont('Courier', '', $graph->fontSize);
        $x1 = $XPage + $lDiag + 4 * $marge;
        $x2 = $x1 + 5 + $marge;
        $y1 = $YDiag +3*$marge;
        for($i=0; $i<$graph->nbVal; $i++) {
            $tab = Xml2Pdf::ConvertColor($graph->colors[$graph->legends[$i]]);
            $graph->pdf->SetFillColor($tab['r'],$tab['g'],$tab['b']);
            $graph->pdf->Rect($x1, $y1+$i*$marge*2, 5,5, 'DF');
            $graph->pdf->SetXY($x2,$y1+$i*$marge*2);
            $graph->pdf->Cell(0,5,$graph->legends[$i]);
            $y1+= $marge;
        }
    }
    
    // }}}  
}
?>
