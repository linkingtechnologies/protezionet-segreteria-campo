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

/*
$Id: Latin1UTF8.php 2007/07/09 $

Stefan Fischerländer <stefan@fischerlaender.de>
http://www.fischerlaender.net

A simple class that tries to sanitize text which contains parts in different encodings.

*/

//require_once(CAMILA_LIB_DIR . 'm2translator/M2Translator.class.php');


class Latin1UTF8
{
    
    var $latin1_to_utf8;
    var $utf8_to_latin1;

    function Latin1UTF8()
    {
        for ($i = 32; $i <= 255; $i++) {
            $this->latin1_to_utf8[chr($i)]              = utf8_encode(chr($i));
            $this->utf8_to_latin1[utf8_encode(chr($i))] = chr($i);
        }
    }
    
    function mixed_to_latin1($text)
    {
        foreach ($this->utf8_to_latin1 as $key => $val) {
            $text = str_replace($key, $val, $text);
        }
        return $text;
    }
    
    function mixed_to_utf8($text)
    {
        return utf8_encode($this->mixed_to_latin1($text));
    }
}


function camila_configurator_worktable_description_db_onupdate($lform)
{
    global $_CAMILA;

    $record             = Array();
    $record['category'] = $lform->fields['short_title']->value;
    
    $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_WORKT, $record, 'UPDATE', 'category_scriptname=' . $_CAMILA['db']->qstr($lform->fields['page_url']->value));
    
    if (!$updateSQL) {
        camila_information_text(camila_get_translation('camila.worktable.db.error'));
    }
    
    
    return true;
}



function camila_configurator_worktable_title_db_onupdate($lform)
{
    global $_CAMILA;

    $result = $_CAMILA['db']->Execute('select scriptname from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($lform->fields['id']->value));
    if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
    
    $scriptname = $result->fields['scriptname'];
    
    $record = Array();
    if ($lform->fields['short_title']->value != '')
        $record['short_title'] = $lform->fields['short_title']->value;
    if ($lform->fields['full_title']->value != '')
        $record['full_title'] = $lform->fields['full_title']->value;
    
    $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_PLANG, $record, 'UPDATE', 'page_url=' . $_CAMILA['db']->qstr($scriptname) . ' and lang=' . $_CAMILA['db']->qstr($_CAMILA['lang']));
    
    if (!$updateSQL) {
        camila_information_text(camila_get_translation('camila.worktable.db.error'));
    }
    
    if ($lform->fields['sequence']->value != '') {
        $record['label_order'] = $lform->fields['sequence']->value;
        $updateSQL             = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url=' . $_CAMILA['db']->qstr($scriptname));
        
        if (!$updateSQL)
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
    }
    
    
    if ($lform->fields['category']->value != '' && $lform->fields['category']->value != ' ') {
        
        $result = $_CAMILA['db']->Execute('select page_url from ' . CAMILA_TABLE_PLANG . ' where short_title=' . $_CAMILA['db']->qstr($lform->fields['category']->value) . ' AND page_url LIKE ' . $_CAMILA['db']->qstr('cf_app.php?cat%') . ' and lang=' . $_CAMILA['db']->qstr($_CAMILA['lang']));
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
        
        $caturl = $result->fields['page_url'];
        
        $record                        = Array();
        $record['category_scriptname'] = $caturl;
        
        $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_WORKT, $record, 'UPDATE', 'id=' . $_CAMILA['db']->qstr($lform->fields['id']->value));
        if (!$updateSQL)
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
        
        $record           = Array();
        $record['parent'] = $caturl;
        
        $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url=' . $_CAMILA['db']->qstr($scriptname));
        if (!$updateSQL)
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
        
        $record            = Array();
        $record['visible'] = 'yes';
        
        $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url=' . $_CAMILA['db']->qstr($caturl));
        if (!$updateSQL)
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
        
    } else {
        
        $record           = Array();
        $record['parent'] = '';
        
        $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url=' . $_CAMILA['db']->qstr($scriptname));
        if (!$updateSQL)
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
        
    }
    
    $result = $_CAMILA['db']->Execute('update ' . CAMILA_TABLE_PAGES . ' SET visible=' . $_CAMILA['db']->qstr('no') . ' where url NOT IN (select distinct category_scriptname from ' . CAMILA_TABLE_WORKT . ') AND url LIKE ' . $_CAMILA['db']->qstr('cf_app.php?cat%'));
    if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
    
    
    $record = Array();
    if ($lform->fields['share_key']->value != '')
        $record['share_key'] = $lform->fields['share_key']->value;
    if ($lform->fields['share_caninsert']->value != '')
        $record['share_caninsert'] = $lform->fields['share_caninsert']->value;
    if ($lform->fields['share_canupdate']->value != '')
        $record['share_canupdate'] = $lform->fields['share_canupdate']->value;
    if ($lform->fields['share_candelete']->value != '')
        $record['share_candelete'] = $lform->fields['share_candelete']->value;
	
	if ($lform->fields['filter']->value != '')
        $record['filter'] = $lform->fields['share_candelete']->value;
    
    $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url=' . $_CAMILA['db']->qstr($scriptname));
    
    if (!$updateSQL) {
        camila_information_text(camila_get_translation('camila.worktable.db.error'));
    }
    
    
    return true;
}


function camila_configurator_reconfig(&$field, &$row, $fields)
{
    global $_CAMILA;
    $myLink = new CHAW_link(camila_get_translation('camila.worktable.reconfig'), 'cf_worktable_wizard_step2.php?camila_custom=' . $field->value);
    $myLink->set_br(0);
    $row->add_column($myLink);
}


function camila_configurator_delete(&$field, &$row, $fields)
{
    global $_CAMILA;
    $myLink = new CHAW_link(camila_get_translation('camila.worktable.delete'), 'cf_worktable_admin.php?camila_custom=' . $field->value . '&camila_worktable_op=delete');
    $myLink->set_br(0);
    $row->add_column($myLink);
}


function camila_configurator_db_ondelete_field($lform)
{
    global $_CAMILA;

    $result = $_CAMILA['db']->Execute('update ' . CAMILA_TABLE_WORKC . ' set is_deleted=' . $_CAMILA['db']->qstr('y') . ' where id=' . $_CAMILA['db']->qstr($_REQUEST[CAMILA_TABLE_WORKC . '_id']));
    if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

	$myText = new CHAW_text('');
	$_CAMILA['page']->add_text($myText);

    camila_information_text(camila_get_translation('camila.form.data.deleted'));
    return true;
}


function camila_configurator_import(&$field, &$row, $fields)
{
    global $_CAMILA;
    $myLink = new CHAW_link(camila_get_translation('camila.worktable.import'), 'cf_worktable_wizard_step4.php?camila_custom=' . $field->value);
    $myLink->set_br(0);
    $row->add_column($myLink);
}


function camila_configurator_rebuild(&$field, &$row, $fields)
{
    global $_CAMILA;
    $myLink = new CHAW_link(camila_get_translation('camila.worktable.rebuild'), 'cf_worktable_admin.php?camila_custom=' . $field->value . '&camila_worktable_op=rebuild');
    $myLink->set_br(0);
    $row->add_column($myLink);
}


function camila_configurator_templates(&$field, &$row, $fields)
{
    global $_CAMILA;
    $myLink = new CHAW_link(camila_get_translation('camila.worktable.showtemplatefields'), 'cf_worktable_admin.php?camila_custom=' . $field->value . '&camila_worktable_op=showtemplatefields');
    $myLink->set_br(0);
    $row->add_column($myLink);
}


function camila_configurator_template_fieldname_form(&$field, &$row, $fields)
{
    global $_CAMILA;
    $myText = new CHAW_text('${worktable' . $fields['wt_id']->value . '_' . $field->value . '}');
    $myText->set_br(0);
    $row->add_column($myText);
}


function camila_configurator_template_fieldname_table(&$field, &$row, $fields)
{
    global $_CAMILA;
    $myText = new CHAW_text('${' . $field->value . '}');
    $myText->set_br(0);
    $row->add_column($myText);
}


class configurator
{
    
    var $default_maxlength = Array('integer' => 5, 'string' => 255, 'date' => '10', 'hyperlink' => 250);
    var $default_size = Array('integer' => 5, 'string' => 30, 'date' => 10, 'hyperlink' => 30, 'phonenumber' => 30, 'autoincrement' => 5, 'formula' => 1, 'query' => 1);
    var $column_size = Array('C' => 250);
    
    var $fields = Array();
    var $requires = Array();
    var $boundsheets = Array();
    var $interactive = true;
    var $db;
    var $menuitems_script;
    
    //thanks to http://drupal.org/node/141051
    var $sql_reserved_words = Array('A', 'ABORT', 'ABS', 'ABSOLUTE', 'ACCESS', 'ACTION', 'ADA', 'ADD', 'ADMIN', 'AFTER', 'AGGREGATE', 'ALIAS', 'ALL', 'ALLOCATE', 'ALSO', 'ALTER', 'ALWAYS', 'ANALYSE', 'ANALYZE', 'AND', 'ANY', 'ARE', 'ARRAY', 'AS', 'ASC', 'ASENSITIVE', 'ASSERTION', 'ASSIGNMENT', 'ASYMMETRIC', 'AT', 'ATOMIC', 'ATTRIBUTE', 'ATTRIBUTES', 'AUDIT', 'AUTHORIZATION', 'AUTO_INCREMENT', 'AVG', 'AVG_ROW_LENGTH', 'BACKUP', 'BACKWARD', 'BEFORE', 'BEGIN', 'BERNOULLI', 'BETWEEN', 'BIGINT', 'BINARY', 'BIT', 'BIT_LENGTH', 'BITVAR', 'BLOB', 'BOOL', 'BOOLEAN', 'BOTH', 'BREADTH', 'BREAK', 'BROWSE', 'BULK', 'BY', 'C', 'CACHE', 'CALL', 'CALLED', 'CARDINALITY', 'CASCADE', 'CASCADED', 'CASE', 'CAST', 'CATALOG', 'CATALOG_NAME', 'CEIL', 'CEILING', 'CHAIN', 'CHANGE', 'CHAR', 'CHAR_LENGTH', 'CHARACTER', 'CHARACTER_LENGTH', 'CHARACTER_SET_CATALOG', 'CHARACTER_SET_NAME', 'CHARACTER_SET_SCHEMA', 'CHARACTERISTICS', 'CHARACTERS', 'CHECK', 'CHECKED', 'CHECKPOINT', 'CHECKSUM', 'CLASS', 'CLASS_ORIGIN', 'CLOB', 'CLOSE', 'CLUSTER', 'CLUSTERED', 'COALESCE', 'COBOL', 'COLLATE', 'COLLATION', 'COLLATION_CATALOG', 'COLLATION_NAME', 'COLLATION_SCHEMA', 'COLLECT', 'COLUMN', 'COLUMN_NAME', 'COLUMNS', 'COMMAND_FUNCTION', 'COMMAND_FUNCTION_CODE', 'COMMENT', 'COMMIT', 'COMMITTED', 'COMPLETION', 'COMPRESS', 'COMPUTE', 'CONDITION', 'CONDITION_NUMBER', 'CONNECT', 'CONNECTION', 'CONNECTION_NAME', 'CONSTRAINT', 'CONSTRAINT_CATALOG', 'CONSTRAINT_NAME', 'CONSTRAINT_SCHEMA', 'CONSTRAINTS', 'CONSTRUCTOR', 'CONTAINS', 'CONTAINSTABLE', 'CONTINUE', 'CONVERSION', 'CONVERT', 'COPY', 'CORR', 'CORRESPONDING', 'COUNT', 'COVAR_POP', 'COVAR_SAMP', 'CREATE', 'CREATEDB', 'CREATEROLE', 'CREATEUSER', 'CROSS', 'CSV', 'CUBE', 'CUME_DIST', 'CURRENT', 'CURRENT_DATE', 'CURRENT_DEFAULT_TRANSFORM_GROUP', 'CURRENT_PATH', 'CURRENT_ROLE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'CURRENT_TRANSFORM_GROUP_FOR_TYPE', 'CURRENT_USER', 'CURSOR', 'CURSOR_NAME', 'CYCLE', 'DATA', 'DATABASE', 'DATABASES', 'DATE', 'DATETIME', 'DATETIME_INTERVAL_CODE', 'DATETIME_INTERVAL_PRECISION', 'DAY', 'DAY_HOUR', 'DAY_MICROSECOND', 'DAY_MINUTE', 'DAY_SECOND', 'DAYOFMONTH', 'DAYOFWEEK', 'DAYOFYEAR', 'DBCC', 'DEALLOCATE', 'DEC', 'DECIMAL', 'DECLARE', 'DEFAULT', 'DEFAULTS', 'DEFERRABLE', 'DEFERRED', 'DEFINED', 'DEFINER', 'DEGREE', 'DELAY_KEY_WRITE', 'DELAYED', 'DELETE', 'DELIMITER', 'DELIMITERS', 'DENSE_RANK', 'DENY', 'DEPTH', 'DEREF', 'DERIVED', 'DESC', 'DESCRIBE', 'DESCRIPTOR', 'DESTROY', 'DESTRUCTOR', 'DETERMINISTIC', 'DIAGNOSTICS', 'DICTIONARY', 'DISABLE', 'DISCONNECT', 'DISK', 'DISPATCH', 'DISTINCT', 'DISTINCTROW', 'DISTRIBUTED', 'DIV', 'DO', 'DOMAIN', 'DOUBLE', 'DROP', 'DUAL', 'DUMMY', 'DUMP', 'DYNAMIC', 'DYNAMIC_FUNCTION', 'DYNAMIC_FUNCTION_CODE', 'EACH', 'ELEMENT', 'ELSE', 'ELSEIF', 'ENABLE', 'ENCLOSED', 'ENCODING', 'ENCRYPTED', 'END', 'END-EXEC', 'ENUM', 'EQUALS', 'ERRLVL', 'ESCAPE', 'ESCAPED', 'EVERY', 'EXCEPT', 'EXCEPTION', 'EXCLUDE', 'EXCLUDING', 'EXCLUSIVE', 'EXEC', 'EXECUTE', 'EXISTING', 'EXISTS', 'EXIT', 'EXP', 'EXPLAIN', 'EXTERNAL', 'EXTRACT', 'FALSE', 'FETCH', 'FIELDS', 'FILE', 'FILLFACTOR', 'FILTER', 'FINAL', 'FIRST', 'FLOAT', 'FLOAT4', 'FLOAT8', 'FLOOR', 'FLUSH', 'FOLLOWING', 'FOR', 'FORCE', 'FOREIGN', 'FORTRAN', 'FORWARD', 'FOUND', 'FREE', 'FREETEXT', 'FREETEXTTABLE', 'FREEZE', 'FROM', 'FULL', 'FULLTEXT', 'FUNCTION', 'FUSION', 'G', 'GENERAL', 'GENERATED', 'GET', 'GLOBAL', 'GO', 'GOTO', 'GRANT', 'GRANTED', 'GRANTS', 'GREATEST', 'GROUP', 'GROUPING', 'HANDLER', 'HAVING', 'HEADER', 'HEAP', 'HIERARCHY', 'HIGH_PRIORITY', 'HOLD', 'HOLDLOCK', 'HOST', 'HOSTS', 'HOUR', 'HOUR_MICROSECOND', 'HOUR_MINUTE', 'HOUR_SECOND', 'IDENTIFIED', 'IDENTITY', 'IDENTITY_INSERT', 'IDENTITYCOL', 'IF', 'IGNORE', 'ILIKE', 'IMMEDIATE', 'IMMUTABLE', 'IMPLEMENTATION', 'IMPLICIT', 'IN', 'INCLUDE', 'INCLUDING', 'INCREMENT', 'INDEX', 'INDICATOR', 'INFILE', 'INFIX', 'INHERIT', 'INHERITS', 'INITIAL', 'INITIALIZE', 'INITIALLY', 'INNER', 'INOUT', 'INPUT', 'INSENSITIVE', 'INSERT', 'INSERT_ID', 'INSTANCE', 'INSTANTIABLE', 'INSTEAD', 'INT', 'INT1', 'INT2', 'INT3', 'INT4', 'INT8', 'INTEGER', 'INTERSECT', 'INTERSECTION', 'INTERVAL', 'INTO', 'INVOKER', 'IS', 'ISAM', 'ISNULL', 'ISOLATION', 'ITERATE', 'JOIN', 'K', 'KEY', 'KEY_MEMBER', 'KEY_TYPE', 'KEYS', 'KILL', 'LANCOMPILER', 'LANGUAGE', 'LARGE', 'LAST', 'LAST_INSERT_ID', 'LATERAL', 'LEADING', 'LEAST', 'LEAVE', 'LEFT', 'LENGTH', 'LESS', 'LEVEL', 'LIKE', 'LIMIT', 'LINENO', 'LINES', 'LISTEN', 'LN', 'LOAD', 'LOCAL', 'LOCALTIME', 'LOCALTIMESTAMP', 'LOCATION', 'LOCATOR', 'LOCK', 'LOGIN', 'LOGS', 'LONG', 'LONGBLOB', 'LONGTEXT', 'LOOP', 'LOW_PRIORITY', 'LOWER', 'M', 'MAP', 'MATCH', 'MATCHED', 'MAX', 'MAX_ROWS', 'MAXEXTENTS', 'MAXVALUE', 'MEDIUMBLOB', 'MEDIUMINT', 'MEDIUMTEXT', 'MEMBER', 'MERGE', 'MESSAGE_LENGTH', 'MESSAGE_OCTET_LENGTH', 'MESSAGE_TEXT', 'METHOD', 'MIDDLEINT', 'MIN', 'MIN_ROWS', 'MINUS', 'MINUTE', 'MINUTE_MICROSECOND', 'MINUTE_SECOND', 'MINVALUE', 'MLSLABEL', 'MOD', 'MODE', 'MODIFIES', 'MODIFY', 'MODULE', 'MONTH', 'MONTHNAME', 'MORE', 'MOVE', 'MULTISET', 'MUMPS', 'MYISAM', 'NAME', 'NAMES', 'NATIONAL', 'NATURAL', 'NCHAR', 'NCLOB', 'NESTING', 'NEW', 'NEXT', 'NO', 'NO_WRITE_TO_BINLOG', 'NOAUDIT', 'NOCHECK', 'NOCOMPRESS', 'NOCREATEDB', 'NOCREATEROLE', 'NOCREATEUSER', 'NOINHERIT', 'NOLOGIN', 'NONCLUSTERED', 'NONE', 'NORMALIZE', 'NORMALIZED', 'NOSUPERUSER', 'NOT', 'NOTHING', 'NOTIFY', 'NOTNULL', 'NOWAIT', 'NULL', 'NULLABLE', 'NULLIF', 'NULLS', 'NUMBER', 'NUMERIC', 'OBJECT', 'OCTET_LENGTH', 'OCTETS', 'OF', 'OFF', 'OFFLINE', 'OFFSET', 'OFFSETS', 'OIDS', 'OLD', 'ON', 'ONLINE', 'ONLY', 'OPEN', 'OPENDATASOURCE', 'OPENQUERY', 'OPENROWSET', 'OPENXML', 'OPERATION', 'OPERATOR', 'OPTIMIZE', 'OPTION', 'OPTIONALLY', 'OPTIONS', 'OR', 'ORDER', 'ORDERING', 'ORDINALITY', 'OTHERS', 'OUT', 'OUTER', 'OUTFILE', 'OUTPUT', 'OVER', 'OVERLAPS', 'OVERLAY', 'OVERRIDING', 'OWNER', 'PACK_KEYS', 'PAD', 'PARAMETER', 'PARAMETER_MODE', 'PARAMETER_NAME', 'PARAMETER_ORDINAL_POSITION', 'PARAMETER_SPECIFIC_CATALOG', 'PARAMETER_SPECIFIC_NAME', 'PARAMETER_SPECIFIC_SCHEMA', 'PARAMETERS', 'PARTIAL', 'PARTITION', 'PASCAL', 'PASSWORD', 'PATH', 'PCTFREE', 'PERCENT', 'PERCENT_RANK', 'PERCENTILE_CONT', 'PERCENTILE_DISC', 'PLACING', 'PLAN', 'PLI', 'POSITION', 'POSTFIX', 'POWER', 'PRECEDING', 'PRECISION', 'PREFIX', 'PREORDER', 'PREPARE', 'PREPARED', 'PRESERVE', 'PRIMARY', 'PRINT', 'PRIOR', 'PRIVILEGES', 'PROC', 'PROCEDURAL', 'PROCEDURE', 'PROCESS', 'PROCESSLIST', 'PUBLIC', 'PURGE', 'QUOTE', 'RAID0', 'RAISERROR', 'RANGE', 'RANK', 'RAW', 'READ', 'READS', 'READTEXT', 'REAL', 'RECHECK', 'RECONFIGURE', 'RECURSIVE', 'REF', 'REFERENCES', 'REFERENCING', 'REGEXP', 'REGR_AVGX', 'REGR_AVGY', 'REGR_COUNT', 'REGR_INTERCEPT', 'REGR_R2', 'REGR_SLOPE', 'REGR_SXX', 'REGR_SXY', 'REGR_SYY', 'REINDEX', 'RELATIVE', 'RELEASE', 'RELOAD', 'RENAME', 'REPEAT', 'REPEATABLE', 'REPLACE', 'REPLICATION', 'REQUIRE', 'RESET', 'RESIGNAL', 'RESOURCE', 'RESTART', 'RESTORE', 'RESTRICT', 'RESULT', 'RETURN', 'RETURNED_CARDINALITY', 'RETURNED_LENGTH', 'RETURNED_OCTET_LENGTH', 'RETURNED_SQLSTATE', 'RETURNS', 'REVOKE', 'RIGHT', 'RLIKE', 'ROLE', 'ROLLBACK', 'ROLLUP', 'ROUTINE', 'ROUTINE_CATALOG', 'ROUTINE_NAME', 'ROUTINE_SCHEMA', 'ROW', 'ROW_COUNT', 'ROW_NUMBER', 'ROWCOUNT', 'ROWGUIDCOL', 'ROWID', 'ROWNUM', 'ROWS', 'RULE', 'SAVE', 'SAVEPOINT', 'SCALE', 'SCHEMA', 'SCHEMA_NAME', 'SCHEMAS', 'SCOPE', 'SCOPE_CATALOG', 'SCOPE_NAME', 'SCOPE_SCHEMA', 'SCROLL', 'SEARCH', 'SECOND', 'SECOND_MICROSECOND', 'SECTION', 'SECURITY', 'SELECT', 'SELF', 'SENSITIVE', 'SEPARATOR', 'SEQUENCE', 'SERIALIZABLE', 'SERVER_NAME', 'SESSION', 'SESSION_USER', 'SET', 'SETOF', 'SETS', 'SETUSER', 'SHARE', 'SHOW', 'SHUTDOWN', 'SIGNAL', 'SIMILAR', 'SIMPLE', 'SIZE', 'SMALLINT', 'SOME', 'SONAME', 'SOURCE', 'SPACE', 'SPATIAL', 'SPECIFIC', 'SPECIFIC_NAME', 'SPECIFICTYPE', 'SQL', 'SQL_BIG_RESULT', 'SQL_BIG_SELECTS', 'SQL_BIG_TABLES', 'SQL_CALC_FOUND_ROWS', 'SQL_LOG_OFF', 'SQL_LOG_UPDATE', 'SQL_LOW_PRIORITY_UPDATES', 'SQL_SELECT_LIMIT', 'SQL_SMALL_RESULT', 'SQL_WARNINGS', 'SQLCA', 'SQLCODE', 'SQLERROR', 'SQLEXCEPTION', 'SQLSTATE', 'SQLWARNING', 'SQRT', 'SSL', 'STABLE', 'START', 'STARTING', 'STATE', 'STATEMENT', 'STATIC', 'STATISTICS', 'STATUS', 'STDDEV_POP', 'STDDEV_SAMP', 'STDIN', 'STDOUT', 'STORAGE', 'STRAIGHT_JOIN', 'STRICT', 'STRING', 'STRUCTURE', 'STYLE', 'SUBCLASS_ORIGIN', 'SUBLIST', 'SUBMULTISET', 'SUBSTRING', 'SUCCESSFUL', 'SUM', 'SUPERUSER', 'SYMMETRIC', 'SYNONYM', 'SYSDATE', 'SYSID', 'SYSTEM', 'SYSTEM_USER', 'TABLE', 'TABLE_NAME', 'TABLES', 'TABLESAMPLE', 'TABLESPACE', 'TEMP', 'TEMPLATE', 'TEMPORARY', 'TERMINATE', 'TERMINATED', 'TEXT', 'TEXTSIZE', 'THAN', 'THEN', 'TIES', 'TIME', 'TIMESTAMP', 'TIMEZONE_HOUR', 'TIMEZONE_MINUTE', 'TINYBLOB', 'TINYINT', 'TINYTEXT', 'TO', 'TOAST', 'TOP', 'TOP_LEVEL_COUNT', 'TRAILING', 'TRAN', 'TRANSACTION', 'TRANSACTION_ACTIVE', 'TRANSACTIONS_COMMITTED', 'TRANSACTIONS_ROLLED_BACK', 'TRANSFORM', 'TRANSFORMS', 'TRANSLATE', 'TRANSLATION', 'TREAT', 'TRIGGER', 'TRIGGER_CATALOG', 'TRIGGER_NAME', 'TRIGGER_SCHEMA', 'TRIM', 'TRUE', 'TRUNCATE', 'TRUSTED', 'TSEQUAL', 'TYPE', 'UESCAPE', 'UID', 'UNBOUNDED', 'UNCOMMITTED', 'UNDER', 'UNDO', 'UNENCRYPTED', 'UNION', 'UNIQUE', 'UNKNOWN', 'UNLISTEN', 'UNLOCK', 'UNNAMED', 'UNNEST', 'UNSIGNED', 'UNTIL', 'UPDATE', 'UPDATETEXT', 'UPPER', 'USAGE', 'USE', 'USER', 'USER_DEFINED_TYPE_CATALOG', 'USER_DEFINED_TYPE_CODE', 'USER_DEFINED_TYPE_NAME', 'USER_DEFINED_TYPE_SCHEMA', 'USING', 'UTC_DATE', 'UTC_TIME', 'UTC_TIMESTAMP', 'VACUUM', 'VALID', 'VALIDATE', 'VALIDATOR', 'VALUE', 'VALUES', 'VAR_POP', 'VAR_SAMP', 'VARBINARY', 'VARCHAR', 'VARCHAR2', 'VARCHARACTER', 'VARIABLE', 'VARIABLES', 'VARYING', 'VERBOSE', 'VIEW', 'VOLATILE', 'WAITFOR', 'WHEN', 'WHENEVER', 'WHERE', 'WHILE', 'WIDTH_BUCKET', 'WINDOW', 'WITH', 'WITHIN', 'WITHOUT', 'WORK', 'WRITE', 'WRITETEXT', 'X509', 'XOR', 'YEAR', 'YEAR_MONTH', 'ZEROFILL', 'ZONE', 'created', 'created_by', 'created_src', 'created_by_surname', 'created_by_name', 'last_upd', 'last_upd_by', 'last_upd_src', 'grp', 'last_upd_by_name', 'last_upd_by_surname', 'mod_num', 'is_deleted', 'info', 'cf_bool_is_selected', 'cf_bool_is_special', 'share_key', 'share_canupdate', 'share_caninsert', 'share_candelete', 'token');
    
    
    
    function translation_init()
    {
        $this->i18n = new M2Translator($_REQUEST['lang'], CAMILA_DIR . 'lang/');
    }
    
    
    function camila_get_translation($string)
    {
        if ($this->interactive)
            return camila_get_translation($string);
        
        if (!is_object($this->i18n))
            $this->translation_init();
        
        if ($this->i18n->get($string) != '*' . $string . '*')
            return $this->i18n->get($string);
        else
            return '';
    }
	
	function camila_information_text($text)
	{
		if (function_exists(camila_information_text))
			camila_information_text($text);
		else
			echo $text;
	}
    
    
    function camila_get_translation_array($options_string)
    {
        
        $arr1 = explode(',', $this->camila_get_translation($options_string));
        
        $tr = Array();
        foreach ($arr1 as $name => $value) {
            $arr2         = explode(';', $value);
            $tr[$arr2[0]] = $arr2[1];
        }
        
        return $tr;
    }
    
    
    function start_wizard($source = 'xls')
    {
        
        //require_once(CAMILA_DIR . 'datagrid/form.class.php');
        
        //require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/filebox.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/static_listbox.php');
        
        global $_CAMILA;
        
        if ($_REQUEST['configurator_step'] == '2' && (($_REQUEST['configurator_wtname'] != '' && $_REQUEST['configurator_wtdesc'] != '') || $_REQUEST['configurator_filename'] != '')) {
            $this->xls_read();
        } else {
            
            $form               = new phpform('configurator');
            $form->submitbutton = camila_get_translation('camila.worktable.xls.submit');
            $form->drawrules    = false;
            new form_hidden($form, 'step', '1');
            new form_hidden($form, 'sheet', '1');
            new form_hidden($form, 'wtname', camila_get_translation('camila.worktable.name.predefault'));
            new form_hidden($form, 'wtdesc', camila_get_translation('camila.worktable.desc.predefault'));
            new form_filebox($form, 'file', camila_get_translation('camila.worktable.xls.choose'), 50, CAMILA_TMP_DIR);
            
            $fp = $form->process();
            
            if (isset($form->fields['file']->value))
                $file = $form->fields['file']->value[0];
            else
                $file = $_REQUEST['configurator_filename'];
            
            if ($file != '' || $_REQUEST['camila_noxlsfile'] == 'y') {
                if (!($_REQUEST['camila_noxlsfile'] == 'y')) {
					$myText = new CHAW_text('');
					$_CAMILA['page']->add_text($myText);

                    $sheets = $this->xls_count_sheets(CAMILA_TMP_DIR . '/' . $file);
                    
                    $sheet_list = '';
                    for ($i = 0; $i < $sheets; $i++) {
                        if ($i > 0)
                            $sheet_list .= ',';
                        $sheet_list .= ($i + 1) . ';' . ($i + 1) . ' - ' . $this->boundsheets[$i]['name'];
                    }
                    
                    $myText = new CHAW_text(camila_get_translation('camila.wizard.choosesheetnum'));
                    $myText->set_br(2);
                    $_CAMILA['page']->add_text($myText);
                } else {
					$myText = new CHAW_text('');
					$_CAMILA['page']->add_text($myText);
                    $myText = new CHAW_text(camila_get_translation('camila.wizard.choosedescandtitle'));
                    $myText->set_br(2);
                    $_CAMILA['page']->add_text($myText);
                }
	
                $form2               = new phpform('configurator');
                $form2->submitbutton = camila_get_translation('camila.wizard.next');
                $form2->drawrules    = false;
                
                if (!($_REQUEST['camila_noxlsfile'] == 'y'))
				{
                    new form_static_listbox($form2, 'sheet', camila_get_translation('camila.worktable.xls.sheetnum'), $sheet_list);
				}
                else {
                    new form_textbox($form2, 'wtname', camila_get_translation('camila.worktable.name'), true, 20, 20);
                    new form_textbox($form2, 'wtdesc', camila_get_translation('camila.worktable.desc'), true, 20, 30);
                }
                new form_hidden($form2, 'step', '2');
                new form_hidden($form2, 'filename', $file);
				
				if (is_object($form2->fields['sheet'])) {
					$form2->fields['sheet']->set_br(2);
				}
                
                $form2->process();
                $form2->fields['step']->value = 2;
                
                $form2->draw();
            } else {
				$myText = new CHAW_text('');
				$_CAMILA['page']->add_text($myText);
			
                $myText = new CHAW_text(camila_get_translation('camila.wizard.choosexlsfile'));
                $myText->set_br(2);
                $_CAMILA['page']->add_text($myText);
                
                $form->draw();
                $myText = new CHAW_text(camila_get_translation('camila.wizard.or'));
                $myText->set_br(0);
                $_CAMILA['page']->add_text($myText);
                
                $myLink = new CHAW_link(camila_get_translation('camila.wizard.skip'), $_SERVER['PHP_SELF'] . '?camila_noxlsfile=y');
                $myLink->set_br(2);
                $_CAMILA['page']->add_link($myLink);
                
            }
        }
        
    }
    
    
    function xls_count_sheets($file)
    {
        require_once(CAMILA_LIB_DIR . 'php-excel-reader/excel_reader2.php');
        
        $data              = new Spreadsheet_Excel_Reader($file, false);
        $this->boundsheets = $data->boundsheets;
        return count($data->sheets);
        
    }
    
    
    function xls_read($sequence = -1)
    {
        global $_CAMILA;
        
        if ($this->interactive) {
            $filename = $_REQUEST['configurator_filename'];
            $sheetnum = $_REQUEST['configurator_sheet'] - 1;
            $name     = $_REQUEST['configurator_wtname'];
            $desc     = $_REQUEST['configurator_wtdesc'];
            $this->db = $_CAMILA['db'];
        } else {
            $filename = $this->filename;
            $sheetnum = $this->sheetnum;
            $name     = 'name';
            $desc     = 'desc';
        }
        
        $success = true;
        
        $result = $this->db->Execute('select max(id) as id from ' . CAMILA_TABLE_WORKT);
        if ($result === false)
		{
			//echo 'select max(id) as id from ' . CAMILA_TABLE_WORKT;
			if (function_exists(camila_error_page))
				camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
			else
				echo ($this->camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
		}

        
        $id                    = intval($result->fields['id']) + 1;
        $record['id']          = $id;
        $record['status']      = 'd';
        $record['active']      = 'n';
        $record['filename']    = basename($filename);
        $record['sheetnum']    = $sheetnum;
        $record['scriptname']  = 'cf_worktable' . $id . '.php';
        $record['short_title'] = $name;
        $record['full_title']  = $desc;
        $record['tablename']   = CAMILA_TABLE_WORKP . $id;
        $record['sequence']    = ($sequence > 0) ? $sequence : $id;
        
        $insertSQL = $this->db->AutoExecute(CAMILA_TABLE_WORKT, $record, 'INSERT');
        if (!$insertSQL) {
            $this->camila_information_text($this->camila_get_translation('camila.worktable.db.error'));
            $success = false;
        }
        
        if ($filename != '') {
            require_once(CAMILA_LIB_DIR . 'php-excel-reader/excel_reader2.php');
            
            if ($this->interactive)
                $data = new Spreadsheet_Excel_Reader(CAMILA_TMP_DIR . '/' . $filename);
            else
                $data = new Spreadsheet_Excel_Reader($filename);
            
            $fmt     = str_replace(Array(
                'd',
                'm',
                'y'
            ), Array(
                'dd',
                'mm',
                'yyyy'
            ), strtolower($_CAMILA['date_format']));
            $datefmt = preg_replace('/[^a-z0-9]/', '', $fmt);
            
            $i = 1;
            while ($data->val(1, $i, $sheetnum) != '') {
                $cols[$i - 1] = $data->val(1, $i, $sheetnum);
                
                //print_r($data->sheets[$sheetnum]['cellsInfo']);
                for ($j = 1; $j <= $data->rowcount($sheetnum); $j++) {
                    $curr = $data->type($j, $i, $sheetnum);
                    
                    if ($data->sheets[$sheetnum]['cellsInfo'][$j][$i]['hyperlink']['link'] != '')
                        $curr = 'hyperlink';
                    
                    if ($curr != '') {
                        $safefmt = strtolower(preg_replace('/[^a-z0-9]/', '', $data->read16bitstring($data->sheets[$sheetnum]['cellsInfo'][$j][$i]['format'], 0)));
                        //echo $data->read16bitstring($data->sheets[$sheetnum]['cellsInfo'][$j][$i]['format'],0);
                        
                        if ($curr == 'unknown' && $safefmt == $datefmt)
                            $curr = 'date';
                        
                        $types[$i - 1] = $curr;
                        continue;
                        
                    }
                    
                }
                
                $i++;
            }
            
            if (!$this->interactive || $sheetnum + 2 <= count($data->sheets) && $this->is_configuration_sheet($data, $sheetnum + 1)) {
                $colNum   = count($cols);
                $fieldArr = Array();
                for ($i = 0; $i < $colNum; $i++) {
                    //echo $data->val(2, 2 + $i, $sheetnum + 1);
                    $name                        = $data->val(1, 2 + $i, $sheetnum + 1);
                    $sequence                    = $data->val(2, 2 + $i, $sheetnum + 1);
                    $nameAbbrev                  = $data->val(3, 2 + $i, $sheetnum + 1);
                    $type                        = $data->val(4, 2 + $i, $sheetnum + 1);
                    $listboxOptions              = $data->val(5, 2 + $i, $sheetnum + 1);
                    $maxLength                   = $data->val(6, 2 + $i, $sheetnum + 1);
                    $required                    = $data->val(7, 2 + $i, $sheetnum + 1);
                    $defaultValue                = $data->val(8, 2 + $i, $sheetnum + 1);
                    $readonly                    = $data->val(9, 2 + $i, $sheetnum + 1);
                    $visible                     = $data->val(10, 2 + $i, $sheetnum + 1);
                    $forceCase                   = $data->val(11, 2 + $i, $sheetnum + 1);
                    $mustBeUnique                = $data->val(12, 2 + $i, $sheetnum + 1);
                    $fieldOptions                = $data->val(13, 2 + $i, $sheetnum + 1);
                    $autosuggestWorktableName    = $data->val(14, 2 + $i, $sheetnum + 1);
                    $autosuggestWorktableColName = $data->val(15, 2 + $i, $sheetnum + 1);
					$help                        = $data->val(16, 2 + $i, $sheetnum + 1);
                    
                    
                    $name                        = isUTF8($name) ? $name : utf8_encode($name);
                    $sequence                    = isUTF8($sequence) ? $sequence : utf8_encode($sequence);
                    $nameAbbrev                  = isUTF8($nameAbbrev) ? $nameAbbrev : utf8_encode($nameAbbrev);
                    $type                        = isUTF8($type) ? $type : utf8_encode($type);
                    $listboxOptions              = isUTF8($listboxOptions) ? $listboxOptions : utf8_encode($listboxOptions);
                    $maxLength                   = isUTF8($maxLength) ? $maxLength : utf8_encode($maxLength);
                    $required                    = isUTF8($required) ? $required : utf8_encode($required);
                    $defaultValue                = isUTF8($defaultValue) ? $defaultValue : utf8_encode($defaultValue);
                    $readonly                    = isUTF8($readonly) ? $readonly : utf8_encode($readonly);
                    $visible                     = isUTF8($visible) ? $visible : utf8_encode($visible);
                    $forceCase                   = isUTF8($forceCase) ? $forceCase : utf8_encode($forceCase);
                    $mustBeUnique                = isUTF8($mustBeUnique) ? $mustBeUnique : utf8_encode($mustBeUnique);
                    $fieldOptions                = isUTF8($fieldOptions) ? $fieldOptions : utf8_encode($fieldOptions);
                    $autosuggestWorktableName    = isUTF8($autosuggestWorktableName) ? $autosuggestWorktableName : utf8_encode($autosuggestWorktableName);
                    $autosuggestWorktableColName = isUTF8($autosuggestWorktableColName) ? $autosuggestWorktableColName : utf8_encode($autosuggestWorktableColName);
					$help                        = isUTF8($help) ? $help : utf8_encode($help);
        
                    if ($this->interactive) {
                        $yesNoArr     = camila_get_translation_array('camila.worktable.options.noyes');
                        $fieldTypeArr = camila_get_translation_array('camila.worktable.options.fieldtype');
                        $forceArr     = camila_get_translation_array('camila.worktable.options.force');
                        $orderDirArr  = camila_get_translation_array('camila.worktable.options.order.dir');
                    } else {
                        $yesNoArr     = $this->camila_get_translation_array('camila.worktable.options.noyes');
                        $fieldTypeArr = $this->camila_get_translation_array('camila.worktable.options.fieldtype');
                        $forceArr     = $this->camila_get_translation_array('camila.worktable.options.force');
                        $orderDirArr  = $this->camila_get_translation_array('camila.worktable.options.order.dir');
                    }
                    
                    $record = Array();
					
					$record['id']=$this->db->GenID(CAMILA_APPLICATION_PREFIX . 'worktablecolseq', 10000);
                    $record['wt_id']                  = $id;
                    $record['applied_to_database']    = 'n';
                    $record['is_deleted']             = 'n';
                    //$record['orig_name'] = preg_replace("/[\n\r]/", "", $value);
                    //$record['orig_type'] = $types[$key];
                    $record['type']                   = array_search($type, $fieldTypeArr);
                    $record['listbox_options']        = $listboxOptions;
                    $record['name']                   = preg_replace("/[\n\r]/", ' ', $name);
                    $record['col_name']               = $this->get_field_name($name);
                    $record['name_abbrev']            = $nameAbbrev;
                    $record['readonly']               = array_search($readonly, $yesNoArr);
                    $record['must_be_unique']         = array_search($mustBeUnique, $yesNoArr);
                    $record['visible']                = array_search($visible, $yesNoArr);
                    $record['required']               = array_search($required, $yesNoArr);
                    $record['sequence']               = $sequence;
                    $record['maxlength']              = $maxLength;
                    $record['size']                   = $this->default_size[$record['type']];
                    $record['force_case']             = array_search($forceCase, $forceArr);
                    $record['default_value']          = $defaultValue;
                    $record['field_options']          = $fieldOptions;
                    $record['autosuggest_wt_name']    = $autosuggestWorktableName;
                    $record['autosuggest_wt_colname'] = $autosuggestWorktableColName;
					$record['help']					  = $help;
                    $insertSQL = $this->db->AutoExecute(CAMILA_TABLE_WORKC, $record, 'INSERT');
                    if (!$insertSQL) {
                        if (function_exists(camila_information_text))
							camila_information_text(camila_get_translation('camila.worktable.db.error'));
						else
							echo ($this->camila_get_translation('camila.worktable.db.error'));
                        $success = false;
                    }
                    
                    $fieldArr[$record['col_name']] = $record['name'];
                }
                
                $shortTitle = $data->val(18, 2, $sheetnum + 1);
                $fullTitle  = $data->val(19, 2, $sheetnum + 1);
                $orderField = $data->val(20, 2, $sheetnum + 1);
                $orderDir   = $data->val(21, 2, $sheetnum + 1);
                $canUpdate  = $data->val(22, 2, $sheetnum + 1);
                $canInsert  = $data->val(23, 2, $sheetnum + 1);
                $canDelete  = $data->val(24, 2, $sheetnum + 1);
                $category   = $data->val(25, 2, $sheetnum + 1);
                
                $shortTitle = isUTF8($shortTitle) ? $shortTitle : utf8_encode($shortTitle);
                $fullTitle  = isUTF8($fullTitle) ? $fullTitle : utf8_encode($fullTitle);
                $orderField = isUTF8($orderField) ? $orderField : utf8_encode($orderField);
                $orderDir   = isUTF8($orderDir) ? $orderDir : utf8_encode($orderDir);
                $canUpdate  = isUTF8($canUpdate) ? $canUpdate : utf8_encode($canUpdate);
                $canInsert  = isUTF8($canInsert) ? $canInsert : utf8_encode($canInsert);
                $canDelete  = isUTF8($canDelete) ? $canDelete : utf8_encode($canDelete);
                $category   = isUTF8($category) ? $category : utf8_encode($category);

                $record                = Array();
                $record['id']          = $id;
                $record['short_title'] = $shortTitle;
                $record['full_title']  = $fullTitle;
                $record['order_field'] = array_search($orderField, $fieldArr);
                $record['order_dir']   = array_search($orderDir, $orderDirArr);
                $record['canupdate']   = array_search($canUpdate, $yesNoArr);
                $record['caninsert']   = array_search($canInsert, $yesNoArr);
                $record['candelete']   = array_search($canDelete, $yesNoArr);
                $record['category']    = $category;

				//echo $this->db->qstr($id);
                $updateSQL = $this->db->AutoExecute(CAMILA_TABLE_WORKT, $record, 'UPDATE', 'id=' . $this->db->qstr($id));
                if (!$updateSQL) {
					echo CAMILA_TABLE_WORKT;
					print_r($record);
                    $this->camila_information_text($this->camila_get_translation('camila.worktable.db.error'));
                    $success = false;
                }
                
                $j = 0;
                while ($data->val(18 + $j, 3, $sheetnum + 1) != '') {
                    $title = $data->val(18 + $j, 3, $sheetnum + 1);
                    $title = isUTF8($title) ? $title : utf8_encode($title);
                    
                    $url = $data->val(18 + $j, 4, $sheetnum + 1);
                    $url = isUTF8($url) ? $url : utf8_encode($url);
                    
                    $record['id']       = $this->db->GenID(CAMILA_APPLICATION_PREFIX . 'bookmarkseq', 10000);
                    $record['title']    = $title;
                    $record['base_url'] = 'cf_worktable' . $id . '.php';
                    $record['url']      = $record['base_url'];
                    if ($url != '')
                        $record['url'] .= '?filter=' . urlencode($url);
                    $record['lang']     = $_REQUEST['lang'];
                    $record['sequence'] = $j + 1;
                    
                    $insertSQL = $this->db->AutoExecute(CAMILA_APPLICATION_PREFIX . 'camila_bookmarks', $record, 'INSERT');
                    if (!$insertSQL) {
                        camila_information_text(camila_get_translation('camila.worktable.db.error'));
                        $success = false;
                    }
                    
                    $j++;
                }
            } else {
                $count = 1;
                foreach ($cols as $key => $value) {
                    $record                        = Array();
					$record['id']=$this->db->GenID(CAMILA_APPLICATION_PREFIX . 'worktablecolseq', 10000);
                    $record['wt_id']               = $id;
                    $record['applied_to_database'] = 'n';
                    $record['is_deleted']          = 'n';
                    $record['orig_name']           = preg_replace("/[\n\r]/", "", $value);
                    $record['orig_type']           = $types[$key];
                    if ($types[$key] == 'number')
                        $record['type'] = 'integer';
                    elseif ($types[$key] == 'date')
                        $record['type'] = 'date';
                    elseif ($types[$key] == 'hyperlink')
                        $record['type'] = 'hyperlink';
                    else
                        $record['type'] = 'string';
                    
                    $record['col_name']       = $this->get_field_name($value);
                    $name                     = preg_replace("/[\n\r]/", ' ', $value);
                    $record['name']           = !isUTF8($name) ? utf8_encode($name) : $name;
                    $record['name_abbrev']    = $record['name'];
                    $record['readonly']       = 'n';
                    $record['must_be_unique'] = 'n';
                    $record['visible']        = 'y';
                    $record['required']       = 'n';
                    $record['sequence']       = $count;
                    $record['maxlength']      = $this->default_maxlength[$record['type']];
                    $record['size']           = $this->default_size[$record['type']];
                    $insertSQL                = $this->db->AutoExecute(CAMILA_TABLE_WORKC, $record, 'INSERT');
                    if (!$insertSQL) {
                        camila_information_text(camila_get_translation('camila.worktable.db.error'));
                        $success = false;
                    }
                   
                    $count++;
                }
                
            }
            
            if ($success && $this->interactive) {
				$myText = new CHAW_text('');
				$_CAMILA['page']->add_text($myText);
                camila_information_text(camila_get_translation('camila.worktable.xls.successful'));
			}
            
        } else {
            
            if ($interactive) {
				$myText = new CHAW_text('');
				$_CAMILA['page']->add_text($myText);
                $myText = new CHAW_text(camila_get_translation('camila.wizard.filecardcreated'));
                $myText->set_br(2);
                $_CAMILA['page']->add_text($myText);
            }
            
        }
        
        if ($this->interactive) {
            $form3               = new phpform('camila', 'cf_worktable_wizard_step2.php', HAW_METHOD_GET);
            $form3->submitbutton = camila_get_translation('camila.wizard.next');
            $form3->drawrules    = false;
            new form_hidden($form3, 'custom', $id);
            $form3->process();
            $form3->draw();
        }
        
        return $id;
    }
    
    
    function configure_columns($id, $returl = '')
    {
        global $_CAMILA;
        
        if ($this->interactive) {
            $this->db = $_CAMILA['db'];
        }
        
        //require_once(CAMILA_DIR . 'datagrid/db_form.class.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/integer.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/static_listbox.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/sql_listbox.php');
		//require_once(CAMILA_DIR . 'datagrid/elements/form/textarea.php');
        
        if ($_REQUEST['camila_addfield'] == 'y') {
            
            $result = $_CAMILA['db']->Execute('select * from ' . CAMILA_TABLE_WORKC . ' where wt_id=' . $_CAMILA['db']->qstr($id));
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
            
            while (!$result->EOF) {
                $this->fields[] = $result->fields['col_name'];
                $result->MoveNext();
            }
            
            $result2 = $_CAMILA['db']->Execute('select max(sequence) as seq from ' . CAMILA_TABLE_WORKC . ' where wt_id=' . $_CAMILA['db']->qstr($id));
            if ($result2 === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
            $sequence = intval($result2->fields['seq']) + 1;
            
            $record                        = Array();
			$record['id']=$this->db->GenID(CAMILA_APPLICATION_PREFIX . 'worktablecolseq', 10000);
            $record['applied_to_database'] = 'n';
            $record['is_deleted']          = 'n';
            $record['wt_id']               = $id;
            $record['orig_name']           = camila_get_translation('camila.worktable.field.newfieldtitle');
            $record['orig_type']           = 'string';
            $record['type']                = 'string';
            $record['col_name']            = $this->get_field_name($record['orig_name']);
            $record['name']                = $record['orig_name'];
            $record['name_abbrev']         = $record['orig_name'];
            $record['readonly']            = 'n';
            $record['must_be_unique']      = 'n';
            $record['visible']             = 'y';
            $record['required']            = 'n';
            $record['sequence']            = $sequence;
            $record['maxlength']           = $this->default_maxlength[$record['type']];
            $record['size']                = $this->default_size[$record['type']];
            $insertSQL                     = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_WORKC, $record, 'INSERT');
            if (!$insertSQL) {
                camila_information_text(camila_get_translation('camila.worktable.db.error'));
                $success = false;
            }
        }
        
        if (!isset($_REQUEST['camila_delete']) && $id != '') {
			$myText = new CHAW_text('');
			$_CAMILA['page']->add_text($myText);
            $myText = new CHAW_text(camila_get_translation('camila.wizard.configurecolumns'));
			$myText->set_br(2);
            $_CAMILA['page']->add_text($myText);
        }
        
        if (!camila_form_in_update_mode(CAMILA_TABLE_WORKC)) {
            
            $resultTable = $_CAMILA['db']->Execute('select * from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($id));
            if ($resultTable === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
            
            $form = new phpform('camila', 'cf_worktable_wizard_step3.php', HAW_METHOD_GET);
            $form->submitbutton = camila_get_translation('camila.wizard.next');
            
            $form->drawrules = false;
            new form_hidden($form, 'custom', $id);
            if ($returl != '')
                new form_hidden($form, 'returl', $returl);

            $query = 'select * from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $_CAMILA['db']->qstr($id) . ' and is_deleted<>' . $_CAMILA['db']->qstr('y') . ')';
            
            new form_textbox($form, 'short_title', camila_get_translation('camila.worktable.name'), false, 20, 20);
            new form_textbox($form, 'full_title', camila_get_translation('camila.worktable.desc'), false, 20, 30);
            new form_sql_listbox($form, 'order_field', camila_get_translation('camila.worktable.order.by'), $query, 'col_name', 'name');
            new form_static_listbox($form, 'order_dir', camila_get_translation('camila.worktable.order.dir'), camila_get_translation('camila.worktable.options.order.dir'));
            new form_static_listbox($form, 'canupdate', camila_get_translation('camila.worktable.canupdate'), camila_get_translation('camila.worktable.options.yesno'));
            new form_static_listbox($form, 'caninsert', camila_get_translation('camila.worktable.caninsert'), camila_get_translation('camila.worktable.options.yesno'));
            new form_static_listbox($form, 'candelete', camila_get_translation('camila.worktable.candelete'), camila_get_translation('camila.worktable.options.yesno'));
            
            $candelete   = 'y';
            $canupdate   = 'y';
            $caninsert   = 'y';
            $order_dir   = 'asc';
            $short_title = camila_get_translation('camila.worktable.name.predefault');
            $full_title  = camila_get_translation('camila.worktable.desc.predefault');
            
            if ($resultTable->fields['candelete'] != '')
                $candelete = $resultTable->fields['candelete'];
            
            if ($resultTable->fields['canupdate'] != '')
                $canupdate = $resultTable->fields['canupdate'];
            
            if ($resultTable->fields['caninsert'] != '')
                $caninsert = $resultTable->fields['caninsert'];
            
            if ($resultTable->fields['order_field'] != '')
                $order_field = $resultTable->fields['order_field'];
            
            if ($resultTable->fields['order_dir'] != '')
                $order_dir = $resultTable->fields['order_dir'];
            
            if ($resultTable->fields['short_title'] != '')
                $short_title = $resultTable->fields['short_title'];
            
            if ($resultTable->fields['full_title'] != '')
                $full_title = $resultTable->fields['full_title'];
            
            $form->fields['short_title']->value = $short_title;
            $form->fields['full_title']->value  = $full_title;
            $form->fields['candelete']->value   = $candelete;
            $form->fields['canupdate']->value   = $canupdate;
            $form->fields['caninsert']->value   = $caninsert;
            $form->fields['order_field']->value = $order_field;
            $form->fields['order_dir']->value   = $order_dir;
            
            $form->process();
            $form->draw();
        }
        
        $dbform                         = new dbform(CAMILA_TABLE_WORKC, 'id', 'sequence,name,name_abbrev,type,listbox_options,maxlength,required,readonly,must_be_unique,default_value,field_options,autosuggest_wt_name,autosuggest_wt_colname', 'sequence', 'asc', '(wt_id=' . $_CAMILA['db']->qstr($id) . ' and is_deleted<>' . $_CAMILA['db']->qstr('y') . ')', true, false, true, false, false);
        $dbform->mapping                = camila_get_translation('camila.worktable.mapping.worktable');
        $dbform->drawheadersubmitbutton = true;
        
        new form_hidden($dbform, 'id');
        new form_hidden($dbform, 'wt_id', $id);
        
        new form_integer($dbform, 'sequence', camila_get_translation('camila.worktable.field.sequence'), true);
        new form_textbox($dbform, 'name', camila_get_translation('camila.worktable.field.name'), true, 40, 40);
        new form_textbox($dbform, 'name_abbrev', camila_get_translation('camila.worktable.field.name.abbrev'), false, 20, 20);
        new form_static_listbox($dbform, 'type', camila_get_translation('camila.worktable.field.type'), camila_get_translation('camila.worktable.options.fieldtype'));
        new form_textbox($dbform, 'listbox_options', camila_get_translation('camila.worktable.field.listofvalues'), false, 50, 9999);
        new form_integer($dbform, 'size', camila_get_translation('camila.worktable.field.size'), false);
        new form_integer($dbform, 'maxlength', camila_get_translation('camila.worktable.field.maxlength'), false);
        new form_static_listbox($dbform, 'required', camila_get_translation('camila.worktable.field.required'), camila_get_translation('camila.worktable.options.yesno'));
        new form_textbox($dbform, 'default_value', camila_get_translation('camila.worktable.field.defaultval'), false, 20, 100);
        new form_static_listbox($dbform, 'readonly', camila_get_translation('camila.worktable.field.readonly'), camila_get_translation('camila.worktable.options.yesno'));
        new form_static_listbox($dbform, 'visible', camila_get_translation('camila.worktable.field.visible'), camila_get_translation('camila.worktable.options.yesno'));
        new form_static_listbox($dbform, 'force_case', camila_get_translation('camila.worktable.field.force'), camila_get_translation('camila.worktable.options.force'));
        new form_static_listbox($dbform, 'must_be_unique', camila_get_translation('camila.worktable.field.unique'), camila_get_translation('camila.worktable.options.noyes'));
        new form_textbox($dbform, 'field_options', camila_get_translation('camila.worktable.field.options'), false, 50, 250);
        new form_textbox($dbform, 'autosuggest_wt_name', camila_get_translation('camila.worktable.field.autosuggestwtname'), false, 40, 40);
        new form_textbox($dbform, 'autosuggest_wt_colname', camila_get_translation('camila.worktable.field.autosuggestwtcolname'), false, 40, 40);
		new form_textarea($dbform, 'help', camila_get_translation('camila.worktable.field.help'), false, 10, 80, 1000);
		
        $dbform->ondelete = 'camila_configurator_db_ondelete_field';
        
        $dbform->formupdatelinktext = camila_get_translation('camila.worktable.field.formupdatelinktext');
        
        $dbform->process();
        $dbform->draw();
        
        if ($id != '') {
            $myLink = new CHAW_link(camila_get_translation('camila.worktable.field.addnew'), 'cf_worktable_wizard_step2.php?camila_custom=' . $id . '&camila_addfield=y');
            $myLink->set_br(2);
            $_CAMILA['page']->add_link($myLink);
        }
        
        
    }
    
    
    function configure_table($id, $rebuild = false, $returl = '')
    {
        
        global $_CAMILA;
        
        if ($this->interactive) {
            $this->db = $_CAMILA['db'];
        }
        
        if ($_REQUEST['camila_phpform_sent'] == 1 && !isset($_REQUEST['camila_worktable_op'])) {
            $record                = Array();
            $record['order_field'] = $_REQUEST['camila_order_field'];
            $record['order_dir']   = $_REQUEST['camila_order_dir'];
            $record['canupdate']   = $_REQUEST['camila_canupdate'];
            $record['caninsert']   = $_REQUEST['camila_caninsert'];
            $record['candelete']   = $_REQUEST['camila_candelete'];
            if ($_REQUEST['camila_short_title'] != '')
                $record['short_title'] = $_REQUEST['camila_short_title'];
            if ($_REQUEST['camila_full_title'] != '')
                $record['full_title'] = $_REQUEST['camila_full_title'];
            
            $updateSQL = $this->db->AutoExecute(CAMILA_TABLE_WORKT, $record, 'UPDATE', 'id=' . $this->db->qstr($id));
            if (!$updateSQL) {
                camila_information_text(camila_get_translation('camila.worktable.db.error'));
            }
        }
        
        $resultTable = $this->db->Execute('select * from ' . CAMILA_TABLE_WORKT . ' where id=' . $this->db->qstr($id));
        if ($resultTable === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());

        $result = $this->db->Execute('select count(*) as c from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $this->db->qstr($id) . ' and applied_to_database = ' . $this->db->qstr('y') . ' and is_deleted<>' . $this->db->qstr('y') . ')');
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        
        $colsApplied = intval($result->fields['c']);
        
        $result = $this->db->Execute('select count(*) as c from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $this->db->qstr($id) . ' and applied_to_database = ' . $this->db->qstr('n') . ' and is_deleted<>' . $this->db->qstr('y') . ')');
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        
        $colsToApply = intval($result->fields['c']);
        
        //if ($colsToApply > 0 || $rebuild) {
        require_once(CAMILA_DIR . 'db/schema.inc.php');

        $schema = "<?xml version=\"1.0\"?>\n";
        $schema .= "<schema version=\"0.2\">\n";
        $schema .= "<table name=\"" . CAMILA_TABLE_WORKP . "$id\">\n";
        $schema .= "<field name=\"id\" type=\"I\">\n";
        $schema .= "<KEY/>\n";
        //$schema .= "<AUTOINCREMENT/>\n";
        $schema .= "</field>\n";
        $schema .= "<field name=\"created\" type=\"T\"/>\n";
        $schema .= "<field name=\"created_by\" type=\"C\" size=\"50\"/>\n";
        $schema .= "<field name=\"created_by_name\" type=\"C\" size=\"50\"/>\n";
        $schema .= "<field name=\"created_by_surname\" type=\"C\" size=\"50\"/>\n";
        $schema .= "<field name=\"created_src\" type=\"C\" size=\"30\"/>\n";
        $schema .= "<field name=\"last_upd\" type=\"T\"/>\n";
        $schema .= "<field name=\"last_upd_by\" type=\"C\" size=\"50\"/>\n";
        $schema .= "<field name=\"last_upd_by_name\" type=\"C\" size=\"50\"/>\n";
        $schema .= "<field name=\"last_upd_by_surname\" type=\"C\" size=\"50\"/>\n";
        $schema .= "<field name=\"last_upd_src\" type=\"C\" size=\"30\"/>\n";
        $schema .= "<field name=\"grp\" type=\"C\" size=\"20\"/>\n";
        $schema .= "<field name=\"mod_num\" type=\"I\"/>\n";
        $schema .= "<field name=\"is_deleted\" type=\"C\" size=\"1\"/>\n";
        $schema .= "<field name=\"cf_bool_is_selected\" type=\"C\" size=\"1\"><DEFAULT value=\"n\"/>
</field>\n";
        $schema .= "<field name=\"cf_bool_is_special\" type=\"C\" size=\"1\"><DEFAULT value=\"n\"/>
</field>\n";
        
        //if ($this->db->databaseType == 'sqlite' || $rebuild)
        $result = $this->db->Execute('select * from ' . CAMILA_TABLE_WORKC . ' where wt_id=' . $this->db->qstr($id) . ' order by sequence');
        //else
        //    $result = $this->db->Execute('select * from ' . CAMILA_TABLE_WORKC . ' where wt_id='.$this->db->qstr($id) . ' and applied_to_database = ' . $this->db->qstr('n') . ' order by sequence');
        
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        
        while (!$result->EOF) {
            $schema .= $this->get_xml_schema_tag($result->fields);
            $result->MoveNext();
        }
        
        $schema .= "</table>\n";
        $schema .= "</schema>\n";
        
        $filename = CAMILA_TMP_DIR . '/' . CAMILA_TABLE_WORKP . $id . '.xml';
        $f        = fopen($filename, 'w');
        fwrite($f, $schema);
        fclose($f);
        
        if (!$rebuild && $colsApplied > 0 && ($this->db->databaseType == 'sqlite' || $this->db->databaseType == 'sqlite3')) {
            $sql0   = sprintf($this->db->_dropSeqSQL, 'temp_' . CAMILA_TABLE_WORKP . $id);
            $result = $this->db->Execute($sql0);
            //if ($result === false)
            //    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
            $sql1   = 'create table temp_' . CAMILA_TABLE_WORKP . $id . ' as select * from ' . CAMILA_TABLE_WORKP . $id;
            $result = $this->db->Execute($sql1);
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        }
        
        $result = create_table($filename, '', $this->db, (($colsApplied > 0 && $this->db->databaseType != 'sqlite' && $this->db->databaseType != 'sqlite3') && !$rebuild) ? false : true);
        
        if ($result['result'] == 2) {
            $record                        = Array();
            $record['applied_to_database'] = 'y';
            $updateSQL                     = $this->db->AutoExecute(CAMILA_TABLE_WORKC, $record, 'UPDATE', 'wt_id=' . $this->db->qstr($id));
            if (!$updateSQL)
                camila_information_text(camila_get_translation('camila.worktable.db.error'));
        } else
            echo $filename . ' - KO - ' . $result['sql'] . ' - ' . $result['statements'];
        
        
        if (!$rebuild && $colsApplied > 0 && ($this->db->databaseType == 'sqlite' || $this->db->databaseType == 'sqlite3')) {
            $sql    = 'pragma table_info (temp_' . CAMILA_TABLE_WORKP . $id . ')';
            $result = $this->db->Execute($sql);
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
            $cols = '';
            while (!$result->EOF) {
                $cols .= ',' . $result->fields['name'];
                $result->MoveNext();
            }
            
            $cols = substr($cols, 1);
            
            $sql2   = 'insert into ' . CAMILA_TABLE_WORKP . $id . '(' . $cols . ') select ' . $cols . ' from temp_' . CAMILA_TABLE_WORKP . $id;
            $result = $this->db->Execute($sql2);
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
            
            $sql3   = sprintf($this->db->_dropSeqSQL, 'temp_' . CAMILA_TABLE_WORKP . $id);
            $result = $this->db->Execute($sql3);
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        }
        //}
        
        
        
        $success3 = $this->create_script_from_template($id);
        
        
        
        $resultTemp = $this->db->Execute('select id from ' . CAMILA_TABLE_WORKT . ' where id<>' . $this->db->qstr($id));
        if ($resultTemp === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        
        while (!$resultTemp->EOF) {
            
            $successTemp = $this->create_script_from_template($resultTemp->fields['id']);
            $resultTemp->MoveNext();
        }
        
        
        $record                = Array();
        $record['short_title'] = $resultTable->fields['short_title'];
        $record['full_title']  = $resultTable->fields['full_title'];
        
        $success4  = true;
        $updateSQL = $this->db->AutoExecute(CAMILA_TABLE_PLANG, $record, 'UPDATE', 'page_url=' . $this->db->qstr($resultTable->fields['scriptname']) . ' and lang=' . $this->db->qstr($_CAMILA['lang']));
        
        if (!$updateSQL) {
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
            $success4 = false;
        }
        
        //require_once(CAMILA_DIR . 'datagrid/form.class.php');
        //require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
        
		/////
				$this->camila_delete_files(CAMILA_TMP_DIR);
		
        if ($this->interactive) {
			$myText = new CHAW_text('');
			$_CAMILA['page']->add_text($myText);
			
            $myText = new CHAW_text(camila_get_translation('camila.wizard.configurationapplied'));
			$myText->set_br(2);
            $_CAMILA['page']->add_text($myText);
            
            if ($returl != '')
                $form3 = new phpform('camila', $returl, HAW_METHOD_GET);
            elseif ($colsApplied > 0)
                $form3 = new phpform('camila', 'cf_worktable_admin.php', HAW_METHOD_GET);
            else
                $form3 = new phpform('camila', 'cf_worktable_wizard_step4.php', HAW_METHOD_GET);
            
            $form3->submitbutton = camila_get_translation('camila.wizard.next');
            $form3->drawrules    = false;
            new form_hidden($form3, 'custom', $id);
            
            $form3->process();
            $form3->draw();
        }
        
    }
    
    function create_script_from_template($id)
    {
        
        global $_CAMILA;
        
        $this->menuitems_script = '';
        $this->formulas         = 'Array(';
        $this->queries          = 'Array(';
        
        $resultTable = $this->db->Execute('select * from ' . CAMILA_TABLE_WORKT . ' where id=' . $this->db->qstr($id));
        if ($resultTable === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        
        
        $result = $this->db->Execute('select * from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $this->db->qstr($id) . ' and is_deleted<>' . $this->db->qstr('y') . ') order by sequence');
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        
        //require_once(CAMILA_LIB_DIR . 'minitemplator/MiniTemplator.class.php');
        
        $t = new MiniTemplator;
        $t->readTemplateFromFile(CAMILA_DIR . 'templates/worktable.inc.php');
        
        $report_fields = 'id,';
        
        if (CAMILA_WORKTABLE_SPECIAL_ICON_ENABLED)
            $report_fields .= 'cf_bool_is_special,';
        
        if (CAMILA_WORKTABLE_SELECTED_ICON_ENABLED)
            $report_fields .= 'cf_bool_is_selected,';
        
        $default_fields = 'cf_bool_is_special,cf_bool_is_selected,';
        $mapping        = $this->camila_get_translation('camila.worktable.sysfields.mapping') . '#';
        $mappingAbbrev  = $this->camila_get_translation('camila.worktable.sysfields.mapping') . '#';
        $order_field    = $resultTable->fields['order_field'];
        $order_dir      = $resultTable->fields['order_dir'];
        $canupdate      = $resultTable->fields['canupdate'] == 'y' ? 'true' : 'false';
        $caninsert      = $resultTable->fields['caninsert'] == 'y' ? 'true' : 'false';
        $candelete      = $resultTable->fields['candelete'] == 'y' ? 'true' : 'false';
		$forceReadonly = ($canupdate == 'false') ? true : false;
        $rcount         = 0;
        $vcount         = 0;
        $fcount         = 0;
        $qcount         = 0;
		$groupvisibilityfield = 'grp';
		$personalvisibilityfield = 'created_by';

        while (!$result->EOF) {
            if ($vcount > 0)
                $default_fields .= ',';
            
            
            if ($rcount > 0) {
                $report_fields .= ',';
                $mapping .= '#';
                $mappingAbbrev .= '#';
            } else {
                if ($order_field == '')
                    $order_field = $result->fields['col_name'];
            }
            
            
            if ($result->fields['type'] != 'formula' && $result->fields['type'] != 'query')
                $report_fields .= $result->fields['col_name'];
            else if ($result->fields['type'] == 'query') {
                $report_fields .= $result->fields['col_name'] . ' as cf_query_' . $result->fields['col_name'];
                if ($qcount > 0)
                    $this->queries .= ',';
                
                $this->queries .= '\'' . 'cf_query_' . $result->fields['col_name'] . '\'=>\'' . $result->fields['field_options'] . '\'';
                $qcount++;
                
            } else {
                $report_fields .= $result->fields['col_name'] . ' as cf_formula_' . $result->fields['col_name'];
                if ($fcount > 0)
                    $this->formulas .= ',';
                
                $this->formulas .= '\'' . 'cf_formula_' . $result->fields['col_name'] . '\'=>\'' . $result->fields['field_options'] . '\'';
                $fcount++;                
            }
            
            
            if ($result->fields['visible'] == 'y') {
                
                if ($result->fields['type'] != 'formula' && $result->fields['type'] != 'query')
                    $default_fields .= $result->fields['col_name'];
                else if ($result->fields['type'] == 'query')
                    $default_fields .= $result->fields['col_name'] . ' as cf_query_' . $result->fields['col_name'];
                else
                    $default_fields .= $result->fields['col_name'] . ' as cf_formula_' . $result->fields['col_name'];
                
                $vcount++;
            }
            
            if ($result->fields['type'] != 'formula' && $result->fields['type'] != 'query') {
                $mappingAbbrev .= $result->fields['col_name'] . '=' . $this->escape($result->fields['name_abbrev']);
                $mapping .= $result->fields['col_name'] . '=' . $this->escape($result->fields['name']);
            } else if ($result->fields['type'] == 'query') {
                $mappingAbbrev .= 'cf_query_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name_abbrev']);
                $mappingAbbrev .= '#' . $result->fields['col_name'] . ' as cf_query_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name_abbrev']);
                
                $mapping .= 'cf_formula_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name']);
                $mapping .= '#' . $result->fields['col_name'] . 'as cf_query_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name']);
                
            } else {
                $mappingAbbrev .= 'cf_formula_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name_abbrev']);
                $mappingAbbrev .= '#' . $result->fields['col_name'] . ' as cf_formula_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name_abbrev']);
                
                $mapping .= 'cf_formula_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name']);
                $mapping .= '#' . $result->fields['col_name'] . 'as cf_formula_' . $result->fields['col_name'] . '=' . $this->escape($result->fields['name']);
            }
            
            
            $rcount++;
            $t->setVariable('form_element', $this->get_form_element($result->fields, CAMILA_TABLE_WORKP . $id, $forceReadonly));

			if (stripos(strtolower($result->fields['field_options']), $this->camila_get_translation('camila.worktable.field.groupvisibilityfield')) !== false) {
				$groupvisibilityfield = $result->fields['col_name'];
			}
			
			if (stripos(strtolower($result->fields['field_options']), $this->camila_get_translation('camila.worktable.field.personalvisibilityfield')) !== false) {
				$personalvisibilityfield = $result->fields['col_name'];
			}

            $t->addBlock('element');
            
            $result->MoveNext();
        }

        $report_fields .= ',created,created_by,created_by_surname,created_by_name,last_upd,last_upd_by,last_upd_by_surname,last_upd_by_name,mod_num';
        foreach ($this->requires as $value) {
            $t->setVariable('form_require', $value);
            $t->addBlock('require');
        }
        
        $result = $this->db->Execute('select distinct autosuggest_wt_name from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $this->db->qstr($id) . ' and is_deleted<>' . $this->db->qstr('y') . ')');
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
        
        while (!$result->EOF) {
            
            $tablename = $result->fields['autosuggest_wt_name'];
            
            if ($tablename != '') {
                $result2 = $this->db->Execute('select id, tablename from ' . CAMILA_TABLE_WORKT . ' where short_title=' . $this->db->qstr($tablename));
                if ($result2 === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());

                $extid    = $result2->fields['id'];
				//echo 'select id, tablename from ' . CAMILA_TABLE_WORKT . ' where short_title=' . $this->db->qstr($tablename).$extid;
                $table    = $result2->fields['tablename'];
                $exttable = false;

                if (substr($tablename, 0, 1) == '[') {
                    $exttable = true;
                    $table    = substr($tablename, 1, -1);
					//2017 PostgreSQL
					$extid = '-1';
                }

                $result2 = $this->db->Execute('select sequence,col_name,autosuggest_wt_colname,field_options from ' . CAMILA_TABLE_WORKC . ' where (autosuggest_wt_name=' . $this->db->qstr($tablename) . ' and wt_id=' . $this->db->qstr($id) . ' and is_deleted<>' . $this->db->qstr('y') . ')');
                if ($result2 === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
                
                while (!$result2->EOF) {
                    
                    $field       = $result2->fields['col_name'];
                    $sequence    = $result2->fields['sequence'];
                    $suggcolname = $result2->fields['autosuggest_wt_colname'];
					//2017
					$fieldOptions = $result2->fields['field_options'];

                    //search for col_names
                    $result3 = $this->db->Execute('select col_name from ' . CAMILA_TABLE_WORKC . ' where (name=' . $this->db->qstr($suggcolname) . ' and wt_id=' . $this->db->qstr($extid) . ' and is_deleted<>' . $this->db->qstr('y') . ')');
                    if ($result3 === false)
                        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
                    
                    $col_name = $result3->fields['col_name'];
                    
                    if ($exttable)
                        $col_name = $suggcolname;
                    
                    $suggfield  = $col_name;
                    $infofields = '';
                    $destfields = '';
                    
                    $query1 = 'select col_name, autosuggest_wt_colname from ' . CAMILA_TABLE_WORKC . ' where (autosuggest_wt_name=' . $this->db->qstr($tablename) . ' and autosuggest_wt_colname <>' . $this->db->qstr($suggcolname) . ' and wt_id=' . $this->db->qstr($id) . ' and is_deleted<>' . $this->db->qstr('y') . ' and sequence >= ' . $sequence . ' ) order by sequence';
                    $query2 = 'select col_name, autosuggest_wt_colname from ' . CAMILA_TABLE_WORKC . ' where (autosuggest_wt_name=' . $this->db->qstr($tablename) . ' and autosuggest_wt_colname <>' . $this->db->qstr($suggcolname) . ' and wt_id=' . $this->db->qstr($id) . ' and is_deleted<>' . $this->db->qstr('y') . ' and sequence < ' . $sequence . ' ) order by sequence';
                    
                    $result4 = $this->db->Execute($query1);
                    if ($result4 === false)
                        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
                    
                    while (!$result4->EOF) {                        
                        $suggcolname = $result4->fields['autosuggest_wt_colname'];
                        $result3     = $this->db->Execute('select col_name from ' . CAMILA_TABLE_WORKC . ' where (name=' . $this->db->qstr($suggcolname) . ' and wt_id=' . $this->db->qstr($extid) . ' and (is_deleted<>' . $this->db->qstr('y') . ' or is_deleted is null))');
                        if ($result3 === false)
                            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
                        
                        $col_name      = $result3->fields['col_name'];
                        $dest_col_name = $result4->fields['col_name'];
                        
                        if ($dest_col_name != '')
                            $destfields .= ',' . $dest_col_name;

                        if ($col_name != '')
                            $infofields .= ',' . $col_name;

                        if ($exttable)
                            $infofields .= ',' . $suggcolname;

                        $result4->MoveNext();
                    }
                    
                    $result4 = $this->db->Execute($query2);
                    if ($result4 === false)
                        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
                    
                    while (!$result4->EOF) {
                        
                        $suggcolname = $result4->fields['autosuggest_wt_colname'];
                        $result3     = $this->db->Execute('select col_name from ' . CAMILA_TABLE_WORKC . ' where (name=' . $this->db->qstr($suggcolname) . ' and wt_id=' . $this->db->qstr($extid) . ' and (is_deleted<>' . $this->db->qstr('y') . ' or is_deleted is null))');
                        if ($result3 === false)
                            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $this->db->ErrorMsg());
                        
                        $col_name      = $result3->fields['col_name'];
                        $dest_col_name = $result4->fields['col_name'];
                        
                        if ($dest_col_name != '')
                            $destfields .= ',' . $dest_col_name;
                        
                        if ($col_name != '')
                            $infofields .= ',' . $col_name;
                        
                        if ($exttable)
                            $infofields .= ',' . $suggcolname;
                        
                        $result4->MoveNext();
                    }
					//2017
					if (stripos(strtolower($fieldOptions), $this->camila_get_translation('camila.worktable.field.hideautosuggest')) !== false) {
						
					}
					else
					{
						$script .= "if (is_object(\$form->fields['$field']))\n";
						$script .= "{\n";
						$script .= "\$form->fields['$field']->autosuggest_table = '" . $table . "';\n";
						$script .= "\$form->fields['$field']->autosuggest_field = '" . $suggfield . "';\n";
						$script .= "\$form->fields['$field']->autosuggest_idfield = '" . 'id' . "';\n";
						$script .= "\$form->fields['$field']->autosuggest_infofields = '" . substr($infofields, 1) . "';\n";
						$script .= "\$form->fields['$field']->autosuggest_pickfields = '" . substr($infofields, 1) . "';\n";
						$script .= "\$form->fields['$field']->autosuggest_destfields = '" . substr($destfields, 1) . "';\n";
						$script .= "}\n";
					}
                    
                    $result2->MoveNext();
                }
                
            }
            
            $result->MoveNext();
        }
        
        $this->formulas .= ');';
        $this->queries .= ');';
        
        $t->setVariable('menuitems_script', $this->menuitems_script);
        $t->setVariable('formulas', $this->formulas);
        $t->setVariable('queries', $this->queries);
        $t->setVariable('autosuggest_script', $script);
        $t->setVariable('table', CAMILA_TABLE_WORKP . $id);
        $t->setVariable('report_fields', $report_fields);
        $t->setVariable('default_fields', $default_fields);
        $t->setVariable('mapping', $mapping);
        $t->setVariable('mapping_abbrev', $mappingAbbrev);
        $t->setVariable('order_field', $order_field);
        $t->setVariable('order_dir', $order_dir);
        $t->setVariable('canupdate', $canupdate);
        $t->setVariable('caninsert', $caninsert);
        $t->setVariable('candelete', $candelete);

		$t->setVariable('group_visibility_field', $groupvisibilityfield);
		$t->setVariable('personal_visibility_field', $personalvisibilityfield);

        $t->generateOutputToString($output);
        //$t->generateOutputToFile(CAMILA_WORKTABLES_DIR . '/' . CAMILA_TABLE_WORKP . $id . '.inc.php');
        
        $trans = new Latin1UTF8();
        
        $fh = fopen(CAMILA_WORKTABLES_DIR . '/' . CAMILA_TABLE_WORKP . $id . '.inc.php', 'wb');
        fwrite($fh, $trans->mixed_to_utf8($output));
        fclose($fh);
        
        $record            = Array();
        $record['visible'] = 'yes';
        $record['active']  = 'yes';
        
        $success3  = true;
        $updateSQL = $this->db->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url=' . $this->db->qstr($resultTable->fields['scriptname']));
        if (!$updateSQL) {
			if (function_exists(camila_information_text))
				camila_information_text(camila_get_translation('camila.worktable.db.error'));
			else
				echo($this->camila_get_translation('camila.worktable.db.error'));
            $success3 = false;
        }
        
        return $success3;
    }
    
    
    function get_xml_schema_tag($rs)
    {
        
        $tag = "<field name=\"" . $rs['col_name'] . "\"";
        
        switch ($rs['type']) {
            case 'integer':
            case 'integer-listofvalues':
            case 'autoincrement':
                
                $tag .= " type=\"I\">";
                break;
            
            case 'date':
                $tag .= " type=\"D\">";
                break;
            
            case 'datetime':
                $tag .= " type=\"T\">";
                break;
            
            case 'textarea':
			case 'html-textarea':
                $tag .= " type=\"X\">";
                break;
				
            case 'float':
                $tag .= " type=\"F\">";
                break;

            default:
                $tag .= " type=\"C\" size=\"" . $this->column_size['C'] . "\">";
                
        }
        
        $tag .= "</field>\n";
        
        return $tag;
        
    }
    
    
    function get_form_element($rs,$table,$forceReadonly=false)
    {
        //new form_static_listbox($form2, 'sheet', camila_get_translation('camila.worktable.xls.sheetnum'), $sheet_list);
        $required   = $rs['required'] == 'y' ? 'true' : 'false';
        $field      = $rs['col_name'];
        $name       = $this->escape($rs['name']);
        $size       = $rs['size'];
        $maxlength  = $rs['maxlength'];
        $options    = $this->escape($rs['listbox_options']);
        $validation = '';
        $forcecase  = $rs['force_case'];
        $unique     = $rs['must_be_unique'];
		$help		= $rs['help'];

        if ($forcecase == 'upper')
            $validation = 'uppercase';
        
        if ($forcecase == 'lower')
            $validation = 'lowercase';
   
        if ($unique == 'y') {
            if ($validation != '')
                $validation .= ',';
            $validation .= 'unique';
        }
        
        if ($size == '')
            $size = $this->default_size[$rs['type']];
        
        switch ($rs['type']) {
            case 'autoincrement':
                //$this->add_require('integer');
                $script = "new form_integer(\$form, '$field', '$name', $required, $size, $maxlength, '$validation');";
                $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->defaultvalue = worktable_get_next_autoincrement_value('" . CAMILA_TABLE_WORKP . $rs['wt_id'] . "','$field');";
                
                break;
            
            case 'integer':
                //$this->add_require('integer');
                $script = "new form_integer(\$form, '$field', '$name', $required, $size, $maxlength, '$validation');";
                break;
			
			case 'float':
                //$this->add_require('decimal');
                $script = "new form_decimal(\$form, '$field', '$name', $required, 5, 2, '$validation');";
                break;
            
            case 'date':
                //$this->add_require('date');
                $script = "new form_date(\$form, '$field', '$name', $required, '$validation');";
                $this->menuitems_script .= "\$jarr=Array();\n";
                $this->menuitems_script .= "\$jarr['url'] = \"javascript:camila_inline_update_selected('" . $field . "','')\";\n";
                $this->menuitems_script .= "\$jarr['visible'] = 'yes';\n";
                $this->menuitems_script .= "\$jarr['short_title'] = 'MODIFICA " . $name . "...';\n";
                $this->menuitems_script .= "\$jarr['parent'] = 'index.php';\n";
                $this->menuitems_script .= "\$report->menuitems[]=\$jarr;\n";
                break;
            
            case 'datetime':
                //$this->add_require('datetime');
                $script = "new form_datetime(\$form, '$field', '$name', $required, '$validation');";
                $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->hslots = 60;";
                break;
            
            case 'string-listofvalues':
            case 'integer-listofvalues':
                //$this->add_require('static_listbox');
                $script = "new form_static_listbox(\$form, '$field', '$name', '$options', $required, '$validation');";
                
                $this->menuitems_script .= "\$jarr=Array();\n";
                $this->menuitems_script .= "\$jarr['url'] = '" . $field . "';\n";
                $this->menuitems_script .= "\$jarr['visible'] = 'yes';\n";
                $this->menuitems_script .= "\$jarr['short_title'] = 'MODIFICA " . $name . "';\n";
                $this->menuitems_script .= "\$jarr['parent'] = 'index.php';\n";
                $this->menuitems_script .= "\$report->menuitems[]=\$jarr;\n";
                
                $opts = explode(',', $options);
                foreach ($opts as $key => $value) {
                    $this->menuitems_script .= "\$jarr=Array();\n";
                    $this->menuitems_script .= "\$jarr['url'] = \"javascript:camila_inline_update_selected('" . $field . "','" . str_replace("\"", "\\\"", $value) . "')\";\n";
                    //$this->menuitems_script .= "\$jarr['url'] = \"javascript:camila_inline_update_selected('".$field."','".$value."')\";\n";
                    $this->menuitems_script .= "\$jarr['visible'] = 'yes';\n";
                    $this->menuitems_script .= "\$jarr['short_title'] = '" . $value . "';\n";
                    $this->menuitems_script .= "\$jarr['parent'] = '" . $field . "';\n";
                    $this->menuitems_script .= "\$report->menuitems[]=\$jarr;\n";
                }
                
                
                break;
            
            case 'phonenumber';
                //$this->add_require('phonenumber');
                $script = "new form_phonenumber(\$form, '$field', '$name', $required, $size, $maxlength, '$validation');";
                break;
            
            case 'textarea';
                //$this->add_require('textarea');
                $script = "new form_textarea(\$form, '$field', '$name', $required, 10, 80, $maxlength, '$validation');";
                break;
			
			case 'html-textarea';				
				//$this->add_require('tinymce_textarea');
				$toolbar = "menubar:false, plugins: 'code link image table preview',toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink | image | table | preview | code'";
                $script = "new form_tinymce_textarea(\$form, '$field', '$name', $required, \"selector: 'textarea[name=$table"._."$field]', $toolbar\", 10, 80, $maxlength, '$validation');";
                break;

            case 'hyperlink';
                //$this->add_require('weblink');
                $script = "new form_weblink(\$form, '$field', '$name', $required, $size, $maxlength, '$validation');";
                break;
            
            case 'formula';
                //$this->add_require('textbox');
                $script = "new form_textbox(\$form, '$field', '$name', $required, $size, $maxlength, '$validation');";
                break;
            
            case 'query';
                //$this->add_require('textbox');
                $script = "new form_textbox(\$form, '$field', '$name', $required, $size, $maxlength, '$validation');";
                break;
            
            default:
                //$this->add_require('textbox');
                $script = "new form_textbox(\$form, '$field', '$name', $required, $size, $maxlength, '$validation');";
                
                $this->menuitems_script .= "\$jarr=Array();\n";
                $this->menuitems_script .= "\$jarr['url'] = \"javascript:camila_inline_update_selected('" . $field . "','')\";\n";
                $this->menuitems_script .= "\$jarr['visible'] = 'yes';\n";
                $this->menuitems_script .= "\$jarr['short_title'] = 'MODIFICA " . $name . "...';\n";
                $this->menuitems_script .= "\$jarr['parent'] = 'index.php';\n";
                $this->menuitems_script .= "\$report->menuitems[]=\$jarr;\n";
                
        }
        
        
        if ($rs['type'] == 'formula' || $rs['type'] == 'query') {
            $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->updatable = false;";
            
        } else {

        if ($rs['readonly'] == 'y' || $forceReadonly)
                $script .= "\nif (\$_CAMILA['adm_user_group'] != CAMILA_ADM_USER_GROUP && is_object(\$form->fields['$field'])) \$form->fields['$field']->updatable = false;";
        }

		if ($help != '') {
			$script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->help = '".$this->escape($help)."';";
		}
    
        if ($rs['default_value'] == $this->camila_get_translation('camila.date.today'))
            $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->defaultvalue = date('Y-m-d');";
        elseif ($rs['default_value'] == $this->camila_get_translation('camila.time.now'))
            $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->defaultvalue = date('Y-m-d H:i:s');";
        elseif ($rs['default_value'] == $this->camila_get_translation('camila.worktable.field.default.lastval')) {
            $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->defaultvalue = worktable_get_last_value_from_file('" . $this->escape($rs['name']) . "');";
            $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->write_value_to_file = worktable_get_safe_temp_filename('" . $this->escape($rs['name']) . "');";
        } elseif ($rs['default_value'] != '')
            $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->defaultvalue = worktable_parse_default_expression('" . $this->escape($rs['default_value']) . "', \$form);";
        
        $script .= "\n";
        
        if (strpos(strtolower($rs['field_options']), $this->camila_get_translation('camila.worktable.field.autofocus')) !== false) {
            $script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->autofocus = true;";
        }
		
		//2017
		if (stripos(strtolower($rs['field_options']), $this->camila_get_translation('camila.worktable.field.groupvisibilityfield')) !== false) {
			$script .= "\nif (is_object(\$form->fields['$field'])) \$form->fields['$field']->defaultvalue = \$_CAMILA['user_group'];";
			$script .= "\nif (\$_CAMILA['adm_user_group'] != CAMILA_ADM_USER_GROUP && is_object(\$form->fields['$field'])) \$form->fields['$field']->updatable = false;";
		}

		if (stripos(strtolower($rs['field_options']), $this->camila_get_translation('camila.worktable.field.personalvisibilityfield')) !== false) {
			$script .= "\nif (is_object(\$form->fields['$field']) && \$_CAMILA['user_visibility_type'] != 'group') \$form->fields['$field']->defaultvalue = \$_CAMILA['user'];";
			$script .= "\nif (\$_CAMILA['adm_user_group'] != CAMILA_ADM_USER_GROUP && \$_CAMILA['user_visibility_type'] != 'group' && is_object(\$form->fields['$field'])) \$form->fields['$field']->updatable = false;";
		}

        return $script;        
    }
    
    
    function admin()
    {
        
        if (camila_form_in_update_mode(CAMILA_TABLE_WORKT)) {
            
            require_once(CAMILA_DIR . 'datagrid/db_form.class.php');
            global $_CAMILA;
            
            $options = ' ,';
            $query   = 'select short_title from ' . CAMILA_TABLE_PLANG . ' where page_url LIKE ' . $_CAMILA['db']->qstr('cf_app.php?cat%') . ' and lang=' . $_CAMILA['db']->qstr($_CAMILA['lang']) . ' order by page_url';
            $result  = $_CAMILA['db']->Execute($query);
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
            
            while (!$result->EOF) {
                $options .= ',' . $result->fields['short_title'];
                $result->MoveNext();
            }
            
            require_once(CAMILA_DIR . 'datagrid/db_form.class.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/integer.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/static_listbox.php');
            
            $dbform = new dbform(CAMILA_TABLE_WORKT, 'id');
            
            new form_hidden($dbform, 'id');
            new form_integer($dbform, 'sequence', camila_get_translation('camila.worktable.field.sequence'), true, 3);
            new form_textbox($dbform, 'short_title', camila_get_translation('camila.worktable.name'), true, 20);
            new form_textbox($dbform, 'full_title', camila_get_translation('camila.worktable.desc'), true, 40, 40);
            new form_static_listbox($dbform, 'category', camila_get_translation('camila.worktable.category'), $options);
            new form_textbox($dbform, 'filter', camila_get_translation('camila.worktable.visibility.filter'), false, 40, 250);
            new form_textbox($dbform, 'share_key', camila_get_translation('camila.worktable.share.key'), false, 40, 40);
            new form_static_listbox($dbform, 'share_caninsert', camila_get_translation('camila.worktable.share.caninsert'), camila_get_translation('camila.worktable.options.noyes'));
            new form_static_listbox($dbform, 'share_canupdate', camila_get_translation('camila.worktable.share.canupdate'), camila_get_translation('camila.worktable.options.noyes'));
            new form_static_listbox($dbform, 'share_candelete', camila_get_translation('camila.worktable.share.candelete'), camila_get_translation('camila.worktable.options.noyes'));

			$dbform->formupdatelinktext = camila_get_translation('camila.worktable.field.formupdatelinktext');
            $dbform->onupdate           = camila_configurator_worktable_title_db_onupdate;
            
            $dbform->process();
            $dbform->draw();
            
        } else {
            
            require(CAMILA_DIR . 'datagrid/report.class.php');
            
            $report_fields            = 'id as camila_worktable_delete,id as camila_worktable_reconfig,id as camila_worktable_import,id as camila_worktable_rebuild,sequence,short_title,full_title,category,share_key,share_caninsert,share_canupdate,share_candelete';
            $default_fields           = $report_fields;
            $mapping                  = camila_get_translation('camila.worktable.mapping.worktable.admin');
            $stmt                     = 'select ' . $report_fields . ' from ' . CAMILA_TABLE_WORKT;
            $report                   = new report($stmt, '', 'sequence', 'asc', $mapping, null, 'id', '', '', false, false);
            $report->additional_links = Array(
                camila_get_translation('camila.report.insertnew') => 'cf_worktable_wizard.php'
            );
            
            $report->process();
            $report->fields['camila_worktable_reconfig']->onprint = camila_configurator_reconfig;
            $report->fields['camila_worktable_reconfig']->dummy   = true;
            
            $report->fields['camila_worktable_import']->onprint = camila_configurator_import;
            $report->fields['camila_worktable_import']->dummy   = true;
            
            $report->fields['camila_worktable_rebuild']->onprint = camila_configurator_rebuild;
            $report->fields['camila_worktable_rebuild']->dummy   = true;
            
            $report->fields['camila_worktable_delete']->onprint = camila_configurator_delete;
            $report->fields['camila_worktable_delete']->dummy   = true;
            
            //$report->fields['camila_worktable_templates']->onprint = camila_configurator_templates;
            //$report->fields['camila_worktable_templates']->dummy = true;
            
            $report->draw();
            
        }
    }
    
    
    function admin_categories()
    {
        
        global $_CAMILA;
        require_once(CAMILA_DIR . 'datagrid/db_form.class.php');
        require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
        require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
        require_once(CAMILA_DIR . 'datagrid/elements/form/integer.php');
        
        $dbform = new dbform(CAMILA_TABLE_PLANG, 'page_url', 'short_title,full_title', 'page_url', 'asc', 'page_url LIKE ' . $_CAMILA['db']->qstr('cf_app.php?cat%') . ' and lang=' . $_CAMILA['db']->qstr($_CAMILA['lang']));
        
        new form_hidden($dbform, 'page_url');
        new form_textbox($dbform, 'short_title', camila_get_translation('camila.worktable.name'), true, 20);
        new form_textbox($dbform, 'full_title', camila_get_translation('camila.worktable.desc'), true, 40, 20);
        
        $dbform->mapping  = camila_get_translation('camila.worktable.categories.mapping');
        $dbform->onupdate = camila_configurator_worktable_description_db_onupdate;
        
        $dbform->process();
        $dbform->draw();
        
    }
    
    
    function xls_import($id, $returl = '')
    {
        
        global $_CAMILA;
        
        require_once(CAMILA_DIR . 'datagrid/form.class.php');
        require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
        require_once(CAMILA_DIR . 'datagrid/elements/form/filebox.php');
        require_once(CAMILA_DIR . 'datagrid/elements/form/static_listbox.php');
        
        //if ($returl != '')
        //    $form3 = new phpform('camilastep4', $returl);
        //else
        $form3 = new phpform('camilastep4', 'cf_worktable_wizard_step4.php');
        
        $form3->submitbutton = camila_get_translation('camila.wizard.next');
        $form3->drawrules    = false;
        new form_hidden($form3, 'custom', $id);
        
        if ($returl != '')
            new form_hidden($form3, 'returl', $_REQUEST['camila_returl']);
        
        new form_filebox($form3, 'filename', camila_get_translation('camila.worktable.xls.choose'), 50, CAMILA_TMP_DIR);
        
        $sheet_list = '';
        for ($i = 0; $i < 10; $i++) {
            if ($i > 0)
                $sheet_list .= ',';
            $sheet_list .= ($i) . ';' . ($i + 1);
        }
        
        new form_static_listbox($form3, 'sheetnum', camila_get_translation('camila.worktable.xls.sheetnum'), $sheet_list);
		$form3->fields['sheetnum']->set_br(2);
					
        $success = true;
        
        if ($form3->process()) {
            $filename = $form3->fields['filename']->value[0];
            $sheetnum = $form3->fields['sheetnum']->value;
            
            $result = $_CAMILA['db']->Execute('select short_title, scriptname, tablename, filename, sheetnum from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($id));
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
            
            $table           = $result->fields['tablename'];
            $worktablename   = $result->fields['short_title'];
            $worktablescript = $result->fields['scriptname'];
            
            if ($filename == '' && $result->fields['filename'] != '') {
                $filename = $result->fields['filename'];
                $sheetnum = $result->fields['sheetnum'];
            }
            
            if ($filename != '') {
                require_once(CAMILA_LIB_DIR . 'php-excel-reader/excel_reader2.php');
                $data = new Spreadsheet_Excel_Reader(CAMILA_TMP_DIR . '/' . $filename);
                
                $excelColNames = Array();
                
                $i = 0;
                while ($data->val(1, $i + 1, $sheetnum) != '') {
                    $name              = $data->val(1, $i + 1, $sheetnum);
                    $excelColNames[$i] = camila_strtoupper_utf8(isUTF8($name) ? $name : utf8_encode($name));
                    $i++;
                }
                
                $result = $_CAMILA['db']->Execute('select * from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $_CAMILA['db']->qstr($id) . ' and is_deleted<>' . $_CAMILA['db']->qstr('y') . ') order by sequence');
                if ($result === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                
                $fields       = Array();
                $types        = Array();
                $defVals      = Array();
                $forceCase    = Array();
                $orig_types   = Array();
                $fieldMapping = Array();
                
                $forceArr = camila_get_translation_array('camila.worktable.options.force');
                
                $count = 0;
                
                while (!$result->EOF) {
                    $colName                = $result->fields['col_name'];
                    $name                   = camila_strtoupper_utf8($result->fields['name']);
                    $fieldMapping[$colName] = isUTF8($name) ? $name : utf8_encode($name);
                    $fields[$count]         = $colName;
                    $types[$count]          = $result->fields['type'];
                    $orig_types[$count]     = $result->fields['orig_type'];
                    $defVals[$count]        = $result->fields['default_value'];
                    $forceCase[$count]      = $result->fields['force_case'];
                    $count++;
                    $result->MoveNext();
                }
                
                $successCount = 0;
                $failCount    = 0;
				$totalRowCount = $data->rowcount($sheetnum);
				//$trPending = false;

                //db fields
                for ($i = 2; $i <= $totalRowCount; $i++) {
					
					$_CAMILA['db']->beginTrans();
					//$trPending = true;

                    $record   = Array();
                    $emptyrow = true;
                    
                    //db fields
                    reset($fields);
                    foreach ($fields as $k => $v) {
                        
                        //k  Field position into database
                        //k2 Position in Excel file
                        $k2 = array_search($fieldMapping[$v], $excelColNames);
                        
                        //Is it in Excel file?
                        if ($k2 !== false) {
                            
                            $excelColName     = camila_strtoupper_utf8($data->value(1, $k2 + 1, $sheetnum));
                            //$excelColName = $v;
                            $worktableColName = array_search($excelColName, $fieldMapping);
                            $worktableColName = $v;
                            
                            if ($worktableColName != '') {
                                if ($types[$k] == 'date' && $data->val($i, $k2 + 1, $sheetnum) != '') {
                                    $numValue = $data->sheets[$sheetnum]['cellsInfo'][$i][$k2 + 1]['raw'];
                                    
                                    $utcDays  = floor($numValue - ($data->nineteenFour ? SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904 : SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS));
                                    $utcValue = ($utcDays) * SPREADSHEET_EXCEL_READER_MSINADAY;
                                    $dateinfo = gmgetdate($utcValue);
                                    
                                    $fractionalDay = $numValue - floor($numValue) + .0000001; // The .0000001 is to fix for php/excel fractional diffs
                                    
                                    $totalseconds = floor(SPREADSHEET_EXCEL_READER_MSINADAY * $fractionalDay);
                                    $secs         = $totalseconds % 60;
                                    $totalseconds -= $secs;
                                    $hours                     = floor($totalseconds / (60 * 60));
                                    $mins                      = floor($totalseconds / 60) % 60;
                                    $dt                        = date('Y-m-d', mktime($hours, $mins, $secs, $dateinfo["mon"], $dateinfo["mday"], $dateinfo["year"]));
                                    $record[$worktableColName] = $_CAMILA['db']->BindDate($dt);
                                } elseif ($orig_types[$k] == 'number' && $data->sheets[$sheetnum]['cellsInfo'][$i][$k2 + 1]['raw'] != '')
                                    $record[$worktableColName] = $data->sheets[$sheetnum]['cellsInfo'][$i][$k2 + 1]['raw'];
                                elseif ($types[$k] == 'hyperlink' && $data->hyperlink($i, $k2 + 1, $sheetnum) != '') {
                                    //$record[$worktableColName] = '<a href="' . $data->hyperlink($i, $k2+1, $sheetnum) . '" target="_blank">' . $data->value($i, $k2+1, $sheetnum) . '</a>';
                                    $record[$worktableColName] = $data->hyperlink($i, $k2 + 1, $sheetnum);
                                }
								elseif ($types[$k] == 'datetime' && $data->value($i, $k2 + 1, $sheetnum) != '' && strlen($data->value($i, $k2 + 1, $sheetnum))==19) {
									$value = $data->value($i, $k2 + 1, $sheetnum);
									$mm = substr($value, camila_get_translation('camila.dateformat.monthpos'), 2);
									$dd = substr($value, camila_get_translation('camila.dateformat.daypos'), 2);
									$yyyy = substr($value, camila_get_translation('camila.dateformat.yearpos'), 4);
									$h = substr($value, 11, 2);
									$m = substr($value, 14, 2);
									$s = substr($value, 17, 2);
									$dbVal = $_CAMILA['db']->BindTimeStamp($yyyy.'-'.$mm.'-'.$dd.' '.$h.':'.$m.':'.$s);									
									$record[$worktableColName] = $dbVal;
                                }
								else
                                    $record[$worktableColName] = $data->value($i, $k2 + 1, $sheetnum);
                                
                                if ($defVals[$k] != '' && $record[$worktableColName] == '')
                                    $record[$worktableColName] = camila_parse_default_expression($defVals[$k], '_camila_seq_num_', true);
                                
                                if ($record[$worktableColName] != '') {
                                    if ($forceCase[$k] == 'upper')
                                        $record[$worktableColName] = mb_strtoupper($record[$worktableColName], 'UTF-8');
                                    if ($forceCase[$k] == 'lower')
                                        $record[$worktableColName] = mb_strtolower($record[$worktableColName], 'UTF-8');
                                    $emptyrow = false;
                                }
                            }
                        } else {
                            if ($defVals[$k] != '')
                                $record[$fields[$k]] = camila_parse_default_expression($defVals[$k], '_camila_seq_num_', true);
                        }
                        
                    }
                    
                    if (!$emptyrow) {

                        $now = $_CAMILA['db']->BindTimeStamp(gmdate("Y-m-d H:i:s", time()));
                        $id  = $_CAMILA['db']->GenID(CAMILA_APPLICATION_PREFIX.'worktableseq', 100000);
                      
                        foreach ($record as $k => $v)
                            $record[$k] = str_replace('_camila_seq_num_', $id, $v);

                        $record['id']                  = $id;
						//echo '<'.$record['id'].'>';
                        $record['created']             = $now;
                        $record['created_by']          = $_CAMILA['user'];
                        $record['created_src']         = 'import';
                        $record['created_by_surname']  = $_CAMILA['user_surname'];
                        $record['created_by_name']     = $_CAMILA['user_name'];
                        $record['last_upd']            = $now;
                        $record['last_upd_by']         = $_CAMILA['user'];
                        $record['last_upd_src']        = 'import';
                        $record['last_upd_by_surname'] = $_CAMILA['user_surname'];
                        $record['last_upd_by_name']    = $_CAMILA['user_name'];
                        $record['mod_num']             = 0;
                        
                        $insertSQL = $_CAMILA['db']->AutoExecute($table, $record, 'INSERT');
                        
                        if (!$insertSQL) {
                            //camila_information_text(camila_get_translation('camila.worktable.db.importerror'));
                            $failCount++;
                            $success = false;
                        } else
                            $successCount++;
                    }

					if ($i % 200 == 0)
					{
						$_CAMILA['db']->commitTrans();
						$_CAMILA['db']->beginTrans();
					}
                }
            }
			
			$_CAMILA['db']->commitTrans();
			
			$myText = new CHAW_text('');
			$_CAMILA['page']->add_text($myText);

            camila_information_text(camila_get_translation('camila.worktable.db.importedrows') . ': ' . $successCount);
            camila_information_text(camila_get_translation('camila.worktable.db.skippedrows') . ': ' . $failCount);
            
            @unlink(CAMILA_TMP_DIR . '/' . $filename);
            
        } else {
            $result = $_CAMILA['db']->Execute('select tablename, filename, sheetnum from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($id));
            if ($result === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
            
            $filename = $result->fields['filename'];
            
            //            if ($filename != '') {
            //888
			
			$myText = new CHAW_text('');
			$_CAMILA['page']->add_text($myText);

			
			
			$myText = new CHAW_text(camila_get_translation('camila.wizard.choosefileforimport'));
            $_CAMILA['page']->add_text($myText);
            $form3->draw();
            $success = false;

            //	    }
        }
        
        if ($success) {
            if ($worktablename != '') {
				$myText = new CHAW_text('');
				$_CAMILA['page']->add_text($myText);
                $myLink = new CHAW_link($worktablename, $worktablescript);
                $myLink->set_br(0);
                $_CAMILA['page']->add_link($myLink);
                
                $myText = new CHAW_text(' - ' . camila_get_translation('camila.worktable.db.importok'));
                $_CAMILA['page']->add_text($myText);
            } else {
				$myText = new CHAW_text('');
				$_CAMILA['page']->add_text($myText);
                $myText = new CHAW_text(camila_get_translation('camila.wizard.configurationapplied'));
                $_CAMILA['page']->add_text($myText);
				
				/////
				$this->camila_delete_files(CAMILA_TMP_DIR);
            }
            
        }
        
    }
    
    
    function get_field_name($name)
    {
        $fieldname = preg_replace('/[^a-z]/', '', strtolower($name));
        
        if ($fieldname == '')
            $fieldname = 'field';
        
        $origfieldname = $fieldname;
        
        if (!$this->check_fieldname($fieldname)) {
            $i = 0;
            do {
                $i++;
                $fieldname = $origfieldname . $i;
            } while (!$this->check_fieldname($fieldname));
            
        }
        
        $this->fields[] = $fieldname;
        
        return $fieldname;
    }
    
    
    function check_fieldname($field)
    {
        
        if (in_array(strtoupper($field), $this->sql_reserved_words) || in_array($field, $this->fields))
            return false;
        else
            return true;
    }
    
    
    /*function add_require($require)
    {
        
        if (!in_array($require, $this->requires))
            $this->requires[] = $require;
    }*/
    
    
    function escape($string)
    {
        return str_replace("'", "\'", $string);
    }
    
    
    function is_configuration_sheet($data, $sheetnum)
    {
        
        $confCell = utf8_encode($data->val(17, 1, $sheetnum));
        $text     = camila_get_translation('camila.worktable.configuration');
        if (!isUTF8($text))
            $text = utf8_encode($text);
        
        return ($confCell == $text);
    }
    
    
    function operation($id, $operation, $returl)
    {
        
        global $_CAMILA;
        
        if ($operation == 'delete') {
            require_once(CAMILA_DIR . 'datagrid/form.class.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
            
            $form3               = new phpform('camila', 'cf_worktable_admin.php');
            $form3->submitbutton = camila_get_translation('camila.worktable.delete');
            $form3->drawrules    = false;
            new form_hidden($form3, 'custom', $id);
            new form_hidden($form3, 'worktable_op', $operation);
            if ($returl != '')
                new form_hidden($form3, 'returl', $returl);
            
            if ($form3->process()) {
                
                $result = $_CAMILA['db']->Execute('select tablename,scriptname from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($id));
                if ($result === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                
                $table  = $result->fields['tablename'];
                $script = $result->fields['scriptname'];
                
                $result = $_CAMILA['db']->Execute('delete from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($id));
                if ($result === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                
                $result = $_CAMILA['db']->Execute('delete from ' . CAMILA_TABLE_WORKC . ' where (wt_id=' . $_CAMILA['db']->qstr($id) . ' and is_deleted<>' . $_CAMILA['db']->qstr('y') . ')');
                if ($result === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                
                $stmt   = sprintf($_CAMILA['db']->_dropSeqSQL, $table);
                $result = $_CAMILA['db']->Execute($stmt);
                //if ($result === false)
                //    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                
                $record            = Array();
                $record['visible'] = 'no';
                $record['active']  = 'no';
                
                $updateSQL = $_CAMILA['db']->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url=' . $_CAMILA['db']->qstr($script));
                //if (!$updateSQL) {
                //    camila_information_text(camila_get_translation('camila.worktable.db.error'));
                //    $success3 = false;
                //}
            } else {
				$myText = new CHAW_text('');
                $myText->set_br(1);
                $_CAMILA['page']->add_text($myText);

                camila_information_text(camila_get_translation('camila.worktable.delete.areyousure'));
                
                $result = $_CAMILA['db']->Execute('select short_title from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($id));
                if ($result === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
                
                $name = $result->fields['short_title'];
                
                $myText = new CHAW_text($name, HAW_TEXTFORMAT_BOLD);
                $myText->set_br(2);
                $_CAMILA['page']->add_text($myText);
                $form3->draw();
                $success = false;
            }
        } elseif ($operation == 'rebuild') {
            require_once(CAMILA_DIR . 'datagrid/form.class.php');
            require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
            
            $form3               = new phpform('camila', 'cf_worktable_admin.php');
            $form3->submitbutton = camila_get_translation('camila.worktable.delete');
            $form3->drawrules    = false;
            new form_hidden($form3, 'custom', $id);
            new form_hidden($form3, 'worktable_op', $operation);
            if ($returl != '')
                new form_hidden($form3, 'returl', $returl);
            
            
            if ($form3->process()) {
                
                $this->configure_table($id, true, $returl);
                
            } else {
				$myText = new CHAW_text('');
                $myText->set_br(1);
                $_CAMILA['page']->add_text($myText);
                camila_error_text(camila_get_translation('camila.worktable.rebuild.areyousure'));
                
                $result = $_CAMILA['db']->Execute('select short_title from ' . CAMILA_TABLE_WORKT . ' where id=' . $_CAMILA['db']->qstr($id));
                if ($result === false)
                    camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
              
                $name = $result->fields['short_title'];
                
                $myText = new CHAW_text($name, HAW_TEXTFORMAT_BOLD);
                $myText->set_br(2);
                $_CAMILA['page']->add_text($myText);
                $form3->draw();
                $success = false;
            }
        } elseif ($operation == 'showtemplatefields') {
            $stmt = "select wt_id,name,name_abbrev,col_name as colname_form, col_name as colname_table from " . CAMILA_TABLE_WORKC . ' where (wt_id=' . $_CAMILA['db']->qstr($id) . ' and is_deleted<>' . $_CAMILA['db']->qstr('y') . ') order by name';
            
            require_once(CAMILA_DIR . 'datagrid/report.class.php');
            $report          = new report($stmt);
            $report->mapping = camila_get_translation('camila.worktable.mapping.worktable');
            
            $report->process();

            $report->fields['colname_form']->onprint   = camila_configurator_template_fieldname_form;
            $report->fields['colname_form']->dummy     = true;
            $report->fields['colname_form']->orderable = false;
            
            $report->fields['colname_table']->onprint   = camila_configurator_template_fieldname_table;
            $report->fields['colname_table']->dummy     = true;
            $report->fields['colname_table']->orderable = false;
            
            $report->fields['wt_id']->print = false;
            
            $report->fields['name']->orderable        = false;
            $report->fields['name_abbrev']->orderable = false;
            
            $report->drawfilterbox     = false;
            $report->drawnavigationbox = false;
            $report->draw();
        }
        
        if ($success) {
			$myText = new CHAW_text('');
			$_CAMILA['page']->add_text($myText);
            $myText = new CHAW_text(camila_get_translation('camila.worktable.db.importok'));
            $_CAMILA['page']->add_text($myText);
        }
    }
	
	function camila_delete_files($directory) {

    if( !$dirhandle = @opendir($directory) )
        return;

        while( false !== ($filename = readdir($dirhandle)) ) {
            if( $filename != '.' && $filename != '..' ) {
                $filename = $directory. '/'. $filename;

                if (!unlink($filename))
                    echo 'Error deleting ' . $filename;
        }
    }
}
    
}

?>