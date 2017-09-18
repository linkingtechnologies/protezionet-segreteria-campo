<?php
defined('CAMILA_APPLICATION_NAME') or die('No direct script access.');

  $myText = new CHAW_text('');
  $_CAMILA['page']->add_text($myText);
  
  if (isset($_REQUEST['dbschema']))
      require_once(CAMILA_DIR . '/admin/db_schema.php');

  if (isset($_REQUEST['dbfiles']) || isset($_REQUEST['camila_dbfiles']))
      require_once(CAMILA_DIR . '/admin/db_files.php');

  if (isset($_REQUEST['dbquery']) || isset($_REQUEST['camila_dbquery']) || isset($_REQUEST['camila_custom']))
      require_once(CAMILA_DIR . '/admin/db_query.php');

  if (isset($_REQUEST['dbexport']))
      require_once(CAMILA_DIR . '/admin/db_export.php');

  if (isset($_REQUEST['dbimport']))
      require_once(CAMILA_DIR . '/admin/db_import.php');

  if (isset($_REQUEST['tmplfiles']) || isset($_REQUEST['camila_tmplfiles']))
      require_once(CAMILA_DIR . '/admin/tmpl_files.php');

  if (isset($_REQUEST['tmplimages']) || isset($_REQUEST['camila_tmplimages']))
      require_once(CAMILA_DIR . '/admin/tmpl_images.php');

  if (isset($_REQUEST['users']) || isset($_REQUEST['camila_users']))
      require_once(CAMILA_DIR . '/admin/users.php');

  if (isset($_REQUEST['bookmarks']) || isset($_REQUEST['camila_bookmarks']))
      require_once(CAMILA_DIR . '/admin/bookmarks.php');

?>