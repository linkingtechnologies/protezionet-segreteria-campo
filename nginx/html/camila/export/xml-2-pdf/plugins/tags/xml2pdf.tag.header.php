<?php
/**
 * Tag header plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.header.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{

/**
 * include parent class.
 */
require_once('Xml2PdfTextTag.php');

// }}}
// doc {{{

/**
 * <header> tag.
 *
 * Tag header is used to add page header in the document.
 *
 * {@example header.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.header.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.header.pkg
 */ // }}}
Class xml2pdf_tag_header extends Xml2PdfTextTag {
    // class properties {{{
    
    /**
     * fill the elements background.
     * @var boolean
     */
    protected $fill = false;

    /**
     * fill color.
     * @var string
     */
    protected $fillColor = '#ffffff';

    /**
     * draw borders.
     * @var boolean
     */
    public $border = false;

    /**
     * borders color.
     * @var string
     */
    protected $borderColor = '#000000';

    /**
     * left margin.
     * @var float
     */
    protected $left = 0;

    /**
     * top margin.
     * @var float
     */
    protected $top;

    /**
     * elements alignment.
     * @var string
     */
    protected $align = 'L';

    /**
     * header start page.
     * @var int
     */
    protected $startPage = 1;
    
    /**
     * header end page.
     * @var int
     */
    protected $endPage = 0;

    /**
     * elements array.
     * @var array
     */
    public $elements = array();

    /**
     * header height.
     * @var float
     */
    private $_height;

    // }}}
    // xml2pdf_tag_header::__construct() {{{
    
    /**
     * Constructor.
     *
     * @param array $tagProperties tag properties
     * @return void
     */
    public function __construct($tagProperties) {
        parent::__construct($tagProperties);
        if(isset($tagProperties['BORDER'])) {
            $this->border = $tagProperties['BORDER'];
        }
        if(isset($tagProperties['BORDERCOLOR'])) {
            $this->borderColor = $tagProperties['BORDERCOLOR'];
        }
        if(isset($tagProperties['FILL'])) {
            $this->fill = $tagProperties['FILL'];
        }
        if(isset($tagProperties['FILLCOLOR'])) {
            $this->fillColor = $tagProperties['FILLCOLOR'];
        }
        if(isset($tagProperties['START'])) {
            $this->startPage = $tagProperties['START'];
        }
        if(isset($tagProperties['END'])) {
            $this->endPage = $tagProperties['END'];
        }
        if(isset($tagProperties['TOP'])) {
            $this->top = $tagProperties['TOP'];
        }
        if(isset($tagProperties['LEFT'])) {
            $this->left = $tagProperties['LEFT'];
        }
        //if(isset($tagProperties['POSITION'])) {
        //    $this->position = (strtolower($tagProperties['POSITION'])=='absolute')?
        //        'absolute':'relative';
        //}
        if(isset($tagProperties['ALIGN'])) {
            switch(strtoupper($tagProperties['ALIGN'])) {
                case 'L':
                case 'LEFT':
                    $this->align = 'L';
                    break;
                case 'R':
                case 'RIGHT':
                    $this->align = 'R';
                    break;
                case 'C':
                case 'CENTER':
                    $this->align = 'C';
                    break;
            }
        }
    }

    // }}}
    // xml2pdf_tag_header::close() {{{
    
    /**
     * Add the header to the Pdf.
     * @return void
     */
    public function close() {
        $this->pdf->header[] = $this;
    }

    // }}}
    // xml2pdf_tag_header::render() {{{
    
    /**
     * Render the header.
     *
     * @return boolean
     */
    public function render() {
        if(($this->endPage && $this->endPage < $this->pdf->PageNo()) ||
        ($this->startPage > $this->pdf->PageNo())) {
            return false;
        }
        if(count($this->elements)==0) {
            $this->_renderSimpleHeader();
            return true;
        }
        $this->_renderComplexHeader();
        return true;
    }

    // }}}
    // xml2pdf_tag_header::_renderSimpleHeader() {{{
    
    /**
     * Render a simple header using <pdf> tag properties.
     *
     * @return void
     */
    private function _renderSimpleHeader() {
        // Affichage d'un header simple reprenant
        // les attributs du tag PDF + un logo
        $tc = array();
        $dc = Xml2Pdf::convertColor($this->borderColor);
        $fc = Xml2Pdf::convertColor($this->fillColor);
        $tc = Xml2Pdf::convertColor($this->fontColor);
 
        $this->pdf->SetFont($this->font, $this->fontStyle, $this->fontSize);
        $this->pdf->SetTextColor($tc["r"],$tc["g"],$tc["b"]);
        $this->pdf->SetDrawColor($dc["r"],$dc["g"],$dc["b"]);
        $this->pdf->SetFillColor($fc["r"],$fc["g"],$fc["b"]);
 
        //$this->_drawLogo();
 
        $this->pdf->MultiCell(0, $this->pdf->FontSizePt, $this->pdf->title, 
            0, 'C', 0);
        $this->pdf->MultiCell(0, 0, $this->pdf->subject, 0, 'L', 0);
        $this->pdf->MultiCell(0, 0, $this->pdf->creator, 0, 'R', 0);
    }

    // }}}
    // xml2pdf_tag_header::_renderComplexHeader() {{{
    
    /**
     * Render a full header.
     *
     * Render header with Xml2PdfTag elements like :
     * - xml2pdf_tag_paragraph
     * - xml2pdf_tag_image
     * - xml2pdf_tag_numerotation
     * - xml2pdf_tag_date
     *
     * @return void
     */
    private function _renderComplexHeader() {
        //affichage du logo
        //$this->_drawLogo();
        foreach ($this->elements as $element) {
            $position = isset($element->position)?$element->position:
                $this->position;
            $left = isset($element->left)?$element->left:$this->left;
            $top = isset($element->top)?$element->top:$this->top;

            if(is_a($element, 'xml2pdf_tag_paragraph')) {
                if(is_a($element, 'xml2pdf_tag_numerotation')) {
                    $element->content = sprintf($element->content,
                        $this->pdf->PageNo());
                }
                
                $textAlign = isset($element->textAlign)?$element->textAlign:
                    $this->textAlign;
                $align = isset($element->align)?$element->align:
                    $this->align;
                $lineHeight = isset($element->lineHeight)?
                    $element->lineHeight:$this->lineHeight;
                $border = isset($element->border)?$element->border:
                    $this->border;
                $borderColor = Xml2Pdf::convertColor(
                    isset($element->borderColor)?$element->borderColor:
                    $this->borderColor);
                $fill = isset($element->fill)?$element->fill:$this->fill;
                $fillColor = Xml2Pdf::convertColor(isset($element->fillColor)?
                    $element->fillColor:$this->fillColor);
                $font = isset($element->font)?$element->font:$this->font;
                $fontSize = isset($element->fontSize)?$element->fontSize:
                    $this->fontSize;
                $fontStyle = isset($element->fontStyle)?
                    $element->fontStyle:$this->fontStyle;
                $fontColor = Xml2Pdf::convertColor(isset($element->fontColor)?
                    $element->fontColor:$this->fontColor);
                $textAlign = isset($element->textAlign)?
                    $element->textAlign:$this->textAlign;
                                //convert colors
                $this->pdf->SetDrawColor($borderColor["r"],$borderColor["g"],
                    $borderColor["b"]);
                $this->pdf->SetFillColor($fillColor["r"],$fillColor["g"],
                    $fillColor["b"]);
                $this->pdf->setTextColor($fontColor["r"],$fontColor["g"],
                    $fontColor["b"]);
                $this->pdf->SetFont($font, $fontStyle, $fontSize);
                
                // initialize parameters
                $contentLength = $this->pdf->GetStringWidth((string)$element->content);                $width = isset($element->width)?$element->width:
                    $contentLength+5;
                
                //positionnement
                if($position == 'absolute') {
                    $currentX = 0;
                    $currentY = 0;
                } else {
                    if ($align == "R")    {
                        $currentX = $this->pdf->w - $this->pdf->rMargin - 
                            $contentLength-3;
                    } elseif ($align == "L") {
                        $currentX = $this->pdf->lMargin;
                    } elseif ($align == "C") {
                        $currentX = ($this->pdf->w - $contentLength)/2;
                    } else {
                        $currentX = $this->pdf->GetX();
                    }
                    $currentY = $this->pdf->GetY();
                }
                $this->pdf->SetXY(($currentX + $left),($currentY + $top));
                //affichage de la donnée
                $this->pdf->MultiCell($width, $lineHeight, 
                    $element->content, $border, 
                    $textAlign, $fill);
                //calcul de la hauteur max du header
                if($this->_height < $this->pdf->GetY()) {
                    $this->_height = $this->pdf->GetY();
                }
                //réinitialisation des valeurs pour la suite
                $this->_resetValues();
            } elseif(is_a($element, 'xml2pdf_tag_image')) {
                // Displaying the image
//                if($position=='relative') {
//                    $left += $this->pdf->GetX();
//                    $top += $this->pdf->GetY();
//                }
                $this->pdf->Image((string)$element->file, $left, $top, 
                            $element->width, $element->height, $element->type);

            }
            
        }
        //positionnement du début de page sous le header
            $this->pdf->SetY($this->_height);
    }

    // }}}
    // xml2pdf_tag_header::_resetValues() {{{
    
    /**
     * Reset header values.
     *
     * @return void
     */
    private function _resetValues() {
        $dc = array(); // drawColor
        $fc = array(); // fillColor
        $dc = Xml2Pdf::convertColor($this->borderColor);
        $fc = Xml2Pdf::convertColor($this->fillColor);
        $this->pdf->SetDrawColor($dc['r'],$dc['g'],$dc['b']);
        $this->pdf->SetFillColor($fc['r'],$fc['g'],$fc['b']);
        $color = array();
        $color = Xml2Pdf::convertColor($this->fontColor);
        $this->pdf->SetTextColor($color['r'],$color['g'],$color['b']);
        $this->pdf->SetFont($this->font, $this->fontStyle, $this->fontSize);
    }

    // }}}
}
?>
