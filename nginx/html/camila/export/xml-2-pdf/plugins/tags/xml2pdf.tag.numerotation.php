<?php
/**
 * numerotation tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version $Id: xml2pdf.tag.numerotation.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 *
 * @todo manage a format, eg: 'page %d of %t'
 */

// dependances {{{

/**
 * Parent class
 */
require_once('xml2pdf.tag.paragraph.php');

// }}}
// doc {{{

/**
 * <numerotation> tag.
 * 
 * <numerotation> tag is used to add pagination on footer or header.
 *
 * {@example numerotation.xml}
 * 
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.numerotation.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.numerotation.pkg
 */ // }}}
class xml2pdf_tag_numerotation extends xml2pdf_tag_paragraph {
    // xml2pdf_tag_numerotation::close() {{{
    
    /**
     * Add the tag to the footer or header elements.
     *
     * @return void 
     */
    public function close() {
        $this->pdf->AliasNbPages();
        $this->content = camila_get_translation('camila.report.navbox.page').' %s/{nb}';
        $this->parent->elements[] = $this;
    }
    
    // }}}
}
?>
