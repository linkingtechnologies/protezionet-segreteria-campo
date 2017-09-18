<?php
/**
 * filigree tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.filigree.php,v 1.1 2007/01/05 23:07:31 geelweb Exp $
 */

// dependances {{{

/**
 * include parent class
 */
require_once('Xml2PdfTextTag.php');

// }}}
// doc {{{

/**
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.text.pkg
 * @version CVS: $Id: xml2pdf.tag.filigree.php,v 1.1 2007/01/05 23:07:31 geelweb Exp $
 */ // }}}
Class xml2pdf_tag_filigree extends Xml2PdfTextTag {
    // properties {{{

    /**
     * parent tag
     * @var object
     */
    private $_parent = null;

    /**
     * rotation angle (degree)
     * @var int
     */
    public $rotation=0;

    /**
     * left padding
     * @var float
     */
    public $left = 0;

    /**
     * top padding
     * @var float
     */
    public $top = 0;

    /**
     * start page
     * @var int
     */
    protected $start = 1;

    /**
     * end page
     * @var int
     */
    protected $end = 0;

    // }}}
    // xml2pdf_tag_filigree::__construct() {{{

    /**
     *
     */
    public function __construct($tagProperties, $parent) {
        parent::__construct($tagProperties);
        $this->_parent = $parent;

        if(isset($tagProperties['ROTATION'])) {
            $this->rotation = $tagProperties['ROTATION'];
        }
        if(isset($tagProperties['TOP'])) {
            $this->top = $tagProperties['TOP'];
        }
        if(isset($tagProperties['LEFT'])) {
            $this->left = $tagProperties['LEFT'];
        }
        if(isset($tagProperties['START'])) {
            $this->start = $tagProperties['START'];
        }
        if(isset($tagProperties['END'])) {
            $this->end = $tagProperties['END'];
        }
    }

    // }}}
    // xml2pdf_tag_filigree::close() {{{
    
    public function close() {
        if(!is_a($this->_parent, 'xml2pdf_tag_page')) {
            $this->pdf->filigree[] = $this;
        } else {
            $this->render();
        }
    }

    // }}}
    // xml2pdf_tag_filigree::render() {{{

    public function render() {
        if(($this->end && $this->end < $this->pdf->PageNo()) || 
        ($this->start > $this->pdf->PageNo())) {
            return false;
        }
        $this->pdf->SetFont($this->font, $this->fontStyle, $this->fontSize);
        $fontColor = Xml2Pdf::convertColor($this->fontColor);
        $this->pdf->SetTextColor($fontColor['r'], $fontColor['g'], $fontColor['b']);
        if(!$this->left) {
            $this->left = $this->pdf->getX();
        }
        if(!$this->top) {
            $this->top = $this->pdf->getY();
        }
        $initial = $this->pdf->angle;
        $this->pdf->Rotate($this->rotation, $this->left, $this->top);
        $this->pdf->Text($this->left, $this->top, $this->content);
        $this->pdf->Rotate($initial);
    }

    // }}}
}
?>
