<?php
$query = 'select * from ' . CAMILA_APPLICATION_PREFIX . 'camila_bookmarks order by sequence';

$result = $_CAMILA['db']->Execute($query);
if ($result === false)
    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

$camilaUI->insertTitle('Riepilogo ospiti e risorse');

$myText = new CHAW_text('');
$_CAMILA['page']->add_text($myText);

while (!$result->EOF) {
    
    if (strpos($result->fields['url'], 'gby') === false) {
        $myLink = new CHAW_link($result->fields['title'], $result->fields['url']);
        $myLink->set_br(0);
        $_CAMILA['page']->add_link($myLink);
        
        $code = "<span id='" . $result->fields['id'] . "'>0</span>";
        
        $pos = strrpos($result->fields['url'], '?');
        if ($pos !== false)
            $url = $result->fields['url'] . "&camila_json&camila_response_handler=camila_gva_set_response&tqx=reqId:" . $result->fields['id'];
        else
            $url = $result->fields['url'] . "?camila_json&camila_response_handler=camila_gva_set_response&tqx=reqId:" . $result->fields['id'];
        
        $code .= "<script defer src = '" . $url . "'>\n";
        $code .= "</script>\n";
        
        $js = new CHAW_js($code);
        $_CAMILA['page']->add_userdefined($js);
        
        $myText = new CHAW_text('');
        $_CAMILA['page']->add_text($myText);
    }
    
    $result->MoveNext();
    
}

?>