<?php

require_once(CAMILA_LIB_DIR.'qrcode/qrcode.class.php');


Class xml2pdf_tag_qrcode {
    public $top = 0;
    public $left = 0;
    public $width = 0;
    public $height = 0;
	public $size = 1;
    public $position = 'relative';
	public $level = 'M';
	public $base64 = 'no';

    private $_parent;

    protected $pdf;

    public $content = '';
    
    public function __construct($tagProperties, $parent=false) {

        if(isset($tagProperties['WIDTH'])) {
            $this->width = $tagProperties['WIDTH'];
        }
        if(isset($tagProperties['HEIGHT'])) {
            $this->height = $tagProperties['HEIGHT'];
        }
        if(isset($tagProperties['TOP'])) {
            $this->top = $this->mathEval($tagProperties['TOP']);
        }
        if(isset($tagProperties['LEFT'])) {
            $this->left = $this->mathEval($tagProperties['LEFT']);
        }
        if(isset($tagProperties['POSITION'])) {
            $this->position = $tagProperties['POSITION'];
        }
        if(isset($tagProperties['CONTENT'])) {
            $this->content = $tagProperties['CONTENT'];
        }
		if(isset($tagProperties['LEVEL'])) {
            $this->level = $tagProperties['LEVEL'];
        }
		if(isset($tagProperties['SIZE'])) {
            $this->size = $tagProperties['SIZE'];
        }
		if(isset($tagProperties['BASE64'])) {
            $this->base64 = $tagProperties['BASE64'];
        }

        $this->_parent = $parent;    
        $this->pdf = Pdf::singleton();
   }

    /*public function addContent($content) {
        $this->file = base64_decode($content);
    } */
    
    public function close() {
        if (is_a($this->_parent, 'xml2pdf_tag_header')) {
                $this->_parent->elements[] = $this;
        } else {
            if($this->position=='relative') {
                $this->left += $this->pdf->GetX();
                $this->top += $this->pdf->GetY();
            }
			if ($this->base64 == 'yes')
				$qrcode = new QRcode(base64_encode(utf8_encode($this->content)), $this->level);
			else
				$qrcode = new QRcode(utf8_encode($this->content), $this->level);
			//$qrcode = new QRcode($this->content, $this->level); // error level : L, M, Q, H
			$qrcode->disableBorder();
			$qrcode->displayFPDF($this->pdf, $this->left, $this->top, $this->size, array(255,255,255), array(0,0,0));
        }
    }

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
