<?php
defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

  $_CAMILA['page']->camila_export_enabled = false;

    if (isset($_REQUEST['admin'])) {	  
	  require_once(CAMILA_DIR.'/admin/dashboards.inc.php');
	}
  else {
  $query = 'SELECT url,short_title FROM ' . CAMILA_TABLE_PAGES . ', ' . CAMILA_TABLE_PLANG . ' WHERE ('. CAMILA_TABLE_PAGES .'.url = ' . CAMILA_TABLE_PLANG .'.page_url) and level>=' . $_CAMILA['user_level'] .' AND visible='.$_CAMILA['db']->qstr('yes').' AND active=' . $_CAMILA['db']->qstr('yes') . ' and parent=' . $_CAMILA['db']->qstr($_CAMILA['page_url']) . " and lang=" . $_CAMILA['db']->qstr($_CAMILA['lang']) . " ORDER by label_order";

  $result = $_CAMILA['db']->Execute($query);
  if ($result === false)
      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

  $text = new CHAW_text('');
  $text->set_br(2);
  $_CAMILA['page']->add_text($text);

  while (!$result->EOF) {
      $link = new CHAW_link($result->fields['short_title'], $result->fields['url']);
	  $link->set_css_class('btn btn-md btn-default btn-primary btn-space');
      $_CAMILA['page']->add_link($link);
      $result->MoveNext();
  }
  }
  
?>