<?php
/**
 * line tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version $Id: xml2pdf.tag.line.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
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
 * <line> tag.
 *
 * The <line> tag is a child tag of <{@link Xml2Pdf_Tag_graph graph}>. 
 * It's used to add data to a graph of type {@link xml2pdf_graph_line line}.
 *
 * {@example line.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.line.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.line.pkg
 */ // }}}
class xml2pdf_tag_line extends Xml2PdfTag {
    // class properties {{{
    /**
     * parent graph tag.
     * @var object Xml2Pdf_Tag_graph
     */
    public $graph;
    
    /**
     * line legend.
     * @var string 
     */
    public $legend;
    
    // }}}    
    // xml2pdf_tag_line::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag attributes and add the line to the graph.
     * 
     * @param array $attributes $tag attributes
     * @param object $parentTag objet Xml2Pdf_Tag_graph
     * @return void 
     */
    public function __construct(&$attributes, $parentTag) {
        $this->graph = $parentTag;
        $color = null;
        if(isset($attributes['LEGEND'])) {
            $line = $attributes['LEGEND'];
        }
        if(isset($attributes['COLOR'])) {
            $color = $attributes['COLOR'];
        }
        $this->legend = $line;
        $this->graph->data[$line] = array();
        $this->graph->colors[$line] = $color;
        $this->graph->legendFormat = '%l';
    }
    
    // }}}
}
?>
