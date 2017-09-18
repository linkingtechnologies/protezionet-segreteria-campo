<?php
/**
 * tag graph plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.graph.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
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
 * <graph> tag.
 *
 * The tag <graph> is used to draw graph in the document. The differents graph 
 * type are defined using plugins. For each graph type a plugin file must exist
 * into the plugin grap path <i>(define in the config file)</i>, it must be 
 * named <b>xml2pdf_graph_mycustomgraphtype.php</b> and must contain a class 
 * named <b>xml2pdf_graph_mycustomgraphtype</b>.
 *
 * The existing plugins are :
 * - {@link xml2pdf_graph_circle circle}
 * - {@link xml2pdf_graph_line line}
 * - {@link xml2pdf_graph_hbar horizontal bars}
 * - {@link xml2pdf_graph_vbar vertical bars}
 *
 * {@example value.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.graph.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.graph.pkg
 */ // }}}
class xml2pdf_tag_graph extends Xml2PdfTag {
    // class properties {{{
    
    /**
     * graph type.
     * @var string 
     */
    public $type = 'hbar';
    
    /**
     * visual effect.
     * @var string 
     */
    public $mode = '2D';
    
    /**
     * legend format.
     * @var string 
     */
    public $legendFormat = '%l';
    
    /**
     * main color.
     * @var string 
     */   
    public $color = null;
    
    /**
     * max scale value.
     * @var integer 
     */
    public $maxValRepere = 0;
    
    /**
     * number of values of the scale.
     * @var integer 
     */
    public $nbIndRepere = 4;
    
    /**
     * font size.
     * @var integer 
     */
    public $fontSize = 8;
    
    /** 
     * number of values.
     * @var integer 
     */
    public $nbVal = 0;
    
    /**
     * graph width.
     * @var float 
     */
    public $width = 0;
    
    /**
     * graph height.
     * @var float 
     */
    public $height = 0;
    
    /**
     * legend width.
     * @var float 
     */
    public $legendWidth = 0;  
    
    /**
     * left margin.
     * @var float 
     */
    public $left = 0;
    
    /** 
     * top margin.
     * @var float 
     */
    public $top = 0;
    
    /**
     * sum of the graph values.
     * @var float 
     */
    public $sum = 0;
    
    /**
     * values legends.
     * @var array 
     */
    public $legends = array();
    
    /**
     * values colors.
     * @var array 
     */
    public $colors = array();
    
    /**
     * graph datas.
     * @var array 
     */
    public $data = array();
    
    // }}}
    // xml2pdf_tag_graph::__construct() {{{
    
    /**
     * Constructor.
     * 
     * @param array $attributes tag properties
     * @return void 
     */
    public function __construct($attributes) {
        parent::__construct($attributes);
        $left = 0;
        $top = 0;
        $absolute = false;
        if(isset($attributes['TYPE'])) {
            $this->type = strtolower($attributes['TYPE']);
        }
        if(isset($attributes['WIDTH'])) {
            $this->width = $attributes['WIDTH'];
        }
        if(isset($attributes['HEIGHT'])) {
            $this->height = $attributes['HEIGHT'];
        }
        if(isset($attributes['LEFT'])) {
            $left = $attributes['LEFT'];
        }
        if(isset($attributes['TOP'])) {
            $top = $attributes['TOP'];
        }
        if(isset($attributes['POSITION'])) {
            $absolute = (strtolower($attributes['POSITION'])=='absolute');
        }
        if(isset($attributes['MODE'])) {
            $this->mode = strtolower($attributes['MODE']);
        }
        if(isset($attributes['LEGEND'])) {
            $this->legendFormat = $attributes['LEGEND'];
        }
        if(isset($attributes['COLOR'])) {
            $this->color = $attributes['COLOR'];
        }
        if(isset($attributes['MAX'])) {
            $this->maxValRepere = $attributes['MAX'];
        }
        if(isset($attributes['REPERE'])) {
            $this->nbIndRepere = $attributes['REPERE'];
        }
        if(isset($attributes['FONTSIZE'])) {
            $this->fontSize = $attributes['FONTSIZE'];
        }
        $delta = ($this->mode=='3d')?4:0;
        
        if($absolute) {
            $this->left = $left;
            $this->top = $top + $delta;
        } else {
            $this->left = $this->pdf->GetX() + $left;
            $this->top = $this->pdf->GetY() + $top + $delta;
        }
    } 
    
    // }}} 
    // xml2pdf_tag_graph::close() {{{

    /**
     * Close the tag.
     * 
     * @return void 
     * @access public
     */
    public function close() {
        $this->pdf->SetXY($this->left, $this->top);
        $this->_drawGraph();
        $this->pdf->SetXY($this->pdf->lMargin, $this->top + $this->height);
        $this->pdf->resetAllValues();
    }
    
    // }}}     
    // xml2pdf_tag_graph::setLegends() {{{
    
    /**
     * Build the graph legend.
     *
     * @return void 
     */
    public function setLegends() {
        $this->sum = array_sum($this->data);
        $this->nbVal = count($this->data);
        foreach($this->data as $l=>$val) {
            if($this->sum != 0) {
                $p=sprintf('%.2f',$val/$this->sum*100).'%';
            } else {
                $p = '';
            }
            if (is_array($val)) {
                $val = '';
            }
            $legend = str_replace(array('%l','%v','%p'),
                                  array($l,$val,$p), $this->legendFormat);
            $this->legends[] = $legend;
            $this->legendWidth = max($this->pdf->GetStringWidth($legend),
                $this->legendWidth);
        }
    }
    
    // }}}
    // xml2pdf_tag_graph::_drawGraph() {{{

    /**
     * Build the graph using appropriate plugin.
     *
     * Instantiate the plugin graph class to render the graph.
     *
     * @return boolean 
     */
    private function _drawGraph() {
        $clsName = Xml2Pdf::searchPlugin($this->type, 'graph');
        call_user_func(array($clsName, 'render'), $this);
        return true;
    } 
    
    // }}} 
 }
?>
