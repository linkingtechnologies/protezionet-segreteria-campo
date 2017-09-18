<?php
/**
 * tr tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.tr.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
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
 * <tr> tag.
 *
 * {@example table.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.tr.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.table.pkg
 */ // }}}
class xml2pdf_tag_tr extends Xml2PdfTag {
    // class properties {{{
    
    /**
     * table.
     * @var object xml2pdf_tag_table 
     */
    public $table;
    
    /**
     * line cells.
     * @var array object xml2pdf_tag_td
     */
    public $columns;
    
    /**
     * font.
     * @var string 
     */
    public $font = null;
    
    /**
     * font size.
     * @var integer 
     */
    public $fontSize = null;
    
    /**
     * font style.
     * @var string 
     */
    public $fontStyle = null;
    
    /**
     * font color.
     * @var string 
     */
    public $fontColor = null;
    
    /**
     * fill the background.
     * @var boolean 
     */
    public $fill = null;
    
    /**
     * fill color.
     * @var string 
     */
    public $fillColor = null;
    
    /**
     * show the borders.
     * @var boolean 
     */
    public $border = null;
    
    /**
     * border color.
     * @var string 
     */
    public $borderColor = null;
    
    /**
     * text alignment.
     * @var string 
     */
    public $textAlign = null;
    
    /**
     * text line height.
     * @var float 
     */
    public $lineHeight = null;
    
    /**
     * table line height.
     * @var float 
     */
    public $height = 0;
    
    /**
     * columns width.
     * @var array 
     */
    public $columnsWidth = false;
    
    /**
     * number of line.
     * @var integer 
     */
    public $linesNumber = 0;
    
    // }}}
    // xml2pdf_tag_tr::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the properties.
     *
     * @param array $attrs tag properties
     * @param object $parent object xml2pdf_tag_table
     * @return void 
     */
    public function __construct(&$attrs, $parent) {
        parent::__construct($attrs);
        $this->table = $parent;

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
        if(isset($attrs['LINEHEIGHT'])) {
            $this->lineHeight = $attrs['LINEHEIGHT'];
        }
    }
    
    // }}}    
    // xml2pdf_tag_tr::close() {{{
    
    /**
     * Add the line to the table.
     *
     * @return void 
     */
    public function close() {
        $this->table->rows[] = $this;
    }
    
    // }}}    
    // xml2pdf_tag_tr::render() {{{
    
    /**
     * Render the line.
     *
     * @return void 
     */ 
    public function render() {
        if(!$this->columnsWidth) {
            // première ligne, on calcule la largeur des colonnes.
            $this->_calculColumnsWidth();
        }
        $columnsProperties = array('font', 'fontSize', 'fontStyle',
            'fontColor', 'fill', 'fillColor', 'border',
            'borderColor', 'textAlign', 'lineHeight');
        
        // paramétrage des colonnes de la ligne
        $columnsWidth = array_reverse($this->columnsWidth);
        foreach ($this->columns as $column) {
            $height = 0;
            // définies les propriétés non définie de la colonne
            foreach ($columnsProperties as $property) {
                if(!isset($column->$property)) {
                    $column->$property = $this->$property;
                }
            }
            $column->width = array_pop($columnsWidth);
            
            // positionne les infos de police et couleur
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
        }
        // calcule la hauteur de la ligne
        $this->_calculRowHeight();
        if(($this->pdf->getY() + $this->height) >= ($this->pdf->fh - $this->pdf->bMargin)) {
            $this->pdf->AddPage();
            //@todo if in th and tt exist write tt
        }
        
        // affiche les colonnes
        foreach ($this->columns as $column) {
            $this->_renderColumn($column);
        }
        // positionne le curseur pour la ligne suivante
        $this->pdf->Ln($this->height);
        $this->pdf->setX($this->table->left);
    }
    
    // }}}    
    // xml2pdf_tag_tr::_renderColumn(object) {{{
    
    /**
     * Render a row cell
     *
     * @return void 
     */
    private function _renderColumn($column) {
        $column->height = $this->height;
        $column->render();
    }
    
    // }}}    
    // xml2pdf_tag_tr::_calculRowHeight() {{{
    
    /**
     * Calcul the row height.
     *
     * Calcul the row height using the number of text line of the cells, the 
     * text height line, the font and the font size.
     *
     * @return void 
     */
    private function _calculRowHeight() {
        foreach ($this->columns as $column) {
            $nbLines = $column->calculLinesNumber();
            $this->height = max($this->height, 
               $nbLines * $column->lineHeight);
        }
    }
    
    // }}}    
    // xml2pdf_tag_tr::_calculColumnsWidth() {{{
    
    /**
     * Calcul the row cells width.
     *
     * @return void 
     */
    private function _calculColumnsWidth() {
        $totalWidth = $this->table->width;
        $nbColumns = count($this->columns);
        foreach ($this->columns as $column) {
            if(isset($column->width)) {
                // valeur en % de la largeur de la table
                ereg('^([0-9]+)([%])', $column->width, $tokens);
                if(!empty($tokens)) {
                    $column->width = $tokens[1] * $this->table->width / 100;
                    unset($tokens);
                }
                // valeur en dur
                $totalWidth -= $column->width;
                $nbColumns--;
            }
        }
        // renseigne les largeurs non précisé
        if($nbColumns) {
            $width = $totalWidth / $nbColumns;
        }
        foreach ($this->columns as $column) {
            $this->columnsWidth[] = isset($column->width)?$column->width:$width;
        }
    }
    
    // }}}
}
?>
