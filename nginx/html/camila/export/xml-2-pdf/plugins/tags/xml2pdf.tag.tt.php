<?php
/**
 * tt tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.tt.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{

/**
 * parent class.
 */
require_once('xml2pdf.tag.tr.php');

// }}}
// doc {{{

/**
 * <tt> tag.
 *
 * {@example table.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.tt.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.table.pkg
 */ // }}}
class xml2pdf_tag_tt extends xml2pdf_tag_tr {
    // xml2pdf_tag_tt::close() {{{
    
    /**
     * Call when the tag's closed.
     *
     * @return void 
     */
    public function close() {
       $this->table->title = $this;
    }
    
    // }}}
    // xml2pdf_tag_tt::render() {{{
    
    /**
     * Show table title.
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
        // Save the current position
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        $this->pdf->MultiCell($this->table->width, $this->lineHeight, $this->content, 
                         $this->border, 
                         $this->textAlign, 
                         $this->fill);
    }
    
    // }}}
}
?>
