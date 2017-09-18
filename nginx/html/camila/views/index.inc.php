<?php
defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

if (CAMILA_ANON_LOGIN & !isset($_REQUEST['camila_autosuggest'])) {
   
    if ($dh2 = opendir('./lang/')) {
        while (($file2 = readdir($dh2)) !== false) {
            if (substr($file2, -9) == '.lang.php') {
                $url  = 'index.php?username=' . CAMILA_ANON_USER . '&pwd=' . CAMILA_ANON_PASS . '&js=enabled&camila_pwloginbox=yes&submit=yes&lang=' . substr($file2, 0, 2);
                $link = new CHAW_link(CAMILA_APPLICATION_NAME . ' (' . camila_get_translation('camila.lang.' . substr($file2, 0, 2)) . ', ' . camila_get_translation('camila.lang.' . substr($file2, 0, 2) . '.user.anon') . ')', $url);
                $_CAMILA['page']->add_link($link);
                
                $url  = 'index.php?username=' . CAMILA_ADMIN_USER . '&pwd=' . CAMILA_ADMIN_PASS . '&js=enabled&camila_pwloginbox=yes&submit=yes&lang=' . substr($file2, 0, 2);
                $link = new CHAW_link(CAMILA_APPLICATION_NAME . ' (' . camila_get_translation('camila.lang.' . substr($file2, 0, 2)) . ', ' . camila_get_translation('camila.lang.' . substr($file2, 0, 2) . '.user.admin') . ')', $url);
                $link->set_br(2);
                $_CAMILA['page']->add_link($link);
            }
        }
        closedir($dh2);
    }
}


if (!CAMILA_ANON_LOGIN & !isset($_REQUEST['camila_autosuggest'])) {
	if (is_file(CAMILA_VAR_ROOTDIR.'/plugins/index.txt')) {
		require_once('plugins/'.file_get_contents(CAMILA_VAR_ROOTDIR.'/plugins/index.txt').'/dashboards.inc.php');
	}
	else
	{
		$_CAMILA['page']->camila_export_enabled = false;
	}
}


if (isset($_REQUEST['camila_autosuggest'])) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Pragma: no-cache"); // HTTP/1.0
    header("Content-Type: application/json");

	$likeOp = 'LIKE';
	if ($_CAMILA['db']->databaseType == 'postgres9') {
		$likeOp = 'ILIKE';
	}
	
    $ifArr = explode(',', $_REQUEST['infofields']);
    $count = count($ifArr);

    $query = 'select ' . $_REQUEST['id'] . ', ' . $_REQUEST['field'] . ' as value';
    
    if ($_REQUEST['pickfields'] != '')
        $query .= ',' . $_REQUEST['pickfields'];

    $where = $_REQUEST['field'] . ' '.$likeOp.' ' . $_CAMILA['db']->qstr('%' . $_REQUEST['input'] . '%');
	
	//e.g. bar code
	if (ctype_digit($_REQUEST['input'])) {
		$where = $_REQUEST['field'] . ' = ' . $_CAMILA['db']->qstr($_REQUEST['input']);
	}
 
    if ($_REQUEST['objectid'] != '')
        $where = 'id=' . $_CAMILA['db']->qstr($_REQUEST['objectid']);
    
    if ($_CAMILA['user_visibility_type'] == 'personal')
        $where .= ' and ' . CAMILA_WORKTABLE_EXT_TABLE_PERSONAL_VISIBILITY_FIELD . '=' . $_CAMILA['db']->qstr($_CAMILA['user']);

	if ($_CAMILA['user_visibility_type'] == 'group')
        $where .= ' and ' . CAMILA_WORKTABLE_EXT_TABLE_GROUP_VISIBILITY_FIELD . '=' . $_CAMILA['db']->qstr($_CAMILA['user_group']);
    
	
	//echo $query . ' from ' . $_REQUEST['table'] . ' where ' . $where;
    $result = $_CAMILA['db']->SelectLimit($query . ' from ' . $_REQUEST['table'] . ' where ' . $where, $_REQUEST['maxresults']);
	
	
    if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

    $count = 0;
    while (!$result->EOF) {
        $count++;
        
        $infof = '';
        foreach ($ifArr as $value) {
            $v = $result->fields[$value];
            
            if (strlen($v) == 10 && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/", $v)) {
                $v                      = $_CAMILA['db']->UserDate($v, $_CAMILA['date_format']);
                $result->fields[$value] = $v;
            }
            $infof .= $v . ' ';
        }
        $result->fields['info'] = $infof;
        
        $fields[] = $result->fields;
        
        $result->MoveNext();
    }
    
    
    if ($count > 0) {
        //$json = new Services_JSON();
        camila_utf8_encode_array($fields);
        echo json_encode(Array(
            'results' => $fields
        ));
    } else {
        echo "{\"results\": [";
        echo "]}";
    }
    
    exit();
}

if (isset($_REQUEST['camila_autosuggest_filterbox'])) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Pragma: no-cache"); // HTTP/1.0
    header("Content-Type: application/json");
    
	$likeOp = 'LIKE';
	if ($_CAMILA['db']->databaseType == 'postgres9') {
		$likeOp = 'ILIKE';
	}
	
    $query = 'select distinct ' . $_REQUEST['field'] . ' as value';
    
    $where = $_REQUEST['field'] . ' '.$likeOp.' ' . $_CAMILA['db']->qstr('%' . $_REQUEST['input'] . '%');
    
    //    if ($_REQUEST['objectid'] != '')
    //        $where = 'id='.$_CAMILA['db']->qstr($_REQUEST['objectid']);
    
    $result = $_CAMILA['db']->SelectLimit($query . ' from ' . $_REQUEST['table'] . ' where ' . $where, $_REQUEST['maxresults']);
    if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
    
    $count = 0;
    while (!$result->EOF) {
        $count++;
        
        $result->fields['id']   = $result->fields['value'];
        $result->fields['info'] = '';
        
        $fields[] = $result->fields;
        
        $result->MoveNext();
    }
    
    
    if ($count > 0) {
        //$json = new Services_JSON();
        camila_utf8_encode_array($fields);
        echo json_encode(Array(
            'results' => $fields
        ));
    } else {
        echo "{\"results\": [";
        echo "]}";
    }
    
    exit();
}
?>