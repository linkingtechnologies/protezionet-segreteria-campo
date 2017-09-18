<?php
/**
 * value tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.value.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
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
 * <value> tag.
 *
 * The <value> tag is a child tag of <{@link xml2pdf_tag_graph graph}>. 
 * It is used to add data in a graph of type 
 * {@link xml2pdf_graph_circle circle}, {@link xml2pdf_graph_hbar hbar} or
 * {@link xml2pdf_graph_vbar vbar}.
 *
 * {@example value.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.value.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.value.pkg
 */ // }}}
class xml2pdf_tag_value extends Xml2PdfTag {
    // xml2pdf_tag_value::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag properties and add value to the graph.
     *
     * @param array $attributes tag properties
     * @param object $parentTag object xml2pdf_tag_graph
     * @return void 
     */
    public function __construct($attributes, $parentTag) {
        $legend = count($parentTag->data);
        $data = 0;
        $color = null;
 
        if(isset($attributes['LEGEND'])) {
            $legend = $attributes['LEGEND'];
        }
        if(isset($attributes['DATA'])) {
            $data = $attributes['DATA'];
        }
        if(isset($attributes['COLOR'])) {
            $color = $attributes['COLOR'];
        }
        
        $parentTag->data[$legend] = $data;;
        $parentTag->colors[] = $color;
    }
    
    // }}}
}
?>
