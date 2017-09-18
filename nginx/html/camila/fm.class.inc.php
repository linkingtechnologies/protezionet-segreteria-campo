<?php

class CamilaFileManagement
{
    public $rootDir;
    public $canUpload = false;
	public $canDelete = true;
    
	// must be in UTF-8 or `basename` doesn't work
	//setlocale(LC_ALL,'it_IT.UTF-8');

    function CamilaFileManagement($rootDir)
    {
        $this->rootDir = $rootDir;
    }

	function getRootDir() {
		return CAMILA_FM_ROOTDIR;
	}

    function draw()
    {
        global $_CAMILA;
		$_CAMILA['page']->camila_export_enabled = false;
        $code = ('<link rel="stylesheet" type="text/css" media="all" href="' . CAMILA_DIR . 'css/fm.css" />');
        $code .= ('<script src=\'' . CAMILA_DIR . 'js/camila_fm.js\'></script>');
        $_CAMILA['page']->camila_add_js($code, 'camila_fm');
        $_CAMILA['page']->js_no_camila_init = true;
        
        $tmp = realpath($this->rootDir . '/' . $_REQUEST['file']);
        if ($tmp === false)
            $this->error(404, 'File or Directory Not Found... ' . $tmp);
        
        //	if(substr($tmp, 0,strlen(__DIR__)) !== __DIR__)
        //		$this->error(403,"Forbidden");
        
        /*if(!$_COOKIE['_sfm_xsrf'])
        setcookie('_sfm_xsrf',bin2hex(myRand(16)));*/
        /*if($_POST) {
        if($_COOKIE['_sfm_xsrf'] !== $_POST['xsrf'] || !$_POST['xsrf'])
        $this->error(403,"XSRF Failure");
        }*/
        $file  = $_REQUEST['file'] ?: ''; //
        $file2 = $this->rootDir . '/' . $_REQUEST['file'];
        if ($_GET['do'] == 'list') {
            if (is_dir($file2)) {
                $directory = $file;
                $result    = array();
                $files     = array_diff(scandir($this->rootDir . '/' . $directory), array(
                    '.',
                    '..',
					'.htaccess'
                ));
                
                foreach ($files as $entry)
                    if ($entry !== basename(__FILE__)) {
                        $i        = $this->rootDir . '/' . $directory . '/' . $entry;
                        $stat     = stat($i);
                        $result[] = array(
                            'mtime' => $stat['mtime'],
                            'size' => (!is_dir($i)) ? $stat['size'] : $this->dirSize($i),
                            'name' => basename($i),
                            'path' => preg_replace('@^\./@', '', substr($i, strlen($this->rootDir . '/'))),
                            'is_dir' => is_dir($i),
                            'is_deleteable' => ($this->canDelete && !is_dir($i) && is_writable($file2)) || ($this->canDelete && is_dir($i) && is_writable($file2) && $this->isRecursivelyDeleteable($i)),
                            'is_readable' => is_readable($i),
                            'is_writable' => is_writable($i),
                            'is_executable' => is_executable($i)
                        );
                    }
            } else {
                $this->error(412, "Not a Directory");
            }
            echo json_encode(array(
                'success' => true,
                'is_writable' => is_writable($file2),
                'results' => $result
            ));
            exit;
        } elseif ($_POST['do'] == 'delete') {
            $this->deleteFile($file2);
            exit;
        } elseif ($_POST['do'] == 'mkdir') {
            // don't allow actions outside root. we also filter out slashes to catch args like './../outside'
            $dir = $_POST['name'];
            $dir = str_replace('/', '', $dir);
            if (substr($dir, 0, 2) === '..')
                exit;
            chdir($file2);
            @mkdir($_POST['name']);
            exit;
        } elseif ($_POST['do'] == 'upload') {
            var_dump($_POST);
            var_dump($_FILES);
            var_dump($_FILES['file_data']['tmp_name']);
            var_dump(move_uploaded_file($_FILES['file_data']['tmp_name'], $file2 . '/' . $_FILES['file_data']['name']));
            exit;
        } elseif ($_GET['do'] == 'download') {
            $filename = basename($file2);
            header('Content-Type: ' . $this->mimeContentType($file2));
            header('Content-Length: ' . filesize($file2));
            header(sprintf('Content-Disposition: attachment; filename=%s', strpos('MSIE', $_SERVER['HTTP_REFERER']) ? rawurlencode($filename) : "\"$filename\""));
            ob_flush();
            readfile($file2);
            exit;
        }
        
        $MAX_UPLOAD_SIZE = min($this->asBytes(ini_get('post_max_size')), $this->asBytes(ini_get('upload_max_filesize')));
        
        //echo $MAX_UPLOAD_SIZE;
        $html = '<div class="row vertical-align">
  <div class="col-xs-6">';
  
  if ($this->canUpload)
	  $html .= '<div id="camilaFmTop">
  <form class="form-inline" action="?" method="post" id="mkdir" />
  <input type="text" class="form-control" id="dirname" name="name" placeholder="Nome nuova cartella..." />
  <button type="submit" value="create" class="btn btn-default">Crea cartella</button>
  </form>
  </div>';
  $html.='</div>
  <div class="col-xs-6">';
        if ($this->canUpload)
            $html .= '<div id="file_drop_target"><div class="form-group"><label for="upl">Carica file</label><input id="upl" class="form-control" type="file" multiple /></div></div>';
      $html .= '</div>
</div>
<div class="row">
  <div class="col-xs-6">

	
	</div>
<div class="col-xs-6">

<div id="upload_progress"></div>
</div>
</div>
<div class="row">
  <div class="col-xs-12">

	<div id="breadcrumb">&nbsp;</div>
	
	</div>

</div>
<div class="row">
<div class="table-responsive">
<table id="table" class="table"><thead><tr>
	<th>' . camila_get_translation('camila.fm.nameheader') . '</th>
	<th>' . camila_get_translation('camila.fm.sizeheader') . '</th>
	<th>' . camila_get_translation('camila.fm.lastmodifyheader') . '</th>
	<th></th>
</tr></thead><tbody id="list">

</tbody></table>
</table></div>';
        
        $myDiv = new HAW_raw(HAW_HTML, $html);
        $_CAMILA['page']->add_raw($myDiv);
    }
    
    
    
    function mimeContentType($filename)
    {
        
        $mime_types = array(
            
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
        );
        
        $ext = strtolower(array_pop(explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }
    
    
    
    function dirSize($dir)
    {
        $handle = opendir($dir);
        
        while ($file = readdir($handle)) {
            if ($file != '..' && $file != '.' && !is_dir($dir . '/' . $file)) {
                $mas += filesize($dir . '/' . $file);
            } else if (is_dir($dir . '/' . $file) && $file != '..' && $file != '.') {
                $mas += $this->dirSize($dir . '/' . $file);
            }
        }
		if ($mas == '')
			$mas = 0;

        return $mas;
    }
    
    function deleteFile($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array(
                '.',
                '..'
            ));
            foreach ($files as $file)
                $this->deleteFile("$dir/$file");
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }
    function isRecursivelyDeleteable($d)
    {
        $stack = array(
            $d
        );
        while ($dir = array_pop($stack)) {
            if (!is_readable($dir) || !is_writable($dir))
                return false;
            $files = array_diff(scandir($dir), array(
                '.',
                '..'
            ));
            foreach ($files as $file)
                if (is_dir($file)) {
                    $stack[] = "$dir/$file";
                }
        }
        return true;
    }
    
    function user_can_delete()
    {
        
        
    }
    
    function error($code, $msg)
    {
        echo json_encode(array(
            'error' => array(
                'code' => intval($code),
                'msg' => $msg
            )
        ));
        exit;
    }
    
    function asBytes($ini_v)
    {
        $ini_v = trim($ini_v);
        $s     = array(
            'g' => 1 << 30,
            'm' => 1 << 20,
            'k' => 1 << 10
        );
        return intval($ini_v) * ($s[strtolower(substr($ini_v, -1))] ?: 1);
    }
	
	public static function copyFiles($src,$dst,$filterExtension='',$recursive = false) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					if ($recursive)
						copyFiles($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					$ext = strtolower(array_pop(explode('.', $file)));
					if ($filterExtension=='' || $filterExtension==$ext)
						copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	
	public static function listDir($dir='.',$recursive=false) { 
		if (!is_dir($dir)) { 
			return false; 
		}

		$files = array(); 
		self::listDirAux($dir, $files, $recursive);

		return $files; 
	} 

	function listDirAux($dir, &$files, $recursive) { 
		$handle = opendir($dir); 
		while (($file = readdir($handle)) !== false) { 
			if ($file == '.' || $file == '..') { 
				continue; 
			} 
			$filepath = $dir == '.' ? $file : $dir . '/' . $file; 
			if (is_link($filepath)) 
				continue; 
			if (is_file($filepath)) 
				$files[] = $file; 
			else if (is_dir($filepath)) {
				if ($recursive)
					self::listDirAux($filepath, $files, $recursive); 
				else
					$files[] = $file;
			}
		} 
		closedir($handle); 
	}
	
	public static function deleteFiles($directory) {

    if(!$dirhandle = @opendir($directory) )
        return;

        while( false !== ($filename = readdir($dirhandle)) ) {
            if( $filename != '.' && $filename != '..' ) {
                $filename = $directory. '/'. $filename;

                if (!unlink($filename))
                    echo 'Error deleting ' . $filename;
        }
    }
}
    
    /*function myRand($max){
    do{
    $result = floor($max*(hexdec(bin2hex(openssl_random_pseudo_bytes(4)))/0xffffffff));
    }while($result == $max);
    return $result;
    }*/
    
    /*     function openssl_random_pseudo_bytes($length) {
    $length_n = (int) $length; // shell injection is no fun
    $handle = popen("/usr/bin/openssl rand $length_n", "r");
    $data = stream_get_contents($handle);
    pclose($handle);
    return $data;
    }*/
}

?>