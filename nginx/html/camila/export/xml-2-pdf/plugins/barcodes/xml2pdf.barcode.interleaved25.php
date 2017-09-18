<?php
/**
 * interleaved 2 of 5 barcode tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.interleaved25.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
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
 * Interleaved 2 of 5 barcode type plugin.
 * Thanks to Matthias Lau for the original script found on 
 * {@link http://www.fpdf.org fpdf.org}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.interleaved25.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */ // }}}
class xml2pdf_barcode_interleaved25 {
    // xml2pdf_barcode_interleaved25::__construct() {{{

    /**
     * Constructor
     */
    public function __construct() {}

    // }}}
    // xml2pdf_barcode_interleaved25::render() {{{

    /**
     * Render the barcode
     *
     * @param object $barcode an xml2pdf_tag_barcode object
     * @return void
     */
    public static function render($barcode) {
        $wide = $barcode->width?$barcode->width:1;
        $narrow = $wide / 3;
        $code = $barcode->barcode;
        $height = $barcode->height?$barcode->height:10;
        $xpos = $barcode->x;
        $ypos = $barcode->y;
        $pdf = $barcode->pdf;

        // wide/narrow codes for the digits
        $barChar['0'] = 'nnwwn';
        $barChar['1'] = 'wnnnw';
        $barChar['2'] = 'nwnnw';
        $barChar['3'] = 'wwnnn';
        $barChar['4'] = 'nnwnw';
        $barChar['5'] = 'wnwnn';
        $barChar['6'] = 'nwwnn';
        $barChar['7'] = 'nnnww';
        $barChar['8'] = 'wnnwn';
        $barChar['9'] = 'nwnwn';
        $barChar['A'] = 'nn';
        $barChar['Z'] = 'wn';

        // add leading zero if code-length is odd
        if(strlen($code) % 2 != 0){
            $code = '0' . $code;
        }

        $pdf->SetFont('Arial','',10);
        $pdf->Text($xpos, $ypos + $height + 4, $code);
        $pdf->SetFillColor(0);

        // add start and stop codes
        $code = 'AA'.strtolower($code).'ZA';

        for($i=0; $i<strlen($code); $i=$i+2){
            // choose next pair of digits
            $charBar = $code{$i};
            $charSpace = $code{$i+1};
            // check whether it is a valid digit
            if(!isset($barChar[$charBar])){
                $pdf->Error('Invalid character in barcode: '.$charBar);
            }
            if(!isset($barChar[$charSpace])){
                $pdf->Error('Invalid character in barcode: '.$charSpace);
            }
            // create a wide/narrow-sequence (first digit=bars, second digit=spaces)
            $seq = '';
            for($s=0; $s<strlen($barChar[$charBar]); $s++){
                $seq .= $barChar[$charBar]{$s} . $barChar[$charSpace]{$s};
            }
            for($bar=0; $bar<strlen($seq); $bar++){
                // set lineWidth depending on value
                if($seq{$bar} == 'n'){
                    $lineWidth = $narrow;
                }else{
                    $lineWidth = $wide;
                }
                // draw every second value, because the second digit of the pair is represented by the spaces
                if($bar % 2 == 0){
                    $pdf->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
        }
    }

    // }}}
} 
?>
