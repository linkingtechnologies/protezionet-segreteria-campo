<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2022 Umberto Bresciani

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

	
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    
    header('WWW-Authenticate: Basic realm="Camila Framework Authentication"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required';
    exit;
    
} else {
    $url      = $_SERVER['REQUEST_URI'];
    $method   = $_SERVER['REQUEST_METHOD'];
    //$getArgs  = $_GET;
    //$postArgs = $_POST;
	$requestContent = file_get_contents('php://input');
    //parse_str(file_get_contents('php://input'), $putArgs);
    //parse_str(file_get_contents('php://input'), $deleteArgs);
    $_REQUEST['pwd']               = $_SERVER['PHP_AUTH_USER'];
    $_REQUEST['username']          = $_SERVER['PHP_AUTH_PW'];
    $_REQUEST['camila_pwloginbox'] = 'yes';
    $_REQUEST['submit']            = true;

    //$_REQUEST['filter']='%22ORGANIZZAZIONE%22%20contiene%20%22A.V.P.C.%20%22LE%20AQUILE%22%20SEZIONE%20LA%20TORRE%22%20';

    $urlParts  = parse_url($url);
    // substring from 1 to avoid leading slash
    $pathParts = explode('/', substr($urlParts['path'], 1));
    
    $collection   = $pathParts[array_search('rest', $pathParts) + 1];
    $collectionId = $pathParts[array_search('rest', $pathParts) + 2];
    $resourceId   = $pathParts[array_search('rest', $pathParts) + 3];
    

    switch ($method) {

        case 'GET':
		    if (isset($_REQUEST['camila_xml']))
			    $_REQUEST['camila_xml'] = '';
		    else
			    $_REQUEST['camila_json'] = '';
				$_REQUEST['camila_rest']   = '';
			
			if (!isset($_REQUEST['camila_pagnum']))
				$_REQUEST['camila_pagnum'] = -1;

            switch ($collection) {

                case 'worktable':
                    if ($collectionId != '') {
                        require('../camila/header.php');
                        global $_CAMILA;
                        $_CAMILA['page']->camila_worktable_id = $collectionId;
                        
                        if ($resourceId != '') {
                            //$_REQUEST['filter']=urlencode('"Cod. riga" uguale a "+'.$resourceId.'+"');
                            $_REQUEST['camila_w1f'] = '_I_id';
                            $_REQUEST['camila_w1v'] = $resourceId;
                            $_REQUEST['camila_w1c'] = 'eq';
                        }

                        require(CAMILA_WORKTABLES_DIR . '/' . CAMILA_TABLE_WORKP . $collectionId . '.inc.php');
                        require('../camila/footer.php');
                        
                    } else {
                        require('../../camila/header.php');
                        
                        require(CAMILA_DIR . 'datagrid/report.class.php');
                        $report_fields  = 'id,sequence,short_title,full_title,category,share_key,share_caninsert,share_canupdate,share_candelete';
                        $default_fields = $report_fields;
                        $mapping        = camila_get_translation('camila.worktable.mapping.worktable.admin');
                        $stmt           = 'select ' . $report_fields . ' from ' . CAMILA_TABLE_WORKT;
                        $report         = new report($stmt, '', 'sequence', 'asc', $mapping, null, 'id', '', '', false, false);
                        $report->process();
                        $report->draw();
                        require('../../camila/footer.php');
                    }

                    break;
                case 'worktablecolumn':
                    
                    if ($collectionId != '') {
                        
                        require('../camila/header.php');
                        require(CAMILA_DIR . 'datagrid/report.class.php');
                        
                        $report_fields  = 'wt_id,sequence,type,size,maxlength,name,name_abbrev,col_name,required,readonly,listbox_options,field_options,visible,default_value,force_case,must_be_unique,autosuggest_wt_colname,autosuggest_wt_name';
                        $default_fields = $report_fields;
                        $mapping        = camila_get_translation('camila.worktable.mapping.worktable');
                        $stmt           = 'select ' . $report_fields . ' from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $_CAMILA['db']->qstr($collectionId) . ' and is_deleted<>' . $_CAMILA['db']->qstr('y') . ')';
                        $report         = new report($stmt, '', 'sequence', 'asc', $mapping, null, 'id', '', '', false, false);
                        $report->process();
                        $report->draw();
                        require('../camila/footer.php');
                        
                    } else {
                        
                    }
                    
                    break;
				
				case 'report':
                    if ($collectionId != '') {
                        require('../camila/header.php');
                        global $_CAMILA;
                        $_CAMILA['page']->camila_worktable_id = $collectionId;
                        
                        if ($resourceId != '') {
                            //$_REQUEST['filter']=urlencode('"Cod. riga" uguale a "+'.$resourceId.'+"');
                            $_REQUEST['camila_w1f'] = '_I_id';
                            $_REQUEST['camila_w1v'] = $resourceId;
                            $_REQUEST['camila_w1c'] = 'eq';
                        }

                        require(CAMILA_WORKTABLES_DIR . '/' . CAMILA_TABLE_WORKP . $collectionId . '.inc.php');
                        require('../camila/footer.php');
                        
                    } else {
                        unset($_REQUEST['camila_rest']);
						unset($_REQUEST['camila_json']);
						$_REQUEST['camila_xml2pdf'] = 'volontari_Attestati.xml';
						//$_REQUEST['camila_phpform_sent'] = 1;
						$_REQUEST['hawinputsubmit'] = 'Applica modello';

                        require('../../camila/header.php');
						
                        require(CAMILA_DIR . 'datagrid/report.class.php');
                        $report_fields  = 'id,cognome';
                        $default_fields = $report_fields;
                        $mapping        = camila_get_translation('camila.worktable.mapping.worktable.admin');
                        $stmt           = 'select * from ' . CAMILA_TABLE_WORKP. '13';

                        $report         = new report($stmt, '', 'id', 'asc', '', null, 'id', '', '', false, false);
                        $report->process();
                        $report->draw();
                        require('../../camila/footer.php');

                    }
                    
                    break;
					
					
                case 'persistenceChanges':
                    
                    //{"now":1279888110421, "updates": [ {"id": "F89F99F7B887423FB4B9C961C3883C0A", "name": "Main project", "_lastChange": 1279888110370 } ] }
                    
                    if ($collectionId != '') {
                        require('../camila/header.php');
                        
                        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
                        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
                        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
                        header("Pragma: no-cache"); // HTTP/1.0
                        header("Content-Type: application/json");
                        
                        $query = 'select * from ' . CAMILA_APPLICATION_PREFIX . 'worktable' . $collectionId;
                        
                        if ($_REQUEST['since'] > 0)
                            $query .= ' WHERE last_upd>' . $_CAMILA['db']->UserDate($_REQUEST['since'], $_CAMILA['db']->fmtTimeStamp);
                        
                        $result = $_CAMILA['db']->Execute($query);
                        if ($result === false)
                            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                        
                        $count = 0;
                        while (!$result->EOF) {
                            $count++;
                            $result->fields['_lastChange'] = $_CAMILA['db']->UnixTimeStamp($result->fields['last_upd']);
                            $fields[]                      = $result->fields;
                            $result->MoveNext();
                        }
                        
                        if ($count > 0) {
                            $json = new Services_JSON();
                            camila_utf8_encode_array($fields);
                            echo $json->encode(Array(
                                'now' => time(),
                                'updates' => $fields
                            ));
                        } else {
                            echo "{\"now\":" . time() . "}";
                        }
                        
                        exit();
                    } else {
                        
                    }
                    
                    break;
            }
            break;
        
        case 'PUT':

            switch ($collection) {

                case 'worktable':
                    if ($collectionId != '' && $resourceId != '') {
                        //echo $collectionId . '|'. $resourceId . ":-)";
						//print_r($putArgs);

						$content = json_decode($requestContent);
						$field = $content->fieldId;
						$value = $content->value;

						//print_r($content);
						//echo $fieldId;
						$_REQUEST['camila_inline']='';
						$_REQUEST['worktable_worktable'.$collectionId.'_'.$field]=$value;
						$_REQUEST['worktable_worktable'.$collectionId.'_phpform_sent']=1;
						$_REQUEST['camila_inline_field']=$field;
						$_REQUEST['worktable_worktable'.$collectionId.'_sess_mode']='update';
						$_REQUEST['worktable_worktable'.$collectionId.'_id']=$resourceId;
						$_REQUEST['worktable_worktable'.$collectionId.'_sess_key']=urldecode('a%3A1%3A%7Bs%3A12%3A%22camilakey_id%22%3Bs%3A6%3A%22'.$resourceId.'%22%3B%7D');
						
						require('../camila/header.php');
                        global $_CAMILA;
                        $_CAMILA['page']->camila_worktable_id = $collectionId;

                        require(CAMILA_WORKTABLES_DIR . '/' . CAMILA_TABLE_WORKP . $collectionId . '.inc.php');
                        require('../camila/footer.php');

						//http://192.168.1.110:4001/worktable/cf_worktable1.php?camila_inline&worktable_worktable1_imageurl&worktable_worktable1_phpform_sent=2&camila_inline_field=imageurl&worktable_worktable1_sess_mode=update&worktable_worktable1_id=100025&worktable_worktable1_sess_key=a%3A1%3A%7Bs%3A12%3A%22camilakey_id%22%3Bs%3A6%3A%22100025%22%3B%7D

                    }
					
					//cf_worktable1.php?camila_inline&worktable_worktable1_imageurl=new&time=1409776068157&worktable_worktable1_phpform_sent=1&camila_inline_field=imageurl&worktable_worktable1_sess_key=a%3A1%3A%7Bs%3A12%3A%22camilakey_id%22%3Bs%3A6%3A%22100025%22%3B%7D&worktable_worktable1_sess_mode=update&worktable_worktable1_id=100025&type=text&value=&name=worktable_worktable1_imageurl&size=30&maxlength=250&result=OK
                    
					//http://192.168.1.110:4001/worktable/cf_worktable1.php?camila_inline&worktable_worktable1_imageurl&worktable_worktable1_phpform_sent=2&camila_inline_field=imageurl&worktable_worktable1_sess_mode=update&worktable_worktable1_id=100025&worktable_worktable1_sess_key=a%3A1%3A%7Bs%3A12%3A%22camilakey_id%22%3Bs%3A6%3A%22100025%22%3B%7D
                    break;
                    
            }
            break;
    }
    
}

?>