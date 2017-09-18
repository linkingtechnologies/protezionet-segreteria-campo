<?php
Class xml2pdf_tag_roundedbox {

    public function __construct($tagProperties) {
        $pdf = Pdf::singleton();

        $x = $tagProperties['LEFT'];
        $y = $tagProperties['TOP'];
        $w = $tagProperties['WIDTH'];
        $h = $tagProperties['HEIGHT'];
        $r = $tagProperties['RADIUS'];
        $style = $tagProperties['STYLE'];
        $angle = $tagProperties['ANGLE'];

        $lineColor = $tagProperties['LINECOLOR'];
        if ($lineColor != '') {
            $drawColor = Xml2Pdf::convertColor($lineColor);
            $pdf->SetDrawColor($drawColor['r'], $drawColor['g'], $drawColor['b']);

        }

        $lineWidth = $tagProperties['LINEWIDTH'];
        if ($lineWidth != '') {
            $current = $pdf->LineWidth;
            $pdf->SetLineWidth((float) $lineWidth);
        }

        $pdf->RoundedRect($x, $y, $w, $h, $r, $angle, $style);

        if ($lineWidth != '') {
            $pdf->SetLineWidth($current);
        }

    }


    public function close() {
    }

}
?>
