<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2016 Umberto Bresciani

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

   
class report_query extends report_field {


  function report_query($field, $title)
  {
    parent::report_field($field, $title);
    $this->inline = false;
    $this->orderable = false;
  }


  function draw(&$row, $fields)
  {
	  global $_CAMILA;
	  $camilaWT  = new CamilaWorkTable();
	  $camilaWT->db = $_CAMILA['db'];

    $formula = $this->report->queries[$this->field];
    $wTable = substr($formula, 0, strpos($formula, ','));
    $field = substr($formula, strlen($wTable)+1);
    $query = substr($field, strpos($field, ',')+1);
    $field = substr($field, 0, strpos($field, ','));
    //$url = camila_worktable_get_table_id($wTable);

//echo $field;
//echo $query;
//
//echo $query;

    $ttemp = new MiniTemplator();
    $ttemp->setTemplateString(substr($query,1));
	
    foreach ($fields as $key) {

        if ($key->value != '')
            $ttemp->setVariable($key->title, $key->value, true);
        else
            $ttemp->setVariable($key->title, '0', true);
    }

    $ttemp->generateOutputToString($query);
	
	$query = 'SELECT count(*) FROM ${'.$wTable.'} where $' . $query;
	
    if ($field == camila_get_translation('camila.worktable.query.count'))
    {
        //$fields=Array('camila_session_id'=>$_REQUEST['PHPSESSID'],'camila_xml'=>'','filter'=>$query, 'camila_export_no_table_data'=>'y');
        //$this->value = str_replace(array("\r", "\r\n", "\n"), '', strip_tags($this->httpGet($_SERVER['SERVER_NAME'],$_SERVER['SERVER_PORT'],'/app/'.CAMILA_APP_DIR.'/'.$url,$fields,array('PHPSESSID2' => $_REQUEST['PHPSESSID']))));
		$result = $camilaWT->startExecuteQuery($query);
		$this->value = $result->fields[0];
		$camilaWT->endExecuteQuery($query);
    }
    else
    {
        $this->value=camila_get_translation('camila.worktable.query.count').' not yet supported :-(';
    }

    parent::draw($row, $fields);
  }


function http_request( 
    $verb = 'GET',             /* HTTP Request Method (GET and POST supported) */ 
    $ip,                       /* Target IP/Hostname */ 
    $port = 80,                /* Target TCP port */ 
    $uri = '/',                /* Target URI */ 
    $getdata = array(),        /* HTTP GET Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
    $postdata = array(),       /* HTTP POST Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
    $cookie = array(),         /* HTTP Cookie Data ie. array('var1' => 'val1', 'var2' => 'val2') */ 
    $custom_headers = array(), /* Custom HTTP headers ie. array('Referer: http://localhost/ */ 
    $timeout = 1,           /* Socket timeout in seconds */ 
    $req_hdr = false,          /* Include HTTP request headers */ 
    $res_hdr = false           /* Include HTTP response headers */ 
    ) 
{ 
    $ret = ''; 
    $verb = strtoupper($verb); 
    $cookie_str = ''; 
    $getdata_str = count($getdata) ? '?' : ''; 
    $postdata_str = ''; 

    foreach ($getdata as $k => $v) 
                $getdata_str .= urlencode($k) .'='. urlencode($v) . '&'; 

    foreach ($postdata as $k => $v) 
        $postdata_str .= urlencode($k) .'='. urlencode($v) .'&'; 

    foreach ($cookie as $k => $v) 
        $cookie_str .= urlencode($k) .'='. urlencode($v) .'; '; 

    $crlf = "\r\n"; 
    $req = $verb .' '. $uri . $getdata_str .' HTTP/1.1' . $crlf; 
    $req .= 'Host: '. $ip .':'.$port. $crlf; 
//Accept:*/*
//Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.3
//Accept-Encoding:gzip,deflate,sdch
//Accept-Language:it-IT,it;q=0.8,en-US;q=0.6,en;q=0.4
//Connection:keep-alive
//Cookie:camila_js_enabled=1; PHPSESSID=c801271f09ed72c010573a67e5b7378b
//Host:192.168.0.11:4001
//Referer:http://192.168.0.11:4001/worktable/index.php
//User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4
    $req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12' . $crlf; 
    $req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf; 
    $req .= 'Accept-Language: en-us,en;q=0.5' . $crlf; 
    $req .= 'Accept-Encoding: deflate' . $crlf;
    $req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . $crlf;

    foreach ($custom_headers as $k => $v) 
        $req .= $k .': '. $v . $crlf; 
        
    if (!empty($cookie_str)) 
        $req .= 'Cookie: '. substr($cookie_str, 0, -2) . $crlf; 
        
    if ($verb == 'POST' && !empty($postdata_str)) 
    { 
        $postdata_str = substr($postdata_str, 0, -1); 
        $req .= 'Content-Type: application/x-www-form-urlencoded' . $crlf; 
        $req .= 'Content-Length: '. strlen($postdata_str) . $crlf . $crlf; 
        $req .= $postdata_str; 
    } 
    else $req .= $crlf; 
    
    if ($req_hdr) 
        $ret .= $req; 
    
    if (($fp = @fsockopen($ip, $port, $errno, $errstr)) == false) 
        return "Error $errno: $errstr\n"; 

    stream_set_timeout($fp, 0, $timeout * 1000); 
    
    fputs($fp, $req);

        while (!feof($fp)) {
            $line=fgets($fp,128);
echo $line;
        }


//    while ($line = fgets($fp)) $ret .= $line;

//echo $ret;

    fclose($fp); 

    if (!$res_hdr) 
        $ret = substr($ret, strpos($ret, "\r\n\r\n") + 4); 
    
    return $ret; 
} 

  function httpGet($hostname, $port, $url, $fields, $cookie)
  {
    $qs = array();
    foreach ($fields as $k => $v)
        $qs[] = $k.'='.urlencode($v);
    $qs = join('&', $qs);
$url.='?'.$qs;
    $data = false;

    foreach ($cookie as $k => $v) 
        $cookie_str .= urlencode($k) .'='. urlencode($v) .'; '; 

    $ip=gethostbyname($hostname);
	
	echo $url;


    $errno = $errstr = '';
    if ($fp = @fsockopen($ip, $port, $errno, $errstr, 30)) 
    {

        fputs($fp, "GET ".$url." HTTP/1.0\r\n");
        fputs($fp, "Host: ".$hostname."\r\n");
        fputs($fp, "Connection: close\r\n");

    if (!empty($cookie_str)) 
        fputs($fp, "Cookie: ". substr($cookie_str, 0, -2)."\r\n");


        fputs($fp, "\r\n".$qs);

        $content = '';
        $header = 'not yet';

        while (!feof($fp)) {
            $data=true;
            $line=fgets($fp,128);
            if ($line=="\r\n" && $header=="not yet") {
                $header = "passed";
            }
            if ($header=="passed") {
                $content.=$line;
            }
        }

        fclose($fp);
        return $content;

      }
     return false;
    }

}
?>
