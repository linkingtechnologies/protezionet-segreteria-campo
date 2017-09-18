<?php
/**
 * page tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.page.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */

// dependances {{{

/**
 * include parent class
 */
require_once('Xml2PdfTag.php');

// }}}
// doc {{{

/**
 * <page> tag.
 *
 * This tag is used to define the font page properties.
 *
 * {@example page.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.page.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.page.pkg
 */ // }}}
Class xml2pdf_tag_page extends Xml2PdfTag {
    // xml2pdf_tag_page::__construct() {{{

    /**
     * Constructor.
     *
     * Parse the tag attributes
     *
     * @param array $tagProperties tag attributes
     * @return void
     */
    public function __construct($tagProperties) {
        parent::__construct($tagProperties);
        if(isset($tagProperties['FONT'])){
            $this->pdf->pageFont = $tagProperties['FONT'];
        }
        if(isset($tagProperties['FONTSIZE'])){
            $this->pdf->pageFontSize = $tagProperties['FONTSIZE'];
        }
        if(isset($tagProperties['FONTCOLOR'])){
            $this->pdf->pageFontColor = $tagProperties['FONTCOLOR'];
        }
        if(isset($tagProperties['FONTSTYLE'])){
            $this->pdf->pageFontStyle = $tagProperties['FONTSTYLE'];
        }
        $this->pdf->AddPage($tagProperties['ORIENTATION']);
        $this->pdf->resetFont();
    } 
    
    // }}}
}
?>
