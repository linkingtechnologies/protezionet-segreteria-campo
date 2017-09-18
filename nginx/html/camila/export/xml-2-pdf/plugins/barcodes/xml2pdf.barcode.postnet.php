<?php
/**
 * POSTNET barcode tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.postnet.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
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
 * POSTNET barcode type plugin.
 *
 * Thanks to Shailesh Humbad for the original script found on 
 * {@link http://www.fpdf.org fpdf.org}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.postnet.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */ // }}}
class xml2pdf_barcode_postnet {
    // xml2pdf_barcode_postnet::__construct() {{{

    /**
     * Constructor
     */
    public function __construct() {}

    // }}}
    // xml2pdf_barcode_postnet::render() {{{

    /**
     * Render an EAN13 barcode
     *
     * @param object $barcode an xml2pdf_tag_barcode object
     * @return void
     */
    public static function render($barcode) {
        $zipcode = xml2pdf_barcode_postnet::ParseZipCode($barcode->barcode);
        xml2pdf_barcode_postnet::POSTNETBarCode($barcode->x,$barcode->y,
            $zipcode, $barcode->pdf);
        $barcode->pdf->Text($barcode->x,$barcode->y+10,$zipcode);
    }

    // }}}
    // xml2pdf_barcode_postnet::POSTNETBarCode() {{{
 
    /**
     * draws a bar code for the given zip code using pdf lines
     * triggers error if zip code is invalid
     * x,y specifies the lower left corner of the bar code
     *
     * @param float $x
     * @param float $y
     * @param string $zicode
     * @param $pdf
     * @return void
     */
    public static function POSTNETBarCode($x, $y, $zipcode, $pdf) {
        // Save nominal bar dimensions in user units
        // Full Bar Nominal Height = 0.125"
        $FullBarHeight = 9 / $pdf->k;
        // Half Bar Nominal Height = 0.050"
        $HalfBarHeight = 3.6 / $pdf->k;
        // Full and Half Bar Nominal Width = 0.020"
        $BarWidth = 1.44 / $pdf->k;
        // Bar Spacing = 0.050"
        $BarSpacing = 3.6 / $pdf->k;

        $FiveBarSpacing = $BarSpacing * 5;

        // 1 represents full-height bars and 0 represents half-height bars
        $BarDefinitionsArray = Array(
            1 => Array(0,0,0,1,1),
            2 => Array(0,0,1,0,1),
            3 => Array(0,0,1,1,0),
            4 => Array(0,1,0,0,1),
            5 => Array(0,1,0,1,0),
            6 => Array(0,1,1,0,0),
            7 => Array(1,0,0,0,1),
            8 => Array(1,0,0,1,0),
            9 => Array(1,0,1,0,0),
            0 => Array(1,1,0,0,0));
            
        // validate the zip code
        xml2pdf_barcode_postnet::ValidateZipCode($zipcode);

        // set the line width
        $pdf->SetLineWidth($BarWidth);

        // draw start frame bar
        $pdf->Line($x, $y, $x, $y - $FullBarHeight);
        $x += $BarSpacing;

        // draw digit bars
        for($i = 0; $i < 5; $i++)
        {
            xml2pdf_barcode_postnet::DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight,
                $FullBarHeight, $BarDefinitionsArray, $zipcode{$i}, $pdf);
            $x += $FiveBarSpacing;
        }
        // draw more digit bars if 10 digit zip code
        if(strlen($zipcode) == 10)
        {
            for($i = 6; $i < 10; $i++)
            {
                xml2pdf_barcode_postnet::DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight,
                    $FullBarHeight, $BarDefinitionsArray, $zipcode{$i}, $pdf);
                $x += $FiveBarSpacing;
            }
        }
        
        // draw check sum digit
        xml2pdf_barcode_postnet::DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight,
            $FullBarHeight, $BarDefinitionsArray,
            xml2pdf_barcode_postnet::CalculateCheckSumDigit($zipcode), $pdf);
        $x += $FiveBarSpacing;

        // draw end frame bar
        $pdf->Line($x, $y, $x, $y - $FullBarHeight);

    }

    // }}}
    // xml2pdf_barcode_postnet::ParseZipCode() {{{

    /**
     * Reads from end of string and returns first matching valid
     * zip code of form DDDDD or DDDDD-DDDD, in that order.
     * Returns empty string if no zip code found.
     *
     * @param string $stringToParse
     * @return string
     */
    public static function ParseZipCode($stringToParse) {
        // check if string is an array or object
        if(is_array($stringToParse) || is_object($stringToParse)) {
            return "";
        }

        // convert parameter to a string
        $stringToParse = strval($stringToParse);

        $lengthOfString = strlen($stringToParse);
        if ( $lengthOfString < 5 ) {
            return "";
        }
        
        // parse the zip code backward
        $zipcodeLength = 0;
        $zipcode = "";
        for ($i = $lengthOfString-1; $i >= 0; $i--) {
            // conditions to continue the zip code
            switch($zipcodeLength) {
                case 0:
                case 1:
                case 2:
                case 3:
                    if ( is_numeric($stringToParse{$i}) ) {
                        $zipcodeLength += 1;
                        $zipcode .= $stringToParse{$i};
                    } else {
                        $zipcodeLength = 0;
                        $zipcode = "";
                    }
                    break;
                case 4:
                    if ( $stringToParse{$i} == "-" ) {
                        $zipcodeLength += 1;
                        $zipcode .= $stringToParse{$i};
                    } elseif ( is_numeric($stringToParse{$i}) ) {
                        $zipcodeLength += 1;
                        $zipcode .= $stringToParse{$i};
                        break 2;
                    } else {
                        $zipcodeLength = 0;
                        $zipcode = "";
                    }
                    break;
                case 5:
                case 6:
                case 7:
                case 8:
                    if ( is_numeric($stringToParse{$i}) ) {
                        $zipcodeLength = $zipcodeLength + 1;
                        $zipcode = $zipcode . $stringToParse{$i};
                    } else {
                        $zipcodeLength = 0;
                        $zipcode = "";
                    }
                    break;
                case 9:
                    if ( is_numeric($stringToParse{$i}) ) {
                        $zipcodeLength = $zipcodeLength + 1;
                        $zipcode = $zipcode . $stringToParse{$i};
                        break;
                    } else {
                        $zipcodeLength = 0;
                        $zipcode = "";
                    }
                    break;
            }
        }

        // return the parsed zip code if found
        if ( $zipcodeLength == 5 || $zipcodeLength == 10 ) {
            // reverse the zip code
            return strrev($zipcode);
        } else {
            return "";
        }
    }

    // }}}
    // xml2pdf_barcode_postnet::ValidateZipCode() {{{

    /**
     * triggers user error if the zip code is invalid
     * valid zip codes are of the form DDDDD or DDDDD-DDDD
     * where D is a digit from 0 to 9, returns the validated zip code
     *
     * @param string $zipcode
     * @return string
     */
    public static function ValidateZipCode($zipcode) {
        $functionname = "ValidateZipCode Error: ";

        // check if zipcode is an array or object
        if(is_array($zipcode) || is_object($zipcode)) {
            trigger_error($functionname.
                "Zip code may not be an array or object.", E_USER_ERROR);
        }

        // convert zip code to a string
        $zipcode = strval($zipcode);

        // check if length is 5
        if ( strlen($zipcode) != 5 && strlen($zipcode) != 10 ) {
            trigger_error($functionname.
                "Zip code must be 5 digits or 10 digits including hyphen. len:".
                strlen($zipcode)." zipcode: ".$zipcode, E_USER_ERROR);
        }

        if ( strlen($zipcode) == 5 ) {
            // check that all characters are numeric
            for ( $i = 0; $i < 5; $i++ ) {
                if ( is_numeric( $zipcode{$i} ) == false ) {
                    trigger_error($functionname.
                        "5 digit zip code contains non-numeric character.",
                        E_USER_ERROR);
                }
            }
        } else {
            // check for hyphen
            if ( $zipcode{5} != "-" ) {
                trigger_error($functionname.
                    "10 digit zip code does not contain hyphen in right place.",
                    E_USER_ERROR);
            }
            // check that all characters are numeric
            for ( $i = 0; $i < 10; $i++ ) {
                if ( is_numeric($zipcode{$i}) == false && $i != 5 ) {
                    trigger_error($functionname.
                        "10 digit zip code contains non-numeric character.",
                        E_USER_ERROR);
                }
            }
        }

        // return the string
        return $zipcode;
    }

    // }}}
    // xml2pdf_barcode_postnet::CalculateCheckSumDigit() {{{

    /**
     * takes a validated zip code and
     * calculates the checksum for POSTNET
     *
     * @param string $zipcode
     * @return int
     */
    public static function CalculateCheckSumDigit($zipcode) {
        // calculate sum of digits
        if( strlen($zipcode) == 10 ) {
            $sumOfDigits = $zipcode{0} + $zipcode{1} +
                $zipcode{2} + $zipcode{3} + $zipcode{4} +
                $zipcode{6} + $zipcode{7} + $zipcode{8} +
                $zipcode{9};
        } else {
            $sumOfDigits = $zipcode{0} + $zipcode{1} +
                $zipcode{2} + $zipcode{3} + $zipcode{4};
        }

        // return checksum digit
        if( ($sumOfDigits % 10) == 0 )
            return 0;
        else
            return 10 - ($sumOfDigits % 10);
    }

    // }}}
    // xml2pdf_barcode_postnet::DrawDigitBars() {{{

    /**
     * Takes a digit and draws the corresponding POSTNET bars.
     *
     * @param float $x
     * @param float $y
     * @param int $BarSpacing
     * @param int $HalfBarHeight
     * @param int $FullBarHeight
     * @param array $BarDefinitionsArray
     * @param int $digit
     * @param object $pdf
     * @return void
     */
    public static function DrawDigitBars($x, $y, $BarSpacing, $HalfBarHeight, 
    $FullBarHeight, $BarDefinitionsArray, $digit, $pdf) {
        // check for invalid digit
        if($digit < 0 && $digit > 9)
            trigger_error("DrawDigitBars: invalid digit.", E_USER_ERROR);
        
        // draw the five bars representing a digit
        for($i = 0; $i < 5; $i++) {
            if($BarDefinitionsArray[$digit][$i] == 1) {
                $pdf->Line($x, $y, $x, $y - $FullBarHeight);
            } else {
                $pdf->Line($x, $y, $x, $y - $HalfBarHeight);
            }
            $x += $BarSpacing;
        }
    } 

    // }}}
} 
?>
