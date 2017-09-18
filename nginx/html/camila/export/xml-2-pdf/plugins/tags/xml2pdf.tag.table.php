<?php
/**
 * table tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version $Id: xml2pdf.tag.table.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 */

// dependances {{{

/**
 * Parent class
 */
require_once('Xml2PdfTag.php');

// }}}
// doc {{{

/**
 * <table> tag.
 *
 * It is used to add table in the document.
 *
 * {@example table.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.table.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.table.pkg
 * @todo check table positioning, and calcul of width
 */ // }}}
class xml2pdf_tag_table extends Xml2PdfTag {
    // class properties {{{
        
    /**
     * table title.
     * @var object xml2pdf_tag_tt 
     */
    public $title = false;
    
    /**
     * table header.
     * @var object xml2pdf_tag_th
     */
    public $header = false;
    
    /**
     * table rows.
     * @var array 
     */
    public $rows = array();
    
    /**
     * font size.
     * @var integer 
     */
    public $fontSize = 10;
    
    /**
     * height line.
     * @var integer 
     */
    public $lineHeight = 5;
    
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
     * font style.
     * @var string 
     */
    public $fontStyle = '';
    
    /**
     * font family.
     * @var string 
     */
    public $font = 'times';
    
    /**
     * font color.
     * @var string 
     */
    public $fontColor = '#000000';
    
    /**
     * fill color.
     * @var string 
     */
    public $fillColor = '#ffffff';
    
    /**
     * border color.
     * @var string 
     */
    public $borderColor = '#000000';
    
    /**
     * table alignment.
     * @var string 
     */
    public $align = 'L';
    
    /**
     * text alignment.
     * @var string 
     */
    public $textAlign = 'L';
    
    /**
     * positionning mode.
     * @var string 
     */
    public $position = 'relative';
    
    /**
     * fill the background.
     * @var boolean 
     */
    public $fill = false;
    
    /**
     * show the border.
     * @var boolean 
     */
    public $border = true;
    
    /**
     * width.
     * @var float 
     */
    public $width = null;
    
    // }}}
    // xml2pdf_tag_table::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag properties
     *
     * @param array $attrs tag properties
     * @return void 
     */
    public function __construct($attrs) {
        parent::__construct($attrs);
        
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
        if(isset($attrs['ALIGN'])) {
            switch (strtolower($attrs['ALIGN'])) {
                case 'l':
                case 'left':
                    $this->align = 'L';
                    break;
                case 'r':
                case 'right':
                    $this->align = 'R';
                    break;
                case 'c':
                case 'center':
                    $this->align = 'C';
                    break;
            }
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
        if(isset($attrs['LEFT'])) {
            $this->left = $attrs['LEFT'];
        }
        if(isset($attrs['TOP'])) {
            $this->top = $attrs['TOP'];
        }
        if(isset($attrs['POSITION'])) {
            $this->position = $attrs['POSITION'];
        }
    }
    
    // }}}
    // xml2pdf_tag_table::close() {{{
    
    /**
     * call the table renderer.
     *
     * @return void 
     */
    public function close() {
       $this->render();
    }
    
    // }}}    
    // xml2pdf_tag_table::render() {{{
    
    /**
     * Render the table.
     *
     * @return void 
     */
    public function render() {
        // Set the initials X,Y coordonates
        if($this->position == 'absolute') {
            $this->pdf->setXY($this->left, $this->top);
        } else {
            $this->pdf->SetXY($this->pdf->GetX() + $this->left,
                $this->pdf->GetY() + $this->top);
        }
        $this->left = $this->pdf->GetX();
        $this->top = $this->pdf->GetY();
        // calcul the table width
        if(!isset($this->width)) {
            $this->width = 190 - $this->left;
        }
        // render title
        if(isset($this->title) && $this->title) {
            $this->_renderTitle();
            $this->top = $this->pdf->GetY();
            //$this->pdf->Ln($this->title->lineHeight);
            $this->pdf->setX($this->left);
        }
        // render header
        $headerHeight = $this->_renderHeader();
        $this->top += $headerHeight;
        // render rows
        foreach ($this->rows as $row) {
            $rowHeight = $this->_renderRow($row);
            $this->top += $rowHeight;
        }
    }
    
    // }}}    
    // xml2pdf_tag_table::_renderTitle() {{{
    
    /**
     * Render the table title and return its height.
     *
     * @return float 
     */
    private function _renderTitle() {
        $rowProperties = array('font', 'fontSize', 'fontStyle',
           'fontColor', 'fill', 'fillColor', 'border',
           'borderColor', 'textAlign', 'lineHeight');
           
        // render the header
        foreach ($rowProperties as $property) {
            if(!isset($this->title->$property)) {
                $this->title->$property = $this->$property;
            }
        }
        $this->title->render();
        return $this->title->height;
    }
    
    // }}}
    // xml2pdf_tag_table::_renderHeader() {{{
    
    /**
     * Render the table header and return its height.
     *
     * @return float 
     */
    private function _renderHeader() {
        $rowProperties = array('font', 'fontSize', 'fontStyle',
           'fontColor', 'fill', 'fillColor', 'border',
           'borderColor', 'textAlign', 'lineHeight');
           
        // render the header
        foreach ($rowProperties as $property) {
            if(!isset($this->header->$property)) {
                $this->header->$property = $this->$property;
            }
        }
        $this->header->render();
        return $this->header->height;
    }
    
    // }}}    
    // xml2pdf_tag_table::_renderRow() {{{
    
    /**
     * Render a row.
     *
     * @param array $row Object xml2pdf_tag_tr
     * @return float 
     */
    private function _renderRow($row) {
        $rowProperties = array('font', 'fontSize', 'fontStyle',
           'fontColor', 'fill', 'fillColor', 'border',
           'borderColor', 'textAlign', 'width', 'left', 'top', 'lineHeight');
        // initialize font 
        foreach ($rowProperties as $property) {
            if(!isset($row->$property)) {
                $row->$property = $this->$property;
            }
        }
        $row->columnsWidth = $this->header->columnsWidth;
        // render the row
        $row->render();        
        return $row->height;
    }
    
    // }}}
}
?>
