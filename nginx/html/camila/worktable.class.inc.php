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

//require_once(CAMILA_LIB_DIR.'adodb5/adodb.inc.php');
require_once(CAMILA_VENDOR_DIR . '/adodb/adodb-php/adodb.inc.php');

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
	
	function getWorktableRawInfo($id)
    {
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$query = 'SELECT * FROM ' . $this->wtTable . ' WHERE Id = ' . $this->db->qstr($id);
		$result = $this->db->Execute($query);
		$this->db->SetFetchMode($old);
		return $result->fields;
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
		
		$int = (int) filter_var(substr($a['tablename'],strpos($a['tablename'], '_')), FILTER_SANITIZE_NUMBER_INT);
		$tablename = CAMILA_TABLE_WORKP.$int;
		
		$query = 'SELECT id FROM ' . $tablename;
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
			$int = (int) filter_var(substr($a['tablename'],strpos($a['tablename'], '_')), FILTER_SANITIZE_NUMBER_INT);
			$tablename = CAMILA_TABLE_WORKP.$int;
			$ttemp->setVariable($a['short_title'], $tablename, true);
			
			$result2 = $this->getWorktableColumnsById($a['id']);
			while (!$result2->EOF) {
				$b = $result2->fields;
				$ttemp->setVariable($a['short_title'].'.'.$b['name'], $prefix ? $tablename.'.'.$b['col_name'] : $b['col_name'], true);
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
		//camila_parse_default_expression
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

	function insertSuggestionRecords($from,$to,$idsString,$overrides = null) {
		$r = false;
		global $_CAMILA;
		$fId = $this->getWorktableSheetId($from);
		$tId = $this->getWorktableSheetId($to);
		
		$fInfo = $this->getWorktableRawInfo($fId);
		$tInfo = $this->getWorktableRawInfo($tId);

		$qry = 'select sequence,col_name,autosuggest_wt_colname,field_options from ' . $this->wtColumn . ' where (autosuggest_wt_name IS NOT NULL and wt_id=' . $this->db->qstr($tId) . ' and is_deleted<>' . $this->db->qstr('y') . ')';
		//echo $qry;
		$result2 = $this->db->Execute($qry);

		$fields = Array();
		$values = Array();
		$predefaults = Array();
		$ovs = Array();
		if ($overrides != null) {
			foreach($overrides as $k => $v) {
				foreach($v as $k2 => $v2) {
					if (!in_array($k2, $ovs)) {
						$ovs[] = $k2;
					}
				}
			}
		}

		while (!$result2->EOF) {
			$b = $result2->fields;
			//echo $b['col_name'];
			//echo $b['autosuggest_wt_colname'];
			if ($b['autosuggest_wt_colname'] != '') {
				$fields[] = $b['col_name'];
				$values[] = $this->getWorktableSheetColumnName($fId, $b['autosuggest_wt_colname']);
				$predefaults[] = false;
			}
			$result2->MoveNext();
		}

		//$result2 = $this->db->Execute('select sequence,col_name,autosuggest_wt_colname,field_options,default_value from ' . $this->wtColumn . ' where (default_value IS NOT NULL and wt_id=' . $this->db->qstr($tId) . ' and is_deleted<>' . $this->db->qstr('y') . ')');
		$result2 = $this->db->Execute('select sequence,col_name,autosuggest_wt_colname,field_options,default_value from ' . $this->wtColumn . ' where (wt_id=' . $this->db->qstr($tId) . ' and is_deleted<>' . $this->db->qstr('y') . ')');
		
		//$fields = Array();
		//$values = Array();
		//echo "Pre:";
		//print_r($fields);
		//print_r($ovs);
		while (!$result2->EOF) {
			$b = $result2->fields;
			//echo $b['default_value'];
			if ($b['default_value'] != '') {
				$fields[] = $b['col_name'];
				$values[] = $b['default_value'];
				$predefaults[] = true;
			}
			
			//? Check "&& !in_array($b['col_name'], $fields"
			if (in_array($b['col_name'], $ovs) && !in_array($b['col_name'], $fields)) {
				$fields[] = $b['col_name'];
				$values[] = '';
				$predefaults[] = false;
			}
			$result2->MoveNext();
		}
		//echo "Post:";
		//print_r($fields);
		//print_r($values);
		//print_r($overrides);

		$ids = explode(',', $idsString);

		foreach ($ids as $v) {
			//echo '::'.$v.'::';
			$r = $this->insertSuggestion($fInfo['tablename'], $tInfo['tablename'], $v, $fields, $values, $predefaults, $overrides);
		}
		
		return $r;

	}

	function insertSuggestion($fromTable, $toTable, $fromId, $fields, $values, $predefaults, $overrides)
	{
		//print_r($overrides);
		//print_r($fields);
		$ovs = Array();
		if ($overrides != null) {
			foreach($overrides as $k => $v) {
				foreach($v as $k2 => $v2) {
					if (!in_array($k2, $ovs)) {
						$ovs[] = $k2;
					}
				}
			}
		}
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		//$fields=Array();
		//$values=Array();
		$newId = $this->db->GenID(CAMILA_APPLICATION_PREFIX.'worktableseq', 100000);
		$q = 'select * FROM ' . $fromTable . ' where id=' . $this->db->qstr($fromId);
		$result2 = $this->db->Execute($q);
		$b = $result2->fields;		
		foreach ($values as $k => $v) {
			if (!$predefaults[$k]) {
				$values[$k] = $b[$v];
			}
			else
				$values[$k] = camila_parse_default_expression($values[$k], $newId, true);
			
			$xxx = $fields[$k]; 
			
			if (in_array($xxx, $ovs)) {
				//$fields[] = $b['col_name'];
				$values[$k] = $overrides[$fromId][$xxx];
			}

			/*if ($overrides[$fromId]['badgeassegnato'] != '') {
				$values[$k] = $overrides[$fromId][$xxx];
			}*/
		}
		
		//print_r($fields);
		//print_r($values);

		$now = $this->db->BindTimeStamp(date("Y-m-d H:i:s", time()));

		$fields2 = Array();
		$fields2[]='id';
		$fields2[]='created_by';
		$fields2[]='last_upd';
		$fields2[]='last_upd_by';
		$fields2[]='last_upd_src';
		$fields2[]='last_upd_by_surname';
		$fields2[]='last_upd_by_name';

		$values[]=$newId;
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

		$query = 'INSERT INTO '.$toTable.' (';
		$count = 0;
		foreach($fields as $val) {
			if ($count>0)
				$query .= ',';
			$query .= $val;
			$count++;
		}
		//$count = 0;
		foreach($fields2 as $val) {
			if ($count>0)
				$query .= ',' . $val;
			else
				$query .= $val;
			$count++;
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
		
		$this->db->SetFetchMode($old);

		return !($result === false);
	}
	
	function getWorktableRecordIdByKeyColumn($wtFrom, $column, $value)
    {
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$rId = '';
		$sql = "Select Id FROM \${".$wtFrom."} WHERE \${".$wtFrom.".".$column."}=".$this->db->qstr($value);
		//echo $sql;
		$result = $this->startExecuteQuery($sql);
		while (!$result->EOF) {
			$a = $result->fields;
			$rId = $a[0];
			$result->MoveNext();
		}
		$this->db->SetFetchMode($old);
		return $rId;
    }
	
	/* draft, not used yet */
	function updateWorktableRecord($wtTo, $id, $fields, $values)
    {
		$old = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		$query = "UPDATE \${".$wtTo."} ";
		$count = 0;
		foreach($fields as $k => $val) {
			if ($count>0)
				$query .= ',';
			else
				$query = ' SET ';
			$query .= " \${".$wtTo.".".$val."} = " . $this->db->qstr($val);
			$count++;
		}
		
		$query .= " WHERE Id = " . $this->db->qstr($id);
		
		echo $query;
		
		$this->db->SetFetchMode($old);
		return $rId;
    }

}

?>