<?php
/**
 * tag footer plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.footer.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{
/**
 * parent class.
 */
require_once('Xml2PdfTextTag.php');

// }}}
// doc {{{

/**
 * <footer> tag.
 *
 * The tag footer is used to add page footer to the document.
 *
 * {@example footer.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.footer.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.footer.pkg
 */ // }}}
class xml2pdf_tag_footer extends Xml2PdfTextTag {
    // class properties {{{
        
    /**
     * footer elements.
     * @var object Xml2Pdf_tag_paragraph 
     */
    public $elements = array();

    /**
     * first page with footer.
     * @var integer 
     */
    public $startPage = 1;
    
    /**
     * last page with footer.
     * @var integer 
     */
    public $endPage = 0;
    
    /**
     * lines color.
     * @var string 
     */
    public $drawColor = '#000000';
    
    /**
     * borders color.
     * @var string 
     */
    public $borderColor = '#000000';
    
    /**
     * fill color.
     * @var string 
     */
    public $fillColor = '#ffffff';
    
    /**
     * alignment type.
     * @var string 
     */
    public $align = 'L';
    
    /**
     * show borders.
     * @var boolean 
     */
    public $border = false;
    
    /**
     * fill elements background.
     * @var boolean
     */
    public $fill = false;
    
    /**
     * top margin.
     * @var float 
     */
    public $top = 0;
    
    /**
     * left margin.
     * @var float 
     */
    public $left = 0;
    
    /**
     * width.
     * @var float 
     */
    public $width = 0;
    
    /**
     * footer height.
     * @var float 
     */
    private $_height = 0;
    
    /// }}}
    // xml2pdf_tag_footer::__construct() {{{
    
    /**
     * Constructor.
     * 
     * @param array $attributes tag properties.
     * @return void 
     */
    public function __construct($attributes) {
        parent::__construct($attributes);
        if(isset($attributes['START'])) {
            $this->startPage = $attributes['START'];
        }
        if(isset($attributes['END'])) {
            $this->endPage = $attributes['END'];
        }
        $this->lineHeight = $this->fontSize / 2;
    }
    
    // }}} 
    // xml2pdf_tag_footer::close() {{{
    
    /**
     * Add the footer to the Pdf.
     * 
     * @return void 
     */
    public function close() {
        $this->_calcFooterHeight();
        $this->pdf->footer[] = $this;
    }
    
    // }}} 
    // xml2Pdf_tag_footer::_resetFontValues() {{{
    
    /**
     * Initialize font properties.
     *
     * @return void 
     */
    private function _resetFontValues() {
        $dc = array();
        $fc = array();
        $tc = array();
        $dc = Xml2Pdf::convertColor($this->drawColor);
        $fc = Xml2Pdf::convertColor($this->fillColor);
        $tc = Xml2Pdf::convertColor($this->fontColor);
 
        $this->pdf->SetFont($this->font);
        $this->pdf->SetFontSize($this->fontSize);
        $this->pdf->SetTextColor($tc["r"],$tc["g"],$tc["b"]);
        $this->pdf->SetDrawColor($dc["r"],$dc["g"],$dc["b"]);
        $this->pdf->SetFillColor($fc["r"],$fc["g"],$fc["b"]);
    }
    
    // }}} 
    // xml2pdf_tag_footer::render() {{{
    
    /**
     * Render the footer.
     *
     * @return boolean 
     */
    public function render() {
        if(($this->endPage && $this->endPage < $this->pdf->PageNo()) || 
        ($this->startPage > $this->pdf->PageNo())) {
            return false;
        }
        
        foreach ($this->elements as $paragraph) {
            $content = null;
            $this->_resetFontValues();
            if(is_a($paragraph, 'xml2pdf_tag_numerotation')) {
                $content = sprintf($paragraph->content, 
                    $this->pdf->PageNo());
            }
            // initialize parametters
            $contentLength = $this->pdf->GetStringWidth(
                $paragraph->content);
            $width = isset($paragraph->width)?$paragraph->width:
                $contentLength+20;
            $textalign = isset($paragraph->textAlign)?
                $paragraph->textAlign:$this->textAlign;
            $align = isset($paragraph->align)?
                $paragraph->align:$this->align;
            $lineHeight = isset($paragraph->lineHeight)?
                $paragraph->lineHeight:$this->lineHeight;
            $border = isset($paragraph->border)?$paragraph->border:
                $this->border;
            $borderColor = Xml2Pdf::convertColor(
                isset($paragraph->borderColor)?$paragraph->borderColor:
                $this->borderColor);
            $fill = isset($paragraph->fill)?$paragraph->fill:
                $this->fill;
            $fillColor = Xml2Pdf::convertColor(isset($paragraph->fillColor)?
                $paragraph->fillColor:$this->fillColor);
            $font = isset($paragraph->font)?$paragraph->font:$this->font;
                $fontSize = isset($paragraph->fontSize)?$paragraph->fontSize:
                $this->fontSize;
            $fontStyle = isset($paragraph->fontStyle)?
                $paragraph->fontStyle:$this->fontStyle;
            $fontColor = Xml2Pdf::convertColor(
                isset($paragraph->fontColor)?$paragraph->fontColor:
                $this->fontColor);
            $textalign = isset($paragraph->textAlign)?
                $paragraph->textAlign:$this->textAlign;
            $position = isset($paragraph->position)?$paragraph->position:
                'relative';
            $left = isset($paragraph->left)?$paragraph->left:$this->left;
            $top = isset($paragraph->top)?$paragraph->top:$this->top;
            //set colors
            $this->pdf->SetDrawColor($borderColor["r"],$borderColor["g"],
                $borderColor["b"]);
            $this->pdf->SetFillColor($fillColor["r"],$fillColor["g"],
                $fillColor["b"]);
            $this->pdf->setTextColor($fontColor["r"],$fontColor["g"],
                $fontColor["b"]);
            $this->pdf->SetFont($font, $fontStyle, $fontSize);
            //set position
            if($position == "absolute") {
                $currentX = $left;
                $currentY = $this->pdf->bMargin-$top;
            } else {
                if ($align == "R")    {
                    $currentX = $this->pdf->w - $this->pdf->rMargin - 
                        $contentLength-3;
                    $currentY = $this->pdf->bMargin;
                } elseif ($align == "L") {
                    $currentX = $this->pdf->lMargin;
                    $currentY = $this->pdf->bMargin;
                } elseif ($align == "C") {
                    $currentX = ($this->pdf->w - $this->pdf->lMargin - 
                        $this->pdf->rMargin - $contentLength)/2;
                    $currentY = $this->pdf->bMargin;
                } else {
                    $currentX = $this->pdf->GetX() + $left;
                    $currentY = $this->pdf->bMargin;
                    //$currentY = $this->pdf->GetY() + $top;
                }
            }
            $this->pdf->SetXY($currentX, -$currentY);
             
            // data
            if(empty($content)) {
                $content = $paragraph->content;
            }
            $this->pdf->MultiCell($width, $lineHeight, $content,
                $border, $textalign, $fill);
        }
        return true;
    }
    
    // }}}
    // xml2Pdf_tag_footer::_calcFooterHeight() {{{
    
    /**
     * Calcul the footer height.
     *
     * @return void 
     */
    private function _calcFooterHeight() {
        foreach ($this->elements as $paragraph) {
            if($paragraph->top > 0) {
                $top = $paragraph->top;
                if($top > $this->_height) {
                    $this->_height = $top;
                }
            } else {
                if ($this->_height + $this->fontSize/2 > $this->_height) {
                    $this->_height += $this->fontSize/2;
                }
            }
        }
        $this->pdf->setAutoPageBreak(true, $this->pdf->bMargin + $this->_height);
    }
    
    // }}}
} 
?>
