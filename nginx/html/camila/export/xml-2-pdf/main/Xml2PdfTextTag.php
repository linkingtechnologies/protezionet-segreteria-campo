<?php
/**
 * Xml2PdfTextTag.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Xml2PdfTextTag.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 */

// dependances {{{
/**
 * include parent class
 */
require_once('Xml2PdfTag.php');

// }}}
// doc {{{

/**
 * Text tags.
 *
 * Xml2PdfTextTag define the properties and method used to for tags used to
 * write content in the Pdf document. The content can be write using styles
 * information define with {@link xml2pdf_tag_stylesheets styles sheet}.
 *
 * This class extends {@link Xml2PdfTag Xml2PdfTag}, the tag properties define
 * by this parent class (like id) are available in Xml2PdfTextTag.
 *
 * <b>tag properties:</b>
 * - <b>font</b>        - string -   font (eg times)
 * - <b>fontsize</b>    - integer -  font size (eg 10)
 * - <b>fontcolor</b>   - string -   font color (eg #000000)
 * - <b>fontstyle</b>   - string -   font style [B[U[I]]]
 * - <b>lineheight</b>  - integer -  line height (eg 5)
 * - <b>textalign</b>   - string -   text alignment [L|R|C|J]
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Xml2PdfTextTag.php,v 1.4 2007/01/05 23:07:31 geelweb Exp $
 */ // }}}
Class Xml2PdfTextTag extends Xml2PdfTag {
    // class properties {{{
    
    /**
     * true if use styles tag to write then content.
     * @var boolean
     */
    protected $useStyle = false;
    
    /**
     * font.
     * @var string
     */
    public $font = PDF_DEFAULT_FONT;
    
    /**
     * font style.
     * @var string
     */
    public $fontStyle = PDF_DEFAULT_FONTSTYLE;
    
    /**
     * font size.
     * @var integer
     */
    public $fontSize = PDF_DEFAULT_FONTSIZE;
    
    /**
     * font color.
     * @var string
     */
    public $fontColor = PDF_DEFAULT_FONTCOLOR;
    
    /**
     * text alignment.
     * @var string
     */
    public $textAlign = PDF_DEFAULT_TEXTALIGN;
    
    /**
     * line height.
     * @var integer
     */
    public $lineHeight = PDF_DEFAULT_LINEHEIGHT;

    public $lineSpacing;
    
    /**
     * used styles stack.
     * @var array
     */
    protected $styleStack;


    /**
     * border.
     * @var border
     */

	public $border = 0;

    /**
     * border.
     * @var border
     */

	public $width = 0;


    /**
     * current x pos.
     * @var float
     */
    private $_x = 0;

    /**
     * indentation.
     * @var integer
     */
    private $_indent = 0;
    
    // }}}
    // Xml2PdfTextTag::__construct() {{{

    /**
     * Constructor.
     *
     * Parse the tag properties.
     *
     * @param object Pdf $pdf object Pdf
     * @param array $tagProperties tag properties
     * @return void
     */
    public function __construct($tagProperties) {
        parent::__construct($tagProperties);
        // parse the tag properties for text

        $pdf = Pdf::singleton();

        if(isset($tagProperties['FONT']) && $tagProperties['FONT'] != '') {
            $this->font = $tagProperties['FONT'];
        } else {
            $this->font = $pdf->pageFont;
        }
        if(isset($tagProperties['FONTSTYLE'])) {
            $this->fontStyle = $tagProperties['FONTSTYLE'];
        }
        if(isset($tagProperties['FONTCOLOR'])) {
            $this->fontColor = $tagProperties['FONTCOLOR'];
        }
        if(isset($tagProperties['FONTSIZE'])) {
            $this->fontSize = $tagProperties['FONTSIZE'];
        }
        if(isset($tagProperties['TEXTALIGN'])) {
            $this->textAlign = $tagProperties['TEXTALIGN'];
        }
        if(isset($tagProperties['LINEHEIGHT'])) {
            $this->lineHeight = $tagProperties['LINEHEIGHT'];
        }
        if(isset($tagProperties['BORDER'])) {
            $this->border = $tagProperties['BORDER'];
        }
        if(isset($tagProperties['WIDTH'])) {
            $this->width = $tagProperties['WIDTH'];
        }
        if(isset($tagProperties['LINESPACING'])) {
            $this->lineSpacing = $tagProperties['LINESPACING'];
        }

    } 
    
    // }}}
    // Xml2PdfTextTag::close() {{{

    /**
     * Manage the text writing.
     *
     * @return void
     */
    public function close() {
        if($this->useStyle) {
            $this->render();
        } else {
            $this->_cleanContent();
            $this->pdf->setFont($this->font, $this->fontStyle, 
                                $this->fontSize);
            $fontColor = Xml2Pdf::convertColor($this->fontColor);
            $this->pdf->setTextColor($fontColor['r'], $fontColor['g'], 
                                     $fontColor['b']);
            $this->pdf->MultiCell($this->width, $this->lineHeight, $this->content, 
                                  $this->border, $this->textAlign, false);
        }
    } 
    
    // }}}
    // Xml2PdfTextTag::render() {{{

    /**
     * Text renderer.
     *
     * Write the text in the PDF if the tag use styles tags.
     *
     * @return void
     */
    public function render() {
        // first style is text style!
        $this->styleStack[0] = array(
            'font'       => $this->font,
            'font-size'  => $this->fontSize,
            'font-style' => $this->fontStyle,
            'color'      => $this->fontColor,
            'indent'     => 0); 
        $this->_setStyle();

        $this->_x = 10;
        
        while(!empty($this->content)) {
            $line = $this->_makeLine();
            if(!empty($line)) {
                $this->_printLine($line);
            }
        }
        $this->pdf->Ln();
    } 
    
    // }}}
    // Xml2PdfTextTag::_parseContent() {{{

    /**
     * Parse the content.
     *
     * Return a part of the content.
     * <code>
     * array(
     *     0 => 'string', // text witch have been extract or tag name
     *     1 => 'string', // c if return closing tag, o if return opening tag, s if return space, t if return text
     *     2 => 'string'  // text witch don't be parsed.
     * );
     * </code>
     * Return false if all the content have been parsed.
     *
     * @return mixed
     */
    private function _parseContent() {
        if(empty($this->content)) {
            return false;
        }
        $regs = array();
        $result = array();
        if(ereg('^(</([^>]+)>).*', $this->content, $regs)) {
            // Balise fermante
            $result[1] = 'c';
            $result[2] = trim($regs[2]);
        } else if(ereg('^(<([^>]+)>).*', $this->content, $regs)) {
            // Balise ouvrante
            $regs[2] = ereg_replace('^a', 'a ', $regs[2]); // Rustine : l'espace disparaît
            $result[1] = 'o';
            $result[2] = trim($regs[2]);
 
            // Présence d'attributs
            if(ereg('(.+) (.+)=\'(.+)\' *', $regs[2])) {
                $params = split(" +",$regs[2]);
                $result[2] = trim($params[0]);
                while(list($i, $couple) = each($params)) {
                    if($i>0) {
                        $couple = explode('=',$couple);
                        $couple[0] = trim($couple[0]);
                        $couple[1] = trim($couple[1]);
                        $end = strlen($couple[1]) - 2;
                        $result[$couple[0]] = substr($couple[1], 1, $end);
                    }
                }
            }
        } else if(ereg('^( ).*', $this->content, $regs)) {
            // Espace
            $result[1] = 's';
            $result[2] = $regs[1];
        } else if(ereg('^([^< ]+).*', $this->content, $regs)) {
            // Texte
            $result[1] = 't';
            $result[2] = trim($regs[1]);
        }
        // Elagage
        $begin = isset($regs[1])?strlen($regs[1]):0;
        $end = strlen($this->content);
        $this->content = substr($this->content, $begin, $end);
        $result[0] = $this->content; 
        return $result;
    } 
    
    // }}}
    // Xml2PdfTextTag::_makeLine() {{{

    /**
     * Buil the next line to write.
     *
     * @return string
     */
    private function _makeLine() {
        $line='';
        $continue=true;
        $result=true;
        while ($continue && $result) {
            $result = $this->_parseContent();
            if(in_array($result[1], array('s', 't'))) {
                $line .= $result[2];
                $this->_setStyle();
            } elseif (in_array($result[1], array('c', 'o'))) {
                if($result[1] == 'o') {
                    // on ajoute le tag de style
                    $this->_setStyle();
                    $this->styleStack[] = $this->pdf->styles[$result[2]];
                } elseif($result[1] == 'c') {
                    // on enlève le style
                    $foo = array_pop($this->styleStack);
                    $this->_indent=0;
                    if(isset($foo['indent'])) {
                        $this->_indent=$foo['indent'];
                    }
                }
                $continue = false;
                if(empty($line)) {
                    $continue = true;
                }
            }
        }
        return $line;
    } 
    
    // }}}
    // Xml2PdfTextTag::_printLine(string) {{{

    /**
     * Write a line.
     *
     * @param string $line
     * @return boolean
     */
    private function _printLine($line) {
        $width = $this->pdf->getStringWidth($line);
        $this->pdf->setX($this->_x);
        $this->_x += $width;
        
        if($this->_x > 190) {
            $tmp = $line;
            $this->_x -= $width;
            $maxWidth = 190 - $this->_x;
            $this->_x = 10;
            while(!empty($tmp)) {
                $spacePos = -1;
                $lineLength = strlen($tmp);
                $foo = '';
                $indent = 0;
                for($i=0 ; $i<$lineLength ; $i++) {
                    $char = $tmp{$i}; 
                    if($char == " ") {
                        $spacePos = $i;
                        $foo .= " ";
                        //continue;
                    }
                    $foo .= $char;
                    if($this->pdf->getStringWidth($foo) > $maxWidth) {
                        // ca dépasse, faut couper
                        if($spacePos == -1) {
                            $spacePos = $i;
                        }
                        $foo = substr($tmp, 0, $spacePos);
                        $tmp = substr($tmp, $spacePos, $lineLength-$spacePos);
                        $spacePos = -1;
                        $indent = 1;
                        break;  
                    }
                    if($i == $lineLength-1) {
                        $foo = $tmp;
                        $tmp = '';
                        $indent = $this->_indent;
                    }
                }
                $this->pdf->Cell($this->pdf->getStringWidth($foo), 
                    $this->lineHeight, $foo, 0, $indent, 'L', 0);
                $this->_x = $this->pdf->GetX();
                $maxWidth = 190;
            }
        } else {
            $this->pdf->Cell($width, $this->lineHeight, $line, 0, 
                $this->_indent, 'L', 0);
            if($this->_indent == 2) {
                $this->_x = 10;
            }
        }
    } 
    
    // }}}
    // Xml2PdfTextTag::_setStyle() {{{

    /**
     * Search the styles information and put them.
     *
     * @return void
     */
    private function _setStyle() {
        $fontStyle = '';
        $fontColor = null;
        $font = null;
        $fontSize = null;
        //$indentSetted = false;
        $count = count($this->styleStack); 
        for($i=$count-1 ; $i>=0 ; $i--) {
            if(empty($fontColor) && isset($this->styleStack[$i]['color'])) {
                $fontColor = $this->styleStack[$i]['color'];
            }
            if(empty($font) && isset($this->styleStack[$i]['font'])) {
                $font = $this->styleStack[$i]['font'];
            }
            if(empty($fontSize) && isset($this->styleStack[$i]['font-size'])) {
                $fontSize = $this->styleStack[$i]['font-size'];
            }
            if(isset($this->styleStack[$i]['font-style']) 
                && !empty($this->styleStack[$i]['font-style'])) {
                if(false !== strpos($fontStyle, 
                    trim($this->styleStack[$i]['font-style']))) {
                    continue;
                }
                $fontStyle .= trim($this->styleStack[$i]['font-style']);
            }
        }
        $this->pdf->setFont($font, $fontStyle, $fontSize);
        $fontColor = Xml2Pdf::convertColor($fontColor);
        $this->pdf->setTextColor($fontColor['r'], $fontColor['g'], 
            $fontColor['b']);
    } 
    
    // }}}
    // Xml2PdfTextTag::_cleanContent() {{{
    
    /**
     * Remove the styles information to the content.
     *
     * @return void
     */
    private function _cleanContent() {
        $content = '';
        while(!empty($this->content)) {
            $content .= $this->_makeLine();
        }
        $this->content = $content;
    }

    // }}}


    function mathEval($equation) { 
        $equation = preg_replace("/[^0-9+\-.*\/()%]/","",$equation); 
        // fix percentage calcul when percentage value < 10 
        $equation = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$equation); 
        // calc percentage 
        $equation = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$equation); 
        // you could use str_replace on this next line 
        // if you really, really want to fine-tune this equation 
        $equation = preg_replace("/([0-9]+)(%)/",".\$1",$equation); 
        if ( $equation == "" ) { 
            $return = 0; 
        } else { 
            eval("\$return=" . $equation . ";" ); 
        }
        return $return; 
    } 

}
?>
