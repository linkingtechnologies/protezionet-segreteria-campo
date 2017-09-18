<?php
/**
 * Xml2Pdf.
 *
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright Copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Xml2Pdf.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 *
 * @todo add tag <include file="include_file.xml" />
 */

// dependances {{{

/**
 * include config file.
 */
require_once('Xml2Pdf.config.php');

/**
 * include the class Pdf.
 */
require_once('Pdf.php');

// }}}
// doc {{{
/**
 * Parse an XML file and convert it to PDF document.
 *
 * Parse an XML content using the 
 * {@link http://php.net/manual/en/ref.xml.php PHP XML parser functions}
 * then convert it to PDF document using {@link http://www.fpdf.org FPDF}.
 *
 * For each tag XML, the parser try to instantiate the corresponding object 
 * searching it in the tag plugins directory. eg, to a tag named custom, it'll
 * search the file <b>xml2pdf.tag.custom.php</b>, then it'll instantiate the
 * class <b>xml2pdf_tag_custom</b>. 
 *
 * The tag plugins class must have the following method :
 * - void __construtc(object Pdf, array) or void __construct(object Pdf, array, object Pdf)
 * - void addContent(string)
 * - void close()
 *
 * Example of plugin class :
 * <code>
 * class xml2pdf_tag_custom {
 *     /**
 *      * parent tag object.
 *      {@*}
 *     public $parent;
 *
 *     /**
 *      * object Pdf.
 *      {@*}
 *     public $pdf;
 *
 *     /**
 *      * tag content.
 *      {@*}
 *     public $content;
 *
 *     /**
 *      * Constructor :
 *      *
 *      * If he take only 2 arguments the tag will be orphaned, the parent tag 
 *      * will be unknow'.
 *      {@*}
 *     public function __construct($tagProperties, $parent) {
 *         // here you can parse the tagProperties
 *     }
 *     
 *     /**
 *      * add the content.
 *      {@*}
 *     public function addContent($content) {
 *         // here you can managed actions to do on the tag content
 *     }
 *
 *     /**
 *      * Call when the tag is closed.
 *      {@*}
 *     public function close() {
 *         // here you can put the actions to do when the tag is closed.
 *     }
 * }
 * </code>
 *
 * To simplify the plugin class code, you can extends your plugin class with
 * {@link Xml2PdfTag Xml2PdfTag} or {@link Xml2PdfTextTag Xml2PdfTextTag} or 
 * an existant plugin class.
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @tutorial Xml2Pdf/Xml2Pdf.pkg
 * @version CVS: $Id: Xml2Pdf.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 */ // }}}
Class Xml2Pdf {
    // class properties {{{ 
    /**
     * XML content.
     * @var string
     */
    private $_xml = null;
    
    /**
     * Object Pdf.
     * @var object Pdf
     */
    private $_pdf = null;
    
    /**
     * Tags stack.
     * @var array
     */
    private $_tagStack = array();
    
    /**
     * True if the followings tags do not be parsed.
     * @var boolean
     */
    private $_donotparse = false;
    
    // }}}
    // Xml2Pdf::__construct() {{{

    /**      
     * Constructor.
     *
     * @param string $xml xml file or content
     * @return void
     */
    public function __construct($xml) {
        set_exception_handler(array('Xml2Pdf', 'exceptionHandler'));
        $this->_xml = $this->_getXmlContent($xml);
        $this->_pdf = Pdf::singleton();
    }
    
    // }}}
    // Xml2Pdf::render() {{{

    /**
     * Build the document.
     *
     * Parse the XML content, build the PDF document then return it.
     *
     * @return object Pdf
     */
    public function render() {
        $this->_parse();
        return $this->_pdf;
    } 
    
    // }}}
    // Xml2Pdf::_parse() {{{

    /**
     * Parse the XML content.
     *
     * Parse the XML content using the
     * {@link http://php.net/manual/en/ref.xml.php PHP XML parser functions}
     *
     * @return boolean
     */
    private function _parse() {
        $xml_parser = xml_parser_create(XML_ENCODING);

        xml_set_object ($xml_parser, $this);
        xml_set_character_data_handler ($xml_parser, '_parseContent');
        xml_set_element_handler($xml_parser, '_parseOpeningTag', '_parseClosingTag');

        if(!xml_parse ($xml_parser, $this->_xml)) {
            throw new Exception(sprintf('xml error %s at line %d',
                xml_error_string(xml_get_error_code($xml_parser)),
                xml_get_current_line_number($xml_parser)));
        }
        xml_parser_free($xml_parser);
        return true;
    } 
    
    // }}}
    // Xml2Pdf::_parseOpeningTag() {{{

    /**
     * Parse the opening tags.
     *
     * Try to instantitate the tag's plugin class.
     *
     * @param object $parser parser xml
     * @param string $tag tag name
     * @param array $tagProperties tag's attributes
     * @return void
     */
    private function _parseOpeningTag($parser, $tag, $tagProperties) {
        if(isset($this->_pdf->styles[$tag])) {
            $this->_parseContent($parser, '<'.$tag.'>');
            return;
        }
        if($this->_donotparse) {
            $tagToText = '<' . strtolower($tag);
            foreach ($tagProperties as $key=>$value) {
                $tagToText .= ' ' . strtolower($key) . '="' . $value . '"';
            }
            $tagToText .= '/>';
            $this->_parseContent($parser, $tagToText);
            return;
        }
        
        $tagName = strtolower($tag);
        $clsName = Xml2Pdf::searchPlugin($tagName);
        try {
            $parent = array_pop($this->_tagStack);
            array_push($this->_tagStack, $parent);
            $this->_tagStack[] = new $clsName($tagProperties, $parent);
            if($tag == 'LITERAL') {
                $this->_donotparse = true;
            }
        } catch(Exception $e) {
            $this->_tagStack[] = new $clsName($tagProperties);
        }
    } 
    
    // }}}
    // Xml2Pdf::_parseClosingTag() {{{

    /**
     * Parse closing tags.
     *
     * Call the method close() of the last tag of the tags stack.
     *
     * @param object $parser xml parser.
     * @param string $tag tag name.
     * @return void
     */
    private function _parseClosingTag($parser, $tag) {
        if(isset($this->_pdf->styles[$tag])) {
            $this->_parseContent($parser, '</'.$tag.'>');
            return;
        }
        if($tag == 'LITERAL') {
            $this->_donotparse = false;
        }
        if ($this->_donotparse) {
            $this->_parseContent($parser, '</'.strtolower($tag).'>');
        	return;
        }
        $tagObject = array_pop($this->_tagStack);
        $result = $tagObject->close();
    } 
    
    // }}}
    // Xml2Pdf::_parseContent() {{{

    /**
     * Parse the tag content.
     *
     * Call the method addContent() of the last tag of the tags stack.
     *
     * @param object $parser xml parser
     * @param string $content tag content
     * @return void
     */
    private function _parseContent($parser, $content) {
        $tagObject = array_pop($this->_tagStack);
        $tagObject->addContent($content);
        array_push($this->_tagStack, $tagObject);
    } 
    
    // }}}    
    // Xml2Pdf::_getXmlContent() {{{

    /**
     * Get the xml content of a file.
     *
     * @param string $xml xml file or content
     * @return string
     */
    private function _getXmlContent($xml) {
        if(file_exists($xml)) {
            return implode('', file($xml));
        }
        return $xml;
    } 
    
    // }}}
    // Xml2Pdf::exceptionHandler() {{{

    /**
     * Show an error message.
     *
     * It's also the exception handler.
     *
     * @param Exception $exception object Exception
     * @return void
     */
    static function exceptionHandler($exception) {
        print_r('Xml2Pdf error : ' . $exception->getMessage());
    } 
    
    // }}}
    // Xml2Pdf::convertColor() {{{
    
    /**
     * Convert an hexadecimal color on RGB color.
     *
     * @param string $color color in hex format.
     * @return array
     */
    static function convertColor($color) {
        if(is_array($color)) {
            return $color;
        }
        $array = array();
        $array['r'] = substr((string)$color,1,2);
        $array['g'] = substr((string)$color,3,2);
        $array['b'] = substr((string)$color,5,2);

        $array['r'] = hexdec($array['r']);
        $array['g'] = hexdec($array['g']);
        $array['b'] = hexdec($array['b']);
        return $array;
    }

    // }}}
    // Xml2Pdf::getColor() {{{
    
    /**
     * Generate a random RGB color.
     *
     * @return array 
     */
    static function getColor() {
        $array = array();
        $array['r'] = rand(128, 255);
        $array['g'] = rand(128, 255);
        $array['b'] = rand(128, 255);
        return $array;
    }
    
    // }}}
    // Xml2Pdf::searchPlugin() {{{

    /**
     * Search the xml2pdf plugins.
     *
     * Search the plugin, include the file and return the plugin 
     * class name.
     *
     * The path for the plugin types are defined in Xml2Pdf.config.php. If you 
     * add a plugin type you must add the path in the global variable 
     * $pluginsTypesPaths define in this same file.
     *
     * To use the graph plugin to draw circle graph located in
     * <i>xml2pdf.graph.circle.php<i>, do:
     * <code>
     * $clsName = Xml2Pdf::searchPlugin('circle', 'graph');
     * </code>
     *
     * @param string $keyword the plugin keyword (like image, ean13, circle, etc)
     * @param string $pluginType the type of plugin [tag|barcode|graph] 
     * @return string
     */
    static function searchPlugin($keyword, $pluginType='tag') {
        global $pluginsTypesPaths;
        if(isset($pluginsTypesPaths[$pluginType])) {
            $filePath = $pluginsTypesPaths[$pluginType];
        } else {
            throw new Exception('Unknow plugin type : ' . $pluginType);
            exit;
        }
        $fileName = 'xml2pdf.' . $pluginType . '.' . $keyword . '.php';
        $clsName = 'xml2pdf_' . $pluginType . '_' . $keyword;
        if(!file_exists($filePath . '/' . $fileName)) {
            throw new Exception (
                sprintf('Plugin file %s not found in %s plugins directory %s', 
                $fileName, $pluginType, $filePath));
            exit;
        }
        require_once($filePath . '/' . $fileName);
        return $clsName;
    }

    // }}}
}
?>
