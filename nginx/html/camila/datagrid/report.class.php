<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2017 Umberto Bresciani

    Camila PHP Framework is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Camila PHP Framework is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Camila PHP Framework. If not, see <http://www.gnu.org/licenses/>. */


require_once(CAMILA_DIR . 'datagrid/elements/report/field.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/string.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/int.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/real.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/date.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/datetime.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/timestamp.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/icon.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/formula.php');
require_once(CAMILA_DIR . 'datagrid/elements/report/query.php');



function camila_formupdatelink(&$field, &$row, $fields)
{
    global $_CAMILA;
    
    $arr       = array();
    $inline_id = '';
    foreach ($fields as $key) {
        if (substr($key->field, 0, strlen('camilakey_')) == 'camilakey_' && substr($key->field, 0, strlen('camilakey_del_')) != 'camilakey_del_') {
            if (strpos($key->field, '__') !== false)
                $kf .= 'camilakey_' . substr($key->field, strpos($key->field, '__') + 2);
            else
                $kf .= $key->field;
            
            $inline_id = '__cf__' . $key->value;
            $arr[$kf]  = $key->value;
        }
    }
    
    $reqs = 'camila_update=' . urlencode(serialize($arr)) . '&camila_token=' . camila_token(serialize($arr));
    if (isset($_REQUEST['camila_custom']))
        $reqs .= '&camila_custom=' . urlencode($_REQUEST['camila_custom']);
    
    if (isset($_REQUEST['camila_share_key']))
        $reqs .= '&camila_share_key=' . urlencode($_REQUEST['camila_share_key']);
    
	if (isset($_REQUEST['dashboard']))
		$reqs .= '&dashboard='.$_REQUEST['dashboard'];
	
    $pos = strpos($_CAMILA['page_url'], '?');
    if ($pos === false)
        $link = basename($_SERVER['PHP_SELF']) . '?';
    else
        $link = $_CAMILA['page_url'] . '&';
    
    if (isset($_REQUEST['camila_share_key']))
        $link .= 'camila_share_key=' . urlencode($_REQUEST['camila_share_key']) . '&';
    
	
    $myLink = new CHAW_link($field->report->formupdatelinktext, $link . $reqs . '&camila_returl=' . urlencode($_CAMILA['returl']));
	$myLink->set_css_class('btn btn-xs btn-default btn-info');
    $myLink->set_br(0);


    $image = new CHAW_image(CAMILA_IMG_DIR . 'wbmp/bullet_go3.wbmp', CAMILA_IMG_DIR . 'png/bullet_go3.png', '>');
    $image->set_br(0);
    $image->set_id($inline_id);
    
    //$myLink->add_image($image);
    
    $ar    = Array();
    //$ar[0] = $image;
    $ar[1] = $myLink;
    $row->add_column($ar);
    
    if ($field->report->inline_editing) {
        $_CAMILA['page']->camila_add_js("<script type='text/javascript'>var camila_inline=new Array();</script>\n", 'camila_inline_array');
        $_CAMILA['page']->camila_add_js("<script type='text/javascript'>var camila_inline_script='$link';</script>\n", 'camila_inline_script');
        $_CAMILA['page']->camila_add_js("<script type='text/javascript'>camila_inline['$inline_id']='$reqs';</script>\n", $inline_id);
    }
}


function camila_formdeletelink(&$field, &$row, $fields)
{
    global $_CAMILA;
    $arr = array();
    foreach ($fields as $key) {
        if (substr($key->field, 0, strlen('camilakey_del_')) == 'camilakey_del_') {
            if (strpos($key->field, '__') !== false)
                $kf .= 'camilakey_' . substr($key->field, strpos($key->field, '__') + 2);
            else
                $kf .= 'camilakey_' . substr($key->field, 14);
            $arr[$kf] = $key->value;
        }
    }
    
    if ($_SERVER['QUERY_STRING'] != '')
        $url = basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING'] . "&camila_delete=" . serialize($arr) . '&camila_token=' . camila_token(serialize($arr)) . '&camila_returl=' . urlencode($_CAMILA['returl']);
    else
        $url = basename($_SERVER['PHP_SELF']) . "?camila_delete=" . serialize($arr) . '&camila_returl=' . urlencode($_CAMILA['returl']) . '&camila_token=' . camila_token(serialize($arr));
    $myLink = new CHAW_link(camila_get_translation('camila.delete'), $url);
    $myLink->set_br(0);
	$myLink->set_css_class('btn btn-xs btn-default btn-danger');
    $row->add_column($myLink);
}


class report
{
    var $stmt;
    //report_field classes
    var $fields;
    // rows per page
    var $rows = CAMILA_REPORT_RPP;
    // db limit offset
    var $offset;
    // current page viewed
    var $page;
    //$result->FieldCount();
    var $numfields;
    //$result->RecordCount();
    var $totalrows;
    var $title;
    var $condition = '';
    var $condappurl;
    var $additional_links;
    var $additional_links_images;
	var $additional_links_css_classes;
    var $filternum = 0;
    var $keys;
    var $canupdate;
    var $candelete;
    var $tables;
    
    var $formupdatefunction = 'camila_formupdatelink';
    var $formupdatelinktext;
    
    var $table;
    var $mappingseparator = '#';
    var $drawfilterbox = true;
    var $drawnavigationbox = true;
    var $defaultfields;
    var $stmtnumoffields;
    var $stmtfields;
    var $filter;
    
    var $inline_editing = true;
    var $editcolumscookiename;
    
    var $totalconditions = 0;
    var $gbyconditionpresent = false;
    var $gbyconditions = Array();
    
    var $menuitems = Array();
    var $formulas = Array();
    var $queries = Array();
	
	var $bootstrapbuttonsize = CAMILA_UI_DEFAULT_BTN_SIZE;
    
    
    function report($stmt, $title, $orderby = '', $direction = 'asc', $mapping = '', $ordermapping = null, $keys = '', $defaultfields = '', $filter = '', $canupdate = true, $candelete = false)
    {
        
        global $_CAMILA;
        
        $this->defaultfields = $defaultfields;
        
        //if ($_CAMILA['page']->camila_exporting() && $_REQUEST['camila_visible_cols_only'] != 'y') {
        //    $this->defaultfields = '';
        //}

        $this->filter               = $filter;
        $this->canupdate            = $canupdate;
        $this->candelete            = $candelete;
        $this->formupdatelinktext   = camila_get_translation('camila.report.detailform');
        $this->editcolumscookiename = preg_replace('/[^a-z0-9]/', '', CAMILA_APPLICATION_NAME.$_SERVER['PHP_SELF']);
		
		//Disabilito inline se non posso aggiornare
		if (!$this->canupdate)
			$this->inline_editing=false;
        
        if (isset($_REQUEST['filter'])) {
            $this->filter = camila_worktable_filter_decode($_REQUEST['filter'], $_CAMILA['page']->camila_worktable_id);
            //print_r($this->filter);
        }
        
        if (!((isset($_REQUEST['camila_w1f'])) || (isset($_REQUEST['count']) && $_REQUEST['count'] > 0) || (isset($_REQUEST['camila_count']) && $_REQUEST['camila_count'] > 0))) {
            foreach ($this->filter as $k => $v) {
                $_REQUEST[$k] = $v;
            }
        }
        
        if ($_CAMILA['user_preferences']['c_rp'] > 0)
            $this->rows = $_CAMILA['user_preferences']['c_rp'];
        
        //if ($_REQUEST['camila_w1c'] == 'gby') {
        //    $stmt = 'select ' . 'cognome,count(*)' . substr($stmt, strpos($stmt, ' from '));
        //}
        
        $this->stmt = $stmt;
        
        $select_fields  = substr($stmt, 7, strpos($stmt, ' from ') - 7);
        $select_fields2 = '';
        $par_stack      = 0;
        
        for ($a = 0; $a < strlen($select_fields); $a++) {
            if (substr($select_fields, $a, 1) == '(')
                $par_stack++;
            
            if (substr($select_fields, $a, 1) == ')')
                $par_stack--;
            
            if ((substr($select_fields, $a, 1) == ',') && ($par_stack > 0))
                $select_fields2 .= '___CAMILA_COMMA___';
            else
                $select_fields2 .= substr($select_fields, $a, 1);
        }
        
        $this->stmtfields = explode(',', $select_fields2);
        
        $new_stmt = 'select ';

        $count = 1;
        while (isset($_REQUEST['camila_w' . $count . 'c']) || isset($_REQUEST['camila_w' . $count . 'c'])) {
				if ($_REQUEST['camila_w' . $count . 'v'] == '_today_')
				{
					$tod = date("Y-m-d", time());					
					$tod= $_CAMILA['db']->UserDate($tod, $_CAMILA['date_format']);
					$_REQUEST['camila_w' . $count . 'v'] = $tod;
				}
				if (substr($_REQUEST['camila_w' . $count . 'v'],0,9) == '__today__')
				{
					$tod = date("Y-m-d", time());
					$_REQUEST['camila_w' . $count . 'v'] = $tod . ' ' . substr($_REQUEST['camila_w' . $count . 'v'],10);
				}

            if ($_REQUEST['camila_w' . $count . 'c'] == 'gby') {
                
                $this->gbyconditions[]     = substr($_REQUEST['camila_w' . $count . 'f'], 3);
                $this->gbyconditionpresent = true;
            }
            
            $count++;
            
        }

        $this->totalconditions = $count - 1;
        
        
        $this->stmtnumoffields = count($this->stmtfields);
        $count                 = 0;
        

        if ($_REQUEST['camila_save'] == 'Ok') {
            setcookie($this->editcolumscookiename, serialize($_REQUEST['camila_f']), time() + CAMILA_SESSION_DURATION, "/", false);	
        }
        
        for ($a = 0; $a < $this->stmtnumoffields; $a++) {
            $b                    = $a + 1;
            $this->stmtfields[$a] = str_replace('___CAMILA_COMMA___', ',', $this->stmtfields[$a]);

            if (isset($_REQUEST['camila_xml2pdf']) || ($_CAMILA['page']->camila_exporting() && $_REQUEST['camila_visible_cols_only'] != 'y') || (((!isset($_REQUEST['count']) && !isset($_REQUEST['camila_count'])) && $this->defaultfields != '' && strpos(',' . $this->defaultfields . ',', ',' . $this->stmtfields[$a] . ',') !== false) || ((isset($_REQUEST['f' . $b]) && $_REQUEST['f' . $b] == 'y')) || (in_array('f' . $b, $_REQUEST['camila_f'])) || ((isset($_REQUEST['camila_f' . $b]) && $_REQUEST['camila_f' . $b] == 'y')))) {
                $arr = unserialize($_COOKIE[$this->editcolumscookiename]);
                //print_r($arr);
 
                if ((($_CAMILA['page']->camila_exporting() && ($_REQUEST['camila_visible_cols_only'] != 'y')) || $_COOKIE[$this->editcolumscookiename] == '' || $_REQUEST['camila_save'] == 'Ok') || (count($arr) > 0 && ($arr[$b] == 'f' . $b))) {
                    //                      if (!$this->gbyconditionpresent || ($this->gbyconditionpresent && substr($_REQUEST['camila_w'.$this->totalconditions.'f'], 3) == $this->stmtfields[$a])) {
                    if (!$this->gbyconditionpresent || ($this->gbyconditionpresent && in_array($this->stmtfields[$a], $this->gbyconditions))) {
                        
                        $new_stmt .= $this->stmtfields[$a] . ',';
                        $count++;
                    }
                }
            }
        }
        
        if ($this->gbyconditionpresent) {
            $new_stmt .= 'count(*),';
            $this->canupdate = false;
            $this->candelete = false;
        }
        
        if ($count == 0 && $this->defaultfields != '')
            $this->stmt = 'select ' . $this->defaultfields . substr($stmt, strpos($stmt, ' from '));
        elseif ($count > 0)
            $this->stmt = substr($new_stmt, 0, -1) . substr($stmt, strpos($stmt, ' from '));
        
        $this->title      = $title;
        $this->rowsloaded = 0;
        $this->totalrows;
        $this->offset       = 0;
        $this->page         = 1;
        $this->orderby      = $orderby;
        $this->direction    = $direction;
        $this->tablecomment = '';
        
        if ($mapping != '')
            $this->mapping = $this->mappingseparator . $mapping . $this->mappingseparator;
        
        if ($this->gbyconditionpresent) {
            if ($this->mapping != '')
                $this->mapping .= camila_get_translation('camila.mapping.groupby.total') . $this->mappingseparator;
            else
                $this->mapping .= $this->mappingseparator . camila_get_translation('camila.mapping.groupby.total') . $this->mappingseparator;
        }
        
        $this->order_mapping = $ordermapping;
        
        if (strpos($stmt, 'where') !== false)
            $this->tables = explode(',', substr($stmt, strpos($stmt, 'from') + 4, strpos($stmt, 'where') - 5 - strpos($stmt, 'from')));
        else
            $this->tables = explode(',', substr($stmt, strpos($stmt, 'from') + 4));
        
        foreach ($this->tables as $key => $table) {
            $this->tables[$key] = trim($table);
        }
        
        reset($this->tables);
        
        if ($keys != '') {
            $this->keys = explode(',', $keys);
            reset($this->keys);
            
            $kf    = '';
            $count = 0;
            
            foreach ($this->keys as $afield) {
                if (strpos($afield, '.') !== false)
                    $kf .= $afield . ' as camilakey_' . $field = substr($afield, 0, strpos($afield, '.')) . '__' . substr($afield, strpos($afield, '.') + 1);
                else
                    $kf .= $afield . ' as camilakey_' . $afield;
                if ($count == 0 && $this->candelete)
                //$kf .= ', ' . $afield . ' as camila_del_' . $afield;
                    if (strpos($afield, '.') !== false)
                        $kf .= ', ' . $afield . ' as camilakey_del_' . $field = substr($afield, 0, strpos($afield, '.')) . '__' . substr($afield, strpos($afield, '.') + 1);
                    else
                        $kf .= ', ' . $afield . ' as camilakey_del_' . $afield;
                
                $kf .= ', ';
                $count++;
            }
            
            reset($this->keys);
            
            $this->stmt = substr($this->stmt, 0, strpos($this->stmt, 'select') + 7) . ' ' . $kf . ' ' . substr($this->stmt, strpos($this->stmt, 'select') + 7);
        }
        
        $this->table = new CHAW_table();

        
        if (isset($_REQUEST['rpp']))
            $this->rows = $_REQUEST['rpp'];
        
        //$this->urlappend = '?count=' . $_REQUEST['count'] . $_REQUEST['camila_count'];
        
        if (strpos($_CAMILA['page_url'], '?') !== false)
            $this->urlappend = substr($_CAMILA['page_url'], strpos($_CAMILA['page_url'], '?')) . '&count=' . $this->stmtnumoffields;
        else
            $this->urlappend = '?count=' . $this->stmtnumoffields;
        
        for ($b = 1; $b <= $this->stmtnumoffields; $b++) {
            if (((!isset($_REQUEST['count']) && !isset($_REQUEST['camila_count'])) && $this->defaultfields != '' && strpos(',' . $this->defaultfields . ',', ',' . $this->stmtfields[$b - 1] . ',') !== false) || ((isset($_REQUEST['f' . $b]) && $_REQUEST['f' . $b] == 'y')) || (in_array('f' . $b, $_REQUEST['camila_f'])) || ((isset($_REQUEST['camila_f' . $b]) && $_REQUEST['camila_f' . $b] == 'y'))) {
                $this->urlappend .= '&f' . $b . '=' . 'y';
                //echo $afields[$b-1];
            }
        }
        
        if ((isset($_REQUEST['f0']) && $_REQUEST['f0'] != '') || (isset($_REQUEST['camila_f0']) && $_REQUEST['camila_f0'] != '')) {
            if (isset($_REQUEST['f0']))
                $oby = $_REQUEST['f0'];
            else
                $oby = $_REQUEST['camila_f0'];
            //$dict = NewDataDictionary($_CAMILA['db']);
            //$this->orderby = $dict->NameQuote($oby);
            $this->orderby = $oby;
            
            
            if ((array_key_exists($oby, $this->order_mapping)))
                $this->orderby = /*$_CAMILA['db']->Quote(*/ $this->order_mapping[$oby] /*)*/ ;
        }
        $count = 1;
        $where = '';
        
        
        while (isset($_REQUEST['camila_w' . $count . 'f']) || isset($_REQUEST['camila_w' . $count . 'f'])) {
            if ($_REQUEST['camila_w' . $count . 'f'] != '-' && $_REQUEST['camila_w' . $count . "v"] != "-") {
                $this->condappurl .= '&camila_w' . $count . 'f=' . $_REQUEST['camila_w' . $count . "f"];
                $this->condappurl .= '&camila_w' . $count . 'c=' . $_REQUEST['camila_w' . $count . "c"];
                $this->condappurl .= '&camila_w' . $count . 'v=' . $_REQUEST['camila_w' . $count . "v"];
                if (isset($_REQUEST['camila_w' . $count . 'w']))
                    $this->condappurl .= '&camila_w' . $count . "w=" . $_REQUEST['camila_w' . $count . "w"];
                
                if ($_REQUEST['camila_w' . $count . 'c'] != 'gby') {
                    if ($count > 1)
                        $where .= ' ' . $_REQUEST['camila_w' . $count . 'w'] . ' ';
                    
                    $type = '';
                    
                    $field = $_REQUEST['camila_w' . $count . 'f'];
                    if (substr($field, 0, 1) == '_' && substr($field, 2, 1) == '_') {
                        $field = (substr($_REQUEST['camila_w' . $count . 'f'], 3));
                        $type  = substr($_REQUEST['camila_w' . $count . 'f'], 1, 1);
                    }
                    
                    if (strpos($field, '__') !== false)
                        $field = substr($field, 0, strpos($field, '__')) . '.' . substr($field, strpos($field, '__') + 2);
                    
                    $value = $_REQUEST['camila_w' . $count . 'v'];
                    if ($type == 'D' && strlen($value) == 10) {
                        $mm    = substr($value, camila_get_translation('camila.dateformat.monthpos'), 2);
                        $dd    = substr($value, camila_get_translation('camila.dateformat.daypos'), 2);
                        $yyyy  = substr($value, camila_get_translation('camila.dateformat.yearpos'), 4);
                        $value = date('Y-m-d', mktime(0, 0, 0, $mm, $dd, $yyyy));
                    }
                    
                    if ($type == 'T' && strlen($value) == 10) {
                        $mm    = substr($value, camila_get_translation('camila.dateformat.monthpos'), 2);
                        $dd    = substr($value, camila_get_translation('camila.dateformat.daypos'), 2);
                        $yyyy  = substr($value, camila_get_translation('camila.dateformat.yearpos'), 4);
                        $value = date('Ymd', mktime(0, 0, 0, $mm, $dd, $yyyy));
                    }
                    
                    $cond = $_REQUEST['camila_w' . $count . 'c'];
                    
                    if ($cond == 'eq')
                        $where .= '(UPPER(' . $field . ')';
                    elseif (substr($cond, 0, 8) == 'cf_bool_')
                        $where .= '(' . substr($cond, 0, -2);
                    else
                        $where .= '(' . $field;
                    
                    $cond = $_REQUEST['camila_w' . $count . 'c'];
                    
                    if (substr($cond, 0, 8) == 'cf_bool_') {
                        $where .= '=';
                        $value = substr($cond, -1);
                    }
                    
					$likeOp = 'LIKE';
					if ($_CAMILA['db']->databaseType == 'postgres9') {
						$likeOp = 'ILIKE';
					}
	
                    if ($cond == 'let')
                        $where .= '<=';
                    if ($cond == 'get')
                        $where .= '>=';
                    if ($cond == 'lt')
                        $where .= '<';
                    if ($cond == 'gt')
                        $where .= '>';
                    if ($cond == 'eq')
                        $where .= '=';
                    if ($cond == 'neq')
                        $where .= '<>';
                    if ($cond == 'like')
                        $where .= ' '.$likeOp.' ';
                    if ($cond == 'notlike')
                        $where .= ' NOT '.$likeOp.' ';
                    if ($cond == 'empty')
                        $where .= ' IS NULL or ' . $field . ' = ' . $_CAMILA['db']->qstr('');
                    if ($cond == 'notempty')
                        $where .= ' IS NOT NULL and ' . $field . ' <> ' . $_CAMILA['db']->qstr('');
                    if ($cond != 'empty' && $cond != 'notempty') {
                        if ($cond == 'like' || $cond == 'notlike')
                            $where .= $_CAMILA['db']->qstr('%' . $value . '%');
                        elseif ($cond == 'eq')
                            $where .= 'UPPER(' . $_CAMILA['db']->qstr($value) . ')';
                        else
                            $where .= $_CAMILA['db']->qstr($value);
                    }
                    $where .= ')';
                }
            }
            $count++;
        }
        
        $this->filternum = $count - 1;
        
        if ($where != '')
            $this->condition = $where;
        
        $this->urlappend .= $this->condappurl . '&mt=' . $_REQUEST['mt'];
        
        if (isset($_REQUEST['rpp']))
            $this->urlappend .= '&rpp=' . $_REQUEST['rpp'];
        
        if (isset($_REQUEST['camila_addfilter']))
            $this->filternum += $_REQUEST['camila_addfilter'];
        
        if (isset($_REQUEST['custom']))
            $this->urlappend .= '&custom=' . urlencode($_REQUEST['custom']);
		
		if (isset($_REQUEST['dashboard']))
            $this->urlappend .= '&dashboard=' . urlencode($_REQUEST['dashboard']);
        
        if (isset($_REQUEST['camila_custom']))
            $this->urlappend .= '&camila_custom=' . urlencode($_REQUEST['camila_custom']);
        
        if (isset($_REQUEST['camila_share_key']))
            $this->urlappend .= '&camila_share_key=' . urlencode($_REQUEST['camila_share_key']);
        
        $this->urlappendnoorder = $this->urlappend;
        
        if (isset($_REQUEST['d']))
            $this->urlappend .= '&d=' . $_REQUEST['d'];
        
        if ((isset($_REQUEST['f0']) && $_REQUEST['f0'] != '') || (isset($_REQUEST['camila_f0']) && $_REQUEST['camila_f0'] != ''))
            $this->urlappend .= '&camila_f0=' . $this->orderby;
        
        $_CAMILA['returl'] = $this->urlappend;
        
        if (isset($_REQUEST['camila_pagnum']))
            $_CAMILA['returl'] .= '&camila_pagnum=' . intval($_REQUEST['camila_pagnum']);
        
        $jarr                = Array();
        $jarr['url']         = "index.php";
        $jarr['visible']     = "yes";
        $jarr['short_title'] = "OPZIONI";
        $jarr['parent']      = "";
        $this->menuitems[]   = $jarr;
        
        $jarr                = Array();
        $jarr['url']         = basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ($this->page) . '&camila_editcols=y';
        $jarr['visible']     = "yes";
        $jarr['short_title'] = camila_get_translation('camila.report.navbox.addremcols');
        $jarr['parent']      = "index.php";
        $this->menuitems[]   = $jarr;
		
		
		if ($_CAMILA['page']->camila_exporting()) {
			if ($_CAMILA['page']->camila_worktable)
                    $_CAMILA['page']->camila_worktable_filter = $this->getFilterDescription();
		}
		
		
        
        
    }
    
    function process()
    {
        if (!$this->drawnavigationbox || isset($_REQUEST['camila_xml2pdf'])) {
            $this->rows = -1;
            $this->page = 1;
        }
 
        if (isset($_REQUEST['camila_pagnum'])) {
            $this->page = intval($_REQUEST['camila_pagnum']);
            if ($this->page < 0) {
                $this->rows           = -1;
                $this->page           = 1;
                $this->inline_editing = false;
            }
            $this->offset = ($this->page - 1) * $this->rows;
        }
        
        if (is_array($this->stmt)) {
            $stmt = '(';
            foreach ($this->stmt as $v) {
                $stmt .= ($this->condition != '' ? $v . ' where ' . $this->condition : $v);
                $stmt .= ') UNION (';
            }
            $stmt = substr($stmt, 0, strlen($stmt) - 8);
        } else {
            if (stristr($this->stmt, 'group by') === false) {
                $cong = ' and ';
                if (stristr($this->stmt, 'where') === false)
                    $cong = ' where ';
                $stmt = ($this->condition != '' ? $this->stmt . $cong . $this->condition : $this->stmt);
            } else {
                $cong = ' and ';
                if (stristr($this->stmt, 'having') === false)
                    $cong = ' having ';
                $stmt = ($this->condition != '' ? $this->stmt . $cong . $this->condition : $this->stmt);
                
                //$stmt = ($this->condition!='' ? substr( $this->stmt, 0, strpos($this->stmt, "group by")+0).' '.$cong.$this->condition.' '.substr( $this->stmt, strpos( $this->stmt, 'group by')+0) :$this->stmt);
            }
        }
        
        
        if ($this->gbyconditionpresent) {
            //$stmt .= ' group by ' . substr($_REQUEST['camila_w'.$this->totalconditions.'f'], 3);
            $stmt .= ' group by ';
            $count = 0;
            foreach ($this->gbyconditions as $key => $value) {
                if ($count > 0)
                    $stmt .= ',' . $value;
                else
                    $stmt .= $value;
                $count++;
                
            }
            
            //$stmt .= ' group by ' . substr($_REQUEST['camila_w'.$this->totalconditions.'f'], 3);
            
        }
        
        
        if ($this->orderby != '' /*&& strpos($stmt,$this->orderby)!==false*/ ) {
            if (strpos($this->orderby, '__') !== false)
                $this->orderby = substr($this->orderby, 0, strpos($this->orderby, '__')) . '.' . substr($this->orderby, strpos($this->orderby, '__') + 2);
            
            $stmt = $stmt . ' order by ' . $this->orderby;
            
            if (!isset($_REQUEST['d']))
                $stmt .= ' ' . $this->direction;
            elseif ($_REQUEST['d'] == 0) {
                $stmt .= ' asc';
                $this->direction = 'asc';
            } else {
                $stmt .= ' desc';
                $this->direction = 'desc';
            }
        }
        
        global $_CAMILA;
        $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
        $result = $_CAMILA['db']->Execute($stmt);
        //echo $stmt;
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
        
        $this->numfields = $result->FieldCount();
        $this->totalrows = $result->RecordCount();
        
        $_CAMILA['report_record_count'] = $this->totalrows;
        
        if ($this->page < ceil($this->totalrows / $this->rows)) {
            $_CAMILA['report_last_page'] = false;
        } else {
            $_CAMILA['report_last_page'] = true;
        }
        
        
        if ($this->rows > 0)
            $result = $_CAMILA['db']->SelectLimit($stmt, $this->rows, $this->offset);
        
        //$this->totalrows = $result->RecordCount();
        $this->res = $result;
        
		//echo $_CAMILA['db']->databaseType;
      
        if (($_CAMILA['db']->databaseType == 'sqlite' || $_CAMILA['db']->databaseType == 'sqlite3') && count($this->tables) == 1) {
            
            $this->adoMetaColumns = $_CAMILA['db']->MetaColumns($this->tables[0]);
        }
        
		//print_r($this->adoMetaColumns);
        
        for ($i = 0; $i < $this->numfields; $i++) {
            $curr_field = $this->res->FetchField($i);
            $curr       = $curr_field->name;
            $fcurr      = $this->map($curr);

            $type = $this->res->MetaType($curr_field->type);
					

            if (($_CAMILA['db']->databaseType == 'sqlite' || $_CAMILA['db']->databaseType == 'sqlite3') && count($this->tables) == 1 && $this->adoMetaColumns[strtoupper($curr_field->name)]->type != '')
                $type = $this->res->MetaType($this->adoMetaColumns[strtoupper($curr_field->name)]->type);
			
			//???
			
			if ($_CAMILA['db']->databaseType == 'sqlite' && $type='D') {
				$type='C';
			}
			
			if ($_CAMILA['db']->databaseType == 'sqlite3' && $type='D') {
				$type='C';
			}

			//echo $type;

            reset($this->tables);
            foreach ($this->tables as $key => $value) {
                if ($this->mapping != '' && strpos($this->mapping . '=', $this->mappingseparator . $value . '.' . $curr . '=') !== false) {
                    $fcurr = $this->map($value . '.' . $curr);
                    $curr  = $value . '__' . $curr;
                }
            }
			
			//$type = 'datetime';
			//echo $type;

            if (strpos($curr, 'cf_bool_') !== false)
                $this->fields[$curr] = new report_icon($curr, $fcurr);
            else if (strpos($curr, 'cf_formula_') !== false)
                $this->fields[$curr] = new report_formula($curr, $fcurr);
            else if (strpos($curr, 'cf_query_') !== false)
                $this->fields[$curr] = new report_query($curr, $fcurr);
            elseif ($type == 'I')
                $this->fields[$curr] = new report_int($curr, $fcurr);
            elseif ($type == 'N')
                $this->fields[$curr] = new report_real($curr, $fcurr);
            elseif ($type == 'D')
                $this->fields[$curr] = new report_date($curr, $fcurr);
            elseif ($type == 'datetime')
                $this->fields[$curr] = new report_datetime($curr, $fcurr);
            elseif ($type == 'T')
                $this->fields[$curr] = new report_timestamp($curr, $fcurr);
            else
                $this->fields[$curr] = new report_string($curr, $fcurr);
			

            $size = $curr_field->max_length;
            if ($size > 30)
                $size = 30;

            $this->fields[$curr]->metatype = $type;
            $this->fields[$curr]->report   = $this;
            
            if ($this->gbyconditionpresent)
                $this->fields[$curr]->inline = false;
        }
    }
    
    
    function draw_header()
    {
        global $_CAMILA;
        
        if ($this->title != '') {
            $text = new CHAW_text($this->title, HAW_TEXTFORMAT_BIG);
            $text->set_br(2);
            $_CAMILA['page']->add_text($text);
        }
        
        $myRow = new CHAW_row();
        
        reset($this->fields);
        $noprint = 0;
        while ($fld = each($this->fields)) {
            if (!$fld[1]->print)
                $noprint++;
        }
        
        $this->numfields -= $noprint;
        
        $orderby = $this->orderby;
        //if (strpos($this->orderby, '.') !== false)
        //    $orderby = substr($this->orderby, 0, strpos($this->orderby, '.')) . '__' . substr($this->orderby, strpos($this->orderby, '.') + 1);
        
        $count = 0;
        reset($this->fields);
        while ($fld = each($this->fields)) {
            if ($fld[1]->print && !($_CAMILA['page']->camila_exporting() && $fld[1]->dummy) && !($_CAMILA['page']->camila_exporting() && !(strpos($fld[1]->field, 'camilakey_') === false))) {
                if (( /*isset($_REQUEST['d']) && $_REQUEST['d'] == 0*/ $this->direction == 'asc') && ($fld[1]->field == $orderby))
                    $fld[1]->draw_header($myRow, basename($_SERVER['PHP_SELF']) . $this->urlappendnoorder . '&d=1&f0=' . urlencode($fld[1]->field));
                else
                    $fld[1]->draw_header($myRow, basename($_SERVER['PHP_SELF']) . $this->urlappendnoorder . '&d=0&f0=' . urlencode($fld[1]->field));
                $this->colcount++;
            }
            $count++;
        }
        
        $this->table->add_row($myRow);
    }
    
    function load_row(&$row, $line)
    {
        if (!$this->res->EOF) {
            $row = $this->res->fields;
            
            reset($this->fields);
            while ($fld = each($this->fields)) {
                $this->fields[$fld[1]->field]->row = $line;
                if ($this->fields[$fld[1]->field]->type == 'dummy')
                    continue;
                if (strpos($fld[1]->field, 'camilakey_') === false && strpos($fld[1]->field, '__') !== false) {
                    $kf = substr($fld[1]->field, strpos($fld[1]->field, '__') + 2);
                } else
                    $kf = $fld[1]->field;
                
                $this->fields[$fld[1]->field]->value = $row[$kf];
            }
            ++$this->rowsloaded;
            $this->res->MoveNext();
            return true;
        } else
            return false;
    }
    
    function draw_row()
    {
        global $_CAMILA;
        $myRow = new CHAW_row();
        
        $currval = '';
        
        reset($this->fields);
        while ($fld = each($this->fields)) {
            if ($fld[1]->print && !($_CAMILA['page']->camila_exporting() && $fld[1]->dummy) && !($_CAMILA['page']->camila_exporting() && (!(strpos($fld[1]->field, 'camilakey_') === false)))) {
                $fld[1]->draw($myRow, $this->fields);
                
                if ($fld[1]->field == $this->orderby) {
                    $currval = $fld[1]->value;
                }
            }
        }
        
        
        if (isset($_REQUEST['camila_countorderby']) && $currval != $this->lastorderbyvalue && $this->rowcount > 0) {
            $myRow2 = new CHAW_row();
            $text   = camila_get_translation('camila.report.countorderby.total') . " '" . $this->lastorderbyvalue . "': " . $this->currvalcount;
            $text1  = new CHAW_text($text);
            $myRow2->add_column($text1);
            
            $text2 = new CHAW_text("");
            
            for ($i = 0; $i < $_CAMILA['page']->header_cols_count - 1; $i++) {
                $myRow2->add_column($text2);
            }
            
            $this->table->add_row($myRow2);
            $this->currvalcount = 0;
        }
        
        $this->lastorderbyvalue = $currval;
        $this->rowcount++;
        $this->currvalcount++;
        
        $this->table->add_row($myRow);
    }
    
    function draw()
    {
        
        if (isset($_REQUEST['camila_editcols']) && $_REQUEST['camila_editcols'] = 'y') {
            $this->_editcolumns();
            return;
        }
        
        $this->draw_filter();
        
        if ($this->totalrows > 0) {
            $i   = 0;
            $arr = array();
            reset($this->keys);
            foreach ($this->keys as $afield) {
                if (strpos($afield, '.') !== false)
                    $field = substr($afield, 0, strpos($afield, '.')) . '__' . substr($afield, strpos($afield, '.') + 1);
                else
                    $field = $afield;
                
                if ($i < (count($this->keys) - 1)) {
                    $this->fields['camilakey_' . $field]->print = false;
                } else {
                    //                      if ($this->canupdate)
                    $this->fields['camilakey_' . $field]->onprint = $this->formupdatefunction;
                    if ($this->candelete)
                        $this->fields['camilakey_del_' . $field]->onprint = "camila_formdeletelink";
                }
                
                $i++;
            }
            
            $i   = 0;
            $row = array();
            $this->draw_header();
            while ($this->load_row($row, $i++)) {
                if (isset($this->onloadrow)) {
                    $func = $this->onloadrow;
                    $ok   = $func($this);
                } else
                    $ok = true;
                if ($ok) {
                    // check if group changed
                    if (isset($this->group_field)) {
                        if ($this->group_value != $this->fields[$this->group_field]->value) {
                            $this->group_value = $this->fields[$this->group_field]->value;
                            $this->draw_group();
                        }
                    }
                    $this->draw_row();
                }
            }
            
            if (isset($_REQUEST['camila_countorderby']) && $this->rowcount > 0) {
                global $_CAMILA;
                $myRow2 = new CHAW_row();
                $text   = camila_get_translation('camila.report.countorderby.total') . " '" . $this->lastorderbyvalue . "': " . $this->currvalcount;
                $text1  = new CHAW_text($text);
                $myRow2->add_column($text1);
                
                $text2 = new CHAW_text("");
                
                for ($i = 0; $i < $_CAMILA['page']->header_cols_count - 1; $i++) {
                    $myRow2->add_column($text2);
                }
                
                $this->table->add_row($myRow2);
                $this->currvalcount = 0;
            }
            
            
            $this->draw_footer();
            
            $text = new CHAW_text('');
            
            global $_CAMILA;
            $_CAMILA['page']->add_text($text);
            
            //                  $code = "<div id='camilatablemenu' class='camilaWorkTableRSMenu'><p><b>Menu 1</b></p><p><a href=''>item 1</a></p><p><a href=''>item 2</a></p><p><a href=''>item 3</a></p></div>";
            //    $js = new CHAW_js($code);
            //    $_CAMILA['page']->add_userdefined($js);
            
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="hidden-xs hidden-sm"><span class="cf-table-arrow-left"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></span><span class="cf-table-arrow-right"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></span></div><div class="table-responsive">'));	
            $_CAMILA['page']->add_table($this->table);
				$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));	

            //$json  = new Services_JSON();
            //$jarr2 = $json->encode($this->menuitems);
			
			if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
			{
				$jarr2 = json_encode($this->menuitems);
				$code  = "<script>camila_addDOMLoadEvent ( function() {";
				$code .= "var a = '" . str_replace(array(
					"'",
					"\\u0000"
				), array(
					"\'",
					""
				), $jarr2) . "'; camilatablemenu_init(a);} )</script>";
				$_CAMILA['page']->camila_add_js($code);
            }
            
            $text = new CHAW_text('');
            $_CAMILA['page']->add_text($text);
            $this->draw_footer();
        } else {
            global $_CAMILA;
            camila_information_text(camila_get_translation('camila.nodatafound'));
            
            $_CAMILA['datagrid_nodata'] = 1;
            
            if (isset($this->additional_links))
                foreach ($this->additional_links as $key => $value) {
                    $text = new CHAW_text(' | ');
                    $text->set_br(0);
                    $_CAMILA['page']->add_text($text);
                    $link = new CHAW_link($key, $value);
                    if (is_object($this->additional_links_images[$key]))
                        $link->add_image($this->additional_links_images[$key]);
					
					$link->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
					if ($this->additional_links_css_classes[$key] != '')
                        $link->set_css_class($this->additional_links_css_classes[$key]);

                    
					$link->set_br(0);
                    $_CAMILA['page']->add_link($link);
                }
            
            $text = new CHAW_text('');
            $_CAMILA['page']->add_text($text);
        }
    }
    
    function draw_footer()
    {
        if (!$this->drawnavigationbox)
            return;
        
        global $_CAMILA;
        
        if ($this->rows > 0 && !$_CAMILA['page']->camila_exporting()) {
            if ($this->page > 2) {
                $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.first'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=1');
                $myLink->set_br(0);
				$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
                $_CAMILA['page']->add_link($myLink);
                
                $text = new CHAW_text(' | ');
                $text->set_br(0);
                $_CAMILA['page']->add_text($text);
            }
            
            if ($this->page > 1) {
                $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.prev'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ($this->page - 1));
				$myLink->set_br(0);
				$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
                $_CAMILA['page']->add_link($myLink);
            }
            
            $text = new CHAW_text(' ' . camila_get_translation('camila.report.navbox.page') . ' ' . $this->page . '/' . ceil($this->totalrows / $this->rows) . ' ');
            $text->set_br(0);
            $_CAMILA['page']->add_text($text);
            
            if ( /*$this->rowsloaded >= $this->rows*/ $this->page < ceil($this->totalrows / $this->rows)) {
                $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.next'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ($this->page + 1));
                $myLink->set_br(0);
				$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
                $_CAMILA['page']->add_link($myLink);
            }
            
            if ($this->page < (ceil($this->totalrows / $this->rows) - 1)) {
                $text = new CHAW_text(' | ');
                $text->set_br(0);
				$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
                $_CAMILA['page']->add_text($text);
                
                $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.last'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ceil($this->totalrows / $this->rows));
                $myLink->set_br(0);
				$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
                $_CAMILA['page']->add_link($myLink);
            }
            
            $text = new CHAW_text(' | ');
            $text->set_br(0);
            $_CAMILA['page']->add_text($text);
            
            $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.allpages') . ' (' . $this->totalrows . ' ' . camila_get_translation('camila.report.navbox.rows') . ')', basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=-1');
            $myLink->set_br(0);
			$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
            if ($this->page > 0)
                $_CAMILA['page']->add_link($myLink);
            
            $text = new CHAW_text(' | ');
            $text->set_br(0);
            $_CAMILA['page']->add_text($text);
			
			/**/
		
		$myDivOpen = new HAW_raw(HAW_HTML, '<div class="btn-group"><button type="button" data-toggle="dropdown" class="btn btn-default '.$this->bootstrapbuttonsize.' dropdown-toggle">'.camila_get_translation('camila.worktable.options').' <span class="caret"></span></button><ul class="dropdown-menu"><li>');
		$_CAMILA['page']->add_raw($myDivOpen);
			
            $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.countorderby'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=-1&camila_countorderby');
            $myLink->set_br(0);
			//$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
            if ($this->page > 0 && !$this->gbyconditionpresent)
                $_CAMILA['page']->add_link($myLink);
        
		
        }
		
		if ($this->rows < 0 && !$_CAMILA['page']->camila_exporting()) {

			$myDivOpen = new HAW_raw(HAW_HTML, '<div class="btn-group"><button type="button" data-toggle="dropdown" class="btn btn-default '.$this->bootstrapbuttonsize.' dropdown-toggle">'.camila_get_translation('camila.worktable.options').' <span class="caret"></span></button><ul class="dropdown-menu"><li>');
			$_CAMILA['page']->add_raw($myDivOpen);
		}

        if (!$_CAMILA['page']->camila_exporting()) {
            if ($this->defaultfields != '') {
                /*$text = new CHAW_text(' | ');
                $text->set_br(0);
                if ($this->rows > 0 && !$this->gbyconditionpresent)
                    $_CAMILA['page']->add_text($text);*/
                
                $myLink = new CHAW_link(camila_get_translation('camila.report.navbox.addremcols'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&f0=' . $_REQUEST['f0'] . '&camila_pagnum=' . ($this->page) . '&camila_editcols=y');
                $myLink->set_br(0);
				//$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');
                if ($this->page > 0 && !$this->gbyconditionpresent)
                    $_CAMILA['page']->add_link($myLink);
                
            }

			$myDivOpen = new HAW_raw(HAW_HTML, '</li></ul></div>');
			$_CAMILA['page']->add_raw($myDivOpen);
			
			
			$text = new CHAW_text(' | ');
            $text->set_br(0);
            $_CAMILA['page']->add_text($text);
            
            $myLink = new CHAW_link(strtoupper(camila_get_translation('camila.export.xml2pdf')), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&camila_xml2pdf');
            $myLink->set_br(0);
			$myLink->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-info');
            if ($this->page > 0)
                $_CAMILA['page']->add_link($myLink);


            if (isset($this->additional_links))
                foreach ($this->additional_links as $key => $value) {
                    $text = new CHAW_text(' | ');
                    $text->set_br(0);
                    $_CAMILA['page']->add_text($text);
                    $link = new CHAW_link($key, $value);
                    if (is_object($this->additional_links_images[$key]))
                        $link->add_image($this->additional_links_images[$key]);
					
					$link->set_css_class('btn '.$this->bootstrapbuttonsize.' btn-default');

					if ($this->additional_links_css_classes[$key] != '')
                        $link->set_css_class($this->additional_links_css_classes[$key]);
					
					
                    $link->set_br(0);
					
                    $_CAMILA['page']->add_link($link);
                }
            
            $text = new CHAW_text('');
            $_CAMILA['page']->add_text($text);
        }
    }
    
    function map($field)
    {
        $field = str_replace('.', '.', $field);
        if ($this->mapping != '' && strpos($this->mapping . '=', $this->mappingseparator . $field . '=') !== false) {
            $str = trim(substr($this->mapping, strpos($this->mapping, $this->mappingseparator . $field . '=') + strlen($this->mappingseparator . $field . '=')));
            return substr($str, 0, strpos($str, $this->mappingseparator));
        } else
            return $field;
    }
    
    function draw_filter()
    {
        if (!$this->drawfilterbox)
            return;

        global $_CAMILA;

        if ( /*$this->rows>0 && */ !$_CAMILA['page']->camila_exporting()) {
            require_once(CAMILA_DIR . 'datagrid/form.class.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/static_listbox.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
            
            $count          = 0;
            $options        = '';
            $options_array  = array();
            $fields_array   = array();
            $addfilterconds = '';
			
			if ($this->filternum == 0)
                $this->filternum = 1;
			
            reset($this->fields);
			//print_r($_REQUEST);
            while ($fld = each($this->fields)) {
                if ($fld[1]->print && $fld[1]->onprint == '' && substr($fld[1]->field, 0, 10) != 'camilakey_' && substr($fld[1]->field, 0, 8) != 'cf_bool_' && substr($fld[1]->field, 0, 11) != 'cf_formula_' && substr($fld[1]->field, 0, 11) != 'cf_query_' && !($fld[1]->field == 'count(*)' && ($this->gbyconditionpresent))) {
					//print_r($fld);
					$fieldname = '_' . $fld[1]->metatype . '_' . $fld[1]->field;
					//echo $this->filternum;
					if ($_CAMILA['db']->databaseType == 'sqlite') {
						for($x=1; $x<=$this->filternum; $x++)
						{
							if ($_REQUEST['camila_w'.$x.'f'] == '_I_' . $fld[1]->field)
							{
								$_REQUEST['camila_w'.$x.'f'] = '_C_' . $fld[1]->field;
							}
							if ($_REQUEST['camila_w'.$x.'f'] == '_N_' . $fld[1]->field)
							{
								$_REQUEST['camila_w'.$x.'f'] = '_C_' . $fld[1]->field;
							}
							if ($_REQUEST['camila_w'.$x.'f'] == '_D_' . $fld[1]->field)
							{
								$_REQUEST['camila_w'.$x.'f'] = '_C_' . $fld[1]->field;
							}
						}
					}
					
					//echo $fieldname;
					$options .=  $fieldname . ';' . $fld[1]->title . ',';
                    $options_array[$count][0]                                     = '_' . $fld[1]->metatype . '_' . $fld[1]->field;
                    $options_array[$count][1]                                     = $fld[1]->title; ////
                    $fields_array['_' . $fld[1]->metatype . '_' . $fld[1]->field] = $fld[1]->title;
                    $count++;
                }
                
                if (substr($fld[1]->field, 0, 8) == 'cf_bool_') {
                    $addfilterconds .= ',' . $fld[1]->field . '_n' . ';' . '(' . camila_get_translation('camila.report.condstring.hide') . ' ' . $this->map($fld[1]->field) . ')';
                    $addfilterconds .= ',' . $fld[1]->field . '_y' . ';' . '(' . camila_get_translation('camila.report.condstring.show') . ' ' . $this->map($fld[1]->field) . ')';
                }
            }
			//echo $options;
			//print_r($_REQUEST);
            
            $form               = new phpform('camila', null, HAW_METHOD_GET);
            $form->submitbutton = camila_get_translation('camila.filterbutton');
            $form->drawrules    = false;
            $process            = false;
            //if ($this->filternum == 0)
            //    $this->filternum = 1;
            
            $hidden = explode('&', substr($this->urlappend, 1));
            foreach ($hidden as $k => $v) {
                $split = explode('=', $v);
                $l     = substr($split[0], -1);
                if (!(strlen($split[0]) == 10 && substr($split[0], 0, 8) == 'camila_w' && ($l == 'f' || $l == 'v' || $l == 'c' || $l == 'w')))
                    new form_hidden($form, $split[0], $split[1]);
                else {
                    if (substr($split[0], 0, 8) == 'camila_w' && $l == 'f') {
                        $field = $split[1];
                        if (substr($split[1], 0, 1) == '_' && substr($split[1], 2, 1) == '_')
                            $field = (substr($split[1], 3));

                        if ((!in_array($field, $this->fields)) && strpos($options, $field . ';') == false) {
                            $options .= $split[1] . ';' . $this->map($field) . ',';
                            $options_array[$count][0] = $split[1];
                            $options_array[$count][1] = $this->map($field);
                            $fields_array[$split[1]]  = $options_array[$count][1];
                            $count++;
                        }
                    }
                    $process = true;
                }
            }
            
            $filterstring = '';
            $condstring   = camila_get_translation('camila.report.condstring') . $addfilterconds;
            
            for ($i = 1; $i <= $this->filternum; $i++) {
                $val = null;
                if ($i == $this->filternum)
                    $val = 'ignore';
                if ($i > 1)
                    new form_static_listbox($form, 'w' . $i . 'w', /*camila_get_translation('camila.report.filter') . ' ' . $i*/'', 'and;' . camila_get_translation('camila.report.and') . ',or;' . camila_get_translation('camila.report.or'), false, $val);
                new form_static_listbox($form, 'w' . $i . 'f', /*camila_get_translation('camila.report.filter') . */' ' /*. $i*/, $options_array, false, $val);
                new form_static_listbox($form, 'w' . $i . 'c', /*camila_get_translation('camila.report.filter') . */' ' /*. $i*/, $condstring, false, $val);
                $searchtextbox = 'w' . $i . 'v';
                new form_textbox($form, $searchtextbox, /*camila_get_translation('camila.report.filter') .*/ ' ' /*. $i*/, false, 50, 50, $val);

				$form->fields['w' . $i . 'f']->set_br(0);
				$form->fields['w' . $i . 'c']->set_br(0);
				
                $form->fields[$searchtextbox]->set_br(0);

                if ($_CAMILA['page']->camila_worktable && count($this->tables) == 1) {
                    $form->fields[$searchtextbox]->autosuggest_table = $this->tables[0];
                                        
                    $script = 'function (input) { field = document.getElementById("camila_w1f").value; field = field.substring(3); return "index.php?input="+input+"&camila_autosuggest_filterbox&table=' . $this->tables[0] . '&field="+field+"&id=id&infofields="+field+"&pickfields="+field+"&maxresults=5&"; }';
                    
                    $form->fields[$searchtextbox]->autosuggest_advanced_script = $script;

                }

                if ($i > 1)
                    $filterstring .= ' ' . camila_get_translation('camila.report.' . $_REQUEST['camila_w' . $i . 'w']) . ' ';
                //echo $_REQUEST['camila_w' . $i . 'f'];
				//print_r($fields_array);
				$fieldname = $_REQUEST['camila_w' . $i . 'f'];
				$fieldtitle = $fields_array[$fieldname];
				//echo '_C_'.substr($fieldname,3);
				if ($fieldtitle=='')
					$fieldtitle = $fields_array['_C_'.substr($fieldname,3)];
                if ($_REQUEST['camila_w' . $i . 'f'] != '') {
                    if (substr($_REQUEST['camila_w' . $i . 'c'], 0, 8) != 'cf_bool_')
                        $filterstring .= '"' . $fieldtitle . '" ' . $form->fields['w' . $i . 'c']->options2[$_REQUEST['camila_w' . $i . 'c']];
                    else
                        $filterstring .= $form->fields['w' . $i . 'c']->options2[$_REQUEST['camila_w' . $i . 'c']];
                }

                if ($_REQUEST['camila_w' . $i . 'v'] != '' && substr($_REQUEST['camila_w' . $i . 'c'], 0, 8) != 'cf_bool_')
				{
					$v = $_REQUEST['camila_w' . $i . 'v'];
					
					/*if ($v == '_today_')
					{
						$v = '03/05/2015';
						$_REQUEST['camila_w' . $i . 'v'] = '03/05/2015';
					}*/
					
                    $filterstring .= ' "' . $v . '"';	
				}
            }
            
            
            if (isset($_REQUEST['camila_share_key']))
                new form_hidden($form, 'share_key', $_REQUEST['camila_share_key']);
			
            
        
            if ($process)
                $form->process(true);
            
			$myDivOpen = new HAW_raw(HAW_HTML, '<div class="filterbox">');
			$_CAMILA['page']->add_raw($myDivOpen);
            //$_CAMILA['page']->camila_collapsible_start('reportfilter', true, camila_get_translation('camila.report.filters'));
            
			
            $form->draw();
            
            /*if (!$this->gbyconditionpresent) */ {
                $myLink = new CHAW_link(camila_get_translation('camila.report.addfilter'), basename($_SERVER['PHP_SELF']) . $this->urlappend . '&camila_addfilter=1');

				$myLink->set_css_class('btn btn-xs btn-default');
                $_CAMILA['page']->add_link($myLink);
            }
            
            if ($filterstring != '') {
                $text = new CHAW_text("\n" . camila_get_translation('camila.report.tablefiltereddata') . "\n" . $filterstring, HAW_TEXTFORMAT_BOLD);
                
                if ($_CAMILA['page']->camila_worktable)
                    $_CAMILA['page']->camila_worktable_filter = $filterstring;
                
                $_CAMILA['page']->add_text($text);
            }
            
            //$_CAMILA['page']->camila_collapsible_end();
            $myDivClose = new HAW_raw(HAW_HTML, '</div>');
			$_CAMILA['page']->add_raw($myDivClose);

            $text = new CHAW_text('');
            $_CAMILA['page']->add_text($text);
        }
    }
    
	function getFilterDescription() {
		
		require_once(CAMILA_DIR . 'datagrid/form.class.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/static_listbox.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
            
            $count          = 0;
            $options        = '';
            $options_array  = array();
            $fields_array   = array();
            $addfilterconds = '';
			
			if ($this->filternum == 0)
                $this->filternum = 1;
			
            reset($this->fields);
			//print_r($_REQUEST);
            while ($fld = each($this->fields)) {
                if ($fld[1]->print && $fld[1]->onprint == '' && substr($fld[1]->field, 0, 10) != 'camilakey_' && substr($fld[1]->field, 0, 8) != 'cf_bool_' && substr($fld[1]->field, 0, 11) != 'cf_formula_' && substr($fld[1]->field, 0, 11) != 'cf_query_' && !($fld[1]->field == 'count(*)' && ($this->gbyconditionpresent))) {
					//print_r($fld);
					$fieldname = '_' . $fld[1]->metatype . '_' . $fld[1]->field;
					//echo $this->filternum;
					if ($_CAMILA['db']->databaseType == 'sqlite') {
						for($x=1; $x<=$this->filternum; $x++)
						{
							if ($_REQUEST['camila_w'.$x.'f'] == '_I_' . $fld[1]->field)
							{
								$_REQUEST['camila_w'.$x.'f'] = '_C_' . $fld[1]->field;
							}
							if ($_REQUEST['camila_w'.$x.'f'] == '_N_' . $fld[1]->field)
							{
								$_REQUEST['camila_w'.$x.'f'] = '_C_' . $fld[1]->field;
							}
							if ($_REQUEST['camila_w'.$x.'f'] == '_D_' . $fld[1]->field)
							{
								$_REQUEST['camila_w'.$x.'f'] = '_C_' . $fld[1]->field;
							}
						}
					}
					
					//echo $fieldname;
					$options .=  $fieldname . ';' . $fld[1]->title . ',';
                    $options_array[$count][0]                                     = '_' . $fld[1]->metatype . '_' . $fld[1]->field;
                    $options_array[$count][1]                                     = $fld[1]->title; ////
                    $fields_array['_' . $fld[1]->metatype . '_' . $fld[1]->field] = $fld[1]->title;
                    $count++;
                }
                
                if (substr($fld[1]->field, 0, 8) == 'cf_bool_') {
                    $addfilterconds .= ',' . $fld[1]->field . '_n' . ';' . '(' . camila_get_translation('camila.report.condstring.hide') . ' ' . $this->map($fld[1]->field) . ')';
                    $addfilterconds .= ',' . $fld[1]->field . '_y' . ';' . '(' . camila_get_translation('camila.report.condstring.show') . ' ' . $this->map($fld[1]->field) . ')';
                }
            }
			//echo $options;
			//print_r($_REQUEST);
            
            $form               = new phpform('camila', null, HAW_METHOD_GET);
            $form->submitbutton = camila_get_translation('camila.filterbutton');
            $form->drawrules    = false;
            $process            = false;
            //if ($this->filternum == 0)
            //    $this->filternum = 1;
            
            $hidden = explode('&', substr($this->urlappend, 1));
            foreach ($hidden as $k => $v) {
                $split = explode('=', $v);
                $l     = substr($split[0], -1);
                if (!(strlen($split[0]) == 10 && substr($split[0], 0, 8) == 'camila_w' && ($l == 'f' || $l == 'v' || $l == 'c' || $l == 'w')))
                    new form_hidden($form, $split[0], $split[1]);
                else {
                    if (substr($split[0], 0, 8) == 'camila_w' && $l == 'f') {
                        $field = $split[1];
                        if (substr($split[1], 0, 1) == '_' && substr($split[1], 2, 1) == '_')
                            $field = (substr($split[1], 3));

                        if ((!in_array($field, $this->fields)) && strpos($options, $field . ';') == false) {
                            $options .= $split[1] . ';' . $this->map($field) . ',';
                            $options_array[$count][0] = $split[1];
                            $options_array[$count][1] = $this->map($field);
                            $fields_array[$split[1]]  = $options_array[$count][1];
                            $count++;
                        }
                    }
                    $process = true;
                }
            }
		
		
		
		
		$filterstring = '';
		$condstring   = camila_get_translation('camila.report.condstring') . $addfilterconds;

		for ($i = 1; $i <= $this->filternum; $i++) {
			$val = null;
			if ($i == $this->filternum)
				$val = 'ignore';
			//if ($i > 1)
			//	new form_static_listbox($form, 'w' . $i . 'w', /*camila_get_translation('camila.report.filter') . ' ' . $i*/'', 'and;' . camila_get_translation('camila.report.and') . ',or;' . camila_get_translation('camila.report.or'), false, $val);
			//new form_static_listbox($form, 'w' . $i . 'f', /*camila_get_translation('camila.report.filter') . */' ' /*. $i*/, $options_array, false, $val);
			//new form_static_listbox($form, 'w' . $i . 'c', /*camila_get_translation('camila.report.filter') . */' ' /*. $i*/, $condstring, false, $val);
			$searchtextbox = 'w' . $i . 'v';
			//new form_textbox($form, $searchtextbox, /*camila_get_translation('camila.report.filter') .*/ ' ' /*. $i*/, false, 50, 50, $val);

			//$form->fields['w' . $i . 'f']->set_br(0);
			//$form->fields['w' . $i . 'c']->set_br(0);
			
			//$form->fields[$searchtextbox]->set_br(0);

			//if ($_CAMILA['page']->camila_worktable && count($this->tables) == 1) {
			//	$form->fields[$searchtextbox]->autosuggest_table = $this->tables[0];
									
			//	$script = 'function (input) { field = document.getElementById("camila_w1f").value; field = field.substring(3); return "index.php?input="+input+"&camila_autosuggest_filterbox&table=' . $this->tables[0] . '&field="+field+"&id=id&infofields="+field+"&pickfields="+field+"&maxresults=5&"; }';
				
			//	$form->fields[$searchtextbox]->autosuggest_advanced_script = $script;

			//}

			if ($i > 1)
				$filterstring .= ' ' . camila_get_translation('camila.report.' . $_REQUEST['camila_w' . $i . 'w']) . ' ';
			//echo $_REQUEST['camila_w' . $i . 'f'];
			//print_r($fields_array);
			$fieldname = $_REQUEST['camila_w' . $i . 'f'];
			$fieldtitle = $fields_array[$fieldname];
			//echo '_C_'.substr($fieldname,3);
			if ($fieldtitle=='')
				$fieldtitle = $fields_array['_C_'.substr($fieldname,3)];
			if ($_REQUEST['camila_w' . $i . 'f'] != '') {
				if (substr($_REQUEST['camila_w' . $i . 'c'], 0, 8) != 'cf_bool_')
					$filterstring .= '"' . $fieldtitle . '" ' . camila_get_translation('camila.report.condstring.'.$_REQUEST['camila_w' . $i . 'c']);
				else
					$filterstring .= camila_get_translation('camila.report.condstring.'.$_REQUEST['camila_w' . $i . 'c']);
			}

			if ($_REQUEST['camila_w' . $i . 'v'] != '' && substr($_REQUEST['camila_w' . $i . 'c'], 0, 8) != 'cf_bool_')
			{
				$v = $_REQUEST['camila_w' . $i . 'v'];

				$filterstring .= ' "' . $v . '"';	
			}
		}
		
		return $filterstring;
	}
	
    function _editcolumns()
    {
        global $_CAMILA;
        $form = new CHAW_form($_SERVER['PHP_SELF']);
        
        foreach ($_REQUEST as $key => $value) {
            if (!is_array($value)) {
                $myInput = new CHAW_hidden($key, $value);
                if ($key == 'camila_editcols' || (substr($key, 0, 1) == 'f' && $value == 'y'))
                    $export_format = $key;
                else
                    $form->add_input($myInput);
            } else {
                foreach ($value as $k => $v) {
                    $myInput = new CHAW_hidden($key . '[' . $k . ']', $v);
                    $form->add_input($myInput);
                }
            }
        }
        
        $select_all = new CHAW_js('<div class="camilacheckall" id="camilacheckall"><a id="CheckAll_' . $this->key . '" href="">' . camila_get_translation('camila.form.checkall') . '</a> ' . camila_get_translation('camila.form.checkallseparator') . ' <a id="UncheckAll_' . $this->key . '" href="">' . camila_get_translation('camila.form.uncheckall') . '</a></div>');
        $form->add_userdefined($select_all);
        
        $arr = unserialize($_COOKIE[$this->editcolumscookiename]);
        
        if (($_COOKIE[$this->editcolumscookiename] == '') || (count($arr) > 0 && ($arr[$b] == 'f' . $b))) {
            $new_stmt .= $this->stmtfields[$a] . ',';
            $count++;
        }
        
        for ($a = 0; $a < $this->stmtnumoffields; $a++) {
            $b = $a + 1;
            
            if ($_COOKIE[$this->editcolumscookiename] == '') {
                if ((((isset($_REQUEST['f' . $b]) && $_REQUEST['f' . $b] == 'y')) || (in_array('f' . $b, $_REQUEST['camila_f'])) || ((isset($_REQUEST['camila_f' . $b]) && $_REQUEST['camila_f' . $b] == 'y'))))
                    $myCheckbox = new CHAW_checkbox('camila_f' . '[' . $b . ']', 'f' . $b, $this->map($this->stmtfields[$a]), HAW_SELECTED);
                else
                    $myCheckbox = new CHAW_checkbox('camila_f' . '[' . $b . ']', 'f' . $b, $this->map($this->stmtfields[$a]));
                if (($b % 2) != 0)
                    $myCheckbox->set_br(0);
                
                $myCheckbox->cols = 2;
            } else {
                $arr = unserialize($_COOKIE[$this->editcolumscookiename]);
                
                if ($arr[$b] == 'f' . $b)
                    $myCheckbox = new CHAW_checkbox('camila_f' . '[' . $b . ']', 'f' . $b, $this->map($this->stmtfields[$a]), HAW_SELECTED);
                else
                    $myCheckbox = new CHAW_checkbox('camila_f' . '[' . $b . ']', 'f' . $b, $this->map($this->stmtfields[$a]));
                if (($b % 2) != 0)
                    $myCheckbox->set_br(0);
                
                $myCheckbox->cols = 2;
            }
            
            $form->add_checkbox($myCheckbox);
        }
        
        $myHidden = new CHAW_hidden('count', $count);
        $form->add_hidden($myHidden);
        
        $text = new CHAW_text('');
        $text->set_br(2);
        $form->add_text($text);
        
        $theSubmission = new CHAW_submit('Ok', 'camila_save');
        $form->add_submit($theSubmission);
        $_CAMILA['page']->add_form($form);
    }
}
?>