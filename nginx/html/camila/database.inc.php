<?php
require(CAMILA_LIB_DIR.'adodb5/adodb.inc.php');

ob_start();

//$_CAMILA['db'] = NewADOConnection(CAMILA_DB_DRIVER);
$_CAMILA['db'] = NewADOConnection(CAMILA_DB_DSN);
$_CAMILA['db_error'] = strip_tags(ob_get_contents());

//$_CAMILA['db']->Execute("SET CHARACTER SET 'utf8'");

ob_end_clean();

if (is_object($_CAMILA['db']))
	$_CAMILA['db']->Execute("set names 'utf8'");

//  if (!@$_CAMILA['db']->Connect(CAMILA_DB_HOST, CAMILA_DB_USER, CAMILA_DB_PASS, CAMILA_DB_NAME))
//      camila_error_page($_CAMILA['db']->ErrorMsg());

?>