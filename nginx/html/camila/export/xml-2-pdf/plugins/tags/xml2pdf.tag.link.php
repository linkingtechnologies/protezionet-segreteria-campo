<?php
/**
 * link tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.link.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
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
 * <link> tag.
 *
 * Add a link to an external link to an url, or an internal link
 * to an element using its id.
 *
 * {@example link.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.link.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.link.pkg
 */ // }}}
class xml2pdf_tag_link extends Xml2PdfTextTag {
    // xml2pdf_tag_link::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag attributes
     *
     * @param array $tagAttributes tag attributes
     * @return void
     */
	public function __construct($tagAttributes) {
	    parent::__construct($tagAttributes);
	    
	    $left = $top = 0;
	    
	    if(isset($tagAttributes['REF'])) {
	        $ref = $tagAttributes['REF'];
	    }
	    if(isset($tagAttributes['URL'])) {
	        $url = $tagAttributes['URL'];
	    }
	    if(isset($tagAttributes['NAME'])) {
	        $name = $tagAttributes['NAME'];
	    }
	    if(isset($tagAttributes['LEFT'])) {
	        $left = $tagAttributes['LEFT'];
	    }
	    if(isset($tagAttributes['TOP'])) {
	        $top = $tagAttributes['TOP'];
	    }

        // par default les liens sont en bleu et souligné
        if(!isset($tagAttributes['FONTCOLOR'])) {
            $this->fontColor = '#0000FF';
        } 
        if(!isset($tagAttributes['FONTSTYLE'])) {
            $this->fontStyle = 'U';
        }

        // positionne le curseur 
        if($left > 0) {
            $this->pdf->SetX($left);
        }
        if($top > 0) {
            $this->pdf->SetY($top);
        }
        $this->pdf->setFont($this->font, $this->fontStyle, $this->fontSize);
        $color = Xml2Pdf::convertColor($this->fontColor);
        $this->pdf->setTextColor($color['r'], $color['g'], $color['b']);
        
        if(isset($ref) && isset($name)) {
            if(!isset($this->pdf->linksIds[$ref])) {
                $this->pdf->linksIds[$ref] = $this->pdf->AddLink();
            }
            $url = $this->pdf->linksIds[$ref];
        }
        $this->pdf->Write($this->pdf->defaultLn, $name, $url);
	}
	
	// }}}
}
?>
