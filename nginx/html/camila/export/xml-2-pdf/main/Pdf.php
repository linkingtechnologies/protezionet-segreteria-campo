<?php
/**
 * Pdf.
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Pdf.php,v 1.7 2007/01/11 17:20:21 geelweb Exp $
 */

// dependances {{{

/**
 * Include the Fpdf parent class.
 */
require_once('fpdf.php');

// }}}
// doc {{{

/**
 * Document Pdf.
 *
 * This class extends {@link http://www.fpdf.org FPDF}, it represent the PDF
 * document.
 *
 * Thanks to Valentin Schmidt for alpha channel support in png images. The 
 * original script can be found in {@link www.fpdf.org fpdf}.
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License 
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Pdf.php,v 1.7 2007/01/11 17:20:21 geelweb Exp $
 */ // }}}
Class Pdf extends FPDF {
    // class properties {{{
    
	public $k;
	public $lMargin;
	public $rMargin;
	public $tMargin;
	public $LineWidth;
	public $CoreFonts;
	public $FontSize;
	
    /**
     * Curent page font.
     * @var string
     */
    public $pageFont = PDF_DEFAULT_FONT;
    
    /**
     * Curent page font size.
     * @var integer
     */
    public $pageFontSize = PDF_DEFAULT_FONTSIZE;

    /**
     * Curent page font color.
     * @var string
     */
    public $pageFontColor = PDF_DEFAULT_FONTCOLOR;

    /**
     * Curent page font style.
     * @var string
     */
    public $pageFontStyle = PDF_DEFAULT_FONTSTYLE;
    
    /**
     * Curent page draw color.
     * @var string
     */
    public $pageDrawColor = '#000000';

    /**
     * Curent page fill color.
     * @var string
     */
    public $pageFillColor = '#ffffff';
    
    /**
     * Styles array.
     * @var array
     */
    public $styles = array();
    
    /**
     * Document headers.
     * Array of xml2pdf_tag_header objects.
     * @var array
     */
    public $header = array();
    
    /**
     * Document footer.
     * Array of xml2pdf_tag_footer objects.
     * @var array
     */
    public $footer = array();
    
    /**
     * Default height of line break.
     * @var float
     */
    public $defaultLn = 5;

    /**
     * Links array.
     * @var array
     */
    public $linksIds = array();

    /**
     * Array of tmp files use to image management.
     * @var array
     */
    private $_tmpFiles = array();

    /**
     * Document filigrees.
     * Array or xml2pdf_tag_filigree objects.
     * @var array
     */
    public $filigree = array();

    /**
     * Singleton instance of Pdf.
     * @var Pdf
     */ 
    protected static $instance = false;

    /**
     * Current rotation angle in degree.
     * @var float
     */
    public $angle = 0;

    // }}}
    // Pdf::__construct() {{{

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
		parent::__construct();
        //parent::FPDF();
    } 
    
    // }}}
    // Pdf::singleton() {{{

    /**
     * Get the singleton instance of the object or instantiate a new object.
     *
     * @return Pdf
     */
    static function singleton() {
        if (!self::$instance) {
            self::$instance = new Pdf();
        }
		
		self::$instance->AddFont('CenturyGothic','');
//self::$instance->SetFont('CenturyGothic','',11);

        return self::$instance;
    }

    // }}}
    // Pdf::AddPage() {{{

    /**
     * Overrided method to manage filigree.
     *
     * Call the base class method AddPage() and draw the filigrees.
     *
     * @return void
     */
	public function AddPage($orientation='') {
        parent::AddPage($orientation);
        foreach($this->filigree as $o) {
            if(is_a($o, 'xml2pdf_tag_filigree')) {
                $o->render();
            }
        }
    }
    
    // }}}
    // Pdf::addStyles() {{{

    /**
     * Add styles.
     *
     * Add styles tags, they are define using the tag 
     * <{@link xml2pdf_tag_stylesheet stylesheet}>.
     * 
     * the format of the styles array is :
     * <code>
     * array('TAGNAME' => array(
     *                      'font'       => 'times',
     *                      'font-size'  => 10,
     *                      'font-style' => 'B'
     *                      'color'      => '#000000',
     *                      'indent'     => 0),
     *       'TAGNAME2' => array(
     *                      'color' => '#ff0000')
     * );
     * </code>
     *
     * @param array $styles
     * @return void
     */
    public function addStyles($styles) {
        $this->styles = array_merge($this->styles, $styles);
    } 
    
    // }}}
    // Pdf::Close() {{{
    
    /**
     * Overrided method.
     *
     * Call the method Close() of the parent base class and unlink the files 
     * stored in $_tmpFiles.
     *
     * @return void
     */
    public function Close() {
	    parent::Close();
	    // clean up tmp files
	    foreach($this->_tmpFiles as $tmp) {
            @unlink($tmp);
        }
    }

    // }}}
    // Pdf::Footer() {{{

    /**
     * Overrided method
     * Method footer, see FPDF::footer().
     *
     * Renders the footer stored in $footer class property.
     *
     * @return void
     */
    public function Footer() {
        foreach($this->footer as $o) {
            if(is_a($o, 'xml2pdf_tag_footer')) {
                $o->render();
            }
        }
    } 
    
    // }}}
    // Pdf::Header() {{{

    /**
     * Overrided method.
     * Method header, see FPDF::header().
     *
     * Render the headers stored in $header class property.
     *
     * @return void
     */
    public function Header() {
        foreach($this->header as $o) {
            if(is_a($o, 'xml2pdf_tag_header')) {
                $o->render();
            }
        }
    } 
    
    // }}}
    // Pdf::Image() {{{

    /**
     * Overrided method, see FPDF::Image().
     *
     * Add an Image to the Pdf.
     *
     * @param string $file Image file path.
     * @param float $x Abscisse of upper left corner.
     * @param float $y Ordinate of upper left corner.
     * @param float $w Image width in page.
     * @param float $h Image height in page.
     * @param string $type Image type [jpg|jpeg|png].
     * @param mixed $link URL or identifiant returned by FPDF::AddLink().
     * @param bool $isMask If true the image is used like mask for another 
     * image ($x, $y, $w and $h are ignored).
     * @param int $maskImg Image number return by a previous call to this method 
     * method with $isMask at true, the image will be used like mask.
     * @return mixed
     */
    public function Image($file,$x,$y,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0) {
    	//Put an image on the page
	    if(!isset($this->images[$file]))
	    {
		    //First use of image, get info
		    if($type=='') {
			    $pos=strrpos($file,'.');
			    if(!$pos) {
				    $this->Error(
                        'Image file has no extension and no type was specified: ' .
                        $file);
                }
			    $type=substr($file,$pos+1);
		    }
		    $type=strtolower($type);
		    //$mqr=get_magic_quotes_runtime();
		    //set_magic_quotes_runtime(0);
            if($type=='jpg' || $type=='jpeg') {
                $info=$this->_parsejpg($file);
            } elseif($type=='png') {
			    $info=$this->_parsepng($file);
			    if($info=='alpha') {
                    return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link);
                }
		    } else {
			    //Allow for additional formats
			    $mtd='_parse'.$type;
			    if(!method_exists($this,$mtd)) {
                    $this->Error('Unsupported image type: '.$type);
                }
			    $info=$this->$mtd($file);
		    }
		    //set_magic_quotes_runtime($mqr);
		
		    if($isMask){
			    if(in_array($file,$this->_tmpFiles)) {
                    $info['cs']='DeviceGray'; //hack necessary as GD can't produce gray scale images
                }
			    if($info['cs']!='DeviceGray') {
                    $this->Error('Mask must be a gray scale image');
                }
			    if($this->PDFVersion<'1.4') {
                    $this->PDFVersion='1.4';
                }
		    }
		    $info['i']=count($this->images)+1;
		    if($maskImg>0)
			    $info['masked'] = $maskImg;
		    $this->images[$file]=$info;
	    } else {
		    $info=$this->images[$file];
        }
	    //Automatic width and height calculation if needed
	    if($w==0 && $h==0) {
		    //Put image at 72 dpi
		    $w=$info['w']/$this->k;
		    $h=$info['h']/$this->k;
	    }
	    if($w==0) {
            $w=$h*$info['w']/$info['h'];
        }
	    if($h==0) {
		    $h=$w*$info['h']/$info['w'];
        }
		
	    if(!$isMask) {
		    $this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',
                $w*$this->k,$h*$this->k,$x*$this->k,
                ($this->h-($y+$h))*$this->k,$info['i']));
        }
        if($link) {
            $this->Link($x,$y,$w,$h,$link);
        }
		
	    return $info['i'];
    }

    // }}}
    // Pdf::imagePngWithAlpha() {{{
    
    /** 
     * Add a png image width aplha channel.
     *
     * Needs GD 2.x extension
     * (pixel-wise operation, not very fast)
     *
     * @param string $file Image file or string.
     * @param float $x Abscisse of the upper left corner.
     * @param float $y Ordinate of the upper left corner
     * @param float $w Width of image in page.
     * @param float $h Height of image in page
     * @param string $link Link or identifiant returned by FPDF::AddLink().
     * @return void
     */
    public function ImagePngWithAlpha($file, $x, $y, $w=0, $h=0, $link='') {
	    $tmp_alpha = tempnam('.', 'mska');
	    $this->_tmpFiles[] = $tmp_alpha;
	    $tmp_plain = tempnam('.', 'mskp');
	    $this->_tmpFiles[] = $tmp_plain;

        if(!is_file($file)) {
            $img = imagecreatefromstring($file);
            $wpx = imagesx($img);
            $hpx = imagesy($img);
        } else {
	        list($wpx, $hpx) = getimagesize($file);
            $img = imagecreatefrompng($file);
        }
	    $alpha_img = imagecreate( $wpx, $hpx );

	    // generate gray scale pallete
	    for($c=0;$c<256;$c++) {
		    ImageColorAllocate($alpha_img, $c, $c, $c);
        }

	    // extract alpha channel
	    $xpx=0;
	    while ($xpx<$wpx){
		    $ypx = 0;
		    while ($ypx<$hpx){
			    $color_index = imagecolorat($img, $xpx, $ypx);
			    $col = imagecolorsforindex($img, $color_index);
			    imagesetpixel($alpha_img, $xpx, $ypx, $this->_gamma( (127-$col['alpha'])*255/127) );
			    ++$ypx;
		    }
		    ++$xpx;
	    }

	    imagepng($alpha_img, $tmp_alpha);
	    imagedestroy($alpha_img);

	    // extract image without alpha channel
	    $plain_img = imagecreatetruecolor ( $wpx, $hpx );
	    imagecopy($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx );
	    imagepng($plain_img, $tmp_plain);
	    imagedestroy($plain_img);
	
	    //first embed mask image (w, h, x, will be ignored)
	    $maskImg = $this->Image($tmp_alpha, 0,0,0,0, 'PNG', '', true); 
	
	    //embed image, masked with previously embedded mask
	    $this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg);
    }

    // }}}
    // Pdf::resetFont() {{{

    /**
     * Reset the font properties.
     *
     * Reset the font properties using page font properties.
     *
     * @return void
     */
    public function resetFont() {
        $this->SetFont($this->pageFont, $this->pageFontStyle, $this->pageFontSize);
        $color = Xml2Pdf::convertColor($this->pageFontColor);
        $this->SetTextColor($color['r'], $color['g'], $color['b']);
    } 
    
    // }}}  
    // Pdf::resetAllValues() {{{
    
    /**
     * Reset all values.
     *
     * Reset all values using the curent page values.
     *
     * @return void 
     * @access public
     */
    public function resetAllValues() {
        $dc = Xml2Pdf::convertColor($this->pageDrawColor);
        $fc = Xml2Pdf::convertColor($this->pageFillColor);
        $this->SetDrawColor($dc["r"],$dc["g"],$dc["b"]);
        $this->SetFillColor($fc["r"],$fc["g"],$fc["b"]);
        $this->resetFont();
    } 
    
    // }}} 
    // Pdf::rotate() {{{

    /**
     * Make a rotation arround a given center.
     *
     * @param float $angle Angle in degree.
     * @param float $x Abscisse of the rotaion center (0 = current x).
     * @param float $y Ordinate of the rotation center (0 = current y).
     * @return void
     */
    public function Rotate($angle, $x=0, $y=0) {
        $x = !$x ? $x : $this->x;
        $y = !$y ? $y : $this->y;
        if ($this->angle != 0) {
            $this->_out('Q');
        }
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf(
                'q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', 
				$c, $s, - $s, $c, $cx, $cy, - $cx, - $cy));
        } 
    }
    
    // }}}
    // Pdf::_parsepng() {{{

	/**
     * Override to accept string. (like a blob). And to accept png with alpha 
     * channel.
     *
     * @param string $file file path or string
     * @return array
	 */
	public function _parsepng($file){
		if (is_file($file)) {
		    return parent::_parsepng($file);
		}
		else {
			//Check signature
			$f = strval($file);
			$stringChar = 0;  // rang du caractere sur lequel est le pointeur
			if(substr($f, 0, 8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
                $this->Error('Not a PNG file: '.$file);
            }
			$stringChar += 8;
			//Read header chunk
			$stringChar += 4;
			if(substr($f, $stringChar, 4)!='IHDR') {
                $this->Error('Incorrect PNG file: '.$file);
            }
			$stringChar += 4;

			$a = unpack('Ni',substr($f, $stringChar, 4));
			$w = $a['i'];
			$stringChar += 4;
			$b = unpack('Ni',substr($f, $stringChar, 4));
			$h = $b['i'];
			$stringChar += 4;
			unset($a, $b);
			
			$bpc=ord(substr($f, $stringChar, 1));
			$stringChar += 1;
			if($bpc>8) {
                $this->Error('16-bit depth not supported: '.$file);
            }
			$ct=ord(substr($f, $stringChar, 1));
			$stringChar += 1;
			if($ct==0) {
				$colspace='DeviceGray';
            } elseif($ct==2) {
				$colspace='DeviceRGB';
            } elseif($ct==3) {
				$colspace='Indexed';
            } else {
                return 'alpha';
            }

			if(ord(substr($f, $stringChar, 1))!=0) {
                $this->Error('Unknown compression method: '.$file);
            }
			$stringChar += 1;
			if(ord(substr($f, $stringChar, 1))!=0) {
                $this->Error('Unknown filter method: '.$file);
            }
			$stringChar += 1;
			if(ord(substr($f, $stringChar, 1))!=0) {
                $this->Error('Interlacing not supported: '.$file);
            }
			$stringChar += 1;
			$stringChar += 4;
            $parms='/DecodeParms <</Predictor 15 /Colors ' . 
                ($ct==2 ? 3 : 1) . ' /BitsPerComponent ' . $bpc . ' /Columns ' . $w . '>>';
			//Scan chunks looking for palette, transparency and image data
			$pal='';
			$trns='';
			$data='';
			do {
				$a = unpack('Ni',substr($f, $stringChar, 4));
				$n = $a['i'];
				$stringChar += 4;
				$type = substr($f, $stringChar, 4);
				$stringChar += 4;
				if($type=='PLTE') {
					//Read palette
					$pal = substr($f, $stringChar, $n);
					$stringChar += $n;
					$stringChar += 4;
				} elseif($type=='tRNS') {
					//Read transparency info
					$t = substr($f, $stringChar, $n);
					$stringChar += $n;
					if($ct==0) {
                        $trns=array(ord(substr($t,1,1)));
                    } elseif($ct==2) {
						$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
                    } else {
						$pos=strpos($t,chr(0));
						if($pos!==false) {
                            $trns=array($pos);
                        }
					}
					$stringChar += 4;
				} elseif($type=='IDAT') {
					//Read image data block
					$data .= substr($f, $stringChar, $n);
					$stringChar += $n;
					$stringChar += 4;
				} elseif($type=='IEND') {
					break;
                } else {
                    $stringChar += $n + 4;
                }
			} while($n);
			if($colspace=='Indexed' && empty($pal)) {
                $this->Error('Missing palette in '.$file);
            }
            return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc,
                'f'=>'FlateDecode', 'parms'=>$parms, 'pal'=>$pal, 
                'trns'=>$trns, 'data'=>$data);
		}
	}

    // }}}
    // Pdf::_putimages() {{{

    /**
     * Overrided method for alpha channel support.
     *
     * @return void
     */
    public function _putimages() {
	    $filter = $this->compress ? '/Filter /FlateDecode ' : '';
	    reset($this->images);
	    while(list($file,$info)=each($this->images)) {
		    $this->_newobj();
	    	$this->images[$file]['n'] = $this->n;
		    $this->_out('<</Type /XObject');
		    $this->_out('/Subtype /Image');
		    $this->_out('/Width ' . $info['w']);
		    $this->_out('/Height ' . $info['h']);

		    if(isset($info['masked'])) {
                $this->_out('/SMask ' . ($this->n-1) . ' 0 R');
            }

		    if($info['cs']=='Indexed') {
                $this->_out('/ColorSpace [/Indexed /DeviceRGB ' .
                    (strlen($info['pal'])/3-1) . ' ' . ($this->n+1) . ' 0 R]');
            } else {
			    $this->_out('/ColorSpace /' . $info['cs']);
			    if($info['cs']=='DeviceCMYK') {
                    $this->_out('/Decode [1 0 1 0 1 0 1 0]');
                }
		    }
		    $this->_out('/BitsPerComponent ' . $info['bpc']);
		    if(isset($info['f'])) {
                $this->_out('/Filter /' . $info['f']);
            }
		    if(isset($info['parms'])) {
                $this->_out($info['parms']);
            }
		    if(isset($info['trns']) && is_array($info['trns'])) {
			    $trns='';
			    for($i=0 ; $i<count($info['trns']) ; $i++) {
                    $trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
                }
			    $this->_out('/Mask [' . $trns . ']');
		    }
		    $this->_out('/Length ' . strlen($info['data']) . '>>');
		    $this->_putstream($info['data']);
		    unset($this->images[$file]['data']);
		    $this->_out('endobj');
		    //Palette
		    if($info['cs']=='Indexed') {
			    $this->_newobj();
			    $pal = $this->compress ? gzcompress($info['pal']) : $info['pal'];
			    $this->_out('<<' . $filter . '/Length ' . strlen($pal) . '>>');
			    $this->_putstream($pal);
			    $this->_out('endobj');
		    }
	    }
    }

    // }}}
    // Pdf::_gamma() {{{

    /**
     * GD seems to use a different gamma, 
     * this method is used to correct it again
     *
     * @param float $v Gamma value.
     * @return float
     */
    private function _gamma($v){
	    return pow ($v/255, 2.2) * 255;
    }

    // }}}


	function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
	{
		$d0 = $a - $b;
		if($cw){
			$d = $b;
			$b = $o - $a;
			$a = $o - $d;
		}else{
			$b += $o;
			$a += $o;
		}
		while($a<0)
			$a += 360;
		while($a>360)
			$a -= 360;
		while($b<0)
			$b += 360;
		while($b>360)
			$b -= 360;
		if ($a > $b)
			$b += 360;
		$b = $b/360*2*M_PI;
		$a = $a/360*2*M_PI;
		$d = $b - $a;
		if ($d == 0 && $d0 != 0)
			$d = 2*M_PI;
		$k = $this->k;
		$hp = $this->h;
		if (sin($d/2))
			$MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
		else
			$MyArc = 0;
		//first put the center
		$this->_out(sprintf('%.2F %.2F m',($xc)*$k,($hp-$yc)*$k));
		//put the first point
		$this->_out(sprintf('%.2F %.2F l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
		//draw the arc
		if ($d < M_PI/2){
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
		}else{
			$b = $a + $d/4;
			$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
		}
		//terminate drawing
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
			$op='b';
		else
			$op='s';
		$this->_out($op);
	}

	function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
	{
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
			$op='B';
		else
			$op='S';
		$MyArc = 4/3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

		$xc = $x+$w-$r;
		$yc = $y+$r;
		$this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
		if (strpos($corners, '2')===false)
			$this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
		else
			$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

		$xc = $x+$w-$r;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
		if (strpos($corners, '3')===false)
			$this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
		else
			$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

		$xc = $x+$r;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
		if (strpos($corners, '4')===false)
			$this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
		else
			$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

		$xc = $x+$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
		if (strpos($corners, '1')===false)
		{
			$this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
			$this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
		}
		else
			$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}


	function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
			$x1*$this->k,
			($h-$y1)*$this->k,
			$x2*$this->k,
			($h-$y2)*$this->k,
			$x3*$this->k,
			($h-$y3)*$this->k));
	}

}
?>
