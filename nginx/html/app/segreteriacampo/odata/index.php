<?php
  require '../../../camila/odata/AltoRouter.php';
  require '../../../camila/odata/Controller.class.php';

  $folder = dirname($_SERVER['SCRIPT_NAME']);
  $lang = $_REQUEST['lang'];
  $router = new AltoRouter();
  $router->setBasePath($folder);

  $controller = new Controller($_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'], $folder, 'worktable', 'db.sqlite');
  $controller->lang=$lang;
  $router->map( 'GET', '/'.$lang, function() use ($controller) { $controller->service_description(); });
  $router->map( 'GET', '/'.$lang.'/[\$metadata:cmd]', function() use ($controller) { $controller->service_metadata(); });
  $router->map( 'GET', '/'.$lang.'/[a:collection]', function($collection, $query_string_parameters = array()) use ($controller) { $controller->serve_collection($collection, $query_string_parameters); });
  $router->map( 'GET', '/'.$lang.'/[a:collection]/', function($collection, $query_string_parameters = array()) use ($controller) { $controller->serve_collection($collection, $query_string_parameters); });
  $router->map( 'GET', '/'.$lang.'/[a:collection]\([a:id]\)', function($collection, $id) use ($controller) { $controller->serve_entry($collection, $id); });
  $router->map( 'GET', '/'.$lang.'/[a:collection]/[\$count:count]', function($collection) use ($controller) { $controller->count_collection($collection); });
  
  $router->map( 'PUT', '/[a:collection]\([a:id]\)', function($collection, $id) use ($controller) { $controller->update_entry($collection, $id); });
  $router->map( 'POST', '/[a:collection]', function($collection) use ($controller) { $controller->create_entry($collection); });
  $router->map( 'POST', '/[a:collection]/', function($collection) use ($controller) { $controller->create_entry($collection); });
  $router->map( 'DELETE', '/[a:collection]\([a:id]\)', function($collection, $id) use ($controller) { $controller->delete_entry($collection, $id); });
  
  $match = $router->match();
  
  if( $match && is_callable( $match['target'] ) ) {
      call_user_func_array( $match['target'], $match['params'] ); 
  }
?>