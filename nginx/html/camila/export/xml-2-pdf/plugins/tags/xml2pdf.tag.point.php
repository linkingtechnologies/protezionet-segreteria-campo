<?php
/**
 * point tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version $Id: xml2pdf.tag.point.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{

/**
 * Parent class.
 */
require_once('Xml2PdfTag.php');

// }}}
// doc {{{

/**
 * <point> tag.
 *
 * The tag <point> is a child of the tag <{@link Xml2Pdf_Tag_line line}>, 
 * it's used to add a point to a line of a tag.
 *
 * <b>example:</b>
 * {@example line.xml}
 *
 * <b>ta attributes:</b>
 * - <b>x</b>  - float - abscisce
 * - <b>y</b>  - float - ordinate
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.point.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.point.pkg
 */ // }}}
class xml2pdf_tag_point extends Xml2PdfTag {
    // xml2pdf_tag_point::__construct() {{{
    
    /**
     * Constructor
     * 
     * Parse the tag properties.
     *
     * @param array $attributes tag attributes
     * @param object $parentTag object xml2pdf_tag_line
     * @return void 
     */
    public function __construct(&$attributes, $parentTag) {
        if(!isset($attributes['X']) || !isset($attributes['Y'])) {
            Xml2Pdf::showError('Tag point must have x and y attributes.');
        }
        $parentTag->graph->data[$parentTag->legend]['x'][] = $attributes['X'];
        $parentTag->graph->data[$parentTag->legend]['y'][] = $attributes['Y'];
    }
    
    // }}}
}
?>
