<?php
/**
 * UPC_A barcode plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.upc_a.php,v 1.3 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */

// dependances {{{

/**
 *
 */
require_once(XML2PDF_PLUGINS_BARCODES_PATH . '/xml2pdf.barcode.ean13.php');

// }}}
// doc {{{
/**
 * UPC_A barcode plugin.
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.barcode.upc_a.php,v 1.3 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Barcode
 */ // }}}
class xml2pdf_barcode_upc_a extends xml2pdf_barcode_ean13 {
    // xml2pdf_barcode_upc_a::__construct() {{{
    
    /**
     *
     */
    public function __construct() {}

    // }}}
    // xml2pdf_barcode_upc_a::render() {{{

    /**
     *
     */
    public static function render($barcode) {
        $barcode->len=12;
        parent::render($barcode);
    }

    // }}}
}

?>
