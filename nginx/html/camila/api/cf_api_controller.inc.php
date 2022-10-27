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

require_once('../../camila/config.inc.php');
require_once('../../camila/i18n.inc.php');
require_once('../../camila/database.inc.php');
require_once('../../camila/auth.class.inc.php');
require_once('../../camila/worktable.class.inc.php');

require_once(CAMILA_VENDOR_DIR . '/adodb/adodb-php/adodb-csvlib.inc.php');
//require_once(CAMILA_LIB_DIR . 'adodb5/adodb-csvlib.inc.php');

$camilaAuth                  = new CamilaAuth();
$camilaAuth->db              = $_CAMILA['db'];
$camilaAuth->userTable       = CAMILA_TABLE_USERS;
$camilaAuth->authUserTable   = CAMILA_AUTH_TABLE_USERS;
$camilaAuth->applicationName = CAMILA_APPLICATION_NAME;


if (!isset($_SERVER['PHP_AUTH_USER'])) {
    $camilaAuth->raiseError();
    exit;
} else {
    $url            = $_SERVER['REQUEST_URI'];
    $method         = $_SERVER['REQUEST_METHOD'];
    $getArgs        = $_GET;
    $postArgs       = $_POST;
    $requestContent = file_get_contents('php://input');
    //parse_str(file_get_contents('php://input'), $putArgs);
    //parse_str(file_get_contents('php://input'), $deleteArgs);
    
    if (!$camilaAuth->checkCredentials($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
	{
		$camilaAuth->raiseError();
		exit;
	}

    $urlParts  = parse_url($url);
    // substring from 1 to avoid leading slash
    $pathParts = explode('/', substr($urlParts['path'], 1));
    
    $version    = $pathParts[array_search('api', $pathParts) + 1];
    $resource   = $pathParts[array_search('api', $pathParts) + 2];
    $resourceId = $pathParts[array_search('api', $pathParts) + 3];
    
    switch ($method) {
        
        case 'GET':
            
            switch ($resource) {
                
                case 'query':
                    
                    $query        = $getArgs['q'];
                    $camilaWT     = new CamilaWorkTable();
                    //$camilaWT->wtTable = 'cms_camila_worktables';
                    //$camilaWT->wtColumn = 'cms_camila_worktables_cols';
                    $camilaWT->db = $_CAMILA['db'];
                    global $camilaWT;
                    //echo $query;
                    $result = $camilaWT->startExecuteQuery($query);

                    if ($result) {
                        //$rs->timeToLive = 1;
                        //echo _rs2serialize($result,$conn,$sql);
                        
                        echo '{"done" : true,"totalSize" : ' . $result->RecordCount() . ',"records" : [';
                        
                        $count = 0;
                        while (!$result->EOF) {
                            $a = $result->fields;
                            if ($count > 0)
                                echo ",";
                            echo json_encode($a);
                            //print_r($a);
                            $count++;                            
                            $result->MoveNext();
                        }
                        
                        echo ']}';
						
						$result = $camilaWT->endExecuteQuery();
                        
                        //$result->Close();
                    } else
                        err($conn->ErrorNo() . $sep . $conn->ErrorMsg());

                    /*		
                    if (isset($_REQUEST['fetch']))
                    $ADODB_FETCH_MODE = $_REQUEST['fetch'];
                    
                    if (isset($_REQUEST['nrows'])) {
                    $nrows = $_REQUEST['nrows'];
                    $offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : -1;
                    $rs = $conn->SelectLimit($sql,$nrows,$offset);
                    } else
                    $rs = $conn->Execute($sql);
                    */
                    
                    
                    if ($collectionId != '') {
                        
                    } else {
                        
                    }
                    
                    break;
					

					case 'objects':

					//echo $resourceId;
					$camilaWT     = new CamilaWorkTable();
                    $camilaWT->db = $_CAMILA['db'];
                    global $camilaWT;
					$result2 = $camilaWT->getWorktableColumns($resourceId);
					while (!$result2->EOF) {
						$b = $result2->fields;
						print_r($b);
						//$ttemp->setVariable($a['short_title'].'.'.$b['name'], $prefix ? $a['tablename'].'.'.$b['col_name'] : $b['col_name'], true);
						
						echo "!";
						$result2->MoveNext();
					}
					break;
            }
            break;
        
        case 'PATCH':
			echo "!!!";
            
            switch ($collection) {
                    
            }
            break;

		case 'POST':
			echo "!!!";
            
            switch ($collection) {
                    
            }
            break;
    }
}
?>