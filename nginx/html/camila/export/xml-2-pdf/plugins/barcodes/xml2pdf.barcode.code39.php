<?php
/**
 * code39 barcode tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.code39.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
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
 * code39 barcode type plugin.
 *
 * Thanks to Emmanuel Havet for the script found on 
 * {@link http://www.fpdf.org fpdf.org}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.code39.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */ // }}}
class xml2pdf_barcode_code39 {
    // xml2pdf_barcode_code39::__construct() {{{

    /**
     * Constructor
     */
    public function __construct() {}

    // }}}
    // xml2pdf_barcode_code39::render() {{{

    /**
     * Render an EAN13 barcode
     *
     * @param object $barcode an xml2pdf_tag_barcode object
     * @return void
     */
    public static function render($barcode) {
        $width = $barcode->width?$barcode->width:0.4;
        $height = $barcode->height?$barcode->height:20;
        xml2pdf_barcode_code39::Code39($barcode->x, $barcode->y, 
            $barcode->barcode, false, false, $width, $height, true, 
            $barcode->pdf);
    }

    // }}}
    // xml2pdf_barcode_code39::Code39() {{{

    /**
     *
     * @param float $x
     * @param float $y
     * @param string $code
     * @param boolean $ext
     * @param boolean $cks
     * @param float $w
     * @param float $h
     * @param boolean $wide
     * @param object $pdf
     * @return void
     */
    public static function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.4, $h = 20, $wide = true, $pdf, $fontSize = 10) {

        if ($code == '')
            return;

        //suppression des accents
        $code = strtr($code, 'àâäéèêëìîïòôöùûü', 'aaaeeeeiiiooouuu');

        //affichage du code
		if ($fontSize>0)
		{
            $pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial', '', $fontSize);
            $pdf->Text($x, $y+$h+4, $code);
		}

        if($ext) {
            //encodage étendu
            $code = xml2pdf_barcode_code39::encode_code39_ext($code);
        } else {
            //passage en majuscules
            $code = strtoupper($code);
            //contrôle validité
            if(!preg_match('|^[0-9A-Z. $/+%-]*$|', $code)) {
                $pdf->Error('Invalid barcode value: '.$code);
            }
        }

        //calcul du checksum
        if ($cks) {
            $code .= xml2pdf_barcode_code39::checksum_code39($code);
        }

        //ajout des caractères début / fin
        $code = '*'.$code.'*';

        //tableaux de correspondance caractères / barres
        $narrow_encoding = array (
            '0' => '101001101101', 
            '1' => '110100101011', 
            '2' => '101100101011',
            '3' => '110110010101', 
            '4' => '101001101011', 
            '5' => '110100110101',
            '6' => '101100110101', 
            '7' => '101001011011', 
            '8' => '110100101101',
            '9' => '101100101101', 
            'A' => '110101001011', 
            'B' => '101101001011',
            'C' => '110110100101', 
            'D' => '101011001011', 
            'E' => '110101100101',
            'F' => '101101100101', 
            'G' => '101010011011', 
            'H' => '110101001101',
            'I' => '101101001101', 
            'J' => '101011001101', 
            'K' => '110101010011',
            'L' => '101101010011', 
            'M' => '110110101001', 
            'N' => '101011010011',
            'O' => '110101101001', 
            'P' => '101101101001', 
            'Q' => '101010110011',
            'R' => '110101011001', 
            'S' => '101101011001', 
            'T' => '101011011001',
            'U' => '110010101011', 
            'V' => '100110101011', 
            'W' => '110011010101',
            'X' => '100101101011', 
            'Y' => '110010110101', 
            'Z' => '100110110101',
            '-' => '100101011011', 
            '.' => '110010101101', 
            ' ' => '100110101101',
            '*' => '100101101101', 
            '$' => '100100100101', 
            '/' => '100100101001',
            '+' => '100101001001', 
            '%' => '101001001001' );

        $wide_encoding = array (
            '0' => '101000111011101', '1' => '111010001010111', 
            '2' => '101110001010111', '3' => '111011100010101', 
            '4' => '101000111010111', '5' => '111010001110101',
            '6' => '101110001110101', '7' => '101000101110111', 
            '8' => '111010001011101', '9' => '101110001011101', 
            'A' => '111010100010111', 'B' => '101110100010111',
            'C' => '111011101000101', 'D' => '101011100010111', 
            'E' => '111010111000101', 'F' => '101110111000101', 
            'G' => '101010001110111', 'H' => '111010100011101',
            'I' => '101110100011101', 'J' => '101011100011101', 
            'K' => '111010101000111', 'L' => '101110101000111', 
            'M' => '111011101010001', 'N' => '101011101000111',
            'O' => '111010111010001', 'P' => '101110111010001', 
            'Q' => '101010111000111', 'R' => '111010101110001', 
            'S' => '101110101110001', 'T' => '101011101110001',
            'U' => '111000101010111', 'V' => '100011101010111', 
            'W' => '111000111010101', 'X' => '100010111010111', 
            'Y' => '111000101110101', 'Z' => '100011101110101',
            '-' => '100010101110111', '.' => '111000101011101', 
            ' ' => '100011101011101', '*' => '100010111011101', 
            '$' => '100010001000101', '/' => '100010001010001',
            '+' => '100010100010001', '%' => '101000100010001');

        //le code barre est déterminé en version large ou étroite (meilleure lisibilité)
        //large observe un rapport 3:1 pour le rapport barre large / barre etroite
        //etroit                   2:1
        $encoding = $wide ? $wide_encoding : $narrow_encoding;

        //espace inter-caractère
        $gap = ($w > 0.29) ? '00' : '0';

        //encodage
        $encode = '';
        for ($i = 0; $i< strlen($code); $i++) {
            $encode .= $encoding[$code{$i}].$gap;
        }

        //dessin
        xml2pdf_barcode_code39::draw_code39($encode, $x, $y, $w, $h, $pdf);
    }

    // }}}
    // xml2pdf_barcode_code39::checksum_code39() {{{

    /**
     *
     * @param string $code
     * @return char
     */
    public static function checksum_code39($code) {

        //somme des positions des caractères en démarrant de zéro
        //somme modulo 43
        //le caractère de contrôle est celui à la position du modulo
        //exemple : 115 % 43 = 29 -> 'T' est à la place 29 dans le tableau

        $chars = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
            'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
        $sum = 0;
        for ($i=0 ; $i<strlen($code); $i++) {
            $a = array_keys($chars, $code{$i});
            $sum += $a[0];
        }
        $r = $sum % 43;
        return $chars[$r];
    }

    // }}}
    // xml2pdf_barcode_code39::encode_code39_ext() {{{

    /**
     *
     * @param string $code
     * @return string
     */
    public static function encode_code39_ext($code) {
        //encodage en code 39 étendu

        $encode = array(
            chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
            chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
            chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '£K',
            chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
            chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
            chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
            chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
            chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
            chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
            chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
            chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
            chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
            chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
            chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
            chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
            chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
            chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
            chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
            chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
            chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
            chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
            chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
            chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
            chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
            chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
            chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
            chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
            chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
            chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
            chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
            chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
            chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');

        $code_ext = '';
        for ($i = 0 ; $i<strlen($code); $i++) {
            if (ord($code{$i}) > 127) {
                $pdf->Error('Invalid character: '.$code{$i});
            }
            $code_ext .= $encode[$code{$i}];
        }
        return $code_ext;
    }

    // }}}
    // xml2pdf_barcode_code39::draw_code39() {{{

    /**
     *
     * @param string $code
     * @param float $x
     * @param float $y
     * @param float $w
     * @param float $h
     * @param object $pdf
     * @return void
     */
    public static function draw_code39($code, $x, $y, $w, $h, $pdf) {
        //Dessine les barres
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(0,0,0);
        for($i=0; $i<strlen($code); $i++) {
            if($code{$i} == '1') {
                $pdf->Rect($x+$i*$w, $y, $w, $h, 'F');
            }
        }
    }

    // }}}

    function mathEval($equation) { 
        $equation = preg_replace("/[^0-9+\-.*\/()%]/","",$equation); 
        // fix percentage calcul when percentage value < 10 
        $equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation); 
        // calc percentage 
        $equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation); 
        // you could use str_replace on this next line 
        // if you really, really want to fine-tune this equation 
        $equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation); 
        if ( $equation == "" ) { 
            $return = 0; 
        } else { 
            eval("\$return=" . $equation . ";" ); 
        }
        return $return; 
    }

} 
?>
