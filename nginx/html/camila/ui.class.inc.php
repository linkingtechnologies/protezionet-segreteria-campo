<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2022 Umberto Bresciani

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

class CamilaUserInterface
{
	function __construct()
    {
    }

	function insertButton($link, $text, $icon, $br = true, $badge='') {
		global $_CAMILA;
		$b = '';
		if ($badge != '')
			$b=' <span class="badge">'.$badge.'</span>';
		$html = '<a href="'.$link.'" type="button" class="btn btn-md btn-default btn-primary btn-space" aria-label=""><span class="glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span> '.$text.$b.'</a>';
		if ($br)
			$html.='<br />';
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, $html));
	}

	function insertTitle($text, $icon){
		global $_CAMILA;
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<h3><span class="glyphicon glyphicon-'.$icon.'"></span> '.$text.'</h3>'));
	}
	
	function insertSubTitle($text, $icon){
		global $_CAMILA;
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<h4><span class="glyphicon glyphicon-'.$icon.'"></span> '.$text.'</h4>'));
	}

	public static function insertWarning($text){
		global $_CAMILA;
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="alert alert-warning" role="alert">'.$text.'</div>'));
	}
	
	public static function insertSuccess($text){
		global $_CAMILA;
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="alert alert-success" role="success">'.$text.'</div>'));
	}

	function insertDivider(){
		global $_CAMILA;
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<br /><hr />'));
	}
	
	function insertText($txt, $br=1) {
		global $_CAMILA;
		$text = new CHAW_text($txt);
		$text->set_br($br);
		$_CAMILA['page']->add_text($text);
	}
	
	function insertLink($link, $txt, $br=1) {
		global $_CAMILA;
		$myLink = new CHAW_link($txt, $link);
		//$myLink->set_css_class('btn');
		$myLink->set_br($br);
		$_CAMILA['page']->add_link($myLink);
	}
	
	function printHomeMenu($confFile, $defaultId = '') {
		$current = Array();
		global $_CAMILA;
		$menu = new SimpleXMLElement(file_get_contents($confFile));
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div id="linkset2"><div id="nav"><ul class="nav nav-tabs">'));
		
		foreach ($menu as $k => $v) {
			$curr = false;
			if ((isset($_REQUEST['dashboard']) && strpos('?'.$_SERVER['QUERY_STRING'], (string)($v->url)) !==  false) || ((string)($v->id) == $defaultId) || (isset($_REQUEST['dashboard']) && strpos(','.(string)($v->pages).',', ','.$_REQUEST['dashboard'].',') !==  false))
			{
				$current = Array('id' => (string)$v->id, 'url' => (string)$v->url, 'title' => (string)$v->title);
				$curr = true;
			}
			$url = $v->url;
			$title = '';
			if ((string)$v->lic_title != '')
				$title = camila_get_translation((string)$v->lic_title);
			else
				$title = $v->title;

			$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<li role="presentation" class="'. ($curr ? 'active' : '' ).'"><a class="'. ($curr ? 'active' : '' ).'" href="'.$url.'">'.$title.'</a></li>'));
		}

		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</ul></div></div>'));
		return $current;
	}
	
	function insertLineBreak() {
		global $_CAMILA;
		$html.='<br />';
		$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, $html));
	}

}

?>