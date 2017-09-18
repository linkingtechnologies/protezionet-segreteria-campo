<?php
/**
 * data tag plugin file.
 * @filesource
 *
 * @author guillaume luchet <guillaume@geelweb.org>
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @version CVS: $Id: xml2pdf.tag.data.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage DeprecatedTag
 * @deprecated 0.6.10.8
 */

// dependances {{{

/**
 * parent class
 */
require_once('xml2pdf.tag.paragraph.php');

// }}}
// doc {{{

/**
 * <data> tag.
 *
 * only present for compatibility whith older version.
 *
 * @author guillaume luchet <guillaume@geelweb.org>
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @version CVS: $Id: xml2pdf.tag.data.php,v 1.3 2006/12/26 08:38:00 geelweb Exp $
 * @package Xml2Pdf
 * @subpackage DeprecatedTag
 * @deprecated 0.6.10.8
 */ // }}}
class xml2pdf_tag_data extends xml2pdf_tag_paragraph {
}
?>
