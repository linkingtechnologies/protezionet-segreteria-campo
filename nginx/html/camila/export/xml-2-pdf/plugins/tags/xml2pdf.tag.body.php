<?php
/**
 * body tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.body.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */

// doc {{{

/**
 * <body> tag.
 *
 * The body tag is used to define the main pdf properties.
 *
 * {@example body.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.body.pkg
 * @version CVS: $Id: xml2pdf.tag.body.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */ // }}}
Class xml2pdf_tag_body {   
    // xml2pdf_tag_body::__construct() {{{

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct($tagProperties) {
        $pdf = Pdf::singleton();
        $formatArray = array('A3' => array(841.89, 1190.55),
                             'A4' => array(595.28, 841.89),
                             'A5' => array(420.94, 595.28),
                             'LETTER' => array(612, 791),
                             'LEGAL' => array(612, 1008));
        $unitArray = array('PT'=>1, 'MM'=>72/25.4, 'CM'=>72/2.54, 'IN'=>72);
        
        if(isset($tagProperties['FORMAT'])) {
            $format = strtoupper($tagProperties['FORMAT']);
            if(!isset($formatArray[$format])) {
                throw new Exception('unknow format page in tag body');
                die();
            }
            list($pdf->fwPt, $pdf->fhPt) = $formatArray[$format];
        }
        if(isset($tagProperties['ORIENTATION'])) {
            $orientation = strtoupper($tagProperties['ORIENTATION']);
            if($orientation == 'P' || $orientation == 'PORTRAIT') {
                $pdf->defOrientation = 'p';
                $pdf->wPt = $pdf->fwPt;
                $pdf->hPt = $pdf->fhPt;
            } elseif($orientation == 'L' || $orientation == 'LANDSCAPE') {
                $pdf->defOrientation = 'l';
                $pdf->wPt = $pdf->fhPt;
                $pdf->hPt = $pdf->fwPt;
            } else {
                throw new Exception('unknow orientation page in tag body');
                die();
            }
        }
        if(isset($tagProperties['UNIT'])) {
            $unit = strtoupper($tagProperties['UNIT']);
            if(!isset($unitArray[$unit])) {
                throw new Exception('unknow unit scale in tag body');
                die();
            }
            $pdf->k = $unitArray[$unit];
        }
        if(isset($tagProperties['MARGINLEFT'])) {
            if(!is_float((float)$tagProperties['MARGINLEFT'])) {
                throw new Exception('margins needs a float type in tag body');
            }
            $pdf->lMargin = $tagProperties['MARGINLEFT'];
        }
        if(isset($tagProperties['MARGINRIGHT'])) {
            if(!is_float((float)$tagProperties['MARGINRIGHT'])) {
                throw new Exception('margins needs a float type in tag body');
            }
            $pdf->rMargin = $tagProperties['MARGINRIGHT'];
        }
        if(isset($tagProperties['MARGINTOP'])) {
            if(!is_float((float)$tagProperties['MARGINTOP'])) {
                throw new Exception('margins needs a float type in tag body');
            }
            $pdf->tMargin = $tagProperties['MARGINTOP'];
        }
        if(isset($tagProperties['MARGINBOTTOM'])) {
            if(!is_float((float)$tagProperties['MARGINBOTTOM'])) {
                throw new Exception('margins needs a float type in tag body');
            }
            $pdf->SetAutoPageBreak(true, $tagProperties['MARGINBOTTOM']);
        }
        if(isset($tagProperties['MARGINS'])) {
            if(!is_float((float)$tagProperties['MARGINS'])) {
                throw new Exception('margins needs a float type in tag body');
            } 
            $pdf->lMargin = $tagProperties['MARGINS'];
            $pdf->rMargin = $tagProperties['MARGINS'];
            $pdf->tMargin = $tagProperties['MARGINS'];
            $pdf->SetAutoPageBreak(true, $tagProperties['MARGINS']);
        }
    } 
    
    // }}}
    // xml2pdf_tag_body::addContent(string) {{{
    
    /**
     * Add content.
     *
     * @return void
     */
    public function addContent($content) {
        // the body tag have not content just childs tags, but if the xml contain
        // break line or space the method addContent will be call.
    } 
    
    // }}}
    // xml2pdf_tag_body::close() {{{

    /**
     * close the tag.
     *
     * @return void
     */
    public function close() {
    } 
    
    // }}}
}
?>
