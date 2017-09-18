<?php
require_once 'Constant.class.php';

class DatabaseAnalyzer {

	protected $database_name = '';

	/**
	  * Creates DatabaseAnalyzer for specific sqlite database.
	  *
	  * @param string $database_name
	  */
	public function __construct($database_name = '') {
		$this->database_name = $database_name;
		$this->database = new PDO('sqlite:'.$this->database_name);
		$this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function __destruct() {
		$this->database = null;
	}
	
	public function get_tables() {
		$result = $this->database->prepare("SELECT tbl_name FROM sqlite_master WHERE type = 'table' AND tbl_name NOT LIKE 'sqlite_%'");
		$result->execute();
		$tables = $result->fetchAll(PDO::FETCH_ASSOC);

		return $tables;
	}
	
	public function get_columns($table_name) {
		$result = $this->database->prepare("PRAGMA table_info(${table_name})");
		$result->execute();
		$columns = $result->fetchAll(PDO::FETCH_ASSOC);
		
		return $columns;
	}
	
	public function table_exists($table_name) {
		$result = $this->database->prepare("SELECT tbl_name FROM sqlite_master WHERE type = 'table' AND tbl_name NOT LIKE 'sqlite_%'");
		$result->execute();
		$tables = $result->fetchAll(PDO::FETCH_COLUMN, 0);
		
		return in_array($table_name, $tables);
	}
	
	public function table_get_rows($table_name) {
		if ($this->table_exists($table_name)) {
			$result = $this->database->prepare("SELECT * FROM ${table_name}");
			$result->execute();
			$rows = $result->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		}
	}
	
	public function table_get_entry($table_name, $pk_column, $id) {
		if ($this->table_exists($table_name)) {
			$result = $this->database->prepare("SELECT * FROM ${table_name} WHERE ${pk_column} = ?");
			$result->execute(array($id));
			$row = $result->fetch(PDO::FETCH_ASSOC);
			return $row;
		}
	}
	
	public function table_row_count($table_name) {
		if ($this->table_exists($table_name)) {
			$result = $this->database->prepare("SELECT COUNT(*) FROM ${table_name}");
			$result->execute();
			
			$count = $result->fetch(PDO::FETCH_COLUMN, 0);
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