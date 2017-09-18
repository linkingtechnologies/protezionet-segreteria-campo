<?php
/**
 * code39 extended barcode plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.code39extend.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */

// dependances {{{

/**
 *
 */
require_once(XML2PDF_PLUGINS_BARCODES_PATH . '/xml2pdf.barcode.code39.php');

// }}}
// doc {{{

/**
 * code 39 extended barcode plugin.
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version $Id: xml2pdf.barcode.code39extend.php,v 1.2 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */ // }}}
class xml2pdf_barcode_code39extend extends xml2pdf_barcode_code39 {
    // xml2pdf_barcode_code39extend::__construct() {{{

    public function __construct() {}

    // }}}
    // xml2pdf_barcode_code39extend::render() {{{

    public static function render($barcode) {
        $width = $barcode->width?$barcode->width:0.4;
        $height = $barcode->height?$barcode->height:20;
        xml2pdf_barcode_code39::Code39($barcode->x, $barcode->y, 
            $barcode->barcode, true, false, $width, $height, true, 
            $barcode->pdf, $barcode->fontsize);
    }

    // }}}
}

?>
