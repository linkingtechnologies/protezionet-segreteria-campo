<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2019 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */


function camila_selectformupdatelink(&$field, &$row, $fields)
{
    $arr = Array();
    $inline_id = '';
    foreach($fields as $key) {
        if (substr($key->field, 0, strlen('camilakey_')) == 'camilakey_') {
            $arr[$key->field] = $key->value;
            $inline_id = '__cf__' . $key->value;
        }

    }

	$link = basename($_SERVER["PHP_SELF"])."?camila_update=".urlencode(serialize($arr)) . '&camila_token=' . camila_token(serialize($arr));
	if (isset($_REQUEST['dashboard']))
		$link .= '&dashboard='.$_REQUEST['dashboard'];
    $myLink = new CHAW_link(camila_get_translation('camila.updatebutton'), $link);
    $myLink->set_br(0);
    $row->add_column($myLink);
    global $_CAMILA;
    $reqs = 'camila_update=' . urlencode(serialize($arr)) . '&camila_token=' . camila_token(serialize($arr));
	if (isset($_REQUEST['dashboard']))
		$link .= '&dashboard='.$_REQUEST['dashboard'];
    $_CAMILA['page']->camila_add_js("<script type='text/javascript'>var camila_inline=new Array();</script>\n", 'camila_inline_array');
    $_CAMILA['page']->camila_add_js("<script type='text/javascript'>var camila_inline_script='".basename($_SERVER['PHP_SELF'])."';</script>\n", 'camila_inline_script');
    $_CAMILA['page']->camila_add_js("<script type='text/javascript'>camila_inline['$inline_id']='$reqs';</script>\n", $inline_id);

}


function camila_selectformdeletelink(&$field, &$row, $fields)
{
    $arr = Array();
    foreach($fields as $key) {
        if (substr($key->field, 0, strlen('camilakey_')) == 'camilakey_')
        $arr[$key->field] = $key->value;
    }
    
    if ($_SERVER['QUERY_STRING'] != '')
        $url=basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING']."&camila_delete=". urlencode(serialize($arr)) . '&camila_token=' . camila_token(serialize($arr));
    else
        $url=basename($_SERVER['PHP_SELF'])."?camila_delete=".serialize($arr) . '&camila_token=' . camila_token(serialize($arr));
	if (isset($_REQUEST['dashboard']))
		$url .= '&dashboard='.$_REQUEST['dashboard'];
    $myLink = new CHAW_link(strtoupper(camila_get_translation('camila.deletebutton')), $url);
	$myLink->set_css_class('btn btn-xs btn-default btn-danger');
    $myLink->set_br(0);
    $row->add_column($myLink);
}


class selectform {

  var $table;
  var $keys;      // keys that identifies one unique row, use commas for more than one
  var $fields;    // fields for showing at the listbox, use commas for more than one
  var $order;     // order used to show the items, use like the order by clause
  var $options;   // options tags for the selectionform
  var $value;     // selected value, same order from keys (array)

  var $formupdatelinktext;

  var $whereclause;

  // filter support
  var $filter;

    function selectform($table, $keys, $fields, $order, $orderdirection='asc', $whereclause='', $candelete=false, $caninsert=false)
    {
        $this->table = $table;
        $this->keys = explode(',', $keys);
        $this->fields = $fields;
        $this->order = $order;
        $this->orderdirection=$orderdirection;
        $this->whereclause = $whereclause;
        $this->candelete = $candelete;
        $this->caninsert = $caninsert;

        reset($this->keys );
        reset($this->fields );
    }

    // process input from selection
    // returns true if anything was selected
    function process()
    {
        if( isset($this->filter) )
            $this->filter->process();
        $afield = "select_{$this->table}_field";
        if( !isset( $_REQUEST['camila_update'] ) && !isset( $_REQUEST['camila_delete'] ) )
            return false;

        if( isset( $_REQUEST['camila_update']) ) {
            $this->value = unserialize( stripslashes($_REQUEST['camila_update']) );
            $check = camila_token($_REQUEST['camila_update']);
        } else {
            $this->value = unserialize( stripslashes($_REQUEST['camila_delete']) );
            $check = camila_token($_REQUEST['camila_delete']);
        }

        if ($check != $_REQUEST['camila_token'] && $_REQUEST['camila_update'] != 'new')
            camila_error_page('Accesso non consentito a questa pagina');
        return true;
    }

    function select_data()
    {
        return;
    }

    function draw($draw_filter = true)
    {
        include_once(CAMILA_DIR . 'datagrid/selectform_report.class.php');

        $stmt = 'select ';
        $count = 0;

        reset($this->keys);
        foreach($this->keys as $afield)
        {
            $stmt .= $afield.' as camilakey_'.$afield;
            if ($count==0 && $this->candelete)
                $stmt .= ', '.$afield.' as camila_del_'.$afield;
            $stmt .= ', ';
            $count++;
        }

        $stmt .= $this->fields;
        $stmt .= ' from '.$this->table;

        if ($this->whereclause != '')
            $stmt .= ' where '.$this->whereclause;

        $report = new selectform_report($stmt, '', $this->order, $this->orderdirection, '', null, implode(',', $this->keys));
        //$report = new selectform_report($stmt, '', $this->order, $this->orderdirection, '');
        $report->additional_links = $this->additional_links;
        $report->mapping = $this->mapping;
        $report->drawfilterbox = $this->drawfilterbox;
        $report->drawnavigationbox = $this->drawnavigationbox;

        if ($this->formupdatelinktext != '')
            $report->formupdatelinktext = $this->formupdatelinktext;

        $report->process();

        $i=0;
        $arr = Array();
        reset($this->keys);
        foreach($this->keys as $afield)
        {
            if ($i<(count($this->keys)-1)) {
                $report->fields['camilakey_'.$afield]->print = false;
            }

            else {
                $report->fields['camilakey_'.$afield]->onprint = "camila_selectformupdatelink";
                if ($this->candelete)
                    $report->fields['camila_del_'.$afield]->onprint = "camila_selectformdeletelink";
            }

            $i++;
        }

        $report->draw();
	}

}
?>
