<?php
require_once(CAMILA_VENDOR_DIR . '/adodb/adodb-php/adodb.inc.php');

class CamilaAuth
{    
    public $db;
    public $userTable;
    public $applicationName;
    private $domainName;
    public $authUserTable;
    
    function __construct($domain = '')
    {
        $this->domainName = $domain;
		$this->userTable = CAMILA_TABLE_USERS;
		$this->applicationName = CAMILA_APPLICATION_NAME;
		$this->authUserTable = CAMILA_AUTH_TABLE_USERS;
    }

    function getUserInfoSqlFromSessionId($sessionId)
    {
        $query = 'SELECT * FROM ' . $this->userTable . ' WHERE session_id=' . $this->db->qstr($sessionId) . ' AND session_end>' . $this->db->DBTimeStamp(time());
        return $query;
    }
    
    function getUserInfoSqlFromUsername($username)
    {
        $query = 'SELECT * FROM ' . $this->userTable . ' WHERE lower(username) = ' . $this->db->qstr(strtolower($username));
        return $query;
    }
    
    function getUserLevelFromUsername($username)
    {
        $old   = $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
        $query = $this->getUserInfoSqlFromUsername($username);
        
        $result = $this->db->Execute($query);
        $utable = $result->fields;
        
        if ($result)
            $users = $result->RecordCount();
        
        $old = $this->db->SetFetchMode($old);
        
        $level = -1;
        if ($users == 1) {
            $level = $utable['level'];
        }
        
        return $level;
        
    }
    
    function getUsernameFromSession()
    {
        $sessionLogin = $this->applicationName . '_logged_user';
        if ($this->domainName != '')
            $sessionLogin = $this->domainName . '_logged_user';
        $loggedUser = $_SESSION[$sessionLogin];
        return $loggedUser;
    }
	
	function getLangFromSession()
    {
        $sessionLang = $this->applicationName . '_lang';
        if ($this->domainName != '')
            $sessionLang = $this->domainName . '_lang';
        $lang = $_SESSION[$sessionLang];
        return $lang;
    }

    function putUsernameIntoSession($username, $lang = '')
    {
        $sessionLogin = $this->applicationName . '_logged_user';
		$sessionLang = $this->applicationName . '_lang';
        if ($this->domainName != '') {
            $sessionLogin = $this->domainName . '_logged_user';
			$sessionLang = $this->applicationName . '_lang';
		}

		if ($lang != '')
            $_SESSION[$sessionLang] = $lang;

        $_SESSION[$sessionLogin] = $username;
    }
    
    function getAuthDatabaseConnection($authDSN)
    {
        ob_start();
        $db2    = NewADOConnection($authDSN);
        $error = strip_tags(ob_get_contents());
        //echo $error;
        ob_end_clean();
        return $db2;
    }
	
	function getDatabaseConnection($DSN)
    {
        ob_start();
        $db2    = NewADOConnection($DSN);
        $error = strip_tags(ob_get_contents());
        //echo $error;
        ob_end_clean();
        return $db2;
    }

    function getAuthUserInfoSqlFromUsername($username)
    {
        $attribs = 'attrib_01,attrib_02,attrib_03,attrib_04,attrib_05,attrib_06,attrib_07,attrib_08,attrib_09,attrib_10,attrib_11,attrib_12,attrib_13,attrib_14,attrib_15,';
        $query   = 'SELECT id, ' . $attribs . ' visibility_type, level, ' . $this->authUserTable . '.grp, username, password, preferences, session_id FROM ' . $this->authUserTable . " WHERE UPPER(username)=UPPER('" . $username . "')";
        return $query;
    }

    function logout($username)
    {
        $sessionLogin = $this->applicationName . '_logged_user';
        if ($this->domainName != '')
            $sessionLogin = $this->domainName . '_logged_user';
        $_SESSION[$sessionLogin] = '';
        
        /*$result = $_CAMILA['db']->Execute('UPDATE '. CAMILA_TABLE_USERS . ' SET session_id=? WHERE id=?', Array('',$_CAMILA['user_id']));
        if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());*/       
    }
	
	function checkPasswords($pass, $dbPass)
	{
		$check = false;
		if (defined('CAMILA_AUTH_PASSWORD_HASHING') && (CAMILA_AUTH_PASSWORD_HASHING)) {
			$check = password_verify($pass, $dbPass);
		}
		else
		{
			if ($dbPass == trim($pass)) {
				$check = true;
			} else {
				//echo "KO";
			}
		}
		//$check = true;
		return $check;
	}

   
    function checkCredentials($username, $password)
    {
		$check = false;
        $query  = $this->getAuthUserInfoSqlFromUsername($username);
        $dbAuth = $this->getAuthDatabaseConnection(CAMILA_AUTH_DSN);
        $dbAuth->SetFetchMode(ADODB_FETCH_ASSOC);
        //echo $query;
        //print_r($dbAuth);
        $result = $dbAuth->Execute($query);
        if ($result === false) {
            //camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
        } else {
            $table = $result->fields;
			$check = $this->checkPasswords($password, $table['password']);
        }
		return $check;
    }

	function hashPassword($password) {
		if (defined('CAMILA_AUTH_PASSWORD_HASHING') && (CAMILA_AUTH_PASSWORD_HASHING)) {
			$password = password_hash($password, PASSWORD_DEFAULT);
		}
		return $password;
	}

	function updatePassword($username, $password)
    {
		$this->hashPassword($password);

		$check = false;
        $dbAuth = $this->getAuthDatabaseConnection(CAMILA_AUTH_DSN);
        $dbAuth->SetFetchMode(ADODB_FETCH_ASSOC);
		$query   = 'UPDATE ' . $this->authUserTable . ' SET password = '.$dbAuth->Quote($password) ."  WHERE UPPER(username)=UPPER('" . $username . "')";

        $result = $dbAuth->Execute($query);
        if ($result === false) {
            //camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
        } else {
                $check = true;
        }
		return $check;
    }
	
	function checkUserTable() {
		$rs = $this->db->execute('SELECT * FROM ' . CAMILA_TABLE_USERS);
		$count = -1;
		if ($rs)
			$count = $rs->RecordCount();
		return $count;
	}

	function getAutoincrementValue() {
		$result = $this->db->Execute('select max(id) as id from ' . CAMILA_TABLE_USERS);
		if ($result === false)
			camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
		return intval($result->fields['id']) + 1;
	}

	function raiseError() {
		header('WWW-Authenticate: Basic realm="Camila Framework Authentication"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Authentication required';
	}

}
?>