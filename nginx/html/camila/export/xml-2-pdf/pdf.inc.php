<?php
/**
 * Define the pdf properties.
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: pdf.inc.php,v 1.2 2007/01/05 23:08:29 geelweb Exp $
 *
 */

if(!defined('FPDF_FONTPATH')) {
    /**
     * define the font path.
     * @var string
     */
    define('FPDF_FONTPATH', FPDF_CLASS_PATH . '/font/');
}

if(!defined('PDF_DEFAULT_FONT')) {
    /**
     * define the pdf default font.
     * @var string
     */
    define('PDF_DEFAULT_FONT', 'times');
}

if(!defined('PDF_DEFAULT_FONTSIZE')) {
    /**
     * define the pdf default font size.
     * @var integer
     */
    define('PDF_DEFAULT_FONTSIZE', 10);
}

if(!defined('PDF_DEFAULT_FONTSTYLE')) {
    /**
     * define the pdf default font style.
     * [B[U[I]]]
     * @var string
     */
    define('PDF_DEFAULT_FONTSTYLE', '');
}

if(!defined('PDF_DEFAULT_FONTCOLOR')) {
    /**
     * define the pdf default font color.
     * @var string
     */
    define('PDF_DEFAULT_FONTCOLOR', '#000000');
}

if(!defined('PDF_DEFAULT_TEXTALIGN')) {
    /**
     * define the pdf default text alignment.
     * [[L|left]|[R|right]|[C|center]|[J|justify]]
     * @var string
     */
    define('PDF_DEFAULT_TEXTALIGN', 'L');
}

if(!defined('PDF_DEFAULT_LINEHEIGHT')) {
    /**
     * define the pdf default line height.
     * @var integer
     */
    define('PDF_DEFAULT_LINEHEIGHT', 5);
}

if(!defined('PDF_DEFAULT_HEADER_FONT')) {
    /**
     * define the pdf default header font.
     * @var string
     */
    define('PDF_DEFAULT_HEADER_FONT', 'times');
}

if(!defined('PDF_DEFAULT_HEADER_FONTSIZE')) {
    /**
     * define the pdf default header font size.
     * @var integer
     */
    define('PDF_DEFAULT_HEADER_FONTSIZE', 9);
}

if(!defined('PDF_DEFAULT_HEADER_FONTSTYLE')) {
    /**
     * define the pdf default header font style.
     * [B[U[I]]]
     * @var string
     */
    define('PDF_DEFAULT_HEADER_FONTSTYLE', '');
}

if(!defined('PDF_DEFAULT_HEADER_FONTCOLOR')) {
    /**
     * define the pdf default header font color.
     * @var string
     */
    define('PDF_DEFAULT_HEADER_FONTCOLOR', '#000000');
}

if(!defined('PDF_DEFAULT_FOOTER_FONT')) {
    /**
     * define the pdf default footer font.
     * @var string
     */
    define('PDF_DEFAULT_FOOTER_FONT', 'times');
}

if(!defined('PDF_DEFAULT_FOOTER_FONTSIZE')) {
    /**
     * define the pdf default footer font size.
     * @var integer
     */
    define('PDF_DEFAULT_FOOTER_FONTSIZE', 8);
}

if(!defined('PDF_DEFAULT_FOOTER_FONTSTYLE')) {
    /**
     * define the pdf default footer font style.
     * [B[U[I]]]
     * @var string
     */
    define('PDF_DEFAULT_FOOTER_FONTSTYLE', '');
}

if(!defined('PDF_DEFAULT_FOOTER_FONTCOLOR')) {
    /**
     * define the pdf default footer font color.
     * @var string
     */
    define('PDF_DEFAULT_FOOTER_FONTCOLOR', '#000000');
}

if(!defined('PDF_DEFAULT_PARAGRAPH_BORDER')) {
    /**
     * define the default paragraph border.
     * [true|false]
     * @var boolean
     */
    define('PDF_DEFAULT_PARAGRAPH_BORDER', false);
}

if(!defined('PDF_DEFAULT_PARAGRAPH_BORDERCOLOR')) {
    /**
     * define the default paragraph border color.
     * @var string
     */
    define('PDF_DEFAULT_PARAGRAPH_BORDERCOLOR', '#000000');
}

if(!defined('PDF_DEFAULT_PARAGRAPH_FILL')) {
    /**
     * define the default paragraph fill mode.
     * [true|false]
     * @var boolean
     */
    define('PDF_DEFAULT_PARAGRAPH_FILL', false);
}

if(!defined('PDF_DEFAULT_PARAGRAPH_FILLCOLOR')) {
    /**
     * define the default paragraph fill color.
     * @var string
     */
    define('PDF_DEFAULT_PARAGRAPH_FILLCOLOR', '#ffffff');
}

if(!defined('PDF_DEFAULT_PARAGRAPH_POSITION')) {
    /**
     * define the default paragraph positioning mode.
     * [relative|absolute]
     * @var string
     */
    define('PDF_DEFAULT_PARAGRAPH_POSITION', 'relative');
}

if(!defined('PDF_DEFAULT_PARAGRAPH_ALIGN')) {
    /**
     * define the default paragraph alignment.
     * [[L|left][R|right][C|center]]
     * @var string
     */
    define('PDF_DEFAULT_PARAGRAPH_ALIGN', false);
}
?>
