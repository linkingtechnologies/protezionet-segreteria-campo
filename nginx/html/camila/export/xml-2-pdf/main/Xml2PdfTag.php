<?php
/**
 * Xml2PdfTag.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright Copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Xml2PdfTag.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 */

// doc {{{

/**
 * Class Xml2PdfTag.
 *
 * This class describe an XML tag, most of tags plugins classes extends
 * Xml2PdfTag.
 *
 * <b>attributes: </b>
 * - <b>id</b> - string - element id
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright Copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Xml2PdfTag.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 */ // }}}
Class Xml2PdfTag {
    // class properties {{{
    
    /**
     * Pdf object.
     * @var Pdf
     */
    public $pdf;
    
    /**
     * Tag content.
     * @var string
     */
    public $content = '';

    /**
     * Tag identifiant
     * @var string
     */
    public $id = false;

    // }}}
    // Xml2PdfTag::__construct() {{{

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct($tagProperties=array()) {
        $this->pdf = Pdf::singleton();
        if(isset($tagProperties['ID'])) {
            $this->id = $tagProperties['ID'];
            if(!isset($this->pdf->linksIds[$this->id])) {
                $this->pdf->linksIds[$this->id] = $this->pdf->AddLink();
            }
            $this->pdf->SetLink($this->pdf->linksIds[$this->id], -1);
        }
    } 
    
    // }}}
    // Xml2PdfTag::addContent() {{{

    /**
     * Add content.
     *
     * Add content to existing content.
     *
     * @return void
     */
    public function addContent($content) {
        $this->content .= $content;
    } 
    
    // }}}
    // Xml2PdfTag::close() {{{

    /**
     * Close tag.
     *
     * @return boolean
     */
    public function close() {
        return true;
    } 
    
    // }}}
}
?>
