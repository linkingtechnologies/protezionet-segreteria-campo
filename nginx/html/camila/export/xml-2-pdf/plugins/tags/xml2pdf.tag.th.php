<?php
/**
 * th tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.th.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{

/**
 * parent class
 */
require_once('xml2pdf.tag.tr.php');

// }}}
// doc {{{

/**
 * <th> tag.
 *
 * {@example table.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.th.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.table.pkg
 */ // }}}
class xml2pdf_tag_th extends xml2pdf_tag_tr  {
    // xml2pdf_tag_th::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag properties.
     *
     * @param array $attrs tag properties
     * @param object $parent parent tag
     * @return void 
     */
    public function __construct($attrs, $parent) {
        parent::__construct($attrs, $parent);
    }
    
    // }}}    
    // xml2pdf_tag_th::close() {{{
    
    /**
     * Add the table header
     *
     * @return void 
     */
    public function close() {
       $this->table->header = $this;
    }
    
    // }}}
}
?>
