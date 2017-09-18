<?php
/**
 * pdf tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.pdf.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */

// doc {{{

/**
 * <pdf> tag.
 *
 * Represent the document. The properties are write in the header if 
 * header tag don't have child tags.
 *
 * {@example pdf.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.pdf.pkg
 * @version CVS: $Id: xml2pdf.tag.pdf.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */ // }}}
Class xml2pdf_tag_pdf {
    // xml2pdf_tag_pdf::__construct() {{{

    /**
     * Constructor.
     *
     * @param $tagProperties tag attributes
     * @return void
     */
    public function __construct($tagProperties) {
        $pdf = Pdf::singleton();
        if(isset($tagProperties['TITLE'])) {
            $pdf->setTitle($tagProperties['TITLE']);
        }
        if(isset($tagProperties['SUBJECT'])) {
            $pdf->setSubject($tagProperties['SUBJECT']);
        }
        if(isset($tagProperties['CREATOR'])) {
            $pdf->setCreator($tagProperties['CREATOR']);
        }
        if(isset($tagProperties['FONT'])) {
            $pdf->pageFont($tagProperties['FONT']);
        }

    } 
    
    // }}}
    // xml2pdf_tag_pdf::addContent() {{{
    
    /**
     * add content.
     *
     * @return void
     */
    public function addContent($content) {
        return;
    }

    // }}}
    // xml2pdf_tag_pdf::close() {{{

    /**
     * Call when tag's closed.
     *
     * @return void
     */
    public function close() {
        return;
    } 
   
    // }}}
}
?>
