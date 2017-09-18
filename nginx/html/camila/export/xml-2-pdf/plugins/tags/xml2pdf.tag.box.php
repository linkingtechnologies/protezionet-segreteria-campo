<?php
Class xml2pdf_tag_box {
    /**
     * Constructor.
     *
     * Parse the tag attributes and add the new line to the document.
     *
     * @param array $tagProperties tag properties
     * @return void
     */
    public function __construct($tagProperties) {
        $pdf = Pdf::singleton();


        $lineColor = $tagProperties['LINECOLOR'];
        if ($lineColor != '') {
            $drawColor = Xml2Pdf::convertColor($lineColor);
            $pdf->SetDrawColor($drawColor['r'], $drawColor['g'], $drawColor['b']);

        }


        $lineWidth = $tagProperties['LINEWIDTH'];
        if ($lineWidth != '') {
            $currentLW = $pdf->LineWidth;
            $pdf->SetLineWidth((float) $lineWidth);
        }

        $x1 = $this->mathEval($tagProperties['LEFT']);
        $x2 = $this->mathEval($tagProperties['RIGHT']);
        $y1 = $this->mathEval($tagProperties['TOP']);
        $y2 = $this->mathEval($tagProperties['TOP']);

        $pdf->Line((float)$x1, (float)$y1, (float)$x2, (float)$y2);

        $y1 = $this->mathEval($tagProperties['BOTTOM']);
        $y2 = $y1;

        $pdf->Line((float)$x1, (float)$y1, (float)$x2, (float)$y2);

        //$x1 = $this->mathEval($tagProperties['LEFT']);
        $x2 = $x1;
        $y1 = $this->mathEval($tagProperties['TOP']);
        $y2 = $this->mathEval($tagProperties['BOTTOM']);

        $pdf->Line((float)$x1, (float)$y1, (float)$x2, (float)$y2);

        $x1 = $this->mathEval($tagProperties['RIGHT']);
        $x2 = $x1;
        //$y1 = $this->mathEval($tagProperties['TOP']);
        //$y2 = $this->mathEval($tagProperties['BOTTOM']);

        $pdf->Line((float)$x1, (float)$y1, (float)$x2, (float)$y2);

        if ($lineWidth != '') {
            $pdf->SetLineWidth($currentLW);
        }

    }

    /**
     *
     * @return void
     */
    public function close() {
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
