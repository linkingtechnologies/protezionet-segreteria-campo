<?php
/**
 * barcode tag plugin file.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @version CVS: $Id: xml2pdf.tag.barcode.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage Tag
 *
 * @todo manage barcode in header or footer
 * @todo manage barcode on table cell
 */

// dependances {{{

/**
 * parent class
 */
require_once('Xml2PdfTag.php');

// }}}
// doc {{{

/**
 * <barcode> tag.
 *
 * draw an EAN13 or UPC-A codebar (based on a script of 
 * {@link http://www.fpdf.org FPDF}).
 *
 * {@example barcode.xml}
 *
 * @author guillaume l. <guillaume@geelweb.org> 
 * @link http://www.geelweb.org geelweb-dot-org 
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright Copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @subpackage Tag
 * @tutorial Xml2Pdf/Xml2Pdf.Tag.barcode.pkg
 * @version CVS: $Id: xml2pdf.tag.barcode.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 */ // }}}
class xml2pdf_tag_barcode extends Xml2PdfTag  {
    // class properties {{{
        
    /**
     * x pos
     * @var float 
     */
    public $x = 0;
    
    /**
     * y pos
     * @var float 
     */
    public $y = 0;
    
    /**
     * bar width
     * @var float 
     */
    public $width = 0;
    
    /**
     * bar height
     * @var float 
     */
    public $height = 0;
    
    /**
     * bar code
     * @var string 
     */
    public $barcode = '';
    
    /**
     * norm
     * @var string 
     */
    public $norm = 'EAN13';
    
    /**
     * positionning type
     * @var string 
     */
    public $position = 'relative';
    
    /**
     * bar code number of characters
     * @var integer 
     */
    public $len = 13;
    
	public $fontsize = 10;
    // }}}
    // xml2pdf_tag_barcode::__construct() {{{
    
    /**
     * Constructor.
     *
     * Parse the tag properties.
     *
     * @param array $attrs tag properties
     * @return void 
     */
    public function __construct(&$attrs) {
        parent::__construct($attrs);
        $availableAttrs = array('x', 'y', 'barcode', 'width', 'height',
                                'norm', 'position', 'fontsize');
        foreach ($attrs as $attr=>$value) {
            $attr = strtolower($attr);
            if(in_array($attr, $availableAttrs)) {
                $this->$attr = $value;
            } else {
                die('property ' . $attr . ' unrecognize for tag barcode');
            }
       }

       $this->x = $this->mathEval($this->x);
       $this->y = $this->mathEval($this->y);

    }
    
    // }}}
    // xml2pdf_tag_barcode::close() {{{
    
    /**
     * Close the tag and launch the render.
     *
     * Calcul the bar code length and the real coordonate then launch
     * the traitment to draw the bar code.
     *
     * @return void 
     */
    public function close() {
        if($this->position=='relative') {
            $this->x += $this->pdf->GetX();
            $this->y += $this->pdf->GetY();
        }
        //$clsName = $this->getRenderPlugin();
        $norm = strtolower($this->norm);
        $clsName = Xml2Pdf::searchPlugin($norm, 'barcode');
        call_user_func(array($clsName, 'render'), $this);
        return true;
    }
    
    // }}}
    // xml2pdf_tag_barcode::getCheckDigit() {{{
    
    /**
     * Generate a check digit to the bar code.
     *
     * @param string $barcode bar code
     * @return integer 
     */
    public static function getCheckDigit($barcode) {
        //Calcule le chiffre de contrôle
        $sum=0;
        for($i=1;$i<=11;$i+=2)
            $sum+=3*$barcode{$i};
        for($i=0;$i<=10;$i+=2)
            $sum+=$barcode{$i};
        $r=$sum%10;
        if($r>0)
            $r=10-$r;
        return $r;
    }
    
    // }}} 
    // xml2pdf_tag_barcode::testCheckDigit() {{{
    
    /**
     * Check the check digit.
     *
     * @param string $barcode bar code.
     * @return boolean 
     */
    public static function testCheckDigit($barcode) {
        //Vérifie le chiffre de contrôle
        $sum=0;
        for($i=1;$i<=11;$i+=2)
            $sum+=3*$barcode{$i};
        for($i=0;$i<=10;$i+=2)
            $sum+=$barcode{$i};
        return ($sum+$barcode{12})%10==0;
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
