<?php
/**
 * td tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.td.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{

/**
 * parent class
 */
require_once('Xml2PdfTag.php');

// }}}
// doc {{{

/**
 * <td> tag.
 *
 * {@example table.xml}
 *        
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.td.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.table.pkg
 *
 * @todo manage stylsheet
 * @todo manage colspan attribute
 * @todo manage forced height
 */ // }}}
class xml2pdf_tag_td extends Xml2PdfTag {
    // class properties {{{
        
    /**
     * parent tag
     * @var xml2pdf_tag_tr object
     */
    public $row;
    
    /**
     * font
     * @var string 
     */
    public $font = null;
    
    /**
     * font size
     * @var integer 
     */
    public $fontSize = null;
    
    /**
     * font style
     * @var string 
     */
    public $fontStyle = null;
    
    /**
     * font color
     * @var string 
     */
    public $fontColor = null;
    
    /**
     * fill the background
     * @var boolean 
     */
    public $fill = null;
    
    /** 
     * fill color
     * @var string 
     */
    public $fillColor = null;
    
    /**
     * show border
     * @var boolean 
     */
    public $border = null;
    
    /**
     * border color
     * @var string 
     */
    public $borderColor = null;
    
    /**
     * text alignment
     * @var string 
     */
    public $textAlign = null;
    
    /**
     * width
     * @var float 
     */
    public $width = null;
    
    /**
     * line height
     * @var float 
     */
    public $lineHeight = null;
    
    /**
     * height
     * @var float 
     */
    public $height = 0;
    
    // }}}
    // xml2pdf_tag_td::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag properties
     *
     * @param array $attrs tag properties
     * @param object $parent xml2pdf_tag_tr object
     * @return void 
     */
    public function __construct(&$attrs, $parent) {
       parent::__construct($attrs);
       $this->row = $parent;
       if(isset($attrs['FONT'])) {
            $this->font = $attrs['FONT'];
        }
        if(isset($attrs['FONTSIZE'])) {
            $this->fontSize = $attrs['FONTSIZE'];
        }
        if(isset($attrs['FONTSTYLE'])) {
            $this->fontStyle = $attrs['FONTSTYLE'];
        }
        if(isset($attrs['FONTCOLOR'])) {
            $this->fontColor = $attrs['FONTCOLOR'];
        }
        if(isset($attrs['FILL'])) {
            $this->fill = $attrs['FILL'];
        }
        if(isset($attrs['FILLCOLOR'])) {
            $this->fillColor = $attrs['FILLCOLOR'];
        }
        if(isset($attrs['BORDER'])) {
            $this->border = $attrs['BORDER'];
        }
        if(isset($attrs['BORDERCOLOR'])) {
            $this->borderColor = $attrs['BORDERCOLOR'];
        }
        if(isset($attrs['TEXTALIGN'])) {
            switch (strtolower($attrs['TEXTALIGN'])) {
                case 'l':
                case 'left':
                $this->textAlign = 'L';
                break;
                case 'r':
                case 'right':
                $this->textAlign = 'R';
                break;
                case 'c':
                case 'center':
                $this->textAlign = 'C';
                break;
                case 'j':
                case 'justify':
                $this->textAlign = 'J';
                break;
            }
        }
        if(isset($attrs['WIDTH'])) {
            $this->width = $attrs['WIDTH'];
        }
        if(isset($attrs['LINEHEIGHT'])) {
            $this->lineHeight = $attrs['LINEHEIGHT'];
        }
    }
    
    // }}}    
    // xml2pdf_tag_td::close() {{{
    
    /**
     * Add the cell to the row.
     *
     * @return void 
     */
    public function close() {
       $this->row->columns[] = $this;
    }
    
    // }}}    
    // xml2pdf_tag_td::calculLinesNumber() {{{
    
    /**
     * Calcul the number of text line of the cell using the content and the 
     * render params.
     *
     * @return float 
     */
    public function calculLinesNumber() {
        $str = trim($this->content);
        $strWidth = $this->pdf->getStringWidth($str);
        $width = $this->width - 2*$this->pdf->cMargin;
        if($strWidth <= $width) {
            return 1;
        } 
        // plusieurs ligne, il faut prévoir le découpage
        $this->pdf->SetFont($this->font, $this->fontStyle, $this->fontSize);
        $fontkey = $this->font.$this->fontStyle;
        if(!isset($this->pdf->fonts[$fontkey])) {
            $fontkey = $this->font.strtoupper($this->fontStyle);
        }
        $cw = $this->pdf->fonts[$fontkey]['cw'];
        $wmax = $width * 1000 / $this->fontSize * $this->pdf->k;
        $str = str_replace("\r", "", $str);
        $nb = strlen($str);
        if ($nb > 0 and $str[$nb-1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        $foo = '';
        while ($i < $nb) {
            $c = $str[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $foo = '';
                $nl++;
                continue;
            } 
            if ($c == " ") {
                $sep = $i;
            } 
            $l += $cw["$c"];
            $foo .= $c;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    } 
                } else {
                    $i = $sep + 1;
                } 
                $sep = -1;
                $j = $i;
                $l = 0;
                $foo = '';
                $nl++;
            } else {
                $i++;
            } 
        }
        return $nl;
    } 
    
    // }}}    
    // xml2pdf_tag_td::render() {{{
    
    /**
     * render the cell.
     *
     * @return void 
     */
    public function render() {
       // set colors
        $borderColor = Xml2Pdf::convertColor($this->borderColor);
        $this->pdf->SetDrawColor($borderColor["r"],$borderColor["g"],
                $borderColor["b"]);
        $fillColor = Xml2Pdf::convertColor($this->fillColor);
        $this->pdf->SetFillColor($fillColor["r"],$fillColor["g"],
            $fillColor["b"]);
        $fontColor = Xml2Pdf::convertColor($this->fontColor);
        $this->pdf->setTextColor($fontColor["r"],$fontColor["g"],
            $fontColor["b"]);
        $this->pdf->SetFont($this->font, $this->fontStyle, 
            $this->fontSize);
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        if($this->fill || $this->border) {
            $mode = '';
            if($this->fill) {
                $mode .= 'F';
            }
            if($this->border) {
                $mode .= 'D';
            }
            $this->pdf->Rect($x, $y, $this->width, $this->height, $mode);
        }
        $this->pdf->MultiCell($this->width, $this->lineHeight, $this->content, 
                         0,
                         $this->textAlign, 
                         0);
        //Put the position to the right of the cell
        $this->pdf->SetXY($x + $this->width, $y); 
    } 
    
    // }}}
}
?>
