<?php
/**
 * ln tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.ln.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// doc {{{

/**
 * <ln> tag.
 *
 * {@example ln.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.ln.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.ln.pkg
 */ // }}}
Class xml2pdf_tag_ln {
    // xml2pdf_tag_ln::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag attributes and add the new line to the document.
     *
     * @param array $tagProperties tag properties
     * @return void
     */
    public function __construct($tagProperties) {
        $pdf = Pdf::singleton();
        $lineHeight = isset($tagProperties['LINEHEIGHT'])?
            $tagProperties['LINEHEIGHT']:PDF_DEFAULT_LINEHEIGHT;
        $pdf->Ln((float)$lineHeight);
    }

    // }}}
    // xml2pdf_tag_ln::close() {{{
    
    /**
     *
     * @return void
     */
    public function close() {
    }

    // }}}
}
?>
