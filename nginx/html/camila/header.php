<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2022 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */


require_once('camila.php');


require_once('autoloader.inc.php');

if (defined('CAMILA_AUTH_PASSWORD_HASHING') && (CAMILA_AUTH_PASSWORD_HASHING) && !function_exists('password_verify')){
	require_once(CAMILA_LIB_DIR . 'ircmaxell/password_compat/password.php');
}

$camilaAuth = new CamilaAuth();
$camilaAuth->db = $_CAMILA['db'];
$camilaAuth->userTable = CAMILA_TABLE_USERS;
$camilaAuth->authUserTable = CAMILA_AUTH_TABLE_USERS;
$camilaAuth->applicationName = CAMILA_APPLICATION_NAME;

$useSmartMenus = true;
$smartMenusTheme = 'sm-camila';

  function camila_ob_file_callback($buffer)
  {
    global $_CAMILA;
    if (!$_CAMILA['page']->camila_export_file_exists || $_REQUEST['camila_export_overwrite'] == 'y') {
        fwrite($_CAMILA['ob_file'],$buffer);
        fclose($_CAMILA['ob_file']);
    }
  }

  $camila_time_start = camila_microtime_float();
  $camila_auth_fail = false;

  if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
      camila_stripslashes_array($_GET);
      camila_stripslashes_array($_POST);
      camila_stripslashes_array($_REQUEST);
      camila_stripslashes_array($_COOKIE);
  }

  if (!camila_isUTF8($_GET))
      camila_utf8_encode_array($_GET);

  if (!camila_isUTF8($_POST))
      camila_utf8_encode_array($_POST);

  if (!camila_isUTF8($_REQUEST))
      camila_utf8_encode_array($_REQUEST);

  if (!camila_isUTF8($_COOKIE))
      camila_utf8_encode_array($_COOKIE);

  if (isset($_REQUEST[CAMILA_APPLICATION_NAME]))
      session_id($_REQUEST[CAMILA_APPLICATION_NAME]);

  //session_set_cookie_params(3600);

  ini_set("magic_quotes_runtime", 0);
  //24/04/16:Disabled
  //$old_error_handler = set_error_handler('camila_error_handler');
  if (!isset($_REQUEST['camila_session_id']))  session_start();

  $_CAMILA['user_level'] = CAMILA_DEFAULT_USER_LEVEL;
  $_CAMILA['user_group'] = '';
  $_CAMILA['page_login'] = 0;
  $_CAMILA['javascript_enabled'] = 0;
  $_CAMILA['skin'] = 0;
  $_CAMILA['user_loggedin'] = 0;
  $_CAMILA['output'] = HAW_OUTPUT_AUTOMATIC;
  $_SERVER['HTTP_ACCEPT_LANGUAGE'];
  $_CAMILA['lang'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
  if ($_CAMILA['lang'] == '')
	  $_CAMILA['lang'] = 'it';


  if (isset($_REQUEST['output']) && $_REQUEST['output']!='') {
      $_CAMILA['output'] = $_REQUEST['output'];
  }

  if (isset($_REQUEST['lang']) && $_REQUEST['lang']!='') {
      $_CAMILA['lang'] = $_REQUEST['lang'];
  }
  
  if (!CAMILA_LOGIN_MLANG)
	  $_CAMILA['lang'] = CAMILA_LANG;

  if (isset($_REQUEST['openid_mode']) || (isset($HTTP_COOKIE_VARS['camila_js_enabled']) && $HTTP_COOKIE_VARS['camila_js_enabled'] == '1') || (isset($_REQUEST['js']) && $_REQUEST['js'] == 'enabled'))
      $_CAMILA['javascript_enabled'] = 1;

  // tentativo di accesso
  if (isset($_REQUEST['camila_login_token']) || isset($_REQUEST['openid_mode']) || (((isset($_REQUEST['camila_s'])) && (isset($_REQUEST['camila_n'])) && (isset($_REQUEST['camila_1'])) && (isset($_REQUEST['camila_2'])) && (isset($_REQUEST['camila_3']))) || (isset($_REQUEST['submit'])) && (isset($_REQUEST['camila_pwloginbox'])) && ($_REQUEST['camila_pwloginbox'] == 'yes'))) {

      $openid_username = '';

      if (isset($_REQUEST['openid_mode'])) {
          require CAMILA_DIR . 'openid_common.php';

          $consumer = getConsumer();

          // Complete the authentication process using the server's
          // response.
          $return_to = getReturnTo();
          $response = $consumer->complete($return_to);

          // Check the response status.
          if ($response->status == Auth_OpenID_CANCEL) {
              // This means the authentication was cancelled.
              $msg = 'Verification cancelled.';
          } else if ($response->status == Auth_OpenID_FAILURE) {
              // Authentication failed; display the error message.
              $msg = "OpenID authentication failed: " . $response->message;
          } else if ($response->status == Auth_OpenID_SUCCESS) {
              // This means the authentication succeeded
              $ax = new Auth_OpenID_AX_FetchResponse();
              $obj = $ax->fromSuccessResponse($response);
              $openid_username = $obj->data['http://axschema.org/contact/email'][0];
          }

      }

      if (isset($_REQUEST['p1']))
          $p1 = $_REQUEST['p1'];
      else
          $p1 = $_REQUEST['camila_1'];

      if (isset($_REQUEST['p2']))
          $p2 = $_REQUEST['p2'];
      else
          $p2 = $_REQUEST['camila_2'];

      if (isset($_REQUEST['p3']))
          $p3 = $_REQUEST['p3'];
      else
          $p3 = $_REQUEST['camila_3'];

      if (isset($_REQUEST['name']))
          $name = $_REQUEST['name'];
      else
          $name = $_REQUEST['camila_n'];

      if (isset($_REQUEST['surname']))
          $surname = $_REQUEST['surname'];
      else
          $surname = $_REQUEST['camila_s'];

      if ((isset($_REQUEST['js']) && $_REQUEST['js'] == 'enabled') || isset($_REQUEST['openid_mode']))
          $_CAMILA['javascript_enabled'] = '1';
      else
          $_CAMILA['javascript_enabled'] = '0';

      setcookie('camila_js_enabled', $_CAMILA['javascript_enabled'], time() + 3600 * 3600);

      $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);
      $attribs = 'attrib_01,attrib_02,attrib_03,attrib_04,attrib_05,attrib_06,attrib_07,attrib_08,attrib_09,attrib_10,attrib_11,attrib_12,attrib_13,attrib_14,attrib_15,';

      if ($_REQUEST['camila_login_token'] != '')
          $query = 'SELECT id, '.$attribs.'visibility_type, level, ' . CAMILA_TABLE_USERS . '.grp, surname, name, password, preferences, session_id FROM ' . CAMILA_TABLE_USERS . " WHERE token='" . $_REQUEST['camila_login_token'] . "'";
      elseif ($openid_username != '')
          $query = 'SELECT id, '.$attribs.'visibility_type, level, ' . CAMILA_TABLE_USERS . '.grp, surname, name, password, preferences, session_id FROM ' . CAMILA_TABLE_USERS . " WHERE UPPER(username)=UPPER('" . $openid_username . "')";
      elseif (CAMILA_CHALLENGE_LOGIN)
          $query = 'SELECT id, '.$attribs.'visibility_type, level, ' . CAMILA_TABLE_USERS . '.grp, surname, name, password, preferences, session_id FROM ' . CAMILA_TABLE_USERS . " WHERE UPPER(name)=UPPER('" . $name . "') AND UPPER(surname)=UPPER('" . $surname . "')";
      else {
          if (CAMILA_USERNAME_LOGIN) {
              //$query = 'SELECT id, '.$attribs.' visibility_type, level, ' . CAMILA_TABLE_USERS . '.grp, username, password, preferences, session_id FROM ' . CAMILA_TABLE_USERS . " WHERE UPPER(username)=UPPER('" . $_REQUEST['username'] . "')";
			  $query = $camilaAuth->getAuthUserInfoSqlFromUsername($_REQUEST['username']);
          }
		  else
              $query = 'SELECT id, '.$attribs.' visibility_type, level, ' . CAMILA_TABLE_USERS . '.grp, username, name, surname, password, preferences, session_id FROM ' . CAMILA_TABLE_USERS . " WHERE UPPER(name)=UPPER('" . $_REQUEST['name'] . "') AND UPPER(surname)=UPPER('" . $_REQUEST['surname'] . "')";
      }

	  
      //$result = $_CAMILA['db']->Execute($query);

	  $dbAuth = $camilaAuth->getAuthDatabaseConnection(CAMILA_AUTH_DSN);
	  $dbAuth->SetFetchMode(ADODB_FETCH_ASSOC);
	  //echo $query;
	  //print_r($dbAuth);
	  $result = $dbAuth->Execute($query);	  

      if ($result === false)
          camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

      $table = $result->fields;

      $c1 = '';
      $c2 = '';
      $c3 = '';
      camila_challenge_numbers($c1, $c2, $c3);

      if ( ($_REQUEST['camila_login_token'] != '' && $result->RecordCount() == 1 ) || ($openid_username!= '' && $result->RecordCount() == 1 ) || ((CAMILA_CHALLENGE_LOGIN && (substr($table['password'], $c1 - 1, 1) == $p1) && (substr($table['password'], $c2 - 1, 1) == $p2) && (substr($table['password'], $c3 - 1, 1) == $p3) || (CAMILA_USERNAME_LOGIN && !CAMILA_CHALLENGE_LOGIN && $camilaAuth->checkPasswords($_REQUEST['pwd'], $table['password']) && (strtoupper($table['username']) == strtoupper($_REQUEST['username']))) || (!CAMILA_USERNAME_LOGIN && !CAMILA_CHALLENGE_LOGIN && ($camilaAuth->checkPasswords($_REQUEST['pwd'], $table['password'])) && (strtoupper($table['name']) == strtoupper($_REQUEST['name'])) && (strtoupper($table['surname']) == strtoupper($_REQUEST['surname'])))  ) && $table['password'] != '')) {

	  $query = $camilaAuth->getUserInfoSqlFromUsername($_REQUEST['username']);
	        $result = $_CAMILA['db']->Execute($query);
			$table = $result->fields;

          $_CAMILA['user'] = $table['username'];
          $_CAMILA['user_id'] = $table['id'];
          $_CAMILA['user_level'] = $table['level'];
          $_CAMILA['user_group'] = $table['grp'];
          $_CAMILA['user_visibility_type'] = $table['visibility_type'];
          $_CAMILA['adm_user_group'] = ($_CAMILA['user_group'] == '' ? CAMILA_ADM_USER_GROUP : $_CAMILA['user_group']);
          $_CAMILA['user_surname'] = $table['surname'];
          $_CAMILA['user_name'] = $table['name'];
          $_CAMILA['user_preferences'] = unserialize($table['preferences']);
          $_CAMILA['user_loggedin'] = 1;
          $_CAMILA['skin'] = CAMILA_DEFAULT_JS_SKIN;
          if ($_CAMILA['user_preferences']['c_sk'] != '')
              $_CAMILA['skin'] = $_CAMILA['user_preferences']['c_sk'];

          $_CAMILA['user_attrib_01'] = $table['attrib_01'];
          $_CAMILA['user_attrib_02'] = $table['attrib_02'];
          $_CAMILA['user_attrib_03'] = $table['attrib_03'];
          $_CAMILA['user_attrib_04'] = $table['attrib_04'];
          $_CAMILA['user_attrib_05'] = $table['attrib_05'];
          $_CAMILA['user_attrib_06'] = $table['attrib_06'];
          $_CAMILA['user_attrib_07'] = $table['attrib_07'];
          $_CAMILA['user_attrib_08'] = $table['attrib_08'];
          $_CAMILA['user_attrib_09'] = $table['attrib_09'];
          $_CAMILA['user_attrib_10'] = $table['attrib_10'];
          $_CAMILA['user_attrib_11'] = $table['attrib_11'];
          $_CAMILA['user_attrib_12'] = $table['attrib_12'];
          $_CAMILA['user_attrib_13'] = $table['attrib_13'];
          $_CAMILA['user_attrib_14'] = $table['attrib_14'];
          $_CAMILA['user_attrib_15'] = $table['attrib_15'];

          $send = $_CAMILA['db']->DBTimeStamp(time() + CAMILA_SESSION_DURATION);

          $result = $_CAMILA['db']->Execute('UPDATE '. CAMILA_TABLE_USERS . ' SET session_id=? WHERE session_id=?', Array('',session_id()));
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          $query = 'UPDATE ' . CAMILA_TABLE_USERS . ' SET session_id=?, session_end='.$send.', output=?, lang=? WHERE id=?';

          $result = $_CAMILA['db']->Execute($query, Array(session_id(), $_CAMILA['output'], $_CAMILA['lang'], $table['id']) );
          if ($result === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

          if (isset($_REQUEST['remembersession']) && $_REQUEST['remembersession'] == 'yes')
              setcookie(CAMILA_APPLICATION_NAME, session_id(), time() + CAMILA_SESSION_DURATION, "/", false);
          else
              setcookie(CAMILA_APPLICATION_NAME, '', time() + CAMILA_SESSION_DURATION, "/", false);

          if (CAMILA_ANON_LOGIN)
              setcookie(CAMILA_APPLICATION_NAME . '_username', $_REQUEST['username'], time() + CAMILA_SESSION_DURATION, "/", false);

		  //230515
		  
		  
		  //$sessionLogin = CAMILA_APPLICATION_NAME . '_logged_user';
		  //$_SESSION[$sessionLogin]=$_REQUEST['username'];

		  //echo $_REQUEST['lang'];
		  $camilaAuth->putUsernameIntoSession($_REQUEST['username'], $_REQUEST['lang']);

          if (isset($_REQUEST['camila_redirect'])) {
              $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.pleasewait'), HAW_ALIGN_CENTER);
              require(CAMILA_DIR . 'deck_settings.php');
              $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR.'skin0.css');
              $text = new CHAW_text(camila_get_translation('camila.manualredirect'), HAW_TEXTFORMAT_ITALIC);
              $_CAMILA['page']->add_text($text);

              $myLink = new CHAW_link(camila_get_translation('camila.clickhere'), $_REQUEST['camila_redirect']);
              $_CAMILA['page']->add_link($myLink);
              $_CAMILA['page']->set_redirection(0.1, $_REQUEST['camila_redirect']);
              $_CAMILA['page']->create_page();

              exit();
          }
      }

      else {
          $camila_auth_fail = true;
      }
  }


  /* User authentication */
  $_CAMILA['db']->SetFetchMode(ADODB_FETCH_ASSOC);

  if (CAMILA_ANON_LOGIN)
        $query = 'SELECT * FROM ' . CAMILA_TABLE_USERS . ' WHERE username= ' . $_CAMILA['db']->qstr($_COOKIE[CAMILA_APPLICATION_NAME . '_username']);
//      $query = 'SELECT * FROM ' . CAMILA_TABLE_USERS . ' WHERE session_id= ' . $_CAMILA['db']->qstr(session_id());
  else
  {
      //$query = 'SELECT * FROM ' . CAMILA_TABLE_USERS . ' WHERE session_id=' . $_CAMILA['db']->qstr(!isset($_REQUEST['camila_session_id'])?session_id():$_REQUEST['camila_session_id']) . ' AND session_end>' . $_CAMILA['db']->DBTimeStamp(time());
	  //$query = $camilaAuth->getUserInfoSqlFromSessionId(!isset($_REQUEST['camila_session_id'])?session_id():$_REQUEST['camila_session_id']);
	  
	  //Dummy query
	  $query = 'SELECT * FROM ' . CAMILA_TABLE_USERS . ' WHERE username IS NULL' ;

	  //if ()
		  //$sessionLogin = CAMILA_APPLICATION_NAME . '_logged_user';
		  //$loggedUser = $_SESSION[$sessionLogin];

		  $loggedUser = $camilaAuth->getUsernameFromSession();
		  $sLang = $camilaAuth->getLangFromSession();
		  //echo $sLang;
		  
		  if (CAMILA_LOGIN_MLANG && $sLang != '') {
			  $_CAMILA['lang'] = $sLang;
		  }
		  
		  //echo $loggedUser;
		  if ($loggedUser != '') {
			  //$query = 'SELECT * FROM ' . CAMILA_TABLE_USERS . ' WHERE username = ' . $_CAMILA['db']->qstr($loggedUser);
			  $query = $camilaAuth->getUserInfoSqlFromUsername($loggedUser);
		  }

  }
  
  //echo $query;
  $camilaAuth->getUserLevelFromUsername($camilaAuth->getUsernameFromSession());
  
  //echo $query;

  $result = $_CAMILA['db']->Execute($query);
  if ($result === false) {
	  
	  array_map('unlink', glob(CAMILA_TMP_DIR.'/*'));
	  $_CAMILA['page'] = new CHAW_deck('', HAW_ALIGN_LEFT);
	  //require(CAMILA_DIR . 'import.xls.inc.php');
	  camila_error_page('', camila_get_translation('camila.error.01'), $link);
  }

  $utable = $result->fields;
  $users = $result->RecordCount();
  
  //print_r($result);
  //echo $users;
  //echo $camilaAuth->getUserLevelFromUsername($camilaAuth->getUsernameFromSession());


  if ($users == 1) {
      $_CAMILA['output'] = $utable['output'];
	  $_CAMILA['output'] = HAW_OUTPUT_AUTOMATIC;
      /////////////$_CAMILA['lang'] = $utable['lang'];
      $_CAMILA['user'] = $utable['username'];
      $_CAMILA['user_id'] = $utable['id'];
      $_CAMILA['user_level'] = $utable['level'];
      $_CAMILA['user_group'] = $utable['grp'];
      $_CAMILA['user_visibility_type'] = $utable['visibility_type'];
      $_CAMILA['adm_user_group'] = ($_CAMILA['user_group'] == '' ? CAMILA_ADM_USER_GROUP : $_CAMILA['user_group']);
      $_CAMILA['user_surname'] = $utable['surname'];
      $_CAMILA['user_name'] = $utable['name'];
      $_CAMILA['user_preferences'] = unserialize($utable['preferences']);
      //$_CAMILA['session_countdown'] = ceil($utable['seconds'] / 60);
      $_CAMILA['user_loggedin'] = 1;
      $_CAMILA['skin'] = CAMILA_DEFAULT_JS_SKIN;
      if ($_CAMILA['user_preferences']['c_sk'] != '')
          $_CAMILA['skin'] = $_CAMILA['user_preferences']['c_sk'];
      
      if ($_CAMILA['javascript_enabled'] == 1 && $_CAMILA['skin'] == 0)
          $_CAMILA['skin'] = CAMILA_DEFAULT_JS_SKIN;  

      $_CAMILA['user_attrib_01'] = $utable['attrib_01'];
      $_CAMILA['user_attrib_02'] = $utable['attrib_02'];
      $_CAMILA['user_attrib_03'] = $utable['attrib_03'];
      $_CAMILA['user_attrib_04'] = $utable['attrib_04'];
      $_CAMILA['user_attrib_05'] = $utable['attrib_05'];
      $_CAMILA['user_attrib_06'] = $utable['attrib_06'];
      $_CAMILA['user_attrib_07'] = $utable['attrib_07'];
      $_CAMILA['user_attrib_08'] = $utable['attrib_08'];
      $_CAMILA['user_attrib_09'] = $utable['attrib_09'];
      $_CAMILA['user_attrib_10'] = $utable['attrib_10'];
      $_CAMILA['user_attrib_11'] = $utable['attrib_11'];
      $_CAMILA['user_attrib_12'] = $utable['attrib_12'];
      $_CAMILA['user_attrib_13'] = $utable['attrib_13'];
      $_CAMILA['user_attrib_14'] = $utable['attrib_14'];
      $_CAMILA['user_attrib_15'] = $utable['attrib_15'];
  }
  else
  {
	  //echo "!!!";
  }

  /////////////////////
  camila_translation_init();

  $fmt = '';
  $f = Array();
  $m = camila_get_translation('camila.dateformat.monthpos');
  $d = camila_get_translation('camila.dateformat.daypos');
  $y = camila_get_translation('camila.dateformat.yearpos');
  $f[$m] = 'm';
  $f[$d] = 'd';
  $f[$y] = 'Y';
  ksort($f);
  reset($f);
  $count = 0;
  /*while (list($k, $v) = each($f)) {
      $fmt.=$v;
      if ($count<2) {
          $fmt.=camila_get_translation('camila.dateformat.separator');
      }
  $count++;
  }*/

  foreach ($f as $k => $v) {
	  $fmt.=$v;
      if ($count<2) {
          $fmt.=camila_get_translation('camila.dateformat.separator');
      }
	  $count++;
  }

  $_CAMILA['date_format'] = $fmt;

  if ($_REQUEST['camila_share_key'] != '')
      $skey = $_REQUEST['camila_share_key'];
  else
      $skey = '___camila___';


  //$_CAMILA['user_level'] = 0;
  //$_CAMILA['lang'] = 'it';
  /*if (CAMILA_ANON_LOGIN)
      $query = 'SELECT url,active,visible,short_title,full_title,label_order,title_attributes,title_color,title_boxcolor,level,filter,parent,login,dos_threshold,help FROM ' . CAMILA_DB_ . "pages, " . CAMILA_DB_ ."pages_lang WHERE active='yes' AND (". CAMILA_DB_ ."pages.url = ".CAMILA_DB_."pages_lang.page_url) AND lang=".$_CAMILA['db']->qstr($_CAMILA['lang'])." ORDER BY label_order";
  else */if ($_CAMILA['user_group'] != '')
      $query = 'SELECT share_key,url,active,visible,short_title,full_title,lang,label_order,title_attributes,title_color,title_boxcolor,level,filter,parent,login,dos_threshold,help FROM ' . CAMILA_TABLE_PAGES . ', ' . CAMILA_TABLE_PLANG .' WHERE (level>=' . $_CAMILA['user_level'] . " OR url='" . basename($_SERVER['PHP_SELF']) . "' ) AND (filter NOT LIKE '%-" . $_CAMILA['user_group'] . "%') AND ((filter='' OR filter LIKE '%-%' OR filter LIKE '%" . $_CAMILA['user_group'] . "%') or (share_key = '".$skey."') ) AND active='yes' AND (". CAMILA_TABLE_PAGES .".url = ".CAMILA_TABLE_PLANG.".page_url) AND lang=".$_CAMILA['db']->qstr($_CAMILA['lang'])." ORDER BY label_order";
  else
      $query = 'SELECT share_key,url,active,visible,short_title,full_title,label_order,title_attributes,title_color,title_boxcolor,level,filter,parent,login,dos_threshold,help FROM ' . CAMILA_TABLE_PAGES . ', ' . CAMILA_TABLE_PLANG .' WHERE (level>=' . $_CAMILA['user_level'] . " OR url='" . basename($_SERVER['PHP_SELF']) . "' ) AND ((filter='' OR filter LIKE '%-%' OR filter LIKE '%" . $_CAMILA['user_group'] . "%') OR (share_key = '".$skey."')) AND active='yes' AND (". CAMILA_TABLE_PAGES .".url = ".CAMILA_TABLE_PLANG.".page_url) AND lang=".$_CAMILA['db']->qstr($_CAMILA['lang'])." ORDER BY label_order";

  //echo $query;
  
  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
  $result = $_CAMILA['db']->Execute($query);
  if ($result === false)
      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

  global $camila_linkset;
  $camila_linkset = new CHAW_linkset();
  $_SERVER['PHP_SELF'] = basename($_SERVER['PHP_SELF']);
  $currentparent = basename($_SERVER['PHP_SELF']);
  $urlfound = false;

  $camila_parents = array();
  $camila_titles = array();
  $camila_visible = array();

  
  while (!$result->EOF) {
      $camila_visible[$result->fields['url']] = $result->fields['visible'];
      $camila_parents[$result->fields['url']] = $result->fields['parent'];
      $camila_titles[$result->fields['url']] = $result->fields['short_title'];
      $camila_menu_links[$result->fields['url']] = new CHAW_link($result->fields['short_title'], $result->fields['url']);
      $camila_menu_links[$result->fields['url']]->set_br(0);

      if ($result->fields['url'] == CAMILA_LOGIN_HOME)
          $_CAMILA['app_title'] = $result->fields['full_title'];

      if ($result->fields['parent'] != '')
          $currentparent = $result->fields['parent'];

      if ($_SERVER['QUERY_STRING'] != '')
          $url = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
      else
          $url = $_SERVER['PHP_SELF'];
      $_t = explode('?', $result->fields['url']);

      if (( (strpos($url, $result->fields['url']) === 0) || (($_t[0]==$_SERVER['PHP_SELF']) && ( (strpos($url, 'camila_'.$_t[1]) !== false) || (isset($_REQUEST['camila_'.$_t[1]])        )   )) ) && (!isset($_CAMILA['page_url']) || strlen($result->fields['url']) > strlen($_CAMILA['page_url']))) {
          $urlfound = true;
          $currentfulltitleparent = $result->fields['full_title'];
          $_CAMILA['page_share_key'] = $result->fields['share_key'];
          $_CAMILA['page_parent'] = $result->fields['parent'];
          $_CAMILA['page_url'] = $result->fields['url'];
          $_CAMILA['page_login'] = $result->fields['login'];
          $_CAMILA['page_short_title'] = $result->fields['short_title'];
          $_CAMILA['page_full_title'] = $result->fields['full_title'];
          $_CAMILA['page_title_color'] = $result->fields['title_color'];
          $_CAMILA['page_title_boxcolor'] = $result->fields['title_boxcolor'];
          $_CAMILA['page_title_attributes'] = $result->fields['title_attributes'];
          $_CAMILA['page_dos_threshold'] = $result->fields['dos_threshold'];
      }

      $result->MoveNext();
  }

  // Access denied
  if (!$urlfound)
      camila_error_page(camila_get_translation('camila.pageforbidden'));

  if ($_CAMILA['page_dos_threshold'] > 0)
      include(CAMILA_DIR . 'header_dos_threshold.php');

  if (isset($_REQUEST['camila_txt'])) {
      ob_start();
  }

//  if (isset($_REQUEST['camila_save']) && $_REQUEST['camila_export_filename']!='') {
//      require_once(CAMILA_DIR . 'fm/elements.php');

//      global $_CAMILA;
//      $rel = $_REQUEST['camila_export_filename'] . '.htm' .camila_hash(CAMILA_FM_PREFIX);
//      $_CAMILA['ob_filename'] = $rel;
//      $abs = get_abs_item($_CAMILA['adm_user_group'], $rel);

//      $_CAMILA['ob_file'] = fopen($abs,'w');
//      ob_start('camila_ob_file_callback');
//  }

  if (isset($_REQUEST['camila_preferences'])) {
      include(CAMILA_DIR . 'preferences.inc.php');
  } elseif (isset($_REQUEST['camila_soap'])) {
      include(CAMILA_DIR . 'export/camila_soap.php');
      $_CAMILA['page'] = new CAMILA_SOAP_deck($_CAMILA['page_short_title']);
  } elseif (isset($_REQUEST['camila_json'])) {
      include(CAMILA_DIR . 'export/camila_json.php');
      $_CAMILA['page'] = new CAMILA_JSON_deck($_CAMILA['page_short_title']);
  } elseif (isset($_REQUEST['camila_hessian'])) {
      include(CAMILA_DIR . 'export/camila_hessian.php');
      $_CAMILA['page'] = new CAMILA_HESSIAN_deck($_CAMILA['page_short_title']);
  } elseif (isset($_REQUEST['camila_js'])) {
      include(CAMILA_DIR . 'export/camila_js.php');
      $_CAMILA['page'] = new CAMILA_JS_deck($_CAMILA['page_short_title']);
  } elseif (isset($_REQUEST['camila_pdf'])) {
      if ($_REQUEST['camila_pdf'] != '') {
          include(CAMILA_DIR . 'export/camila_pdf.php');
          $_CAMILA['page'] = new CAMILA_PDF_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);
      } else {
          include(CAMILA_DIR . 'export.php');
      }
  } elseif (isset($_REQUEST['camila_rtf'])) {
      if ($_REQUEST['camila_rtf'] != '') {
          include(CAMILA_DIR . 'export/camila_rtf.php');
          $_CAMILA['page'] = new CAMILA_RTF_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);
      } else {
          include(CAMILA_DIR . 'export.php');
      }
  } elseif (isset($_REQUEST['camila_csv'])) {
      if ($_REQUEST['camila_csv'] != '') {
          include(CAMILA_DIR . 'export/camila_csv.php');
          $_CAMILA['page'] = new CAMILA_CSV_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);
      } else {
          include(CAMILA_DIR . 'export.php');
      }
  } elseif (isset($_REQUEST['camila_xls'])) {
      if ($_REQUEST['camila_xls'] != '') {
          include(CAMILA_DIR . 'export/camila_xls.php');
          $_CAMILA['page'] = new CAMILA_XLS_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);

      } else {
          include(CAMILA_DIR . 'export.php');
      } 
  } elseif (isset($_REQUEST['camila_ods'])) {
      if ($_REQUEST['camila_ods'] != '') {
          include(CAMILA_DIR . 'export/camila_ods.php');
          $_CAMILA['page'] = new CAMILA_ODS_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);

      } else {
          include(CAMILA_DIR . 'export.php');
      } 
  } elseif (isset($_REQUEST['camila_xml2pdf'])) {
      if ($_REQUEST['camila_xml2pdf'] != '') {
          include(CAMILA_DIR . 'export/camila_xml2pdf.php');
          $_CAMILA['page'] = new CAMILA_XML2PDF_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);
      } else {
          $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.export.options'), HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);
          include(CAMILA_DIR . 'export.inc.php');
      }
  } elseif (isset($_REQUEST['camila_print']) && $_REQUEST['camila_print'] == 'save') {
      include(CAMILA_DIR . 'export.php');
  } elseif (isset($_REQUEST['camila_inline'])) {
      include(CAMILA_DIR . 'export/camila_inline.php');
      $_CAMILA['page'] = new CAMILA_INLINE_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);
  } elseif (isset($_REQUEST['camila_bookmark'])) {
      if ($_REQUEST['camila_bookmark'] != '') {
          $_CAMILA['page'] = new CHAW_deck(camila_get_translation('camila.export.options'), HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);
          include(CAMILA_DIR . 'bookmark.inc.php');
      } 
  } elseif (isset($_REQUEST['camila_gva'])) {
      include(CAMILA_DIR . 'export/camila_gva.php');
      $_CAMILA['page'] = new CAMILA_GVA_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);  
  } elseif (isset($_REQUEST['camila_xml'])) {
      include(CAMILA_DIR . 'export/camila_xml.php');
      $_CAMILA['page'] = new CAMILA_XML_deck($_CAMILA['page_short_title'], HAW_ALIGN_CENTER, HAW_OUTPUT_AUTOMATIC);  
  } else {
      $_CAMILA['page'] = new CHAW_deck($_CAMILA['page_short_title'], HAW_ALIGN_LEFT, HAW_OUTPUT_AUTOMATIC);
  }

  if (session_id())
      $_CAMILA['page']->enable_session();

  $_CAMILA['page']->disable_cache();

  if ($_CAMILA['page']->camila_exporting() && isset($_REQUEST['camila_export_title']))
      $_CAMILA['page_full_title'] = $_REQUEST['camila_export_title'];

  require 'deck_settings.php';

  //Authentication required...
  if (($_CAMILA['page_login']) && (($users == 0) || ($camila_auth_fail)) && (($_CAMILA['page_share_key']!=$_REQUEST['camila_share_key']) || ($_REQUEST['camila_share_key']=='')) ) {

      $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR.'skin0.css');

      $_CAMILA['auth_page']=true;

      if (isset($_REQUEST['submit']))
          $_CAMILA['auth_failed']=true;

      $c1 = '';
      $c2 = '';
      $c3 = '';

      camila_challenge_numbers($c1, $c2, $c3);

      if (!isset($_COOKIE['camila_lbox']) || isset($_REQUEST['lbox']) || !CAMILA_CHALLENGE_LOGIN) {

          if ($_CAMILA['output'] == HAW_OUTPUT_AUTOMATIC && CAMILA_CHALLENGE_LOGIN) {
          if (!isset($_COOKIE['camila_lbox']))
                  $camila_link = new CHAW_link(camila_get_translation('camila.storepasswordonbrowser'), 'javascript:store()');
              else
                  $camila_link = new CHAW_link(camila_get_translation('camila.login.pwd'), 'login.php');
          
              $camila_link->set_br(0);
     
              $camila_linkset->add_link($camila_link);
          $_CAMILA['page']->add_linkset($camila_linkset);

          }
          /*if (CAMILA_CHALLENGE_LOGIN)
              $login_text = new CHAW_text(camila_get_translation('camila.login.num'), $_CAMILA['page_title_attributes']);
          else
              $login_text = new CHAW_text(camila_get_translation('camila.login.pwd'), $_CAMILA['page_title_attributes']);
          $login_text->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
          $login_text->set_br(2);
          
          $_CAMILA['page']->add_text($login_text);*/
          if (!CAMILA_ANON_LOGIN)
              include(CAMILA_DIR.'login_nojs.php');
          else
              include(CAMILA_DIR.'login_anon.php');

      }

      else {

          $camila_link = new CHAW_link(camila_get_translation('camila.login.num'), 'login.php?lbox=classic');
          $camila_link->set_br(0);

          $camila_linkset->add_link($camila_link);

          $camila_link = new CHAW_link(camila_get_translation('camila.resetpasswordonbrowser'), 'javascript:reset()');
          $camila_link->set_br(0);
          
          $camila_linkset->add_link($camila_link);
          
          $login_text = new CHAW_text(camila_get_translation('camila.login.pwd'), $_CAMILA['page_title_attributes']);
          $login_text->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
          $login_text->set_br(2);
          
          $_CAMILA['page']->add_linkset($camila_linkset);
          $_CAMILA['page']->add_text($login_text);
          
          $myLogin = new CHAW_getpwd($c1, $c2, $c3);
          $_CAMILA['page']->add_userdefined($myLogin);
      }

      require_once(CAMILA_DIR.'footer.php');

      exit();
  } else {
      if (!(isset($_REQUEST['camila_print']))) {
          if ($_CAMILA['user_preferences']['c_sk'] != '')
              $_CAMILA['skin'] = $_CAMILA['user_preferences']['c_sk'];
          if ($_CAMILA['javascript_enabled'] == 1 && $_CAMILA['skin'] == 0)
              $_CAMILA['skin'] = CAMILA_DEFAULT_JS_SKIN;
          $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR.'skin' . $_CAMILA['skin'] . '.css?v='.date("mdy"));
      } else {
          $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR.'print.css');
      }
  }

  if (!$_CAMILA['page']->camila_exporting() && !isset($_REQUEST['camila_popup'])) {

      //if ($_CAMILA['page_url'] != CAMILA_HOME)
      //    $_CAMILA['page']->camila_collapsible_start('menu');

      $camila_currpage = $_CAMILA['page_url'];
      $camila_mcount = 0;
      $camila_menu_levels = array();

      while ($camila_currpage != '') {
          $camila_menu_levels[$camila_mcount] = $camila_currpage;
          $camila_currpage = $camila_parents[$camila_currpage];
          $camila_mcount++;
      }

      //  $camila_menu = new CHAW_menu($camila_parents, $camila_titles, $camila_visible);
      if (isset($_CAMILA['user_group']) && $_CAMILA['user_group'] != '')
          $query = "SELECT url,active,visible,label_order,full_title,short_title,parent,login FROM " . CAMILA_TABLE_PAGES . ",".CAMILA_TABLE_PLANG." WHERE level>=$_CAMILA[user_level] AND (filter NOT LIKE '%-" . $_CAMILA["user_group"] . "%')  AND (filter='' OR filter LIKE '%-%' OR filter='" . $_CAMILA["user_group"] . "' OR filter LIKE '%" . $_CAMILA["user_group"] . "%') AND visible='yes' AND active='yes' AND (". CAMILA_TABLE_PAGES .".url = ".CAMILA_TABLE_PLANG.".page_url) AND lang=".$_CAMILA['db']->qstr($_CAMILA['lang'])." ORDER by label_order";
      else
          $query = "SELECT url,active,visible,label_order,full_title,short_title,parent,login FROM " . CAMILA_TABLE_PAGES . ",".CAMILA_TABLE_PLANG." WHERE level>=$_CAMILA[user_level] AND visible='yes' AND active='yes' AND (". CAMILA_TABLE_PAGES .".url = ".CAMILA_TABLE_PLANG.".page_url) AND lang=".$_CAMILA['db']->qstr($_CAMILA['lang'])." ORDER by label_order";

      $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
      $result = $_CAMILA['db']->Execute($query);
      if ($result === false)
          camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

      global $camila_linkset_items;
      $camila_linkset_items = 0;

      $counter = 0;

      while (!$result->EOF) {
          $jarr[$counter] = $result->fields;
          $counter++;
          $camila_link = new CHAW_link($result->fields['short_title'], $result->fields['url']);
          $camila_link->set_br(0);
          //if ($_SERVER['PHP_SELF'] != $result->fields["url"])
          //echo basename($_SERVER['REQUEST_URI']);
          //echo $_CAMILA["page_url"]."/".$result->fields["parent"];
          if (in_array($_CAMILA['page_url'], $camila_parents))
              //if (strpos(basename($_SERVER['REQUEST_URI']),$result->fields["url"])!==0)
              if (($result->fields['parent'] == $_CAMILA['page_url']) || (($result->fields['parent'] == '') && ($_CAMILA['page_url'] == CAMILA_LOGIN_HOME || $_CAMILA['page_url'] == CAMILA_HOME)))
                  if ($result->fields['visible'] == 'yes' && $_CAMILA['page_parent'] != '') {
                      $camila_linkset->add_link($camila_link);
                      $camila_linkset_items++;
                  }
          if (!in_array($_CAMILA['page_url'], $camila_parents))
              //if (strpos(basename($_SERVER['REQUEST_URI']),$result->fields['url'])!==0)
              if (($result->fields['parent'] == $_CAMILA['page_parent']) || (($result->fields['parent'] == '') && ($_CAMILA['page_url'] == CAMILA_LOGIN_HOME || $_CAMILA['page_url'] == CAMILA_HOME)))
                  if ($result->fields['visible'] == 'yes' && $_CAMILA['page_parent'] != '') {
                      $camila_linkset->add_link($camila_link);
                      $camila_linkset_items++;
                  }
          $result->MoveNext();
      }

      //camila_utf8_decode_array($jarr);
	  

      if ($_CAMILA['user_loggedin'] == 1) {
          $camila_menu_id = $_CAMILA['skin'];
		  
		  $code = "<script language=\"javascript\" src=\"".CAMILA_LIB_DIR."dommenu/domLib.js\"></script>\n";
            $code .= "<script language=\"javascript\" src=\"".CAMILA_LIB_DIR."dommenu/domMenu.js\"></script>\n";

		    $code .= "<script language=\"javascript\" src=\"".CAMILA_DIR."js/menu.js\"></script>\n";
		    $code .= "<script language=\"javascript\" src=\"".CAMILA_DIR."js/menu" . $camila_menu_id . ".js\"></script>\n";
			//$code .="<link href=\"".CAMILA_LIB_DIR."smartmenus/libs/demo-assets/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">\n";

		  if ($useSmartMenus) {
		    $_CAMILA['mainmenu'] = $jarr;
			
			$code .="<link href=\"".CAMILA_LIB_DIR."smartmenus/addons/bootstrap/jquery.smartmenus.bootstrap.css\" rel=\"stylesheet\">\n";

		    $code .= "<script language=\"javascript\" src=\"".CAMILA_DIR."js/jquery/jquery.js\"></script>\n";

			$code .="<script src=\"".CAMILA_LIB_DIR."bootstrap/js/bootstrap.min.js\"></script>\n";
		    $code .= "<script language=\"javascript\" src=\"".CAMILA_LIB_DIR."smartmenus/jquery.smartmenus.js\"></script>\n";
			$code .="<script src=\"".CAMILA_LIB_DIR."smartmenus/addons/bootstrap/jquery.smartmenus.bootstrap.js\"></script>\n";
			
			$code .="<script src=\"".CAMILA_LIB_DIR."x-editable/bootstrap3-editable/js/bootstrap-editable.js\"></script>\n";
			$code .="<link href=\"".CAMILA_LIB_DIR."x-editable/bootstrap3-editable/css/bootstrap-editable.css\" rel=\"stylesheet\">\n";
			


			//$code .="<script scr=\"".CAMILA_LIB_DIR."smartmenus/addons/bootstrap/jquery.smartmenus.bootstrap.js\"></script>\n";
			//$code .="<script type=\"text/javascript\">\$(function() {\$('#main-menu').smartmenus({subMenusSubOffsetX: 1,subMenusSubOffsetY: -8});});</script>";

			//$code .="<link href=\"".CAMILA_LIB_DIR."/smartmenus/css/sm-core-css.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
			//$code .="<link href=\"".CAMILA_LIB_DIR."smartmenus/css/sm-core-css.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
			//$code .="<link href=\"".CAMILA_DIR."css/smartmenus/".$smartMenusTheme."/".$smartMenusTheme.".css\" rel=\"stylesheet\" type=\"text/css\" />\n";
		  }
		  else
		  {
		    $json = new Services_JSON();
            $jarr2 = $json->encode($jarr);
            $code .= "<script>camila_addDOMLoadEvent ( function() {";          
            //$code .= "var a = '" . chunk_split(str_replace(array("'","\\u0000","\\"), array("\'","","\\\\"), $jarr2), 100, "'+\n'") . "'; camilamenu_init(a);} )</script>";
            $code .= "var a = '" . str_replace(array("'","\\u0000"), array("\'",""), $jarr2) . "'; camilamenu_init(a);} )</script>";
		  }
          $_CAMILA['page']->camila_add_js($code);
      }
	  
	  
	  
      $camila_linkset_sep = new CHAW_text(' ' . CAMILA_LINKSET_SEPARATOR . ' ');
      $camila_linkset_sep->set_br(0);

      $camila_temp_text = new CHAW_text(camila_get_translation(camila.youarein), HAW_TEXTFORMAT_SMALL | HAW_TEXTFORMAT_ITALIC);
      $camila_temp_text->set_br(0);
      if ($_CAMILA['user_loggedin']) {
          $_CAMILA['page']->add_text($camila_temp_text);
          //$_CAMILA['page']->add_text($camila_linkset_sep);
          $camila_link = new CHAW_link(camila_get_translation('camila.homemenu'), CAMILA_HOME);
          $camila_link->set_br(0);
          $_CAMILA['page']->add_link($camila_link);
          $_CAMILA['page']->add_text($camila_linkset_sep);
      }

      //$camila_temp_text = new CHAW_text($camila_menu_links[$camila_menu_levels[$camila_mcount-1]]->label." ", HAW_TEXTFORMAT_SMALL | HAW_TEXTFORMAT_ITALIC);
      //$camila_temp_text->set_br(0);

      for ($camila_i = $camila_mcount - 1; $camila_i > 0; $camila_i--) {
          $_CAMILA['page']->add_link($camila_menu_links[$camila_menu_levels[$camila_i]]);
          $_CAMILA['page']->add_text($camila_linkset_sep);
      }

      $camila_temp_text = new CHAW_text($_CAMILA['page_short_title']);
      $camila_temp_text->set_br(0);
      $camila_temp_text2 = new CHAW_text('');
      $camila_temp_text2->set_br(2);

      if ($camila_mcount > 0 && basename($_SERVER['PHP_SELF']) != CAMILA_HOME) {
          $_CAMILA['page']->add_text($camila_temp_text);
      } else
          $_CAMILA['page']->add_text($camila_temp_text2);

      if ($camila_linkset_items > 0 && $camila_mcount > 0) {
          $camila_temp_text2 = new CHAW_text('');
		  $camila_temp_text2->set_br(2);
		  $_CAMILA['page']->add_text($camila_temp_text2);
		  $_CAMILA['page']->add_linkset($camila_linkset);
      }

      //if ($_CAMILA['page_url'] != CAMILA_HOME)
      //    $_CAMILA['page']->camila_collapsible_end();
  }


  //$text = new CHAW_text($_CAMILA['page_full_title'], $_CAMILA['page_title_attributes']);
  //$text->set_br(1);
  //$text->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
  //$_CAMILA['page']->add_text($text);

if (!$_CAMILA['page']->camila_exporting())
{
  $query = 'select * from ' . CAMILA_APPLICATION_PREFIX.'camila_bookmarks where base_url='.$_CAMILA['db']->qstr(basename($_SERVER['PHP_SELF'])) . ' and lang=' . $_CAMILA['db']->qstr($_CAMILA['lang']) . ' order by sequence';
  $result = $_CAMILA['db']->Execute($query);
  if ($result === false)
      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
  $bookmark_count = 0;

  $camila_linkset_sep = new CHAW_text(' ' . camila_get_translation('camila.export.separator') . ' ');
  $camila_linkset_sep->set_br(0);


  while (!$result->EOF) {

      if ($bookmark_count >0)
          $_CAMILA['page']->add_text($camila_linkset_sep);
	  else
	  {
		  //div
		  $myDivOpen = new HAW_raw(HAW_HTML, '<div class="bmbox">');
		  $_CAMILA['page']->add_raw($myDivOpen);
		  //$text = new CHAW_text('');
		  //$text->set_br(1);
		  //$_CAMILA['page']->add_text($text);
	  }

      $camila_link = new CHAW_link($result->fields['title'], $result->fields['url']);
      $camila_link->set_css_class('btn btn-sm btn-default');
	  $camila_link->set_br(0);
      $_CAMILA['page']->add_link($camila_link);

      $bookmark_count++;
      $result->MoveNext();
  }

  if ($bookmark_count == 0)
  {
     //$text = new CHAW_text('');
     //$text->set_br(1);
     //$_CAMILA['page']->add_text($text);

  }
  else
  {
	  $myDivOpen = new HAW_raw(HAW_HTML, '</div>');
	  $_CAMILA['page']->add_raw($myDivOpen);
     //$text = new CHAW_text('');
     //$text->set_br(1);
     //$_CAMILA['page']->add_text($text);

  }
}
  $code = '<script>document.title="' . CAMILA_APPLICATION_NAME . ' - ' . str_replace("'", "\'", $_CAMILA['page_full_title']) . '"</script>';
  $js = new CHAW_js($code);
  $_CAMILA['page']->add_userdefined($js);

  $_CAMILA['page']->smartMenusTheme = $smartMenusTheme;


require('../../camila/views/cf_worktable_rebuild.inc.php');

 
?>