<?php
require_once('xml2pdf.tag.paragraph.php');

class xml2pdf_tag_datetime extends xml2pdf_tag_paragraph {
    public $format;
    
    
    public function __construct(&$attributes, $parentTag) {

        $this->format = camila_get_locale_date_adodb_format();

        parent::__construct($attributes, $parentTag);
        if(isset($attributes['FORMAT'])) {
            $this->format = $attributes['FORMAT'];
        }
    }
   
    public function close() {
        $this->content = date($this->format. ' H:i:s');
        $this->parent->elements[] = $this;
    }
}
?>
