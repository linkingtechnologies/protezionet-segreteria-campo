<?php
/**
 * date tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.date.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{

/**
 * parent class
 */
require_once('xml2pdf.tag.paragraph.php');

// }}}
// doc {{{

/**
 * <date> tag.
 * 
 * the tag <date> have the same properties than the tag 
 * <{@link Xml2Pdf_Tag_paragraph paragraph}>. It used to add date in header or
 * footer.
 *
 * {@example date.xml}
 * 
 * <b>tag property:</b>
 * - <b>format</b> date format
 * {@link http://fr3.php.net/manual/fr/function.date.php date format table}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.date.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.date.pkg
 */ // }}}
class xml2pdf_tag_date extends xml2pdf_tag_paragraph {
    // class properties {{{
       
    /**
     * date format.
     * @var string 
     */
    public $format;
    
    // }}}
    // xml2pdf_tag_date::__construct() {{{
    
    /**
     * Constructor.
     * 
     * @param array $attributes tag properties
     * @param object $parentTag object Xml2PdfTag
     * @return void 
     */
    public function __construct(&$attributes, $parentTag) {

        $this->format = camila_get_locale_date_adodb_format();

        parent::__construct($attributes, $parentTag);
        if(isset($attributes['FORMAT'])) {
            $this->format = $attributes['FORMAT'];
        }
    }
    
    // }}} 
    // xml2pdf_tag_date::close() {{{
    
    /**
     * Add the date in header or footer.
     *
     * @return void 
     */
    public function close() {
        $this->content = date($this->format);
        $this->parent->elements[] = $this;
    }
    
    // }}}
}
?>
