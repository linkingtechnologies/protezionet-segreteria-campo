<?php

/*
John Lim's ADOdb functions for TinyButStrong Template Engine
Version 1.00, 2004-09-22, Skrol29
http://www.tinybutstrong.com

Example:
	$conn = NewADOConnection('mysql');
	$conn->Connect('host', 'uid', 'pdw', 'db');
	... 
	$TBS->MergeBlock('blk1','adodb','SELECT * FROM t_examples');

Notes:
	- Before the merge, you have to open a connection using the global variable $conn (change it if you want into the custom function).
	- The keyword 'adodb' enables TBS to call the custom functions.
*/

function tbsdb_adodb_open(&$source,&$query) {
	global $conn;
	$conn->SetFetchMode(ADODB_FETCH_ASSOC); 
	$rs = $conn->Execute($query) ;
	return $rs ;
}

function tbsdb_adodb_fetch(&$rs) {
	return $rs->FetchRow() ;
}

function tbsdb_adodb_close(&$rs) {
	$rs->Close() ;
}


?>