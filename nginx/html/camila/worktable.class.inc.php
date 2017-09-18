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

require_once(CAMILA_LIB_DIR.'adodb5/adodb.inc.php');

class CamilaWorkTable
{
    public $db;
	public $wtTable;
	public $wtColumn;

    function CamilaWorkTable()
    {
		$this->wtTable = CAMILA_TABLE_WORKT;
		$this->wtColumn = CAMILA_TABLE_WORKC;
    }

    function getWorktableSheetId($name)
    {
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		$query = 'SELECT id FROM ' . $this->wtTable . ' WHERE short_title=' . $this->db->qstr($name);
		$result = $this->db->Execute($query);
		$utable = $result->fields;

		$id = $utable['id'];

		$this->db->SetFetchMode($old);
		
		return $id;
    }

	function getWorktableSheets()
    {
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$query = 'SELECT id,short_title,tablename FROM ' . $this->wtTable;
		$result = $this->db->Execute($query);
		$this->db->SetFetchMode($old);
		return $result;
    }
	

	function getWorktableColumns($name)
    {
		$id = $this->getWorktableSheetId($name);
		return $this->getWorktableColumnsById($id);
    }
	
	function getWorktableSheetColumnName($sheetId, $fieldName) {
		$col_name = '';
		$result2 = $this->getWorktableColumnsById($sheetId);
			while (!$result2->EOF) {
				$b = $result2->fields;
				if (strtolower($b['name']) == strtolower($fieldName)) {
					$col_name = $b['col_name'];
					break;
				}
				$result2->MoveNext();
			}
		return $col_name;
	}
	
	function getWorktableColumnsById($id)
    {
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		$query = 'SELECT name,col_name FROM ' . $this->wtColumn . ' WHERE wt_id=' . $id;
		$result = $this->db->Execute($query);

		$this->db->SetFetchMode($old);

		return $result;
    }

	function countWorktableRecords($id)
    {
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$query = 'SELECT tablename FROM ' . $this->wtTable . ' WHERE id=' . $id;
		$result = $this->db->Execute($query);
		$a = $result->fields;
		$query = 'SELECT id FROM ' . $a['tablename'];
		$result2 = $this->db->Execute($query);
		$result3=$result2->RecordCount();
		$this->db->SetFetchMode($old);
		return $result3;
    }
	
	function parseWorktableSqlStatement($sql, $prefix = true) {
		require_once (CAMILA_LIB_DIR . 'minitemplator/MiniTemplator.class.php');
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		$sql2 = '';
		$ttemp = new MiniTemplator();
		$ttemp->setTemplateString($sql);

		$result = $this->getWorktableSheets();
		while (!$result->EOF) {
			$a = $result->fields;
			$ttemp->setVariable($a['short_title'], $a['tablename'], true);
			$result2 = $this->getWorktableColumnsById($a['id']);
			while (!$result2->EOF) {
				$b = $result2->fields;
				$ttemp->setVariable($a['short_title'].'.'.$b['name'], $prefix ? $a['tablename'].'.'.$b['col_name'] : $b['col_name'], true);
				$result2->MoveNext();
			}
			$result->MoveNext();
		}
		$this->db->SetFetchMode($old);
		$ttemp->generateOutputToString($sql2);
		return $sql2;
	}

	function queryWorktableDatabase($sql, $prefix = true)
	{
		$old = $this->db->SetFetchMode(ADODB_FETCH_NUM);
		$query = $this->parseWorktableSqlStatement($sql, $prefix);
		//echo $query;
		$result = $this->db->Execute($query);
		$arr = array();
		while (!$result->EOF) {
			$a = $result->fields;
			$arr[$a[0]]=$a[1];
			$result->MoveNext();
		}
		$this->db->SetFetchMode($old);
		return $arr;
	}

	function startExecuteQuery($sql,$prefix = true)
	{
		$old = $this->db->SetFetchMode(ADODB_FETCH_NUM);
		$query = $this->parseWorktableSqlStatement($sql, $prefix);
		//echo $query;
		$result = $this->db->Execute($query);
		return $result;
	}
	
	function endExecuteQuery()
	{
	}
	
	function insertRow($worktableName, $lang, $fields, $values, $created_by='')
	{
		global $_CAMILA;
		$now = $_CAMILA['db']->BindTimeStamp(date("Y-m-d H:i:s", time()));

		$fields2 = Array();
		$fields2[]='id';
		$fields2[]='created_by';
		$fields2[]='last_upd';
		$fields2[]='last_upd_by';
		$fields2[]='last_upd_src';
		$fields2[]='last_upd_by_surname';
		$fields2[]='last_upd_by_name';

		$values[]=$_CAMILA['db']->GenID(CAMILA_APPLICATION_PREFIX.'worktableseq', 100000);
		if ($created_by != '')
			$values[]=$created_by;
		else
			$values[]=$_CAMILA['user'];
		$values[]=$now;
		if ($created_by != '')
			$values[]=$created_by;
		else
			$values[]=$_CAMILA['user'];
		$values[]='application';
		$values[]=$_CAMILA['user_surname'];
		$values[]=$_CAMILA['user_name'];

		$query = 'INSERT INTO ${' . $worktableName . '} (';
		$count = 0;
		foreach($fields as $val) {
			if ($count>0)
				$query .= ',';
			$query .= '${'. $worktableName . '.' . $val .'}' ;
			$count++;
		}
		foreach($fields2 as $val) {
			$query .= ',' . $val;
		}
		$query .= ') VALUES (';
		$count = 0;
		foreach($values as $val) {
			if ($count>0)
				$query .= ',';
			$query .= $this->db->qstr($val);
			$count++;
		}
		$query .= ')';

		//echo $query;
		$result = $this->startExecuteQuery($query,false);

		return $result;
	}
	
	function getListBoxOptions($query) {
		$data = $this->queryWorktableDatabase($query);
		$opts = '-';
		foreach($data as $k=>$v){
			$opts.=','.$v.";".$k;
		}
		return $opts;
	}


}

?>