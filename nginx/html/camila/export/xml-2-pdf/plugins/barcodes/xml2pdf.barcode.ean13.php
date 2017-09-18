<?php
/**
 * ean13 barcode tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.ean13.php,v 1.3 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */

// dependances {{{

/**
 *
 */
require_once(XML2PDF_PLUGINS_TAGS_PATH . '/xml2pdf.tag.barcode.php');

// }}}
// doc {{{

/**
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.ean13.php,v 1.3 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */ // }}}
class xml2pdf_barcode_ean13 {
    // xml2pdf_barcode_ean13::__construct() {{{
    
    /**
     * Constructor
     */
    public function __construct() {}

    // }}}
    // xml2pdf_barcode_ean13::render() {{{

    /**
     * Render an EAN13 barcode
     *
     * @param object $barcode an xml2pdf_tag_barcode object
     * @return void
     */
    public static function render($barcode) {
        $width = $barcode->width?$barcode->width:0.35;
        $height = $barcode->height?$barcode->height:16;
        //Ajoute des 0 si nécessaire
        $barcode->pdf->SetFillColor(0,0,0);
        $txt = str_pad($barcode->barcode, $barcode->len-1, '0' , STR_PAD_LEFT);
        if($barcode->len==12)
            $txt='0'.$txt;
        //Ajoute ou teste le chiffre de contrôle
        if(strlen($txt)==12) {
            $txt .= xml2pdf_tag_barcode::getCheckDigit($txt);
        } elseif(!xml2pdf_tag_barcode::testCheckDigit($txt)) {
            $barcode->pdf->Error('Incorrect check digit');
        }
        //Convertit les chiffres en barres
        $codes = array(
            'A'=>array(
                '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
                '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
            'B'=>array(
                '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
                '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
            'C'=>array(
                '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
                '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
            );
        $parities = array(
            '0'=>array('A','A','A','A','A','A'),
            '1'=>array('A','A','B','A','B','B'),
            '2'=>array('A','A','B','B','A','B'),
            '3'=>array('A','A','B','B','B','A'),
            '4'=>array('A','B','A','A','B','B'),
            '5'=>array('A','B','B','A','A','B'),
            '6'=>array('A','B','B','B','A','A'),
            '7'=>array('A','B','A','B','A','B'),
            '8'=>array('A','B','A','B','B','A'),
            '9'=>array('A','B','B','A','B','A')
            );
        $code = '101';
        $p = $parities[$txt{0}];
        for($i=1 ; $i<=6 ; $i++)
            $code .= $codes[$p[$i-1]][$txt{$i}];
        $code .= '01010';
        for($i=7 ; $i<=12 ; $i++)
            $code .= $codes['C'][$txt{$i}];
        $code .= '101';
        //Dessine les barres
        for($i=0 ; $i<strlen($code) ; $i++) {
            if($code{$i} == '1')
                $barcode->pdf->Rect($barcode->x+$i*$width, $barcode->y,
                    $width, $height, 'F');
        }
        //Imprime le texte sous le code-barres
        $barcode->pdf->SetFont('Arial', '', 12);
        $barcode->pdf->Text($barcode->x, 
            $barcode->y + $height + 11 / $barcode->pdf->k,
            substr($txt, -$barcode->len));
    }

    // }}}
} 
?>
