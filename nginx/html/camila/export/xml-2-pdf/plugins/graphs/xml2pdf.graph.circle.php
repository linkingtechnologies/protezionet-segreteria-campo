<?php
/**
 * Xml2Pdf circle graph plugin file
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.circle.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 */

// doc {{{

/**
 * plugin class xml2pdf_graph_circle
 * 
 * Draw a circular graph.
 *
 * <code>
 * <graph type='circle' width='100' height='80' mode='3D' legend='%l (%p)' fontsize='10'>
 *   <value legend='Lille' data='1510' color='#f0f5ff'/>
 *   <value legend='Paris' data='1610' color='#feefc1'/>
 *   <value legend='Toulouse' data='1400' color='#ffaaaa'/>
 *   <value legend='Lyon' data='700'/>
 *   <value legend='Bordeaux' data='1001'/>
 * </graph>
 *
 * </code>
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.graph.circle.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Graph
 * @tutorial Xml2Pdf/Xml2Pdf.Graph.circle.pkg
 */ // }}}
class xml2pdf_graph_circle {
    // xml2pdf_graph_circle::__construct() {{{
    /**
     * Constructor.
     *
     */
    public function __construct() {}

    // }}}
    // xml2pdf_graph_circle::render() {{{

    /**
     * Draw a circular graph
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
        $hLegende = 5;
        $rayon = min($graph->width - $marge * 4 - $hLegende - $graph->legendWidth, 
                     $graph->height - $marge * 2);
        $rayon = floor($rayon / 2);
        $XDiag = $XPage + $marge + $rayon;
        $YDiag = $YPage + $marge + $rayon;
        //Secteurs
        $graph->pdf->SetLineWidth(0.2);
 
        $loop = $graph->mode=='3d'?4:1;
        
        for($delta=0 ; $delta<$loop ; $delta++) {
            $angleDebut = 0;
            $angleFin = 0;
            $i = 0;
            reset($graph->data);
            foreach($graph->data as $val) {
                $angle = floor(($val * 360) / doubleval($graph->sum));
                if ($angle != 0) {
                    $angleFin = $angleDebut + $angle;
                    if($graph->colors[$i] != null) {
                        $tab = Xml2Pdf::ConvertColor($graph->colors[$i]);
                        $graph->pdf->SetFillColor($tab['r'],$tab['g'],$tab['b']);
                    } else {
                        $tab = Xml2Pdf::getColor();
                        $graph->pdf->SetFillColor($tab['r'],$tab['g'],$tab['b']);
                        $graph->colors[$i] = $tab;
 
                    }
                    if($delta>0 && $delta<$loop-1) {
                        $graph->pdf->SetDrawColor($tab['r'],$tab['g'],$tab['b']);
                    } else {
                        $graph->pdf->SetDrawColor(0,0,0);
                    }
                    //$this->sector($XDiag, $YDiag-$delta, $rayon, 
                    xml2pdf_graph_circle::sector($XDiag, $YDiag-$delta, $rayon, 
                                   $angleDebut, $angleFin,$graph->colors[$i]);
                    $angleDebut += $angle;
                }
                $i++;
            }
            if ($angleFin != 360) {
                //$this->sector($XDiag, $YDiag-$delta, $rayon, 
                xml2pdf_graph_circle::sector($XDiag, $YDiag-$delta, $rayon, 
                               $angleDebut - $angle, 360, 
                               $graph->colors[$i-1]);
            }
        }
        //Légendes
        $graph->pdf->SetFont('Courier', '', $graph->fontSize);
        $x1 = $XPage + 2 * $rayon + 4 * $marge;
        $x2 = $x1 + $hLegende + $marge;
        $y1 = $YDiag - $rayon + (2 * $rayon - $graph->nbVal*($hLegende + $marge)) / 2;
        for($i=0; $i<$graph->nbVal; $i++) {
            $tab = Xml2Pdf::ConvertColor($graph->colors[$i]);
            $graph->pdf->SetFillColor($tab['r'],$tab['g'],$tab['b']);
            $graph->pdf->Rect($x1, $y1, $hLegende, $hLegende, 'DF');
            $graph->pdf->SetXY($x2, $y1);
            $graph->pdf->Cell(0,$hLegende,$graph->legends[$i]);
            $y1 += $hLegende + $marge;
        }
    } 
    
    // }}}
    // xml2pdf_graph_circle::sector() {{{

    /**
     * Draw a circle sector.
     *
     * @param integer $xc center abscis
     * @param integer $yc center ordonnate
     * @param integer $a begin angle (degree)
     * @param integer $b end angle (degree)
     * @param string $color fill color
     * @param string $style fill style [D(draw)|F(fill)|FD|DF]
     * @param boolean $cw rotate wise (true: clock wise, false: counter clock wise)
     * @param integer $o origine angle (0 to right, 90 to top, 180 to left, 270 to bottom)
     * @return void 
     */
    public static function sector($xc, $yc, $r, $a, $b, $color='#ffffff',
                           $style='FD', $cw=true, $o=90) {
        $tab = Xml2Pdf::ConvertColor($color);
        $pdf = Pdf::singleton();
        $pdf->SetFillColor($tab['r'],$tab['g'],$tab['b']);
        if($cw) {
            $d = $b;
            $b = $o - $a;
            $a = $o - $d;
        } else {
            $b += $o;
            $a += $o;
        }
        $a = ($a%360)+360;
        $b = ($b%360)+360;
        if ($a > $b) {
            $b +=360;
        }
        $b = $b/360*2*M_PI;
        $a = $a/360*2*M_PI;
        $d = $b-$a;
        if ($d == 0 ) {
            $d =2*M_PI;
        }
        $k = $pdf->k;
        $hp = $pdf->h;
        if($style=='F') {
            $op='f';
        } elseif($style=='FD' or $style=='DF') {
            $op='b';
        } else {
            $op='s';
        }
        if (sin($d/2)) {
            $MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
        }
        //first put the center
        $pdf->_out(sprintf('%.2f %.2f m', ($xc)*$k, ($hp-$yc)*$k));
        //put the first point
        $pdf->_out(sprintf('%.2f %.2f l', ($xc+$r*cos($a))*$k, 
            (($hp-($yc-$r*sin($a)))*$k)));
        //draw the arc
        if ($d < M_PI/2) {
            xml2pdf_graph_circle::arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                $xc+$r*cos($b),
                $yc-$r*sin($b));
        } else {
            $b = $a + $d/4;
            $MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
            xml2pdf_graph_circle::arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                $xc+$r*cos($b),
                $yc-$r*sin($b));
            $a = $b;
            $b = $a + $d/4;
            xml2pdf_graph_circle::arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                $xc+$r*cos($b),
                $yc-$r*sin($b));
            $a = $b;
            $b = $a + $d/4;
            xml2pdf_graph_circle::arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                $xc+$r*cos($b),
                $yc-$r*sin($b));
            $a = $b;
            $b = $a + $d/4;
            xml2pdf_graph_circle::arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                $xc+$r*cos($b),
                $yc-$r*sin($b));
        }
        //terminate drawing
        $pdf->_out($op);
    } 
    
    // }}}
   // xml2pdf_graph_circle::arc() {{{

    /**
     * Draw an arc of circle
     *
     * @param integer $x1 center absciss
     * @param integer $y1 center ordonnate
     * @param integer $x2 start point absciss
     * @param integer $y2 start point ordonnate
     * @param integer $x3 end poinf absciss
     * @param integer $y3 end point ordonnate
     * @return void 
     */
    public static function arc($x1, $y1, $x2, $y2, $x3, $y3 ) {
        $pdf = Pdf::singleton();
        $h = $pdf->h;
        $pdf->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            $x1*$pdf->k,
            ($h-$y1)*$pdf->k,
            $x2*$pdf->k,
            ($h-$y2)*$pdf->k,
            $x3*$pdf->k,
            ($h-$y3)*$pdf->k));
    } 
    
    // }}}   
}
?>
