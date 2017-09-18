<?php
require_once 'Constant.class.php';
require_once('cf_worktable_client.inc.php');

class DatabaseAnalyzer {

	protected $database_name = '';
	public $client ;
	public $lang;

	/**
	  * Creates DatabaseAnalyzer for specific sqlite database.
	  *
	  * @param string $database_name
	  */
	public function __construct($database_name = '') {
		$this->database_name = $database_name;
		//$this->database = new PDO('sqlite:'.$this->database_name);
		//$this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$this->client = new WorkTableClient();
		$this->client->restServerHost = $_SERVER['SERVER_NAME'];
		$this->client->restServerPort = $_SERVER['SERVER_PORT'];
		$this->client->restUsername = "rest1";
		$this->client->restPassword = "rest1";
		$folder = dirname($_SERVER['SCRIPT_NAME']);
		$this->client->workTableFolder = substr($folder,1,-6);
		$this->client->cacheFolder = '../var/tmp';
	}

	public function __destruct() {
		$this->database = null;
	}
	
	public function get_tables() {
		//$result = $this->database->prepare("SELECT tbl_name FROM sqlite_master WHERE type = 'table' AND tbl_name NOT LIKE 'sqlite_%'");
		//$result->execute();
		//$tables = $result->fetchAll(PDO::FETCH_ASSOC);

		$obj = $this->client->getWorkTablesMetadata($this->lang);
		$col1 = $this->client->getColId($obj->table->cols, "Nome scheda");
        $col2 = $this->client->getColId($obj->table->cols, "id");		
		$rows = $obj->table->rows;
		$wtables = array();
		foreach ($rows as $name => $value) {			
			$wtables[] = array('name' => $value->c[$col1]->v, 'id' => $value->c[$col2]->v, 'tbl_name' => $value->c[$col1]->v);
        }
		//print_r($wtables);
		return $wtables;
	}
	
	public function get_columns($table_name) {
		/*$result = $this->database->prepare("PRAGMA table_info(${table_name})");
		$result->execute();
		$columns = $result->fetchAll(PDO::FETCH_ASSOC);*/
		$wcolumns = array();
		$obj = $this->client->getWorkTableColumnsMetadata($this->lang, $table_name);
		//print_r($obj);
		//if ($obj)
		{
			$col1 = $this->client->getColId($obj->table->cols, "tipo campo");
			$col2 = $this->client->getColId($obj->table->cols, "col_name");
			$col3 = $this->client->getColId($obj->table->cols, "nome campo");
			$rows = $obj->table->rows;
			$wcolumns = array();
			$wcolumns[] = array('notnull' => 1, 'cid' => 'id', 'type' => 'INTEGER', 'name' => 'id', 'pk' => '1');
			foreach ($rows as $name => $value) {
				$wcolumns[] = array('notnull' => 0, 'cid' => $value->c[$col2]->v, 'type' => $value->c[$col1]->v, 'name' => $value->c[$col2]->v, 'pk' => '0');
			}
		}
		//print_r($wcolumns);
		return $wcolumns;
	}

	public function table_exists($table_name) {
		$tables = $this->get_tables();
		$result = false;
		foreach($tables as $n => $v)
		{
			if ($v['name'] == $table_name)
			{
				$result = true;
			}
		}

		return $result;
		/*$result = $this->database->prepare("SELECT tbl_name FROM sqlite_master WHERE type = 'table' AND tbl_name NOT LIKE 'sqlite_%'");
		$result->execute();
		$tables = $result->fetchAll(PDO::FETCH_COLUMN, 0);

		return in_array($table_name, $tables);*/
	}
	
	public function get_table_id($table_name) {
		$tables = $this->get_tables();
		$result = -1;
		foreach($tables as $n => $v)
		{
			if ($v['name'] == $table_name)
			{
				$result = $v['id'];
			}
		}

		return $result;
		/*$result = $this->database->prepare("SELECT tbl_name FROM sqlite_master WHERE type = 'table' AND tbl_name NOT LIKE 'sqlite_%'");
		$result->execute();
		$tables = $result->fetchAll(PDO::FETCH_COLUMN, 0);

		return in_array($table_name, $tables);*/
	}
	
	public function get_worktable_title($id) {
		$tables = $this->get_tables();
		$worktableTitle = '';
		foreach ($tables as $name => $value) {
			if ($value['id'] == $id)
					$worktableTitle=$value['name'];
		}
		return $worktableTitle;
	}
	
	public function table_get_rows($table_name) {
		if ($this->table_exists($table_name)) {
			/*$result = $this->database->prepare("SELECT * FROM ${table_name}");
			$result->execute();
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);*/

			$tables = $this->get_tables();
			$worktableTitle = '';
			foreach ($tables as $name => $value) {
				if ($value['id'] == $table_name)
					$worktableTitle=$value['name'];
			}

			$obj = $this->client->query($worktableTitle, $this->lang, '', 'id');
			//print_r($obj);
			
			$wrows = array();
			
			if ($obj)
			{

			$cols = $obj->table->cols;
			$wcols = array();
			foreach ($cols as $name => $value) {
				$wcols[$value->id]=$value->name;
			}

			$rows2 = $obj->table->rows;

			foreach ($rows2 as $name => $value) {
				$wrow = array();
				foreach ($value->c as $n => $v) {
					$wrow[$wcols[$n]]=$v->v;
				}
				$wrows[]=$wrow;
			}

			//print_r($wrows);
			//print_r($rows);
			}
			return $wrows;
		}

	}

	public function table_get_entry($table_name, $pk_column, $id) {
		if ($this->table_exists($table_name)) {
			//$result = $this->database->prepare("SELECT * FROM ${table_name} WHERE ${pk_column} = ?");
			/*$result = $this->database->prepare("SELECT * FROM Todo WHERE id=1");
			//$result->execute(array($id));
			$result->execute();
			$row = $result->fetch(PDO::FETCH_ASSOC);*/
			
			//print_r($row);

			$wrow=array();

			$tables = $this->get_tables();
			$worktableTitle = '';
			foreach ($tables as $name => $value) {
				if ($value['id'] == $table_name)
					$worktableTitle=$value['name'];
			}
			
			$filter = 'camila_w1f=_I_id&camila_w1v='.$id.'&camila_w1c=eq';
			$filter = '"Cod. riga" uguale a "'.$id.'"';

			$obj = $this->client->query($worktableTitle, $this->lang, '', 'id', 0, 0, $id);
			//print_r($obj);

			$wrow = array();

			if ($obj)
			{

				$cols = $obj->table->cols;
				$wcols = array();
				foreach ($cols as $name => $value) {
					$wcols[$value->id]=$value->name;
				}

				$rows2 = $obj->table->rows;

				foreach ($rows2 as $name => $value) {

					foreach ($value->c as $n => $v) {
						$wrow[$wcols[$n]]=$v->v;
					}
				}
			
			}
			//print_r($wrow);			
			return $wrow;
		}
	}
	
	public function table_row_count($table_name) {
		if ($this->table_exists($table_name)) {
			/*$result = $this->database->prepare("SELECT COUNT(*) FROM ${table_name}");
			$result->execute();
			
			$count = $result->fetch(PDO::FETCH_COLUMN, 0);*/
			$count = 2;
			return $count;
		}
	}
	
	private function get_table_pk_column($table_name) {
		if ($this->table_exists($table_name)) {
			$columns = $this->get_columns($table_name);
			
			foreach ($columns as $column) {
				if (1 == $column['pk']) {
					return $column['name'];
				}
			}
		}
	}
	
	public function entry_update($table_name, $new_values) {
		$query = "";
		if (!$this->table_exists($table_name)) {
			return false;
		}
		
		$columns = $this->get_columns($table_name);
		
		$pk_column = $this->get_table_pk_column($table_name);
		
		if (!array_key_exists($pk_column, $new_values)) {
			echo "ERROR: No primary key";
			return;
		}
		
		$query = "UPDATE ${table_name} SET ";
		$update_values = array();
		
		foreach($columns as $column) {
			$name = $column['name'];
			if ($name === $pk_column) {
				continue;
			}
			
			if (array_key_exists($name, $new_values)) {
				$query .= "${name} = ?, ";
				$update_values[] = DataConverter::odata2Database($column, $new_values[$name]);
			} else {
				continue;
			}
		}
		
		$query = rtrim($query, ', ');
		
		$query .= " WHERE ${pk_column} = ?";
		$update_values[] = $new_values[$pk_column];
		
		$result = $this->database->prepare($query);
		$result->execute($update_values);
		
		return true;
	}
	
	public function entry_create($table_name, $new_values) {
		$query = "";
		if (!$this->table_exists($table_name)) {
			return false;
		}
		
		$columns = $this->get_columns($table_name);
		
		$pk_column = $this->get_table_pk_column($table_name);
		
		if (!array_key_exists($pk_column, $new_values) 
				|| strlen($new_values[$pk_column]) < 1) {
			$new_values[$pk_column] = $this->table_get_next_key($table_name, $pk_column);
		}
		
		$insert_values = array();
		$query = "INSERT INTO ${table_name} ( ";
		
		foreach($columns as $column) {
			$name = $column['name'];
			
			if (array_key_exists($name, $new_values)) {
				$query .= "${name}, ";
				$insert_values[] = DataConverter::odata2Database($column, $new_values[$name]);
			} else {
				continue;
			}
		}
		
		$query = rtrim($query, ', ');
		$query .= " ) VALUES ( ";
		foreach($columns as $column) {
			$query .= "?, ";
		}
		$query = rtrim($query, ', ');
		$query .= " )";
		
		$result = $this->database->prepare($query);
		$result->execute($insert_values);
		
		return true;
	}
	
	public function entry_delete($table_name, $id) {
		if (!$this->table_exists($table_name)) {
			return false;
		}
		$pk_column = $this->get_table_pk_column($table_name);
		
		$result = $this->database->prepare("DELETE FROM ${table_name} WHERE ${pk_column} = ?");
		$result->execute(array($id));
		return true;
	}
	
	private function table_get_next_key($table_name, $pk_column) {
		if ($this->table_exists($table_name)) {
			$result = $this->database->prepare("SELECT MAX(${pk_column}) FROM ${table_name}");
			$result->execute();
			
			$count = $result->fetch(PDO::FETCH_COLUMN, 0);
			return ($count + 1);
		}	
	}
}

?>