<?php
/**
 * Fichier de configuration.
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright copyright © 2006, guillaume luchet
 * @package Xml2Pdf
 * @version CVS: $Id: Xml2Pdf.config.php,v 1.5 2007/01/05 23:07:31 geelweb Exp $
 *
 */


/**
 * défini le chemin de la classe
 * @var string
 */
define('XML2PDF_CLASS_PATH', dirname(__FILE__));

/**
 * chemin des classes principales
 * @var string
 */
define('XML2PDF_MAIN_PATH', XML2PDF_CLASS_PATH . '/main');

if(!defined('XML2PDF_COMPONENTS_PATH')) {
    /**
     * chemin des classes externes.
     * @var string
     */
    define('XML2PDF_COMPONENTS_PATH', XML2PDF_CLASS_PATH . '/components');
}

if(!defined('FPDF_CLASS_PATH')) {
    /**
     * chemin ver la classe fpdf.
     * @var string
     */
    define('FPDF_CLASS_PATH', CAMILA_LIB_DIR . '/fpdf');
}

if(!defined('XML2PDF_PLUGINS_TAGS_PATH')) {
    /**
     * répertoires des plugins de balises.
     * @var string
     */
    define('XML2PDF_PLUGINS_TAGS_PATH', XML2PDF_CLASS_PATH . '/plugins/tags');
}

if(!defined('XML2PDF_PLUGINS_GRAPHS_PATH')) {
    /**
     * répertoire des plugins de graph.
     * @var string
     */
    define('XML2PDF_PLUGINS_GRAPHS_PATH', XML2PDF_CLASS_PATH . '/plugins/graphs');
}

if(!defined('XML2PDF_PLUGINS_BARCODES_PATH')) {
    /**
     * Répertoire des plugins de code bare.
     * @var string
     */
    define('XML2PDF_PLUGINS_BARCODES_PATH', XML2PDF_CLASS_PATH . '/plugins/barcodes');
}

global $pluginsTypesPaths;
$pluginsTypesPaths = array(
    'tag'     => XML2PDF_PLUGINS_TAGS_PATH, 
    'barcode' => XML2PDF_PLUGINS_BARCODES_PATH, 
    'graph'   => XML2PDF_PLUGINS_GRAPHS_PATH);

// define the include path
$includePath = array(
    XML2PDF_CLASS_PATH,
    XML2PDF_MAIN_PATH,
    XML2PDF_COMPONENTS_PATH,
    FPDF_CLASS_PATH);
ini_set('include_path', ini_get('include_path') . ':' . 
    implode(PATH_SEPARATOR, $includePath));

// define the xml properties
if(!defined('XML_ENCODING')) {
    /**
     * encodage des fichier XML.
     * @var string
     */
    define('XML_ENCODING', 'ISO-8859-1');
}

// define the pdf properties
require_once('pdf.inc.php');

?>
