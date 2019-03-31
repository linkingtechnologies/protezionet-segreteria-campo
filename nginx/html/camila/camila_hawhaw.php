<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2019 Umberto Bresciani

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


require 'hawhaw/hawhaw.inc';
require_once(CAMILA_LIB_DIR . 'tbs/tbs_class.php');

class CamilaTranslator{
	function getTranslation($name){
		return camila_get_translation(str_replace('_', '.', $name));
	}
}

global $i18n;
$i18n = new CamilaTranslator();


class CHAW_deck extends HAW_deck
{
  var $camila_force_js_update_token = '2017';
  var $camila_num_tables = 0;
  var $camila_headjsscripts = Array();
  var $camila_headjsscriptsids = Array();
  var $camila_num_headjsscripts = 0;
  var $camila_jsstrings = Array();
  var $camila_num_jsstrings = 0;
  var $camila_export_enabled = true;

  var $camila_pdf_export_enabled = true;
  var $camila_rtf_export_enabled = true;
  var $camila_csv_export_enabled = false;
  var $camila_xls_export_enabled = false;
  var $camila_xml2pdf_export_enabled = true;
  var $camila_print_export_enabled = true;
  

  var $camila_export_file_exist = false;

  var $camila_worktable = false;
  
  var $camila_footer = '';

  var $js_enabled;
  
  var $js_no_camila_init = false;

  function CHAW_deck($title = '', $alignment=HAW_ALIGN_LEFT, $output=HAW_OUTPUT_AUTOMATIC)
  {
	  $this->camila_force_js_update_token = date("mdy").'2017';
      global $_CAMILA;

      if (!isset($_REQUEST['camila_print'])) {
    	  $this->camila_set_jsstring('CAMILA_IMG_DIR', CAMILA_IMG_DIR);
		  if (array_key_exists('error', $_CAMILA))
			  $this->camila_set_jsstring('CAMILA_ERROR', $_CAMILA['error'] > 0 ? '1' : '0');
		  else
			  $this->camila_set_jsstring('CAMILA_ERROR', '0');
          $this->camila_set_jsstring('CAMILA_EXPORTING', $this->camila_exporting() ? '1' : '0');

          $this->camila_set_jsstring('expand', camila_get_translation('camila.clicktoexpand'));
          $this->camila_set_jsstring('collapse', camila_get_translation('camila.clicktocollapse'));
          $this->camila_set_jsstring('pleasewait', camila_get_translation('camila.pleasewait'));
          $this->camila_set_jsstring('clicktoedit', camila_get_translation('camila.clicktoedit'));

      	  $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/x_core.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/x_event.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xprevsib.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xnextsib.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xwalkul.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xfirstchild.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xaddclass.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xremoveclass.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xtableiterate.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xhasclass.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xparent.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xdisplay.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xappendchild.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xdocsize.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xgetcookie.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xsetcookie.js'></script>\n");
          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xoffsettop.js'></script>\n");

          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xslideto.js'></script>\n");

          $this->camila_add_js("<script type='text/javascript' src='".CAMILA_LIB_DIR."cross-browser.com/x/lib/xwindow.js'></script>\n");

          $this->camila_add_js("<script type=\"text/javascript\" src=\"".CAMILA_DIR."js/camila.js?v=".$this->camila_force_js_update_token."\"></script>\n");
          $this->camila_add_js("<script type=\"text/javascript\" src=\"".CAMILA_DIR."js/camila_instantedit2.js?v=".$this->camila_force_js_update_token."\"></script>\n");
          $this->camila_add_js("<script type=\"text/javascript\" src=\"".CAMILA_DIR."js/camila_security.js?v=".$this->camila_force_js_update_token."\"></script>\n");

		  $this->camila_add_js("<link href=\"".CAMILA_LIB_DIR."bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">\n");

          $this->camila_add_js("<script type=\"text/javascript\">\n");
 
          /*$json = new Services_JSON();
    
          if ($_SERVER['PHP_SELF'] == CAMILA_LOGIN_HOME && $_CAMILA['user_loggedin']==1 && CAMILA_SPLASH_IMG != '')
              $this->camila_add_js("  {camila_init('" . str_replace("'", "\'", $json->encode($this->camila_jsstrings)) . "',true);\n");
          else
              $this->camila_add_js("  {camila_init('" . str_replace("'", "\'", $json->encode($this->camila_jsstrings)) . "');\n");*/

		  if ($_SERVER['PHP_SELF']!= 'cf_docs.php')
		  {
			  $this->camila_add_js("  camila_addDOMLoadEvent ( function()\n");
		  
          if ($_SERVER['PHP_SELF'] == CAMILA_LOGIN_HOME && $_CAMILA['user_loggedin']==1 && CAMILA_SPLASH_IMG != '')
              $this->camila_add_js("  {camila_init('" . str_replace("'", "\'", json_encode($this->camila_jsstrings)) . "',true);\n");
          else
              $this->camila_add_js("  {camila_init('" . str_replace("'", "\'", json_encode($this->camila_jsstrings)) . "');\n");

          $this->camila_add_js("} )");
		}
          $this->camila_add_js("</script>\n");

      }

      if (file_exists($this->camila_export_get_dir() . $this->camila_export_safe_filename() . '.' . $this->camila_export_get_ext()))
          $this->camila_export_file_exists = true;
          
      $this->{get_parent_class(__CLASS__)}($title, $alignment, $output);
}

  function camila_export_get_dir() {
      global $_CAMILA;
      if (CAMILA_FM_EXTFS_ENABLED)
          return CAMILA_FM_ROOTDIR . '/' . $_CAMILA['adm_user_group'] . '/';
      else
          return CAMILA_FM_ROOTDIR . $_REQUEST['camila_export_action'] . '/';
  }

  function camila_export_get_ext() {
      if(isset($_REQUEST['camila_xls']))
          return 'xls';

      if(isset($_REQUEST['camila_csv']))
          return 'csv';

      if(isset($_REQUEST['camila_pdf']))
          return 'pdf';

      if(isset($_REQUEST['camila_rtf']))
          return 'rtf';

      if(isset($_REQUEST['camila_xml2pdf']))
          return 'pdf';

  }

  function camila_export_filename() {
      global $_CAMILA;
      $filename = $this->camila_export_safe_filename().'.'.$this->camila_export_get_ext();
      if (CAMILA_FM_EXTFS_ENABLED)
          $filename = $filename.camila_hash(CAMILA_FM_PREFIX);
      $_CAMILA['camila_export_last_filename'] = $filename;
      return $filename;
  }

  function camila_export_safe_filename() {
      global $_CAMILA;
      $filename = trim($_REQUEST['camila_export_filename']);
      return $filename;
  }

  function camila_export_suggested_filename() {
      global $_CAMILA;
      $filename = trim($_CAMILA['page_full_title']) . ' ' . $_CAMILA['db']->UserDate(date('Y-m-d'), camila_get_locale_date_adodb_format());
      $filename = str_replace("/", "-", $filename);
      return $filename;
  }

  function camila_export_download_link() {
      global $_CAMILA;
      if (CAMILA_FM_AJAXPLORER_ENABLED)
          $fname = $_REQUEST['camila_export_action'] . '/' . $_CAMILA['page']->camila_export_safe_filename().'.'.$_CAMILA['page']->camila_export_get_ext();
      elseif (CAMILA_FM_EXTFS_ENABLED)
          $fname = $_CAMILA['camila_export_last_filename'];
      else
          $fname = $_CAMILA['page']->camila_export_safe_filename().'.'.$_CAMILA['page']->camila_export_get_ext();

      if (CAMILA_FM_AJAXPLORER_ENABLED)
          $url = 'cf_ajaxplorer_content.php?action=download&file=' . urlencode($fname);
      else
          $url = 'cf_docs.php?camila_download=' . urlencode($fname);
      return $url;
  }

  function camila_exporting()
  {
    if (isset($_REQUEST["camila_inline"]) || isset($_REQUEST["camila_print"]) || isset($_REQUEST["camila_soap"]) || isset($_REQUEST["camila_js"]) || isset($_REQUEST["camila_txt"]) || isset($_REQUEST["camila_pdf"]) || isset($_REQUEST["camila_xls"]) || isset($_REQUEST["camila_csv"]) || isset($_REQUEST["camila_rtf"]) || isset($_REQUEST["camila_xml2pdf"]) || isset($_REQUEST["camila_bookmark"]) || isset($_REQUEST["camila_json"]) || isset($_REQUEST["camila_xml"]))
      return true;
    else
      return false;
  }

  // Add js block in head section
  // If $id is different than '' $code is added once
  function camila_add_js($code, $id='')
  {
      if ($id=='' || !in_array($id, $this->camila_headjsscriptsids)) {
          $this->camila_headjsscripts[$this->camila_num_headjsscripts] = $code;
          $this->camila_num_headjsscripts++;
          $this->camila_headjsscriptsids[]=$id;
      }
  }


  function camila_set_jsstring($name,$val)
  {
      $this->camila_jsstrings[$name] = $val;
      $this->camila_num_jsstrings++;
  }

  function camila_collapsible_start($id,$expand=true,$title='')
  {
	if ($expand)
	  $code = "<div class='camilacollapsibleon'>";
	else
	  $code = "<div class='camilacollapsibleoff'>";

	$code.=$title."<div id='camilacollapsible_".$id."'>";
    $js = new CHAW_js($code);
    $this->add_userdefined($js);
    
  }

  function camila_collapsible_end()
  {
    $code = "</div></div>";
    $js = new CHAW_js($code);
    $this->add_userdefined($js);
  }
              
  function add_table($table)
  {
     $this->camila_num_tables++;
     parent::add_table($table);
  }

  
 function print_menu_children($parents,$titles,$visible,$urls,$father)
 {
   $count = 0;

   			//if ($father=='')
			//	echo '<li><a href="'.$urls[$i].'"><img src="../camila/images/png/home.png"></a>';

   for($i=0;$i<count($parents);$i++) {
	   if($parents[$i] == $father) {
	       if ($father!='' && $count==0)
		   {
              echo "<ul class=\"dropdown-menu\">";
			}
			

		  $count++;


		  $class="";
		  if ($father == '')
		  {
		      $currPage = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
			  if ($currPage == $urls[$i])
			     $class = 'current';
		  }

	      echo '<li><a href="'.$urls[$i].'" class="'.$class.'">'.$titles[$i].'</a>';

	      $this->print_menu_children($parents,$titles,$visible,$urls,$urls[$i]);
		  echo '</li>';
	   }
   }

   if ($father!='' && $count>0)
          echo "</ul>";
 }
 
 function add_footer($code) {
	 $this->camila_footer = $code;
	 
	 //$this->add_userdefined($object);
 }
  

  function create_page()
  {
    //global $haw_license_holder;
    //global $haw_license_domain;
    //global $haw_license_key;
    //global $haw_signature;
    //global $haw_sig_text;
    //global $haw_sig_link;

    // add hawoutput query parameter to redirection url, if required
    if ($this->red_url)
      HAW_handle_forced_output($this->red_url, $this->hawoutput);
  
 
    //if ($this->debug)
    //  header("content-type: text/plain");

    if ($this->disable_cache)
      header("cache-control: no-cache");

    if ($this->ml == HAW_HTML)
    {
      // create HTML page header

      if (!$this->debug)
      {
        if ($this->xhtml)
          $ct = "content-type: " . $this->xhtmlmp_preferred_mime_type;
        else
          $ct = sprintf("content-type: text/html;charset=%s", $this->charset);

        header($ct);
      }

      if ($this->css_enabled)
		  echo "<!DOCTYPE html>\n";

      if ($this->language)
      {
          $language = sprintf(" lang=\"%s\"", $this->language);
      }
      else
        $language = "";

     $namespace = "";
        
      printf("<html%s%s>\n", $language, $namespace);
      echo "<head>\n";

      // validation issue: HTML does not allow XHTML-stylish "/>" within <head> part
      // HTML has set option SHORTTAG=YES in SGML declaration
      // ==> "/" closes the tag and ">" will be treated as text
      // <body> allows text, but <head> does not! 
      if ($this->xhtml)
        $endtag = "/>";
      else
        $endtag = ">";

      //if (!$this->iModestyle && !$this->MMLstyle)
      {
        // cHTML and MML don't support meta tags

        //if ($haw_license_domain)
        //  $license = " - registered for $haw_license_domain";
        //else
        //  $license = "";

        //printf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\" %s\n",
        //        $this->charset, $endtag);
        //printf("<meta name=\"GENERATOR\" content=\"%s (PHP) %s%s\" %s\n",
        //       HAW_VERSION, HAW_COPYRIGHT, $license, $endtag);
		
		global $charset;

		$charset = $this->charset;

		//print ('<link rel="icon" href="images/favicon.ico"/>');
		
		////print ('<meta charset="utf-8">');
		//print ('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
		//print ('<meta name="viewport" content="width=device-width, initial-scale=1.0">');

        //if ($this->desktopBrowser && isset($haw_license_key))
        //{
        //  printf("<meta name=\"LICENSE_KEY\" content=\"%s\" %s\n",
        //         $haw_license_key, $endtag);
        //}

		global $timeout;
		$timeout = '';
        if ($this->timeout > 0)
        {
          //printf("<meta http-equiv=\"refresh\" content=\"%d; URL=%s\" %s\n",
          //        $this->timeout, HAW_specchar($this->red_url, $this), $endtag);
		  $timeout = sprintf("<meta http-equiv=\"refresh\" content=\"%d; URL=%s\" %s\n", $this->timeout, HAW_specchar($this->red_url, $this), $endtag);
		}
		
		//echo "\n".'<!--[if lt IE 9]>';
        //echo "\n".'<script src="../../camila/js/html5shiv/html5shiv.js"></script>';
		//echo "\n".'<![endif]-->';
		//echo "\n".'<!--[if lt IE 9]>';
        //echo "\n".'<script src="../../camila/js/respond.js/respond.min.js"></script>';
		//echo "\n".'<![endif]-->';

        /*if ($this->disable_cache)
        {
          echo "<meta http-equiv=\"Cache-Control\" content=\"must-revalidate\" $endtag\n";
          echo "<meta http-equiv=\"Cache-Control\" content=\"no-cache\" $endtag\n";
          echo "<meta http-equiv=\"Cache-Control\" content=\"max-age=0\" $endtag\n";
          echo "<meta http-equiv=\"Expires\" content=\"0\" $endtag\n";
        }*/
		
		global $headjsscripts;
        for ($i=0; $i<$this->camila_num_headjsscripts; $i++)
        {
          //global $_CAMILA;
          //if ($_CAMILA['output'] == HAW_OUTPUT_AUTOMATIC)
	      $headjsscripts .= $this->camila_headjsscripts[$i]."\n";	  
		}

		$TBS = new clsTinyButStrong();
		$TBS->SetOption(array('render'=>TBS_OUTPUT));
		//$TBS->SetOption('noerr', true);
		$TBS->SetOption('protect', false);
		$TBS->MethodsAllowed = true;
		$TBS->LoadTemplate(CAMILA_DIR.'/tbs/templates/head.htm');
		$TBS->Show();
      }


      //if ($this->PDAstyle)
      //{
      //  echo "<meta name=\"HandheldFriendly\" content=\"True\" $endtag\n";
      //}

      if ($this->css_enabled && $this->css)
      {
        printf("<link href=\"%s\" type=\"text/css\" rel=\"stylesheet\" />\n", $this->css);
      }

      // init style properties
      $bgcolor = "";
      $background = "";
      $disp_background = "";
      $size = "";
      $color = "";
      $link_color = "";
      $vlink_color = "";
      $face = "";
      //Camila Framework: text-align changed
      $bodystyle = "text-align: left; ";
      $css_style = "";

      if ($this->desktopBrowser || $this->PDAstyle || $this->xhtml)
      {
        if ($this->desktopBrowser)
        {
          // big-screen browser
 
          if ($this->bgcolor)
          {
            // set background color (=window background color) 
            $bgcolor = " bgcolor=\"" . $this->bgcolor . "\"";
            $bodystyle .= sprintf("background-color:%s; ", $this->bgcolor);
          }

          if ($this->background)
          {
            // set wallpaper (=window wallpaper)
            $background = " background=\"" . $this->background . "\"";
            $bodystyle .= sprintf("background-image:url(%s); ", $this->background);
          }

          /*if (!$this->css) // settings in css-file have priority
          {
            // set display defaults, if not assigned by yet
            if (!$this->disp_bgcolor)
              $this->disp_bgcolor = HAW_DISP_BGCOLOR;
            if (!$this->link_color)
              $this->link_color = HAW_DISP_LINKCOLOR;
            if (!$this->vlink_color)
              $this->vlink_color = HAW_DISP_VLINKCOLOR;
            if (!$this->face)
              $this->face = HAW_DISP_FACE;
          }*/
        }
        else
        {
          // XHTML or PDA

          if ($this->disp_bgcolor)
          {
            // set background color of mobile device
            $bgcolor = " bgcolor=\"" . $this->disp_bgcolor . "\"";
            $bodystyle .= sprintf("background-color:%s; ", $this->disp_bgcolor);
          }

          if ($this->disp_background)
          {
            // set wallpaper of mobile device
            $background = " background=\"" . $this->disp_background . "\"";
            $bodystyle .= sprintf("background-image:url(%s); ", $this->disp_background);
          }
        }

        if ($this->size)
        {
          // set the font size for all characters in a HTML created page
          $size = " size=\"" . $this->size . "\"";
          $bodystyle .= sprintf("font-size:%s; ", $this->size);
        }

        if ($this->color)
        {
          // set the color for all characters in a HTML created page
          $color = " color=\"" . $this->color . "\"";
          $bodystyle .= sprintf("color:%s; ", $this->color);
        }

        if ($this->link_color)
        {
          // set the color of links in a HTML created page
          $link_color = " link=\"" . $this->link_color . "\"";
          //$css_style .= sprintf("a:link { color:%s; }\n", $this->link_color);
        }

        if ($this->vlink_color)
        {
          // set the color of visited links in a HTML created page
          $vlink_color = " vlink=\"" . $this->vlink_color . "\"";
          //$css_style .= sprintf("a:visited { color:%s; }\n", $this->vlink_color);
        }

        if ($this->face)
        {
          // set the font for all characters in a HTML created page
          $face = " face=\"" . $this->face . "\"";
          $bodystyle .= sprintf("font-family:%s; ", $this->face);
        }

        $this->fontstyle_attrbs = $size . $color . $face;
      }

      printf("<title>%s</title>\n", HAW_specchar($this->title, $this));

      if ($this->desktopBrowser && ($this->use_simulator == HAW_SIM_SKIN) && $this->css_enabled)
        // use HAWHAW default- or user-defined skin
        printf("<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\" $endtag\n", $this->skin);

      //Camila Framework: Added

      if ($p_file = fopen(CAMILA_CSS_DIR.'print.css', "r")) {          
          while (!feof($p_file))
              $css_style.= fgets($p_file);
          
          fclose($p_file);  
      }

      if ($this->css_enabled) {
        printf("<style type=\"text/css\">\n<!--\nbody { %s}\n%s-->\n</style>\n", $bodystyle, $css_style);

        //Camila Framework: Added
      global $_CAMILA;
      if ($_CAMILA['user_preferences']['c_tf'] == '')
        $_CAMILA['user_preferences']['c_tf'] = CAMILA_TABLE_FACE;

      if ($_CAMILA['user_preferences']['c_ts'] == '')
        $_CAMILA['user_preferences']['c_ts'] = CAMILA_TABLE_SIZE;

        if (($_CAMILA['user_preferences']['c_ts'] != '') && ($_CAMILA['user_preferences']['c_tf'] != '')) {
            $tablestyle = sprintf("font-family:%s; ", $_CAMILA['user_preferences']['c_tf']);
            $tablestyle .= sprintf("font-size:%s; ", $_CAMILA['user_preferences']['c_ts']);

            printf("<style type=\"text/css\">\n<!--\ntable { %s}\n-->\n</style>\n", $tablestyle);
        }

      }

      echo "</head>\n";

      if ($this->css_enabled)
        echo "<body>\n";
      else
        printf("<body%s%s%s%s>\n", $bgcolor, $background, $link_color, $vlink_color);

        //Camila Framework: new block START

        if (!$this->camila_exporting() && !isset($_REQUEST['camila_popup']) && intval($_CAMILA['error'])==0) {
            $myPreferences = new CHAW_preferences();
            $myPreferences->create($this);
        }
        //Camila Framework: new block END


      if ($this->display_banners)
      {
        if ($this->number_of_top_banners > 0)
        {
          echo "<center>\n";

            for ($i=0; $i<$this->number_of_top_banners; $i++)
            {
              // display banners at the top of the HTML page
              $banner = $this->top_banners[$i];
              $banner->create();
            }

          echo "</center>\n";
        }
      }

      if ($this->desktopBrowser)
      {
        if ($this->css_enabled && ($this->use_simulator == HAW_SIM_SKIN))
        {
          //Camila Framework: New block START

		  if (is_array($_CAMILA['mainmenu']) || is_object($_CAMILA['mainmenu']))
		  {
			  echo '<div class="navbar navbar-default" role="navigation">';
			  echo '  <div class="navbar-header">';
			  echo '    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">';
			  echo '      <span class="sr-only">Toggle navigation</span>';
			  echo '      <span class="icon-bar"></span>';
			  echo '      <span class="icon-bar"></span>';
			  echo '      <span class="icon-bar"></span>';
			  echo '    </button>';
			  echo '    <a class="navbar-brand" href="'.CAMILA_HOME.'">'.CAMILA_APPLICATION_TITLE.'</a>';
			  echo '  </div>';
			  echo '  <div class="navbar-collapse collapse">';

          //echo "<div id=\"camilamenutop\">";
		  //echo "<ul id=\"main-menu\" class=\"sm ".$this->smartMenusTheme."\">";

		  echo "<ul id=\"main-menu\" class=\"nav navbar-nav\">";

		      $parents = array();
			  $titles = array();
			  $visible = array();
			  $urls = array();

			  foreach($_CAMILA['mainmenu'] as $key => $value)
			  {
			    $parents[$key]=$value['parent'];
				$titles[$key]=$value['short_title'];
				$visible[$key]=$value['visible'];
				$urls[$key]=$value['url'];
			  }

			  $this->print_menu_children($parents, $titles, $visible, $urls, $father);

		      //print_r($_CAMILA['mainmenu']);

			  		  echo "</ul>";
		  //echo "</div>";
		  
echo '		    </div><!--/.nav-collapse -->';
echo '</div>';

			  }
		  
          //Camila Framework: New block END
		  
          //Camila Framework: New block START
          //echo "<div id=\"camilamenuleft\"></div>";
          //Camila Framework: New block END

          // use skin design
		  echo "<div class=\"container\">";
          //echo "<div id=\"skin\">\n";

          if ($this->css_class)
            $class_param = " class=\"" . $this->css_class . "\"";
          else
            $class_param = "";
      
          //printf("<div id=\"display\"%s>\n", $class_param);
        }
        else if ($this->use_simulator == HAW_SIM_CLASSIC)
        {
          // use classic HAWHAW design
          //printf("<div id=\"classic\" style=\"background-color: %s; background-image: url(%s); border: %dpx solid #aaa; padding: 8px; width: %s; height: %s; margin: 0px auto; overflow: auto;\">\n",
          //       $this->disp_bgcolor, $this->disp_background, $this->border, $this->width, $this->height);
        }
      }

      if (!$this->css_enabled && $this->fontstyle_attrbs)
      {
        // write style attributes, if any
        printf("<font%s>\n", $this->fontstyle_attrbs);
      }
    }
    else
    {
      // determine default values for WML, HDML and VXML form elements

      while (list($e_key, $e_val) = each($this->element))
      {
        if ($e_val->get_elementtype() == HAW_FORM)
        {
          // one (and only one!) form exists

          $form = $e_val;
          $defaults = $form->get_defaults();
        }
      }

    }

    // text-align aligns left, centered or right
    $divstyle = sprintf("text-align:%s;", $this->alignment);

    // position:relative is needed to work-around MSIE's Peekaboo bug ...
    if (isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], " MSIE "))
      $divstyle .= " position:relative;";

    if ($this->ml == HAW_HTML)
    {
      if ($this->css_enabled)
      {
        if ($this->css_class)
        {
          $class = $this->css_class;

          if (isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], " MSIE "))
            $class .= " peekaboo"; // CSS hook for peekaboo handling
        }
        else
        {
          if (isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], " MSIE "))
            $class = "peekaboo";
          else
            $class = "";
        }
        
        if ($class)
          $class_param = " class=\"" . $class . "\"";
        else
          $class_param = "";
          
        //printf("<div id=\"canvas\" style=\"%s\"%s>\n", $divstyle, $class_param);
        //echo "<span id=\"hawcsshook1\"></span>\n"; // universal CSS hook for toolbars etc.
      }
      else
        printf("<div id=\"canvas\" align=\"%s\">\n", $this->alignment);
    }
    elseif ($this->ml == HAW_WML)
      printf("<p align=\"%s\">\n", $this->alignment);

    $i = 0;
    while (isset($this->element[$i]))
    {
      $page_element = $this->element[$i];
      switch ($page_element->get_elementtype())
      {
        case HAW_PLAINTEXT:
        case HAW_IMAGE:
        case HAW_TABLE:
        case HAW_FORM:
        case HAW_LINK:
        case HAW_PHONE:
        case HAW_LINKSET:
        case HAW_RAW:
        case HAW_PLUGIN:
        case HAW_RULE:
        case HAW_VOICERECORDER:
        {
          $element = $this->element[$i];
          $element->create($this);

          break;
        }
      }

      $i++;
    }

    if ($this->ml == HAW_HTML)
    {
      // create HTML page end

      //  ATTENTION!
      //
      //  DO NOT REMOVE THE COPYRIGHT LINK WITHOUT PERMISSION!
      //  IF YOU DO SO, YOU ARE VIOLATING THE LICENSE TERMS
      //  OF THIS SOFTWARE! YOU HAVE TO PAY NOTHING FOR THIS
      //  SOFTWARE, SO PLEASE BE SO FAIR TO ACCEPT THE RULES.
      // 
      //  PLEASE REFER TO THE LIBRARY HEADER AND THE HAWHAW
      //  HOMEPAGE FOR MORE INFORMATION:
      //  http://www.hawhaw.de/#license

      echo "</div>\n";

      if (!$this->css_enabled && $this->fontstyle_attrbs)
        echo "</font>\n";

      if ($this->desktopBrowser)
      {

        //Camila Framework:Moved down
        //if ($this->css_enabled && ($this->use_simulator == HAW_SIM_SKIN))
        //{
        //  // terminate display divs
        //  echo "</div>\n";
        //  echo "</div>\n";
        //}
        
        //if ($this->use_simulator == HAW_SIM_CLASSIC)
        //  echo "</div>\n"; // terminate classic div
		

        //Camila Framework:Moved down (see above)
        if ($this->css_enabled && ($this->use_simulator == HAW_SIM_SKIN))
        {
          // terminate display divs
		  
		  
          //echo "</div>\n";
          //echo "</div>\n";
		  //echo "</div>\n";
		  
        }

      }

      if ($this->display_banners)
      {
        if ($this->number_of_bottom_banners > 0)
        {
          echo "<center>\n";

            for ($i=0; $i<$this->number_of_bottom_banners; $i++)
            {
              // display banners at the bottom of the HTML page
              $banner = $this->bottom_banners[$i];
              $banner->create();
            }

          echo "</center>\n";
        }
      }
	  
	  		
	echo $this->camila_footer;


      echo "</body>\n";
      echo "</html>\n";
    }
    elseif ($this->ml == HAW_WML)
    {
      // create WML page end
      echo "</p>\n";
      echo "</card>\n";
      echo "</wml>\n";
    }
    elseif ($this->ml == HAW_HDML)
    {
      // create HDML page from hdml card set structure
      $cardset = $this->hdmlcardset;
      $cardset->create_hdmldeck();
    }
    elseif ($this->ml == HAW_VXML)
    {
      // create VoiceXML page end

      // set redirection timeout
      if ($this->red_url && ($this->timeout == 0))
      {
        // special handling for timeout value 0 in VoiceXML:
        // go immediately to next page after all text has been spoken
        // should be used for pure voice apps only!!!
        printf("<block><goto next=\"%s\" /></block>\n", HAW_specchar($this->red_url, $this));
      }
      elseif ($this->timeout > 0)
      {
        // redirect after <timout> to another URL
        // (define dummy grammar in case that no other grammar is active)
        printf("<field><grammar>[(hawhaw really rocks)]</grammar><prompt timeout=\"%ds\"/><noinput><goto next=\"%s\"/></noinput><nomatch /></field>\n",
               $this->timeout, HAW_specchar($this->red_url, $this));
      }
      elseif($this->voice_timeout > 0)
      {
        // there is at least one voice link active
        // wait longest link timeout value until disconnect is forced

        printf("<field><prompt timeout=\"%ds\"/>", $this->voice_timeout);

        // test for user-defined noinput event handler
        if (!isset($this->voice_noinput) || (count($this->voice_noinput) == 0))
          echo "<noinput><exit/></noinput>"; // terminate in case of no input

        echo "</field>\n";
      }

      echo "</form>\n";

      if ($this->voice_links)
        echo $this->voice_links;

      // create event handlers
      HAW_voice_eventhandler("help",    $this->voice_help,    $this);
      HAW_voice_eventhandler("noinput", $this->voice_noinput, $this);
      HAW_voice_eventhandler("nomatch", $this->voice_nomatch, $this);

      echo "</vxml>\n";
    }
  }
  
}

class CHAW_file
{
  var $name;
  var $label;
  var $size;
  var $maxlength;
  var $type;
  var $br;

  function CHAW_file($name, $label, $size="", $maxfilesize="")
  {
    $this->name = $name;
    $this->label = $label;
    $this->maxfilesize = $maxfilesize;
    $this->br = 1;
  }

  /**
    Set size of the input field. <br>
    Note: Will be ignored in case of HDML/VoiceXML output.
    @param size Number of characters fitting into the input field.
  */
  function set_size($size)
  {
    $this->size = $size;
  }

  /**
    Set maximum of allowed characters in the input field. <br>
    Note: Will be ignored in case of HDML output.
    @param maxlength Maximum number of characters the user can enter.
  */
  function set_maxlength($maxlength)
  {
    $this->maxlength = $maxlength;
  }


  function set_br($br)
  {
    if (!is_int($br) || ($br < 0))
      die("invalid argument in set_br()");

    $this->br = $br;
  }

  function get_name()
  {
    return $this->name;
  }

  function get_label()
  {
    return $this->label;
  }

  function get_size()
  {
    return $this->size;
  }

  function get_elementtype()
  {
    return HAW_USERDEFINED;
  }

  function create(&$deck)
  {
    $type = "type=\"file\"";

    if ($this->size)
      $size = sprintf("size=\"%d\"", $this->size);
    else
      $size = "";

    if ($deck->ml == HAW_HTML)
    {      
      // create HTML input
      if ($deck->xhtml || $deck->pureHTML)
      {
        if ($this->maxfilesize!="")
        printf("<input type=\"hidden\" name=\"%s\" value=\"%s\" />\n",
              "MAX_FILE_SIZE", $this->maxfilesize);
        printf("<label for=\"%s\">%s</label>\n",
                $this->name, HAW_specchar($this->label, $deck));
        printf("<input %s name=\"%s\" id=\"%s\" %s /> ",
                $type, $this->name, $this->name, $size);
        // create required amount of carriage return's
        for ($i=0; $i < $this->br; $i++)
          echo "<br />\n";
      }
    }
  }
};


class CHAW_preferences
{

  function CHAW_preferences()
  {
  }

  function get_elementtype()
  {
    return HAW_USERDEFINED;
  }

  function create(&$deck)
  {
    global $_CAMILA;
    if ($deck->ml == HAW_HTML && $_CAMILA['output'] == HAW_OUTPUT_AUTOMATIC)
    {	
		
		if (!(isset($_REQUEST['camila_print'])) && $_CAMILA['user_loggedin']){
		//echo '<section class="top">';
		//echo '<div class="container">';
		//echo '<div class="row">';
		//echo '<div class="col-xs-4 pull-left">';
		//echo '<div class="logo-small">'.CAMILA_APPLICATION_GROUP.'</div>';
		//echo '</div>';
		//echo '<div class="col-xs-8 pull-right text-right">';

		global $prefURL;
		$prefURL =$_SERVER['PHP_SELF'];
		if ($_SERVER['QUERY_STRING']!="")
			$prefURL.="?".urldecode($_SERVER['QUERY_STRING'])."&camila_preferences";
		else
			$prefURL.="?camila_preferences";
	
		global $anonLogin;
		$anonLogin = 1;
	
		global $logoutLink;
		$logoutLink = 0;
		if (!CAMILA_ANON_LOGIN)
			$anonLogin = 0;
		
	
      //if (!CAMILA_ANON_LOGIN)
      //    echo $_CAMILA['user_surname'].' '.$_CAMILA['user_name']."&nbsp;|&nbsp;";
      //echo "<a href='".$prefURL."'>".camila_get_translation('camila.prefs')."</a>";

      /*if (!CAMILA_ANON_LOGIN) {
          if (CAMILA_HELPURL){
              echo "&nbsp;|&nbsp;";
              echo "<a href='".CAMILA_HELPURL."' target='_blank'>" . camila_get_translation('camila.help') . "</a>";
          }
          global $_CAMILA;
          if ($_CAMILA['user_id']!='')
            echo "&nbsp;|&nbsp;<a href='" . CAMILA_LOGOUT_URL . "'>".camila_get_translation('camila.logout')."</a>";
		if ($_CAMILA['user_id']!='')
			$logoutLink = 1;
		
      }*/
	  
	  if ($_CAMILA['user_id']!='')
			$logoutLink = 1;

		//echo '</div>';
		//echo '</div>';
		//echo '</div>';
		//echo '</section>';
		
		$TBS = new clsTinyButStrong();
		$TBS->SetOption(array('render'=>TBS_OUTPUT));
		//$TBS->SetOption('noerr', true);
		$TBS->MethodsAllowed = true;
		$TBS->LoadTemplate(CAMILA_DIR.'/tbs/templates/user_actions.htm');			
		$TBS->Show();
    }
		

      //echo "<div id='camilapreferences'>";
      //echo "<div id='camilaapplicationname'><div>".CAMILA_APPLICATION_NAME."</div><div id='camilaapplicationtitle'>",$_CAMILA['app_title']."</div></div>\n<div id='camilapreferencesbar'>";
      /*if (!(isset($_REQUEST['camila_print'])) && $_CAMILA['user_loggedin']){
      $link=$_SERVER['PHP_SELF'];
      if ($_SERVER['QUERY_STRING']!="")
        $link.="?".urldecode($_SERVER['QUERY_STRING'])."&camila_preferences";
      else
        $link.="?camila_preferences";
      if (!CAMILA_ANON_LOGIN)
          echo $_CAMILA['user_surname'].' '.$_CAMILA['user_name']."&nbsp;|&nbsp;";
      echo "<a href='".$link."'>".camila_get_translation('camila.prefs')."</a>";

      if (!CAMILA_ANON_LOGIN) {
          if (CAMILA_HELPURL){
              echo "&nbsp;|&nbsp;";
              echo "<a href='".CAMILA_HELPURL."' target='_blank'>" . camila_get_translation('camila.help') . "</a>";
          }
          global $_CAMILA;
          if ($_CAMILA['user_id']!='')
            echo "&nbsp;|&nbsp;<a href='" . CAMILA_LOGOUT_URL . "'>".camila_get_translation('camila.logout')."</a>";
      }
    }*/
      //echo '</div></div>';
    }
  }
};

class CHAW_storepwd
{

  function CHAW_storepwd()
  {
  }

  function get_elementtype()
  {
    return HAW_USERDEFINED;
  }

  function create(&$deck)
  {
    global $_CAMILA;
    if ($deck->ml == HAW_HTML && $_CAMILA['output'] == HAW_OUTPUT_AUTOMATIC)
    {
      echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"".CAMILA_DIR."js/login_storepwd.js\"></script>";
      //echo "<a href=\"javascript:store()\"><img src=\"images/storepwd.png\" border=\"0\" alt=\"Memorizza codice numerico su questo computer\"/></a>";
    }
  }
};


class CHAW_getpwd
{

  function CHAW_getpwd($c1,$c2,$c3)
  {
    $this->c1=$c1;
    $this->c2=$c2;
    $this->c3=$c3;
  }

  function get_elementtype()
  {
    return HAW_USERDEFINED;
  }

  function create(&$deck)
  {
    if ($deck->ml == HAW_HTML)
    {
      echo "<div style=\"text-align:left; position:relative;\">";
      echo "<form action=\"login.php\" method=\"post\" onsubmit=\"return get(".$this->c1.",".$this->c2.",".$this->c3.");\">";
      include "login_js.php";
      echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"". CAMILA_DIR ."js/login_getpwd.js\"></script>";
      echo "</div>";
    }
  }
};

class CHAW_js
{

  function CHAW_js($js, $force='false')
  {
    $this->js=$js;
    $this->force=$force;
  }

  function get_elementtype()
  {
    return HAW_USERDEFINED;
  }

  function create(&$deck)
  {
    global $_CAMILA;

    if ( (!isset($_REQUEST['camila_print'])) && ($_CAMILA['output'] == HAW_OUTPUT_AUTOMATIC) && ($deck->ml == HAW_HTML) && ( ($this->force==true) || ($_CAMILA['javascript_enabled'] == '1') ) )
    {
      echo $this->js;
    }
  }
};

class CHAW_tbs
{

  function CHAW_tbs($tbs, $force='false')
  {
    $this->tbs=$tbs;
    $this->force=$force;
  }

  function get_elementtype()
  {
    return HAW_USERDEFINED;
  }

  function create(&$deck)
  {
    global $_CAMILA;

    if ( (!isset($_REQUEST['camila_print'])) && ($_CAMILA['output'] == HAW_OUTPUT_AUTOMATIC) && ($deck->ml == HAW_HTML) && ( ($this->force==true) || ($_CAMILA['javascript_enabled'] == '1') ) )
    {
      $this->tbs->Show();
    }
  }
};


class CHAW_form extends HAW_form
{
  var $_collapsible_pending = false;
  var $css_class;
  
  function set_css_class($css_class)
  {
    $this->css_class = $css_class;
  }

  function camila_collapsible_start($id,$expand=true,$title='',$link='',$css_class='')
  {
    if ($expand)
        $code = "<div class='camilacollapsibleon'>";
    else
        $code = "<div class='camilacollapsibleoff'>";
	
	if ($link != '')
	{
		$title = '<a class="'.$css_class.'" href="'.$link.'">'.$title.'</a>';
	}

    $code.=$title."<div id='camilacollapsible_".$id."'>";
    $js = new CHAW_js($code);
    $this->add_userdefined($js);
    
  }

  function camila_collapsible_end()
  {
    $code = "</div></div>";
    $js = new CHAW_js($code);
    $this->add_userdefined($js);
  }
  
  function add_submit($submit)
  {

    if (isset($_REQUEST['camila_print'])) {
	    $myHiddenElement = new CHAW_hidden('camila_print', 'cm');
	    $this->add_hidden($myHiddenElement);

	}
	
	if ($this->_collapsible_pending)
	    $this->camila_collapsible_end();
	
    $this->_collapsible_pending = false;
    
    parent::add_submit($submit);
  }

   function add_link($link)
  {
    if (!is_object($link))
      die("invalid argument in add_text()");

    $this->element[$this->number_of_elements] = $link;

    $this->number_of_elements++;
  }

  function create(&$deck)
  {
    // add hawoutput query parameter to form, if required
    if ($deck->hawoutput)
    {
      $hidden_hawoutput = new HAW_hidden("hawoutput", $deck->hawoutput);
      $this->add_hidden($hidden_hawoutput);
    }

    // determine all elements that have to be submitted

    $i = 0;
    $varnames = null;
    reset($this->element); //set the array pointer to first element
    while (list($key, $val) = each($this->element))
    {
      switch ($val->get_elementtype())
      {
        case HAW_INPUT:
        case HAW_TEXTAREA:
        case HAW_SELECT:
        case HAW_CHECKBOX:
        case HAW_RADIO:
        case HAW_HIDDEN:
        {
          $element = $val;
          $varnames[$i] = $element->get_name();
          $i++;
        }
      }
    }

    if ($deck->ml == HAW_HTML)
    {
      // start tag of HTML form
      if ($this->method == HAW_METHOD_POST)
      //Camila Framework: Mod (enctype added)
        $method = " method=\"post\" enctype=\"multipart/form-data\"";
      else
        $method = " method=\"get\"";

      printf("<form class=\"%s\" action=\"%s\"%s>\n", $this->css_class, $this->url, $method);

      if ($deck->xhtml)
        echo "<div id=\"hawform\">\n"; // needed for validation
    }
      // not necessary in WML, HDML and VoiceXML!

    if ($deck->ml == HAW_VXML)
    {
      if ($this->voice_text || $this->voice_audio_src)
      {
        // create introducing audio output for VoiceXML form
    
        echo "<block><prompt>";
  
        HAW_voice_audio(HAW_specchar($this->voice_text, $deck),
                        $this->voice_audio_src, HAW_VOICE_PAUSE, $deck);

        echo "</prompt></block>\n";
      }
    }

    $i = 0;
    while (isset($this->element[$i]))
    {
      $form_element = $this->element[$i];
      switch ($form_element->get_elementtype())
      {
        case HAW_PLAINTEXT:
        case HAW_IMAGE:
        case HAW_TABLE:
        case HAW_INPUT:
        case HAW_TEXTAREA:
        case HAW_SELECT:
        case HAW_RADIO:
        case HAW_CHECKBOX:
        case HAW_HIDDEN:
        case HAW_RAW:
        case HAW_RULE:
        case HAW_PLUGIN:
		case HAW_LINK:
        {
          $form_element->create($deck);
          break;
        }

        case HAW_SUBMIT:
        {
          $submit = $this->element[$i];
          $submit->create($deck, $varnames, $this->url, $this->method);
          break;
        }

      }

      $i++;
    }

    if ($deck->ml == HAW_HTML)
    {
      if ($deck->xhtml)
        echo "</div>\n";

      // terminate HTML form
      echo "</form>\n";
    }
  }

};


class CHAW_text extends HAW_text
{
  function set_id($id) {
      $this->id = $id;
  }

  function create(&$deck)
  {
    if ($deck->ml == HAW_HDML)
    {
      // HDML

      if ($deck->alignment != "left")
        $deck->hdmlcardset->add_display_content("<" . $deck->alignment . ">\n");

      // print text
      if ($this->text)
      {
        $content = sprintf("%s\n", HAW_specchar($this->text, $deck));
        $deck->hdmlcardset->add_display_content($content);
      }

      // create required amount of carriage return's
      $br = "";
      for ($i=0; $i < $this->br; $i++)
        $br .= "<br>\n";

      $deck->hdmlcardset->add_display_content($br);
    }
    elseif(($deck->ml == HAW_HTML) || ($deck->ml == HAW_WML))
    {
      // HTML or WML

      if (($deck->ml == HAW_HTML) && $deck->css_enabled && $this->css_class)
      {
        printf("<span class=\"%s\" id=\"%s\">\n", $this->css_class, $this->id);
      }
      
      if (($this->attrib & HAW_TEXTFORMAT_BOXED) && ($deck->ml == HAW_HTML))
      {
        // determine text and background color, if not already assigned

        if (!$this->color)
          $this->color = $deck->disp_bgcolor;
        if (!$this->color)
          $this->color = "#FFFFFF"; // default: white text

        if (!$this->boxcolor)
          $this->boxcolor = $deck->color;
        if (!$this->boxcolor)
          $this->boxcolor = "#000000"; // default: on black background

        if ($deck->css_enabled)
          printf("<div id=\"hawtextbox\" style=\"background-color:%s; margin:0px;\">\n",
                 $this->boxcolor);
        else
        {
          printf("<table border=\"0\" bgcolor=\"%s\" width=\"100%%\"><tr><td><font%s>\n",
                 $this->boxcolor, $deck->fontstyle_attrbs);
  
          // align text in (table-)box
          printf("<div id=\"hawtextbox\" align=\"%s\">\n", $deck->alignment);
        }

        // decrement line breaks because div/table already causes 1 br
        if ($this->br >= 1)
          $this->br--;
      }
      
      if ($this->attrib & HAW_TEXTFORMAT_BOLD)
        echo "<b>\n";

      if ($this->attrib & HAW_TEXTFORMAT_UNDERLINE)
      {
        if ($deck->css_enabled)
          echo "<span style=\"text-decoration:underline;\">\n";
        else
          echo "<u>\n";
      }

      if ($this->attrib & HAW_TEXTFORMAT_ITALIC)
        echo "<i>\n";

      if ($this->attrib & HAW_TEXTFORMAT_BIG)
        echo "<big>\n";

      if ($this->attrib & HAW_TEXTFORMAT_SMALL)
        echo "<small>\n";

      if (($deck->ml == HAW_HTML) && $this->color)
      {
        if ($deck->css_enabled)
          printf("<span style=\"color:%s;\">\n", $this->color);
        else
          printf("<font color=\"%s\">", $this->color);
      }

      // print text
      // Camila Framework: commented
      //if (isset($this->text))
      //  printf("%s\n", HAW_specchar($this->text, $deck));

      //Camila Framework - New block START
      $pieces = explode("\n", $this->text);
      $pcount=0;
      foreach ($pieces as $an_element)
      {
        if ($pcount>0)
          printf("<br/>%s\n", HAW_specchar($an_element, $deck));
        else
          printf("%s\n", HAW_specchar($an_element, $deck));
        $pcount++;
      }
      //Camila Framework - New block END


      if (($deck->ml == HAW_HTML) && $this->color)
      {
        if ($deck->css_enabled)
          echo "</span>";
        else
          echo "</font>";
      }

      if ($this->attrib & HAW_TEXTFORMAT_SMALL)
        echo "</small>\n";

      if ($this->attrib & HAW_TEXTFORMAT_BIG)
        echo "</big>\n";

      if ($this->attrib & HAW_TEXTFORMAT_ITALIC)
        echo "</i>\n";

      if ($this->attrib & HAW_TEXTFORMAT_UNDERLINE)
      {
        if ($deck->css_enabled)
          echo "</span>\n";
        else
          echo "</u>\n";
      }

      if ($this->attrib & HAW_TEXTFORMAT_BOLD)
        echo "</b>\n";

      if (($this->attrib & HAW_TEXTFORMAT_BOXED) && ($deck->ml == HAW_HTML))
      {
        if ($deck->css_enabled)
          echo "</div>\n";
        else
          echo "</div></font></td></tr></table>\n";
      }

      // create required amount of carriage return's
      for ($i=0; $i < $this->br; $i++)
        echo "<br />\n";

      if (($deck->ml == HAW_HTML) && $deck->css_enabled && $this->css_class)
        echo "</span>\n";
    }
    elseif($deck->ml == HAW_VXML)
    {
      // VoiceXML

      if ($this->voice_navigation)
      {
        // enable navigation (repeat/forward)
        static $block_counter = 0;
        printf("<block name=\"block%d\"><assign name=\"nav_counter\" expr=\"%d\"/><prompt>",
               $block_counter, $block_counter);        
      }
      else
        echo "<block><prompt>";
  
      $pause = $this->br * HAW_VOICE_PAUSE; // short pause for each break

      // remove leading commas, dots etc. which may appear after link objects
      HAW_voice_audio(ereg_replace("^[\?!,;.]", " ", HAW_specchar($this->voice_text, $deck)),
                      $this->voice_audio_src, $pause, $deck);
  
      echo "</prompt></block>\n";

      if ($this->voice_navigation)
      {
        // create artificial field to control VoiceXML sequencing
        printf("<field name=\"dummy%d\">\n", $block_counter);
        echo "<property name=\"timeout\" value=\"0.5s\"/>\n";
        echo "<grammar>[(hawhaw really rocks)]</grammar>\n";
        printf("<noinput><assign name=\"dummy%d\" expr=\"true\"/></noinput>\n", $block_counter);
        echo "</field>\n";

        // create block end where forward will go to
        printf("<block name=\"block%dend\"/>\n", $block_counter++);
      }
    }
  }
};


class CHAW_image extends HAW_image
{

  function set_id($id) {
      $this->id = $id;
  }

  function set_css_class($css_class)
  {
    $this->css_class = $css_class;
  }

  function create(&$deck)
  {
    if (isset($_SERVER['HTTP_ACCEPT']))
      $HTTP_ACCEPT = $_SERVER['HTTP_ACCEPT'];
    else
      $HTTP_ACCEPT = "";

    if ($deck->ml == HAW_HDML)
    {
      // HDML

      if ($deck->alignment != "left")
        $deck->hdmlcardset->add_display_content("<" . $deck->alignment . ">\n");

      if ($this->localsrc)
        $icon = sprintf(" icon=\"%s\"", $this->localsrc);
      else
        $icon = "";

      $content = sprintf("<img src=\"%s\" alt=\"%s\"%s>\n",
                         $this->src_bmp,
                         HAW_specchar($this->alt, $deck), $icon);

      $deck->hdmlcardset->add_display_content($content);

      // create required amount of carriage return's
      $br = "";
      for ($i=0; $i < $this->br; $i++)
        $br .= "<br>\n";

      $deck->hdmlcardset->add_display_content($br);
    }
    elseif (($deck->ml == HAW_HTML) || ($deck->ml == HAW_WML))
    {
      // HTML or WML

      $size = "";
      if ($this->html_width)
        $size .= sprintf(" width=\"%d\"", $this->html_width);
      if ($this->html_height)
        $size .= sprintf(" height=\"%d\"", $this->html_height);
      
      if ($deck->ml == HAW_HTML)
      {
        // HTML

        if ($deck->iModestyle && $this->chtml_icon)
        {
          // cHTML icon available ==> use this icon instead of bitmap
          printf("&#%d;", $this->chtml_icon);
        }
        elseif ($deck->MMLstyle && $this->mml_icon)
        {
          // MML icon available ==> use this icon instead of bitmap
          echo CHR(27) . "$" . $this->mml_icon . CHR(15);
        }
        else
        {
          // use HTML bitmap
          
          if ($deck->css_enabled)
            $style = " style=\"vertical-align:middle; border-style:none\"";
          else
            $style = " align=\"middle\" border=\"0\"";


          printf("<span class=\"%s\" id=\"%s\"><img src=\"%s\" alt=\"%s\"%s%s /></span>",$this->css_class, $this->id,
                 $this->src_html, HAW_specchar($this->alt, $deck), $size, $style);
        }

        // evaluate HTML break instruction
        if ($deck->MMLstyle)
          $br_command = "<br>\n"; // MML has problems with clear attribute
        elseif ($deck->xhtml)
          $br_command = "<br style=\"clear:both;\" />\n"; // XHTML does not know clear attribute
        else
          $br_command = "<br clear=\"all\" />\n";
      }
      else
      {
        // WML

        if ($this->localsrc)
          $localsrc = sprintf(" localsrc=\"%s\"", $this->localsrc);
        else
          $localsrc = "";

        if ($deck->gif_enabled && (substr(strtolower($this->src_html), -4) == ".gif"))
          // user agent is able to display the provided GIF image
          printf("<img src=\"%s\" alt=\"%s\"%s%s/>\n", $this->src_html,
                  HAW_specchar($this->alt, $deck), $localsrc, $size);

        elseif (strstr(strtolower($HTTP_ACCEPT), "image/vnd.wap.wbmp"))
          // user agent is able to display .wbmp image
          printf("<img src=\"%s\" alt=\"%s\"%s/>\n", $this->src_wbmp,
                  HAW_specchar($this->alt, $deck), $localsrc);

        elseif (strstr(strtolower($HTTP_ACCEPT), "image/bmp") && $this->src_bmp)
          // user agent is able to display .bmp and .bmp image is available
          printf("<img src=\"%s\" alt=\"%s\"%s/>\n", $this->src_bmp,
                  HAW_specchar($this->alt, $deck), $localsrc);

        else
          // hope that the user agent makes the best of it!
          printf("<img src=\"%s\" alt=\"%s\"%s/>\n", $this->src_wbmp,
                  HAW_specchar($this->alt, $deck), $localsrc);

        // break instruction in WML
        $br_command = "<br/>\n";
      }

      // create required amount of carriage return's
      for ($i=0; $i < $this->br; $i++)
        echo $br_command;
    }
    elseif ($deck->ml == HAW_VXML)
    {
      // VoiceXML

      if ($this->voice_text || $this->voice_audio_src)
      {
        // create image-related audio output for VoiceXML images

        echo "<block><prompt>";

        HAW_voice_audio(HAW_specchar($this->voice_text, $deck),
                        $this->voice_audio_src, HAW_VOICE_PAUSE, $deck);
  
        echo "</prompt></block>\n";
      }
    }
  }


};


class CHAW_table extends HAW_table
{
    var $camila_equal_columns = false;
};


class CHAW_row extends HAW_row
{
};


class CHAW_input extends HAW_input
{
	var $css_class;
  
  function set_css_class($css_class)
  {
    $this->css_class = $css_class;
  }

  function CHAW_input($name, $value, $label, $format="*M")
  {
    global $_CAMILA;
    $this->name = $name;
    //Camila Framework Mod: $value -> HAW_specchar($value)
    $this->value = HAW_specchar($value, $_CAMILA['page']);
    //$this->value = $value;
    $this->label = $label;
    $this->format = $format;
    $this->type = HAW_INPUT_TEXT;
    $this->mode = HAW_INPUT_ALPHABET;
    $this->br = 1;
    $this->voice_text = $label;
    $this->voice_audio_src = "";
    $this->voice_type = "digits";
    $this->voice_grammar = "";
    $this->voice_help = array();
    $this->voice_noinput = array();
    $this->voice_nomatch = array();
  }
  
  function create(&$deck)
  {
    $class_param = "";

    if ($this->type == HAW_INPUT_PASSWORD)
    {
      $type = "type=\"password\"";
      
      if ($deck->css_enabled)
        $class_param = " class=\"hawinputpassword\"";

  	if ($this->css_class != '')
		$class_param = " class=\"".$this->css_class."\"";
    }
    else
    {
      $type = "type=\"text\"";
      
      if ($deck->css_enabled)
        $class_param = " class=\"hawinputtext\"";
	
      if ($this->css_class != '')
		$class_param = " class=\"".$this->css_class."\"";
    }
      
    if ($this->size)
      $size = sprintf("size=\"%d\"", $this->size);
    else
      $size = "";

    if ($this->maxlength)
      $maxlength = sprintf("maxlength=\"%d\"", $this->maxlength);
    else
      $maxlength = "";

    if ($deck->ml == HAW_HTML)
    {
      if ($deck->iModestyle)
        $mode = sprintf(" istyle=\"%d\"", $this->mode);
      else
        $mode = "";

      if ($deck->MMLstyle)
      {
        switch ($this->mode)
        {
          case HAW_INPUT_ALPHABET: { $mode = " mode=\"alphabet\""; break; }
          case HAW_INPUT_KATAKANA: { $mode = " mode=\"katakana\""; break; }
          case HAW_INPUT_HIRAGANA: { $mode = " mode=\"hiragana\""; break; }
          case HAW_INPUT_NUMERIC:  { $mode = " mode=\"numeric\"";  break; }
          default:                 { $mode = " mode=\"alphabet\""; break; }
        }
      }

      // create HTML input
      if ($deck->xhtml || $deck->desktopBrowser)
      {
        if (trim($this->label)!=':')
			printf("<label for=\"%s\">%s</label>\n",
                $this->name, HAW_specchar($this->label, $deck));
        printf("<input %s name=\"%s\" id=\"%s\" value=\"%s\" %s %s%s%s /> ",
                $type, $this->name,
                $this->name, $this->value, $size, $maxlength, $mode, $class_param);
      }
      else
        printf("%s <input %s name=\"%s\" value=\"%s\" %s %s%s%s /> ",
                HAW_specchar($this->label, $deck), $type,
                $this->name, $this->value, $size, $maxlength, $mode, $class_param);

      for ($i=0; $i < $this->br; $i++)
        echo "<br />\n";
    }
    elseif ($deck->ml == HAW_WML)
    {
      // create WML input
      printf("%s<input emptyok=\"true\" format=\"%s\" %s name=\"%s\" value=\"%s\" %s %s/>\n",
              HAW_specchar($this->label, $deck), $this->format,
              $type, $this->name, $this->value, $size, $maxlength);
    }
    elseif ($deck->ml == HAW_HDML)
    {
      // create HDML input

      $options  = " format=\"$this->format\"";
      $options .= " key=\"$this->name\"";

      if ($this->type == HAW_INPUT_PASSWORD)
        $options .= " NOECHO=\"true\"";

      if ($deck->alignment != "left")
        $display_content = "<" . $deck->alignment . ">\n";

      $display_content .= HAW_specchar($this->label, $deck);
      $display_content .= "\n";

      // make user interactive entry card
      $deck->hdmlcardset->make_ui_card($options, $display_content, HAW_HDML_ENTRY);
    }
    elseif ($deck->ml == HAW_VXML)
    {
      // create VoiceXML input

      if ($this->voice_type)
      {
        if (($this->voice_type == "digits") && $this->maxlength)
          $type = sprintf(" type=\"digits?maxlength=%d\"", $this->maxlength);
        else
          $type = sprintf(" type=\"%s\"", $this->voice_type);
      }
      else
        $type = "";

      printf("<field%s name=\"%s\">\n", $type, $this->name);

      if ($this->voice_grammar)
      {
        // external grammar has been defined

        if ($this->voice_grammar["type"])
          $grammar_type = sprintf(" type=\"%s\"", $this->voice_grammar["type"]);
        else
          $grammar_type = ""; // let the interpreter context determine the grammar type

        printf("<grammar src=\"%s\"%s/>\n",
                $this->voice_grammar["src"], $grammar_type);
      }

      if ($this->voice_text || $this->voice_audio_src)
      {
        echo "<prompt>";

        HAW_voice_audio(HAW_specchar($this->voice_text, $deck),
                        $this->voice_audio_src, 0, $deck);

        echo "</prompt>\n";
      }

      // create event handlers
      HAW_voice_eventhandler("help",    $this->voice_help,    $deck);
      HAW_voice_eventhandler("noinput", $this->voice_noinput, $deck);
      HAW_voice_eventhandler("nomatch", $this->voice_nomatch, $deck);
  
      echo "</field>\n";
    }
  }
};


class CHAW_textarea extends HAW_textarea
{
};


class CHAW_select extends HAW_select
{
};


class CHAW_radio extends HAW_radio
{
};


class CHAW_checkbox extends HAW_checkbox
{
  //Camila Framework Add
  var $br=1;

  function set_br($br)
  {
    if (!is_int($br) || ($br < 0))
      die("invalid argument in set_br()");

    $this->br = $br;
  }

  function create(&$deck)
  {
    if ($deck->ml == HAW_HTML)
    {
      // create HTML checkbox

      $state = ($this->is_checked() ? "checked=\"checked\"" : "");

      if ($deck->xhtml || $deck->desktopBrowser)
      {
        printf("<input type=\"checkbox\" name=\"%s\" id=\"%s\" %s value=\"%s\" />",
                $this->name, $this->name, $state, $this->value);

        //Camila Framework Mod BEGIN

        if ($this->cols == 2)
            printf(" <label style=\"display: inline-block;display: -moz-inline-box;width:200px;\" for=\"%s\">%s</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n",
                    $this->name, HAW_specchar($this->label, $deck));
        else
            printf(" <label for=\"%s\">%s</label>\n",
                    $this->name, HAW_specchar($this->label, $deck));


        // create required amount of carriage return's
        $br = '';
        for ($i=0; $i < $this->br-1; $i++)
          $br .= '<br />';
        echo $br;

        //Camila Framework Mod END
      }
      else
        printf("<input type=\"checkbox\" name=\"%s\" %s value=\"%s\" /> %s\n",
                $this->name, $state, $this->value,
                HAW_specchar($this->label, $deck));
        //Camila Framework Mod BEGIN
        // create required amount of carriage return's
        $br = '';
        for ($i=0; $i < $this->br; $i++)
          $br .= '<br />';
        echo $br;
        //Camila Framework Mod END
    }

  }
};


class CHAW_hidden extends HAW_hidden
{
  function create(&$deck)
  {
      if (isset($_REQUEST['camila_print']) && ($_REQUEST['camila_print']!='save'))
          return;
      else
          parent::create($deck);
  }

};


class CHAW_submit extends HAW_submit
{
};


class CHAW_link extends HAW_link
{

  function CHAW_link($label, $url, $title="")
  {

    if (isset($_REQUEST["camila_print"])) {
	    
	    $pos = strpos($url, "?");
 
       if ($pos === false) {
               $url.="?camila_print";
       } else {
               $url.="&camila_print";
       }
	    
	}

    $this->{get_parent_class(__CLASS__)}($label, $url, $title);

  }

};


class CHAW_linkset extends HAW_linkset
{
  function create(&$deck)
  {
    if ($this->number_of_elements > 0)
    {
      if ($deck->ml == HAW_HTML)
      {
        // create linkset in HTML

        if ($deck->css_enabled)
        {
          // create links inside a frame
          
          //Camila Framework - Line Mod
          echo "<div id=\"linkset\">\n";

          //Camila Framework Mod - BEGIN
          if (!$deck->lynx)
          {
            echo "<div id=\"nav\">\n";
            // create link list to avoid whitespace between links
            echo "<ul class=\"nav nav-tabs\">\n";
            while (list($key, $val) = each($this->element))
            {
              echo "<li role=\"presentation\">\n";
              $val->create($deck); // create one list element for each link
              echo "</li>\n";
            }
            echo "</ul>\n";
            //echo "<span id=\"navclear\"></span>\n";
            echo "</div>\n";
          }
          //Camila Framework Mod - END
          /*if ($deck->lynx)
          {
            // create link list to avoid whitespace between links
            echo "<ul>\n";
            while (list($key, $val) = each($this->element))
            {
              echo "<li>\n";
              $val->create($deck); // create one list element for each link
              echo "</li>\n";
            }
            echo "</ul>\n";
          }
          else*/
          {
            while (list($key, $val) = each($this->element))
            {
              //Camila Framework - Line Added
              echo "&nbsp;&lt;";
              $val->create($deck); // create one list element for each link
              //Camila Framework - Line Added
              echo "&gt;";
            }
          }

          echo "</div>\n";
        }
        else
          // create normal links for the small devices
          while (list($key, $val) = each($this->element))
            $val->create($deck);
      }

      

    }
  }  
};


class CHAW_raw extends HAW_raw
{
};



class CHAW_rule extends HAW_rule
{
};


?>
