<?php
/**
 * literal tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.literal.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */

// dependances {{{

/**
 * include parent class
 */
require_once('Xml2PdfTextTag.php');

// }}}
// doc {{{

/**
 * <literal> tag.
 *
 * {@example literal.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.literal.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.literal.pkg
 */ // }}}
Class xml2pdf_tag_literal extends Xml2PdfTextTag {
    // xml2pdf_tag_literal::__construct() {{{

    /**
     * Constructor.
     *
     * Parse the tag properties.
     *
     * @param array $tagProperties tag attributes
     * @return void
     */
    public function __construct($tagProperties) {
        parent::__construct($tagProperties);
        $this->useStyle = false;
    } 
    
    // }}}
    // xml2pdf_tag_literal::close() {{{
    
    /**
     * Render the tag content.
     *
     * @return void
     */
    public function close() {
        $this->pdf->setFont($this->font, $this->fontStyle, $this->fontSize);
        $fontColor = Xml2Pdf::convertColor($this->fontColor);
        $this->pdf->setTextColor($fontColor['r'], $fontColor['g'], $fontColor['b']);

        $this->pdf->MultiCell(190, $this->lineHeight, $this->content, false, $this->textAlign, false);
    }
    
    // }}}
}
?>
