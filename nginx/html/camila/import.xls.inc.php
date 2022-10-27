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


  require_once(CAMILA_DIR . 'datagrid/form.class.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/hidden.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/filebox.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');
  require_once(CAMILA_DIR . 'datagrid/elements/form/checklist.php');

  global $_CAMILA;
  
  $_CAMILA['page']->camila_export_enabled = false;
  

  $deck_title = new CHAW_text(camila_get_translation('camila.worktable.import.app.title'));
  $deck_title->set_br(2);
  //$deck_title->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
  $_CAMILA['page']->add_text($deck_title);

  $form = new phpform('xls_table_import');
  $form->submitbutton = camila_get_translation('camila.worktable.import.upload');
  $form->drawrules = false;
  $form->preservecontext = true;

  global $_CAMILA;

  //new form_hidden($form, 'base_url', basename($_SERVER['PHP_SELF']));
  //new form_textbox($form, 'url', 'Link', false, 100, 100);
  new form_filebox($form, 'file', camila_get_translation('camila.worktable.import.zip.xls'), 50, CAMILA_TMP_DIR);
  
  $form->fields['file']->showmaxsizewarning = false;

  //new form_hidden($form, 'url', $url);

/*function download_remote_file_with_curl($file_url, $save_to)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 0); 
		curl_setopt($ch,CURLOPT_URL,$file_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$file_content = curl_exec($ch);
		curl_close($ch);
 
		$downloaded_file = fopen($save_to, 'w');
		fwrite($downloaded_file, $file_content);
		fclose($downloaded_file);
 
	}*/
	
	function download_remote_file_with_fopen($file_url, $save_to)
	{
		$in=    fopen($file_url, "rb");
		$out=   fopen($save_to, "wb");
 
		while ($chunk = fread($in,8192))
		{
			fwrite($out, $chunk, 8192);
		}
 
		fclose($in);
		fclose($out);
	}

	if ($form->process())
	{
		$url = $form->fields['url']->value;
		$file = $form->fields['file']->value;
		$lang = $_CAMILA['lang'];
		//echo ini_get("allow_url_fopen");
		//echo 'Current PHP version: ' . phpversion();
		/*	  if (FALSE === @fopen($url, 'r')) {
		  echo 'the file '.$url.' doesn\'t exists';
		  }*/
		
		if (isset($form->fields['file']->value))
			$file = $form->fields['file']->value[0];

		if ($file != '') {
			
			$dir = CAMILA_TABLES_DIR.'/xls/'.$lang.'/';

			/*$files = glob($dir.'*'); // get all file names
			foreach($files as $file){ // iterate files
			if(is_file($file))
				unlink($file); // delete file
			}*/
			
			array_map('unlink', glob($dir.'*'));
			$zip = new ZipArchive;
			$res = $zip->open(CAMILA_TMP_DIR.'/'.$file);
			if ($res === TRUE) {
				$zip->extractTo($dir);
				$zip->close();
				$link = new CHAW_link(camila_get_translation('camila.worktable.import.apply.conf'), 'cf_reset.php?msg='.urlencode(camila_get_translation('camila.clickhere')).'&lang='.$lang);
				$link->set_br(3);
				$_CAMILA['page']->add_link($link);
			} else {
				camila_information_text(camila_get_translation('camila.worktable.import.error.processing'));
				$form->draw();
			}
	}
	else
	{
		camila_information_text(camila_get_translation('camila.worktable.import.error.nofile'));
		$form->draw();
	}

	 // file_put_contents('tmp.zip', file_get_contents($url));
	  
	  //download_remote_file_with_fopen($url, CAMILA_TMP_DIR.'/tmp.zip');
	  //echo $url;
/*        $record=Array();
        $record['id']=$_CAMILA['db']->GenID(CAMILA_APPLICATION_PREFIX.'bookmarkseq', 10000).camila_hash(10);
        $record['lang']=$_CAMILA['lang'];
        $record['url']=$form->fields['url']->value;
        $record['base_url']=$form->fields['base_url']->value;
        $record['title']=$form->fields['title']->value;

        $insertSQL = $_CAMILA['db']->AutoExecute(CAMILA_APPLICATION_PREFIX.'camila_bookmarks', $record, 'INSERT');
        if (!$insertSQL) {
            camila_information_text(camila_get_translation('camila.worktable.db.error'));
            $success = false;
        }

        $url = $form->fields['url']->value;*/
		///echo "!!!";

  }
    else
      $form->draw();

  $myLink = new CHAW_link(camila_get_translation('camila.back.page'), $url);
  $myImage = new HAW_image(CAMILA_IMG_DIR.'wbmp/resultset_previous.wbmp', CAMILA_IMG_DIR.'png/resultset_previous.png', '-');
  $myLink->add_image($myImage);
  $_CAMILA['page']->add_link($myLink);


  $_CAMILA['page']->use_simulator(CAMILA_CSS_DIR . 'skin0.css');

  require(CAMILA_DIR . 'deck_settings.php');
  require(CAMILA_DIR . 'footer.php');
  exit();
?>