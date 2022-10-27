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


require_once(CAMILA_DIR.'ui.class.inc.php');
//$_CAMILA['skin'] = $_CAMILA['user_preferences']['c_sk'];

//if ($_CAMILA['javascript_enabled'] == 1 && $_CAMILA['skin'] == 0)
//    $_CAMILA['skin'] = CAMILA_DEFAULT_JS_SKIN;

$_CAMILA['page']= new CHAW_deck();

$camilaUI = new CamilaUserInterface();
$camilaUI->insertTitle(camila_get_translation('camila.prefs'),'user');
$camilaUI->insertLineBreak();
$_CAMILA['page']->camila_export_enabled = false;

//$export_deck_title = new CHAW_text(camila_get_translation('camila.prefs'), $_CAMILA["page_title_attributes"]);
//$export_deck_title->set_br(2);
//$export_deck_title->set_color($_CAMILA['page_title_color'], $_CAMILA['page_title_boxcolor']);
//$export_deck_title->set_color('white', '#000080');
//$_CAMILA['page']->add_text($export_deck_title);


$myForm = new CHAW_form('cf_redirect.php');

/*$text = new CHAW_text(camila_get_translation('camila.prefs.js'));
$text->set_br(0);
$myForm->add_text($text);

$mySelect = new CHAW_select('camila_javascript');
if ($_CAMILA['javascript_enabled'] == 1)
  $mySelect->add_option(camila_get_translation('camila.enabled2'), "on", HAW_SELECTED);
else
  $mySelect->add_option(camila_get_translation('camila.enabled2'), "on");

if ($_CAMILA['javascript_enabled'] != 1)
  $mySelect->add_option(camila_get_translation('camila.disabled2'), "off", HAW_SELECTED);
else
  $mySelect->add_option(camila_get_translation('camila.disabled2'), "off");
$myForm->add_select($mySelect);

$text = new CHAW_text('');
$text->set_br(2);
$myForm->add_text($text);

$text = new CHAW_text(camila_get_translation('camila.prefs.skin'));
$text->set_br(0);
$myForm->add_text($text);
$mySelect = new CHAW_select('camila_skin_number');

if ($_CAMILA['skin'] == 2)
  $mySelect->add_option(camila_get_translation('camila.prefs.skin2'), "2", HAW_SELECTED);
else
  $mySelect->add_option(camila_get_translation('camila.prefs.skin2'), "2");

if ($_CAMILA['skin'] == 1)
  $mySelect->add_option(camila_get_translation('camila.prefs.skin1'), "1", HAW_SELECTED);
else
  $mySelect->add_option(camila_get_translation('camila.prefs.skin1'), "1");

if ($_CAMILA['skin'] == 0)
  $mySelect->add_option(camila_get_translation('camila.prefs.skin0'), "-", HAW_SELECTED);
else
  $mySelect->add_option(camila_get_translation('camila.prefs.skin0'), "-");

$myForm->add_select($mySelect);
$text = new CHAW_text('');
$text->set_br(2);
$myForm->add_text($text);
*/

if ($_CAMILA['user_preferences']['c_ff'] == '')
    $_CAMILA['user_preferences']['c_ff'] = CAMILA_FACE;

if ($_CAMILA['user_preferences']['c_tf'] == '')
    $_CAMILA['user_preferences']['c_tf'] = CAMILA_TABLE_FACE;


$text = new CHAW_text(camila_get_translation('camila.prefs.fonttype'));
$text->set_br(0);
$myForm->add_text($text);
$mySelect = new CHAW_select('camila_font_face');
$mySelect3 = new CHAW_select('camila_table_font_face');


$_fields = explode(',','Arial,Times,Verdana');

while (list($key, $value) = each($_fields)) {
    if ($_CAMILA['user_preferences']['c_ff'] == $value)
        $mySelect->add_option($value, $value, HAW_SELECTED);
    else
        $mySelect->add_option($value, $value);

    if ($_CAMILA['user_preferences']['c_tf'] == $value)
        $mySelect3->add_option($value, $value, HAW_SELECTED);
    else
        $mySelect3->add_option($value, $value);

}

$myForm->add_select($mySelect);
$text = new CHAW_text('');
$text->set_br(2);
$myForm->add_text($text);

if ($_CAMILA['user_preferences']['c_fs'] == '')
    $_CAMILA['user_preferences']['c_fs'] = CAMILA_SIZE;

if ($_CAMILA['user_preferences']['c_ts'] == '')
    $_CAMILA['user_preferences']['c_ts'] = CAMILA_TABLE_SIZE;

$text = new CHAW_text(camila_get_translation('camila.prefs.fontsize'));
$text->set_br(0);
$myForm->add_text($text);
$mySelect = new CHAW_select('camila_font_size');
$mySelect2 = new CHAW_select('camila_table_font_size');


$_fields = explode(',','7pt,8pt,9pt,10pt,11pt,12pt,13pt,15pt,16pt,17pt,18pt,19pt,20pt,21pt,22pt,23pt,24pt');

while (list($key, $value) = each($_fields)) {
    if ($_CAMILA['user_preferences']['c_fs'] == $value)
        $mySelect->add_option($value, $value, HAW_SELECTED);
    else
        $mySelect->add_option($value, $value);

    if ($_CAMILA['user_preferences']['c_ts'] == $value)
        $mySelect2->add_option($value, $value, HAW_SELECTED);
    else
        $mySelect2->add_option($value, $value);

}

$myForm->add_select($mySelect);
$text = new CHAW_text('');
$text->set_br(2);
$myForm->add_text($text);

$text = new CHAW_text(camila_get_translation('camila.prefs.tables.fonttype'));
$text->set_br(0);
$myForm->add_text($text);

$myForm->add_select($mySelect3);
$text = new CHAW_text('');
$text->set_br(2);
$myForm->add_text($text);

$text = new CHAW_text(camila_get_translation('camila.prefs.tables.fontsize'));
$text->set_br(0);
$myForm->add_text($text);
$myForm->add_select($mySelect2);

$text = new CHAW_text('');
$text->set_br(2);
$myForm->add_text($text);


if (!intval($_CAMILA['user_preferences']['c_rp']))
    $_CAMILA['user_preferences']['c_rp'] = CAMILA_REPORT_RPP;

$myInput = new CHAW_input('camila_rows_per_page', $_CAMILA['user_preferences']['c_rp'], camila_get_translation('camila.prefs.tables.rowsperpage'));
$myInput->set_br(2);
$myForm->add_input($myInput);

$url=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];
$url = str_replace("&"."camila_preferences", "", $url);
$url = str_replace("\?"."camila_preferences", "", $url);

$myInput = new CHAW_hidden('camila_redirect', $url);
$myForm->add_input($myInput);
$theSubmission = new CHAW_submit(camila_get_translation('camila.save'), 'submit');
$theSubmission->set_css_class('btn btn-md btn-default');
$myForm->add_submit($theSubmission);
$_CAMILA['page']->add_form($myForm);

if (CAMILA_USER_CAN_CHANGE_PWD) {
    //$myLink = new CHAW_link(camila_get_translation('camila.login.changepwd'),'cf_changepwd.php');
	//$myLink->set_css_class('btn btn-md btn-default');
    //$myLink->set_br(3);
    //$_CAMILA['page']->add_link($myLink);
	
	$camilaUI->insertButton('cf_changepwd.php', camila_get_translation('camila.login.changepwd'), 'lock');
}

$_CAMILA['page']->use_simulator(CAMILA_CSS_DIR . 'skin2.css');
require(CAMILA_DIR . 'deck_settings.php');
require(CAMILA_DIR . 'footer.php');
exit();
?>