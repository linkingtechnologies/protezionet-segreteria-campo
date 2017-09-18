<?php
class Constant {
	static $DATATYPE_MAPPING = array(
			"INTEGER" => "Edm.Int32",
			"TEXT" => "Edm.String",
			"BOOL" => "Edm.Boolean",
			"string" => "Edm.String",
			"phonenumber" => "Edm.String",
			"textarea" => "Edm.String",
			"date" => "Edm.String",
			"string-listofvalues" => "Edm.String",
			"autoincrement" => "Edm.Int32",
			"integer" => "Edm.Int32",
			"integer-listofvalues" => "Edm.Int32",
			"datetime" => "Edm.String",
			"formula" => "Edm.String",
			"query" => "Edm.String"
		);
}

class DataConverter {
    private function __construct() {}
	
	public static function database2OData($column, $value) {
		if (!is_array($column) || !array_key_exists('type', $column)) {
			return null;
		}
		
		if ($column['type'] == 'BOOL') {
			return DataConverter::booleanDB2OData($value);
		} else {
			return $value;
		}
	}
	
	public static function odata2Database($column, $value) {
		if (!is_array($column) || !array_key_exists('type', $column)) {
			return null;
		}
		
		if ($column['type'] == 'BOOL') {
			return DataConverter::booleanOData2DB($value);
		} else {
			return $value;
		}
	}

    public static function booleanDB2OData($db_value) {
		return ($db_value === 1) ? "true" : "false";
    }
	
	public static function booleanOData2DB($odata_value) {
		return ($odata_value && strtolower($odata_value) === "true") ? 1 : 0;
	}
}

?>