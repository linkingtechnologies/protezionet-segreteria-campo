<?php
/**
 * text tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.text.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */

// dependances {{{

/**
 * include parent class
 */
require_once('Xml2PdfTextTag.php');

// }}}
// doc {{{

/**
 * <text> tag.
 *
 * {@example text.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.text.pkg
 * @version CVS: $Id: xml2pdf.tag.text.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */ // }}}
Class xml2pdf_tag_text extends Xml2PdfTextTag {
    // xml2pdf_tag_text::__construct() {{{

    /**
     * Constructor.
     *
     * @param array $tagProperties tag properties
     * @return void
     */
    public function __construct($tagProperties) {
        parent::__construct($tagProperties);
        //$this->useStyle = true;
        if(isset($tagProperties['USESTYLE'])) {
            $this->useStyle = $tagProperties['USESTYLE'];
        }
    } 
    
    // }}}
}
?>
