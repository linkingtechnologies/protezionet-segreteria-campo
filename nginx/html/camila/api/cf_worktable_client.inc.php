<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2016 Umberto Bresciani

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

class WorkTableClient
{
    public $workTableFolder;
    public $restServerHost;
    public $restServerPort;
    public $restUsername;
    public $restPassword;
    public $dbTablePrefix;
    public $db;

    public $cacheFolder = 'var/tmp';
    public $useCache = false;
	public $useMetaCache = true;

    function WorkTableClient($type = 'rest')
    {
		$this->workTableFolder = substr(dirname($_SERVER['REQUEST_URI']),1);
    }
	
	

    function query($workTableName, $lang, $filter, $orderBy = '', $descendingOrder = 0, $page = 0, $id='')
    {
        $obj         = $this->getWorkTablesMetadata($lang);
        $col1        = $this->getColId($obj->table->cols, "Nome scheda");
        $col2        = $this->getColId($obj->table->cols, "id");
        $rowId       = $this->searchRowByColValue($obj->table->rows, $col1, $workTableName);
        $workTableId = $this->getColValue($obj->table->rows, $rowId, $col2);

        if ($orderBy != '') {
            if ($orderBy == 'id') {
                $orderById = $orderBy;
            } else {
                $obj       = $this->getRestObject($workTableId, "worktablecolumn", $lang, "", "");
                $col1      = $this->getColId($obj->table->cols, "nome campo");
                $col2      = $this->getColId($obj->table->cols, "col_name");
                $rowId     = $this->searchRowByColValue($obj->table->rows, $col1, $orderBy);
                $orderById = $this->getColValue($obj->table->rows, $rowId, $col2);
            }
        }

        $obj = $this->getRestObject($workTableId, "worktable", "it", $filter, $orderById, $descendingOrder, $page, $id);
        return $obj;
    }

    function getWorkTablesMetadata($lang)
    {
        return $this->getRestObject("", "worktable", $lang, "", "");
    }

	function getWorkTableColumnsMetadata($lang, $wtId)
    {
        return $this->getRestObject($wtId, "worktablecolumn", $lang, "", "");
    }
    
    function updateFieldById($workTableName, $lang, $id, $fieldName, $newValue)
    {
        $obj         = $this->getWorkTablesMetadata($lang);
        $col1        = $this->getColId($obj->table->cols, "Nome scheda");
        $col2        = $this->getColId($obj->table->cols, "id");
        $rowId       = $this->searchRowByColValue($obj->table->rows, $col1, $workTableName);
        $workTableId = $this->getColValue($obj->table->rows, $rowId, $col2);
        
        $obj     = $this->getRestObject($workTableId, "worktablecolumn", $lang, "", "");
        $col1    = $this->getColId($obj->table->cols, "nome campo");
        $col2    = $this->getColId($obj->table->cols, "col_name");
        $rowId   = $this->searchRowByColValue($obj->table->rows, $col1, $fieldName);
        $fieldId = $this->getColValue($obj->table->rows, $rowId, $col2);
        
        $content          = new stdClass();
        $content->fieldId = $fieldId;
        $content->value   = $newValue;
        
        $json_data = json_encode($content);
        
        $url = $this->getProtocol() . "://" . $this->restServerHost . ":" . $this->restServerPort . "/" . $this->workTableFolder . "/rest/worktable/" . $workTableId . '/' . $id;

        $post = file_get_contents($url, null, stream_context_create(array(
            'http' => array(
                'protocol_version' => 1.1,
                'user_agent' => 'PHPExample',
                'method' => 'PUT',
                'header' => "Accept-language: " . $lang . "\r\nContent-type: application/json\r\n" . "Connection: close\r\n" . "Content-length: " . strlen($json_data) . "\r\n" . "Authorization: Basic " . base64_encode("$this->restUsername:$this->restPassword"),
                'content' => $json_data
            )
        )));
        
        //echo $post;
        /*if ($post) {
        echo $post;
        } else {
        echo "POST failed";
        }*/
        return json_decode($post);
    }
    
    
    function insertRow($workTableName, $lang, $fields, $values)
    {
        $obj = $this->getWorkTablesMetadata($lang);
        
        $col1        = $this->getColId($obj->table->cols, "Nome scheda");
        $col2        = $this->getColId($obj->table->cols, "id");
        $rowId       = $this->searchRowByColValue($obj->table->rows, $col1, $workTableName);
        $workTableId = $this->getColValue($obj->table->rows, $rowId, $col2);
        
        $obj  = $this->getRestObject($workTableId, "worktablecolumn", $lang, "", "");
        $cols = Array();
        
        $fStmt = '';
        
        foreach ($fields as $name => $fieldName) {
            //echo $fieldName;
            $col1    = $this->getColId($obj->table->cols, "nome campo");
            $col2    = $this->getColId($obj->table->cols, "col_name");
            $rowId   = $this->searchRowByColValue($obj->table->rows, $col1, $fieldName);
            $fieldId = $this->getColValue($obj->table->rows, $rowId, $col2);
            $fStmt .= $fieldId . ',';
            
            $cols[$name] = $fieldId;
        }

        $table = $this->dbTablePrefix . 'worktable' . $workTableId;

        $stmt = 'insert into ' . $table . ' ( ' . substr($fStmt, 0, -1);
        $stmt .= ', created, created_by, created_src, created_by_surname, created_by_name, last_upd, last_upd_by, last_upd_src, last_upd_by_surname, last_upd_by_name, mod_num, id';
        $stmt .= ' ) values ( ';
        
        $first = false;
        
        $count = 0;
        $data  = Array();
        
        foreach ($values as $n => $v) {
            
            if ($first)
                $stmt .= ', ';
            else
                $first = true;
            
            $stmt .= '?';
            $data[$count] = $v;
            
            $count++;
        }
        
        $stmt .= ',?,?,?,?,?,?,?,?,?,?,?,?';
        
        $now               = $this->db->BindTimeStamp(gmdate("Y-m-d H:i:s", time()));
        $data[$count]      = $now;
        $data[$count + 1]  = 'client';
        $data[$count + 2]  = 'application';
        $data[$count + 3]  = 'client';
        $data[$count + 4]  = 'client';
        $data[$count + 5]  = $now;
        $data[$count + 6]  = 'client';
        $data[$count + 7]  = 'application';
        $data[$count + 8]  = 'client';
        $data[$count + 9]  = 'client';
        $data[$count + 10] = 0;
        $data[$count + 11] = $this->db->GenID($this->databaseTablePredix . 'worktableseq', 100000);
        ;
        
        $stmt .= ')';
        
        //echo $stmt;
        //print_r($data);
        
        $result = $this->db->Execute($stmt, $data);
        return $result;
        //camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
    }
    
    
    
    function getColId($cols, $label)
    {
        $id = -1;
        foreach ($cols as $name => $value) {
            if ($value->label == $label)
                $id = $value->id;
        }
        return $id;
    }
    
    function getColValue($rows, $rowId, $colId)
    {
        return $rows[$rowId]->c[$colId]->v;
    }
    
    function getColName($cols, $label)
    {
        return strtolower($label);
    }
    
    function searchRowByColValue($rows, $colId, $colValue)
    {
        $id = -1;
        
        foreach ($rows as $name => $value) {
            if ($value->c[$colId]->v == $colValue)
                $id = $name;
        }

        return $id;
    }

    function getRestObject($workTableId, $workTableCollection, $language, $filter, $orderByColId, $descendingOrder = 0, $page = 0, $id='')
    {
	//return $this->getRestObject("", "worktable", $lang, "", "");

        $cacheFile = $this->cacheFolder . '/' . $workTableId . '_' . $workTableCollection . '_' . $language . '_' . $filter . '_' . $orderByColId . '_' . $descendingOrder . '_' . $page . '_';
        if (($this->useCache || ($this->useMetaCache && ($workTableCollection=='worktablecolumn' || $workTableId==''))) && file_exists($cacheFile)) {
			//echo "using cache $cacheFile";
            return json_decode(file_get_contents($cacheFile));
        }

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: " . $language . "\r\n" . "Authorization: Basic " . base64_encode("$this->restUsername:$this->restPassword")
            )
        );
        
        $context = stream_context_create($opts);
        
        $url = $this->getProtocol() . "://" . $this->restServerHost . ":" . $this->restServerPort . "/" . $this->workTableFolder . "/rest/" . $workTableCollection;

		if ($workTableId != '')
		{
			if ($id != '')
				$url .= "/" . $workTableId . '/'.$id.'?camila_json';
			else
				$url .= "/" . $workTableId . '?camila_json';
		}
		else
			$url .= '?camila_json';

        if ($page > 0) {
            $url .= '&camila_pagnum=' . $page;
        }

        if ($orderByColId != '') {
            $url .= '&f0=' . $orderByColId . '&d=' . $descendingOrder;
        }
        
        if ($filter != "")
            $url .= "&filter=" . urlencode($filter);
			
		//	echo $url;
        $file = file_get_contents($url, false, $context);

		//echo $file;
        
        if ($this->useCache || ($this->useMetaCache && ($workTableCollection=='worktablecolumn' || $workTableId=='')))
            file_put_contents($cacheFile, $file);

        return json_decode($file);
    }
    
    function getObjectFromCache($name)
    {
        $cache_filename          = $name . '_data.inc';
        $cachefile_full_filename = $_SERVER['DOCUMENT_ROOT'] . '/cache/' . $cache_filename;
        // check for cache, if it exists and is less than 1 hour old grab it
        if (file_exists($cachefile_full_filename) && filemtime($cachefile_full_filename) > strtotime(removeHours(date('Y-m-d H:i:s'), 1))) {
            $object_data = unserialize(file_get_contents($cachefile_full_filename));
        } // end if
        // cache is missing or too old
        else {
            // Initialise object
            $object_data = new Object($page_filter, '');
            // Create the cache for future use
            file_put_contents($cachefile_full_filename, serialize($object_data));
        } // end else
    }
	
	function postUpdate()
    {
        $article          = new stdClass();
        $article->title   = "An example article";
        $article->summary = "An example of posting JSON encoded data to a web service";
        
        $json_data = json_encode($article);
         
        $post = file_get_contents('http://127.0.0.1:4001/worktable/rest/worktable/' + $this->resourceWorkTableId + '/100329', null, stream_context_create(array(
            'http' => array(
                'protocol_version' => 1.1,
                'user_agent' => 'PHPExample',
                'method' => 'PUT',
                'header' => "Content-type: application/json\r\n" . "Connection: close\r\n" . "Content-length: " . strlen($json_data) . "\r\n" . "Authorization: Basic " . base64_encode("$username:$password"),
                'content' => $json_data
            )
        )));
        
        echo $post;
        /*if ($post) {
        echo $post;
        } else {
        echo "POST failed";
        }*/
    }
	
	function getProtocol() {
		$isSecure = false;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$isSecure = true;
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
		{
			$isSecure = true;
		}
		$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
		return $REQUEST_PROTOCOL;
	}
}
?>