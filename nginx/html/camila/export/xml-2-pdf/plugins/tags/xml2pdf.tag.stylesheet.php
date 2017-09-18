<?php
/**
 * stylesheet tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @version CVS: $Id: xml2pdf.tag.stylesheet.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */

// doc {{{
/**
 * <stylesheet> tag.
 *
 * <stylesheet> tag permit to add style sheet to the document. The tag styles 
 * can be used in text tag.
 *
 * <b>example of style sheet:</b>
 * <code>
 * .b{
 * font-style: B;  //style de la police
 * }
 * .u{
 * font-style: U;
 * }
 * .i{
 * font-style: I;
 * }
 * .br{
 * indent: 2;
 * }
 * .red{
 * color:#ff0000;
 * }
 * </code>
 *
 * {@example stylesheet.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.stylesheet.pkg
 * @version CVS: $Id: xml2pdf.tag.stylesheet.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 */ // }}}
Class xml2pdf_tag_stylesheet {
    // class properties {{{
    
    /**
     * style sheet file path.
     * @var string
     */
    private $_styleSheet = null;
    
    /**
     * styles tags array.
     * @var array
     */
    private $_styleTags;
    
    // }}}
    // xml2pdf_tag_stylesheet::__construct() {{{

    /**
     * Constructor.
     *
     * @param array $tagProperties tag properties
     * @return void
     */
    public function __construct($tagProperties) {
        $pdf = Pdf::singleton();
        if(isset($tagProperties['FILE'])) {
            $this->_styleSheet = $tagProperties['FILE'];
            $this->_parseStyleSheet();
            $pdf->addStyles($this->_styleTags);
        }
    } 
    
    // }}}
    // xml2pdf_tag_stylesheet::_parseStyleSheet() {{{

    /**
     * Parse the style sheet.
     *
     * @return boolean
     */
    private function _parseStyleSheet() {
        $fp = fopen($this->_styleSheet, "r");
        if(!$fp) {
            throw new Exception('can not open the stylesheet file ' . 
                                 $this->_styleSheet);
            die();
        }
        $line = '';
        $currentTag = '';
        $file = '';
        $onComment = false;
        fseek($fp, 0);
        while(!feof($fp)) {
            $tag = array();
            $attr = array();
            $quit = false;
            // la ligne à parser
            $line = fgets($fp);

            //zap les commentaires sur plusieurs lignes
            if(!$onComment) {
                if(ereg('[/][*]', $line)) {
                    $pos = strpos($line, '/*');
                    $lineT = substr($line,0, $pos);
                    $onComment = true;
                }
            }
            if($onComment) {
                while(!$quit) {
                    if(ereg('[*][/]', $line)) {
                        $pos = strpos($line, "*/");
                        $lineT .= substr($line, $pos+2, strlen($line)-$pos+2);
                        $onComment = false;
                        $quit = true;
                    } else {
                        // on est toujours dans du commentaire,
                        // on passe à la ligne suivanye
                        $line = fgets($fp);
                    }
                }
                if(!empty($lineT)) {
                    $line = $lineT;
                }
            }
            // zap les commentaires sur une ligne
            if(ereg('[/][/]', $line)) {
                $pos = strpos($line,'//');
                $line = substr($line, 0, $pos);
            }
        
            // parse la ligne une foi les commentaires zappés
            if(!$onComment && !empty($line)) {
                ereg('^([.])([a-z A-Z 0-9]+)', $line, $tag);
                if(isset($tag[2])) {
                    $currentTag = strtoupper($tag[2]);
                }
                ereg('^([a-z A-Z -]+)([:])([# a-z A-Z 0-9]+)', $line, $attr);
                if(isset($attr[0])) {
                    $this->_styleTags[$currentTag][$attr[1]] = $attr[3];
                }
            }
        }
        fclose($fp);
    } 
    
    // }}}
    // xml2pdf_tag_stylesheet::close() {{{
    
    /**
     * close the tag.
     *
     * @return void
     */
    public function close() {
    } 
    
    // }}}
}
?>
